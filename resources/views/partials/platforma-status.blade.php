{{-- Status slanja priznatih ispita u studentsku platformu.
     Vars: $zapis (Mobilnost|MappingRequest), $student, $posaljiRoute, $moze (bool: smije se slati) --}}
@if(config('platforma.enabled'))
<div class="mt-4 rounded-lg border p-3 text-sm {{ $zapis->platforma_poslato_at ? 'border-green-200 bg-green-50' : ($zapis->platforma_greska ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-gray-50') }}">
    <div class="flex items-start justify-between gap-3">
        <div>
            <div class="font-semibold text-gray-800">Studentska platforma</div>
            @if(!$student?->platforma_student_id)
                <p class="text-gray-600 mt-1">Student nije povezan sa platformom. Priznati ispiti se ne šalju u karton.</p>
            @elseif($zapis->platforma_poslato_at)
                @php $odg = json_decode($zapis->platforma_odgovor ?? '{}', true) ?: []; @endphp
                <p class="text-green-800 mt-1">
                    Poslato {{ $zapis->platforma_poslato_at->format('d.m.Y H:i') }}:
                    upisano {{ $odg['upisano'] ?? 0 }}, ažurirano {{ $odg['azurirano'] ?? 0 }} ispita u kartonu studenta.
                </p>
                @if($zapis->platforma_greska)
                    <p class="text-red-700 mt-1">Poslednji pokušaj nije uspio: {{ $zapis->platforma_greska }}</p>
                @endif
            @elseif($zapis->platforma_greska)
                <p class="text-red-700 mt-1">Slanje nije uspjelo: {{ $zapis->platforma_greska }}</p>
            @else
                <p class="text-gray-600 mt-1">Priznati ispiti još nisu poslati u karton studenta (ID na platformi: {{ $student->platforma_student_id }}).</p>
            @endif
        </div>
        @if($student?->platforma_student_id && $moze && config('platforma.writeback'))
            <form action="{{ $posaljiRoute }}" method="POST" onsubmit="return confirm('Poslati priznate ispite u karton studenta na platformi?')">
                @csrf
                <button type="submit" class="whitespace-nowrap bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-3 py-1.5 rounded-md shadow">
                    {{ $zapis->platforma_poslato_at ? 'Pošalji ponovo' : 'Pošalji u platformu' }}
                </button>
            </form>
        @endif
    </div>
</div>
@endif
