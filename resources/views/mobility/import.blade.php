<x-app-layout>
    <style>
        .import-page {
            background: #f3f4f6;
            min-height: calc(100vh - 80px);
            padding: 40px 24px 56px;
        }

        .import-wrapper {
            max-width: 1100px;
            margin: 0 auto;
        }

        .import-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 32px 36px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
        }

        .import-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .import-title {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .import-subtitle {
            font-size: 15px;
            color: #64748b;
            margin-bottom: 28px;
        }

        .help-button {
            width: 42px;
            height: 42px;
            border-radius: 999px;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #0f172a;
            font-size: 20px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .help-button:hover {
            background: #eff6ff;
            border-color: #93c5fd;
            color: #1d4ed8;
        }

        .help-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 18px;
            padding: 18px 20px;
            margin-bottom: 24px;
            display: none;
        }

        .help-box.active {
            display: block;
        }

        .help-box h3 {
            margin: 0 0 12px;
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }

        .help-box p {
            margin: 0 0 10px;
            color: #475569;
            font-size: 14px;
        }

        .help-box ul {
            margin: 8px 0 16px 18px;
            color: #334155;
            font-size: 14px;
        }

        .help-box li {
            margin-bottom: 6px;
        }

        .import-form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 22px;
            max-width: 760px;
        }

        .field-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .field-label {
            font-size: 15px;
            font-weight: 600;
            color: #1f2937;
        }

        .form-select,
        .form-control {
            width: 100%;
            min-height: 54px;
            border: 1px solid #d1d5db;
            border-radius: 16px;
            box-shadow: none !important;
            font-size: 15px;
            padding: 12px 16px;
            background: #fff;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 0.18rem rgba(37, 99, 235, 0.14) !important;
        }

        .action-bar {
            margin-top: 34px;
            display: flex;
            justify-content: flex-end;
            gap: 14px;
            flex-wrap: wrap;
        }

        .action-form {
            margin: 0;
        }

        .btn-import-secondary,
        .btn-import-primary {
            min-width: 180px;
            height: 50px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 15px;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-import-secondary {
            background: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .btn-import-secondary:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }

        .btn-import-primary {
            background: #2563eb;
            color: #ffffff;
        }

        .btn-import-primary:hover {
            background: #1d4ed8;
        }

        .btn-import-primary:disabled {
            background: #93c5fd;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .preview-box,
        .success-box,
        .error-box,
        .info-box {
            border-radius: 16px;
            padding: 16px 18px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .preview-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
        }

        .success-box {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .info-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #334155;
        }

        .preview-columns {
            margin: 10px 0 0;
            padding-left: 18px;
        }

        .preview-columns li {
            margin-bottom: 4px;
        }

        .preview-valid {
            margin-top: 12px;
            color: #166534;
            font-weight: 600;
        }

        .preview-invalid {
            margin-top: 12px;
            color: #b91c1c;
            font-weight: 600;
        }

        .preview-missing {
            margin-top: 8px;
            padding-left: 18px;
            color: #b91c1c;
        }

        .file-status {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            padding: 12px 14px;
            color: #334155;
            font-size: 14px;
        }

        .file-status strong {
            color: #0f172a;
        }

        .step-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 16px 18px;
            margin-bottom: 18px;
        }

        .step-box-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .step-box-text {
            font-size: 14px;
            color: #475569;
        }

        @media (max-width: 768px) {
            .import-card {
                padding: 24px 18px;
                border-radius: 18px;
            }

            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .action-form {
                width: 100%;
            }

            .btn-import-secondary,
            .btn-import-primary {
                width: 100%;
                min-width: auto;
            }
        }
    </style>

    <div class="import-page">
        <div class="import-wrapper">
            <div class="import-card">

                <div class="import-header">
                    <div>
                        <div class="import-title">Import mobilnosti</div>
                        <div class="import-subtitle">
                            Učitaj Excel fajl i ZIP dokumentaciju za automatski unos postojećih mobilnosti za više studenata odjednom.
                        </div>
                    </div>

                    <button type="button" class="help-button" id="toggle-help" title="Prikaži uputstvo">?</button>
                </div>

                <div class="help-box" id="help-box">
                    <h3>Uputstvo za import</h3>

                    <p><strong>1. Excel fajl</strong></p>
                    <p>Svaki red u Excel fajlu predstavlja jednog studenta i jednu mobilnost. Obavezne kolone su:</p>
                    <ul>
                        <li>Br indeksa</li>
                        <li>Ime</li>
                        <li>Prezime</li>
                        <li>Host fakultet</li>
                        <li>Država</li>
                        <li>Datum početka</li>
                        <li>Datum završetka</li>
                        <li>Tip mobilnosti</li>
                        <li>Studijska godina</li>
                        <li>Priznavanje završeno</li>
                    </ul>

                    <p><strong>2. ZIP fajl</strong></p>
                    <p>ZIP mora sadržati poseban folder za svakog studenta. Naziv foldera mora odgovarati broju indeksa studenta.</p>
                    <p>Primjer: ako je broj indeksa <strong>21/001</strong>, naziv foldera treba da bude <strong>21_001</strong>.</p>

                    <p><strong>3. Nazivi dokumenata</strong></p>
                    <p>Naziv svakog fajla mora odgovarati jednoj od vrsta dokumenata iz baze. Dozvoljeni nazivi su:</p>
                    <ul>
                        @forelse($documentCategories as $category)
                            <li>{{ $category->name }}.pdf / .doc / .docx</li>
                        @empty
                            <li>Nema definisanih kategorija dokumenata u bazi.</li>
                        @endforelse
                    </ul>

                    <p><strong>4. Obavezni dokumenti</strong></p>
                    <ul>
                        <li>Za svaki import je obavezan dokument kategorije <strong>Learning Agreement</strong> ili <strong>LA</strong>.</li>
                        <li>Ako je vrijednost „Priznavanje završeno“ = <strong>da</strong>, obavezni su i dokumenti kategorije <strong>ToR</strong> i <strong>Odluka</strong>.</li>
                    </ul>

                    <p><strong>Primjer ZIP strukture:</strong></p>
                    <ul>
                        <li>21_001 / Learning Agreement.pdf</li>
                        <li>21_001 / ToR.pdf</li>
                        <li>21_001 / Odluka.pdf</li>
                        <li>21_002 / Learning Agreement.pdf</li>
                    </ul>
                </div>

                @if(session('success'))
                    <div class="success-box">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="error-box">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="error-box">
                        <strong>Došlo je do greške:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('import_errors'))
                    <div class="error-box">
                        <strong>Preskočeni redovi:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach(session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(!empty($excelTmpPath) || !empty($zipTmpPath))
                    <div class="info-box">
                        <strong>Fajlovi su privremeno sačuvani.</strong><br>
                        Nije potrebno ponovo birati fajlove prije importa osim ako želiš da ih zamijeniš novim.
                    </div>
                @endif

                <div class="help-box" id="help-box">
    <h3>Uputstvo za import mobilnosti</h3>

    <p><strong>Excel fajl</strong></p>
    <p>
        Excel fajl mora sadržati podatke za više studenata.
        Svaki red predstavlja jednog studenta i jednu mobilnost.
        Prvi red mora sadržati nazive kolona.
    </p>

    <p><strong>Obavezne kolone:</strong></p>
    <ul>
        <li>Br indeksa</li>
        <li>Ime</li>
        <li>Prezime</li>
        <li>Host fakultet</li>
        <li>Država</li>
        <li>Datum početka</li>
        <li>Datum završetka</li>
        <li>Tip mobilnosti (Semestralna ili Godišnja)</li>
        <li>Studijska godina</li>
        <li>Priznavanje završeno (DA / NE)</li>
    </ul>

    <p><strong>Važno:</strong></p>
    <ul>
        <li>Prvi red mora biti header (nazivi kolona)</li>
        <li>Svaki sljedeći red je jedan student</li>
        <li>Vrijednost "Host fakultet" mora odgovarati izabranom fakultetu</li>
    </ul>

    <hr>

    <p><strong>ZIP fajl (dokumentacija)</strong></p>
    <p>
        ZIP fajl mora sadržati posebne foldere za svakog studenta.
        Naziv foldera mora odgovarati broju indeksa studenta.
    </p>

    <p><strong>Primjer:</strong></p>
    <ul>
        <li>52-23</li>
        <li>09-23</li>
        <li>109-22</li>
    </ul>

    <p>
        Ako indeks sadrži znak "/", on se zamjenjuje sa "_"
    </p>

    <p><strong>Primjer strukture ZIP-a:</strong></p>
    <ul>
        <li>52-23 / Learning Agreement.pdf</li>
        <li>52-23 / ToR.pdf</li>
        <li>52-23 / Odluka.pdf</li>
        <li>09-23 / Learning Agreement.pdf</li>
    </ul>

    <hr>

    <p><strong>Dozvoljeni nazivi fajlova</strong></p>
    <p>
        Naziv svakog dokumenta mora odgovarati jednoj od vrsta dokumenata iz baze.
    </p>

    <ul>
        @foreach($documentCategories as $category)
            <li>{{ $category->name }} (npr. {{ $category->name }}.pdf)</li>
        @endforeach
    </ul>

    <p><strong>Napomena:</strong></p>
    <ul>
        <li>Dozvoljeni formati: PDF, DOC, DOCX</li>
        <li>Naziv fajla mora odgovarati nazivu kategorije</li>
    </ul>

    <hr>

    <p><strong>Obavezni dokumenti</strong></p>
    <ul>
        <li>Learning Agreement (obavezan)</li>
        <li>Ako je "Priznavanje završeno" = DA:
            <ul>
                <li>ToR</li>
                <li>Odluka</li>
            </ul>
        </li>
    </ul>
</div>
                @if(isset($previewData) && $previewData)
                    <div class="preview-box">
                        <div><strong>Preview uspješan</strong></div>
                        <div>Broj redova u Excel fajlu: {{ $previewData['row_count'] }}</div>
                        <div>ZIP fajl: {{ $previewData['zip_name'] }}</div>

                        @if(!empty($previewData['header']))
                            <div class="mt-2"><strong>Kolone iz Excel fajla:</strong></div>
                            <ul class="preview-columns">
                                @foreach($previewData['header'] as $column)
                                    <li>{{ $column ?: '(prazna kolona)' }}</li>
                                @endforeach
                            </ul>
                        @endif

                        @if($previewData['is_valid_template'])
                            <div class="preview-valid">
                                Excel template je ispravan.
                            </div>

                            <div class="mt-3">
                                <div><strong>Validni redovi:</strong> {{ $previewData['valid_rows'] }}</div>
                                <div><strong>Nevalidni redovi:</strong> {{ $previewData['invalid_rows'] }}</div>
                            </div>

                            @if(!empty($previewData['row_errors']))
                                <div class="preview-invalid mt-4">
                                    Greške po redovima:
                                </div>
                                <div class="mt-2 space-y-3">
                                    @foreach($previewData['row_errors'] as $rowError)
                                        <div class="bg-white/60 border border-red-200 rounded-xl p-3">
                                            <div class="font-semibold text-red-700">
                                                Red {{ $rowError['row'] }}
                                                @if($rowError['index'])
                                                    — {{ $rowError['index'] }}
                                                @endif
                                                @if($rowError['student'])
                                                    — {{ $rowError['student'] }}
                                                @endif
                                            </div>
                                            <ul class="preview-missing">
                                                @foreach($rowError['errors'] as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="preview-invalid">
                                Nedostaju obavezne kolone:
                            </div>
                            <ul class="preview-missing">
                                @foreach($previewData['missing_columns'] as $column)
                                    <li>{{ $column }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif

                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">
                        Podaci za import
                    </h2>

                    <a href="{{ route('adminDashboardShow') }}"
                       class="inline-flex items-center text-lg font-semibold text-gray-900 hover:text-indigo-600 transition">
                        <span class="mr-2">←</span>
                        Nazad na mobilnosti
                    </a>
                </div>

                <form id="preview-form" method="POST" action="{{ route('admin.mobility.import.preview') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="import-form-grid">
                        <div class="field-group">
                            <label for="fakultet_id" class="field-label">Izaberi fakultet</label>
                            <select name="fakultet_id" id="fakultet_id" class="form-select" required>
                                <option value="">-- Izaberi fakultet --</option>
                                @foreach($fakulteti as $fakultet)
                                    <option value="{{ $fakultet->id }}"
                                        {{ old('fakultet_id', $selectedFakultetId ?? null) == $fakultet->id ? 'selected' : '' }}>
                                        {{ $fakultet->naziv }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if(!empty($excelTmpPath))
                            <input type="hidden" name="excel_tmp_path" value="{{ $excelTmpPath }}">
                            <div class="field-group">
                                <label class="field-label">Excel fajl</label>
                                <div class="file-status">
                                    <strong>Excel fajl je privremeno sačuvan.</strong><br>
                                    Ako želiš drugi fajl, izaberi novi ispod.
                                </div>
                                <input
                                    type="file"
                                    name="excel_file"
                                    id="excel_file"
                                    class="form-control"
                                    accept=".xlsx,.xls"
                                >
                            </div>
                        @else
                            <div class="field-group">
                                <label for="excel_file" class="field-label">Excel fajl</label>
                                <input
                                    type="file"
                                    name="excel_file"
                                    id="excel_file"
                                    class="form-control"
                                    accept=".xlsx,.xls"
                                    required
                                >
                            </div>
                        @endif

                        @if(!empty($zipTmpPath))
                            <input type="hidden" name="zip_tmp_path" value="{{ $zipTmpPath }}">
                            <div class="field-group">
                                <label class="field-label">ZIP fajl sa dokumentacijom</label>
                                <div class="file-status">
                                    <strong>ZIP fajl je privremeno sačuvan.</strong><br>
                                    Ako želiš drugi fajl, izaberi novi ispod.
                                </div>
                                <input
                                    type="file"
                                    name="zip_file"
                                    id="zip_file"
                                    class="form-control"
                                    accept=".zip"
                                >
                            </div>
                        @else
                            <div class="field-group">
                                <label for="zip_file" class="field-label">ZIP fajl sa dokumentacijom</label>
                                <input
                                    type="file"
                                    name="zip_file"
                                    id="zip_file"
                                    class="form-control"
                                    accept=".zip"
                                    required
                                >
                            </div>
                        @endif
                    </div>
                </form>

                <div class="action-bar">
                    <button
                        type="submit"
                        form="preview-form"
                        class="btn-import-secondary"
                    >
                        Provjeri
                    </button>

                    <form method="POST" action="{{ route('admin.mobility.import.store') }}" class="action-form">
                        @csrf
                        <input type="hidden" name="fakultet_id" value="{{ old('fakultet_id', $selectedFakultetId ?? '') }}">
                        <input type="hidden" name="excel_tmp_path" value="{{ $excelTmpPath ?? '' }}">
                        <input type="hidden" name="zip_tmp_path" value="{{ $zipTmpPath ?? '' }}">

                        <button
                            type="submit"
                            class="btn-import-primary"
                            {{ empty($excelTmpPath) || empty($zipTmpPath) ? 'disabled' : '' }}
                        >
                            Importuj mobilnosti
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleButton = document.getElementById('toggle-help');
            const helpBox = document.getElementById('help-box');

            if (toggleButton && helpBox) {
                toggleButton.addEventListener('click', function () {
                    helpBox.classList.toggle('active');
                });
            }
        });
    </script>
</x-app-layout>