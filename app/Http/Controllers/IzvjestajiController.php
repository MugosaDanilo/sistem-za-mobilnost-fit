<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Prepis;
use App\Models\Mobilnost;
use App\Models\NivoStudija;
use App\Models\Fakultet;

class IzvjestajiController extends Controller
{
    public function index(Request $request)
    {
        $driver = DB::getDriverName();

        $filterNivo = $request->get('nivo');
        $filterYear = $request->get('year');
        $filterFakultet = $request->get('fakultet');

        if ($driver === 'sqlite') {
            $studentsQuery = Student::selectRaw("strftime('%Y', created_at) as year, COUNT(*) as total");
            if ($filterNivo) $studentsQuery->where('nivo_studija_id', $filterNivo);
            if ($filterYear) $studentsQuery->whereRaw("strftime('%Y', created_at) = ?", [$filterYear]);
            $students = $studentsQuery->groupBy('year')->orderBy('year')->get();

            // Also get gender breakdown per year
            $studentsWithGender = Student::selectRaw("strftime('%Y', created_at) as year, pol, COUNT(*) as total");
            if ($filterNivo) $studentsWithGender->where('nivo_studija_id', $filterNivo);
            if ($filterYear) $studentsWithGender->whereRaw("strftime('%Y', created_at) = ?", [$filterYear]);
            $genderPerYear = $studentsWithGender->groupBy('year', 'pol')->orderBy('year')->get();

            $prepisRaw = Prepis::with('fakultet')
                ->when($filterFakultet, function($q) use ($filterFakultet) { return $q->where('fakultet_id', $filterFakultet); })
                ->get();
            $prepisiAgg = [];
            foreach ($prepisRaw as $p) {
                $year = \Carbon\Carbon::parse($p->datum)->format('Y');
                $fakultetNaziv = $p->fakultet->naziv ?? 'Nepoznato';
                $key = $year . '|' . $fakultetNaziv;
                if (!isset($prepisiAgg[$key])) {
                    $prepisiAgg[$key] = ['year' => $year, 'fakultet' => $fakultetNaziv, 'total' => 0];
                }
                $prepisiAgg[$key]['total']++;
            }
            $prepisi = collect($prepisiAgg)->sortBy(function($x) { return $x['year'] . $x['fakultet']; })->values()->map(function($x) { return (object)$x; });
        } else {
            // assume MySQL / MariaDB / Postgres with YEAR() support
            $studentsQuery = Student::selectRaw('YEAR(created_at) as year, COUNT(*) as total');
            if ($filterNivo) $studentsQuery->where('nivo_studija_id', $filterNivo);
            if ($filterYear) $studentsQuery->whereRaw('YEAR(created_at) = ?', [$filterYear]);
            $students = $studentsQuery->groupBy('year')->orderBy('year')->get();

            // Also get gender breakdown per year
            $studentsWithGender = Student::selectRaw('YEAR(created_at) as year, pol, COUNT(*) as total');
            if ($filterNivo) $studentsWithGender->where('nivo_studija_id', $filterNivo);
            if ($filterYear) $studentsWithGender->whereRaw('YEAR(created_at) = ?', [$filterYear]);
            $genderPerYear = $studentsWithGender->groupBy('year', 'pol')->orderBy('year')->get();

            $prepisRaw = Prepis::with('fakultet')
                ->when($filterFakultet, function($q) use ($filterFakultet) { return $q->where('fakultet_id', $filterFakultet); })
                ->get();
            $prepisiAgg = [];
            foreach ($prepisRaw as $p) {
                $year = \Carbon\Carbon::parse($p->datum)->format('Y');
                $fakultetNaziv = $p->fakultet->naziv ?? 'Nepoznato';
                $key = $year . '|' . $fakultetNaziv;
                if (!isset($prepisiAgg[$key])) {
                    $prepisiAgg[$key] = ['year' => $year, 'fakultet' => $fakultetNaziv, 'total' => 0];
                }
                $prepisiAgg[$key]['total']++;
            }
            $prepisi = collect($prepisiAgg)->sortBy(function($x) { return $x['year'] . $x['fakultet']; })->values()->map(function($x) { return (object)$x; });
        }

        // Merge gender data into students
        $students = $students->map(function ($row) use ($genderPerYear) {
            $musko = $genderPerYear->where('year', $row->year)->where('pol', 1)->sum('total');
            $zensko = $genderPerYear->where('year', $row->year)->where('pol', 0)->sum('total');
            $row->musko = $musko;
            $row->zensko = $zensko;
            return $row;
        });

        // students by gender (respect filters)
        if ($driver === 'sqlite') {
            $genderQ = Student::selectRaw("pol as pol, COUNT(*) as total");
            if ($filterNivo) $genderQ->where('nivo_studija_id', $filterNivo);
            if ($filterYear) $genderQ->whereRaw("strftime('%Y', created_at) = ?", [$filterYear]);
            $studentsByGender = $genderQ->groupBy('pol')->get();
        } else {
            $genderQ = Student::selectRaw('pol as pol, COUNT(*) as total');
            if ($filterNivo) $genderQ->where('nivo_studija_id', $filterNivo);
            if ($filterYear) $genderQ->whereRaw('YEAR(created_at) = ?', [$filterYear]);
            $studentsByGender = $genderQ->groupBy('pol')->get();
        }

        // students by nivo studija
        $byNivoQuery = Student::selectRaw('nivo_studija_id, COUNT(*) as total');
        if ($filterYear) {
            if ($driver === 'sqlite') {
                $byNivoQuery->whereRaw("strftime('%Y', created_at) = ?", [$filterYear]);
            } else {
                $byNivoQuery->whereRaw('YEAR(created_at) = ?', [$filterYear]);
            }
        }
        if ($filterNivo) {
            $byNivoQuery->where('nivo_studija_id', $filterNivo);
        }
        $byNivo = $byNivoQuery->groupBy('nivo_studija_id')->get()->map(function ($r) {
            $nivo = NivoStudija::find($r->nivo_studija_id);
            return (object) ['label' => $nivo->naziv ?? 'Nepoznato', 'total' => $r->total];
        });

        // cumulative students over years
        $cumulative = collect();
        $sum = 0;
        foreach ($students as $row) {
            $sum += $row->total;
            $cumulative->push((object)['year' => $row->year, 'cumulative' => $sum]);
        }

        // available nivo options for filter
        $nivoOptions = NivoStudija::orderBy('id')->get();

        // Build detailed mobilnosti stats using PHP to be DB-agnostic
        $mobilnostiRaw = Mobilnost::with('student.nivoStudija')->get();

        $mobilnostiAgg = [];
        foreach ($mobilnostiRaw as $m) {
            $date = $m->datum_pocetka ?? $m->created_at ?? null;
            if (!$date) continue;

            $year = \Carbon\Carbon::parse($date)->format('Y');
            if (!isset($mobilnostiAgg[$year])) {
                $mobilnostiAgg[$year] = [
                    'year' => $year,
                    'total' => 0,
                    'musko' => 0,
                    'zensko' => 0,
                    'master' => 0,
                    'osnovne' => 0,
                ];
            }

            $mobilnostiAgg[$year]['total']++;

            $student = $m->student;
            if ($student) {
                // pol: boolean where 1 = musko, 0 = zensko
                if ($student->pol) {
                    $mobilnostiAgg[$year]['musko']++;
                } else {
                    $mobilnostiAgg[$year]['zensko']++;
                }

                $nivo = $student->nivoStudija->naziv ?? null;
                if ($nivo && mb_stripos($nivo, 'master') !== false) {
                    $mobilnostiAgg[$year]['master']++;
                } else {
                    $mobilnostiAgg[$year]['osnovne']++;
                }
            }
        }

        // Convert to sorted array
        $mobilnosti = collect($mobilnostiAgg)->sortBy('year')->values()->map(function ($r) {
            $r['procenat_musko'] = $r['total'] > 0 ? round(($r['musko'] / $r['total']) * 100, 2) : 0;
            $r['procenat_zensko'] = $r['total'] > 0 ? round(($r['zensko'] / $r['total']) * 100, 2) : 0;
            return (object) $r;
        });

        $fakulteti = Fakultet::orderBy('naziv')->get();
        return view('izvjestaji.index', compact('students', 'prepisi', 'mobilnosti', 'studentsByGender', 'byNivo', 'cumulative', 'nivoOptions', 'filterNivo', 'filterYear', 'fakulteti', 'filterFakultet'));
    }

    public function export(Request $request, $type)
    {
        // Gather data same as index
        $driver = DB::getDriverName();

        $filterNivo = $request->get('nivo');
        $filterYear = $request->get('year');
        $filterFakultet = $request->get('fakultet');

        if ($driver === 'sqlite') {
            $studentsQuery = Student::selectRaw("strftime('%Y', created_at) as year, COUNT(*) as total");
            if ($filterNivo) $studentsQuery->where('nivo_studija_id', $filterNivo);
            if ($filterYear) $studentsQuery->whereRaw("strftime('%Y', created_at) = ?", [$filterYear]);
            $students = $studentsQuery->groupBy('year')->orderBy('year')->get();

            $prepisRaw = Prepis::with('fakultet')
                ->when($filterFakultet, function($q) use ($filterFakultet) { return $q->where('fakultet_id', $filterFakultet); })
                ->get();
            $prepisiAgg = [];
            foreach ($prepisRaw as $p) {
                $year = \Carbon\Carbon::parse($p->datum)->format('Y');
                $fakultetNaziv = $p->fakultet->naziv ?? 'Nepoznato';
                $key = $year . '|' . $fakultetNaziv;
                if (!isset($prepisiAgg[$key])) {
                    $prepisiAgg[$key] = ['year' => $year, 'fakultet' => $fakultetNaziv, 'total' => 0];
                }
                $prepisiAgg[$key]['total']++;
            }
            $prepisi = collect($prepisiAgg)->sortBy(function($x) { return $x['year'] . $x['fakultet']; })->values()->map(function($x) { return (object)$x; });
        } else {
            $studentsQuery = Student::selectRaw('YEAR(created_at) as year, COUNT(*) as total');
            if ($filterNivo) $studentsQuery->where('nivo_studija_id', $filterNivo);
            if ($filterYear) $studentsQuery->whereRaw('YEAR(created_at) = ?', [$filterYear]);
            $students = $studentsQuery->groupBy('year')->orderBy('year')->get();

            $prepisRaw = Prepis::with('fakultet')
                ->when($filterFakultet, function($q) use ($filterFakultet) { return $q->where('fakultet_id', $filterFakultet); })
                ->get();
            $prepisiAgg = [];
            foreach ($prepisRaw as $p) {
                $year = \Carbon\Carbon::parse($p->datum)->format('Y');
                $fakultetNaziv = $p->fakultet->naziv ?? 'Nepoznato';
                $key = $year . '|' . $fakultetNaziv;
                if (!isset($prepisiAgg[$key])) {
                    $prepisiAgg[$key] = ['year' => $year, 'fakultet' => $fakultetNaziv, 'total' => 0];
                }
                $prepisiAgg[$key]['total']++;
            }
            $prepisi = collect($prepisiAgg)->sortBy(function($x) { return $x['year'] . $x['fakultet']; })->values()->map(function($x) { return (object)$x; });
        }

        $mobilnostiRaw = Mobilnost::with('student.nivoStudija')->get();
        $mobilnostiAgg = [];
        foreach ($mobilnostiRaw as $m) {
            $date = $m->datum_pocetka ?? $m->created_at ?? null;
            if (!$date) continue;

            $year = \Carbon\Carbon::parse($date)->format('Y');
            if (!isset($mobilnostiAgg[$year])) {
                $mobilnostiAgg[$year] = [
                    'year' => $year,
                    'total' => 0,
                    'musko' => 0,
                    'zensko' => 0,
                    'master' => 0,
                    'osnovne' => 0,
                ];
            }

            $mobilnostiAgg[$year]['total']++;
            $student = $m->student;
            if ($student) {
                $pol = (bool) ($student->pol ?? false);
                if ($pol) {
                    $mobilnostiAgg[$year]['musko']++;
                } else {
                    $mobilnostiAgg[$year]['zensko']++;
                }

                $nivo = $student->nivoStudija->naziv ?? null;
                if ($nivo && mb_stripos($nivo, 'master') !== false) {
                    $mobilnostiAgg[$year]['master']++;
                } else {
                    $mobilnostiAgg[$year]['osnovne']++;
                }
            }
        }

        $mobilnosti = collect($mobilnostiAgg)->sortBy('year')->values()->map(function ($r) {
            $r['procenat_musko'] = $r['total'] > 0 ? round(($r['musko'] / $r['total']) * 100, 2) : 0;
            $r['procenat_zensko'] = $r['total'] > 0 ? round(($r['zensko'] / $r['total']) * 100, 2) : 0;
            return (object) $r;
        });

        // Generate CSV
        if ($type === 'students') {
            return $this->exportCsv('Studenti', ['Godina', 'Broj'], $students);
        } elseif ($type === 'prepisi') {
            return $this->exportCsv('Prepisi', ['Godina', 'Broj'], $prepisi);
        } elseif ($type === 'mobilnost') {
            $data = $mobilnosti->map(function ($r) {
                return [
                    $r->year,
                    $r->total,
                    $r->musko,
                    $r->zensko,
                    $r->procenat_musko,
                    $r->procenat_zensko,
                    $r->master,
                    $r->osnovne,
                ];
            })->toArray();
            return $this->exportCsv('Mobilnost', ['Godina', 'Ukupno', 'Musko', 'Zensko', 'Procenat Musko (%)', 'Procenat Zensko (%)', 'Master', 'Osnovne'], $data);
        }

        return redirect()->back()->with('error', 'Nepoznat tip izvjestaja');
    }

    private function exportCsv($filename, $headers, $data)
    {
        $csvData = [];
        $csvData[] = $headers;

        foreach ($data as $row) {
            if (is_object($row)) {
                // Handle object with year and total properties
                $rowData = [];
                foreach ($headers as $header) {
                    if ($header === 'Godina') {
                        $rowData[] = $row->year ?? '';
                    } elseif ($header === 'Broj') {
                        $rowData[] = $row->total ?? '';
                    } elseif ($header === 'Ukupno') {
                        $rowData[] = $row->total ?? '';
                    } elseif ($header === 'Musko') {
                        $rowData[] = $row->musko ?? '';
                    } elseif ($header === 'Zensko') {
                        $rowData[] = $row->zensko ?? '';
                    } elseif ($header === 'Procenat Musko (%)') {
                        $rowData[] = $row->procenat_musko ?? '';
                    } elseif ($header === 'Procenat Zensko (%)') {
                        $rowData[] = $row->procenat_zensko ?? '';
                    } elseif ($header === 'Master') {
                        $rowData[] = $row->master ?? '';
                    } elseif ($header === 'Osnovne') {
                        $rowData[] = $row->osnovne ?? '';
                    }
                }
                $csvData[] = $rowData;
            } else {
                // Handle array row
                $csvData[] = $row;
            }
        }

        $csv = '';
        foreach ($csvData as $row) {
            $csv .= implode(',', array_map(function ($cell) {
                return '"' . str_replace('"', '""', (string) $cell) . '"';
            }, $row)) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}.csv\"");
    }
}