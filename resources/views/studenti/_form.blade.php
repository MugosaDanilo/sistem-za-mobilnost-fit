@php($editing = isset($student))

<form method="POST" action="{{ $editing ? route('studenti.update', $student) : route('studenti.store') }}">
    @csrf
    @if($editing) @method('PUT') @endif

    <div>
        <label>Ime</label><br>
        <input name="ime" value="{{ old('ime', $student->ime ?? '') }}">
    </div>

    <div>
        <label>Prezime</label><br>
        <input name="prezime" value="{{ old('prezime', $student->prezime ?? '') }}">
    </div>

    <div>
        <label>Broj indeksa</label><br>
        <input name="broj_indeksa" value="{{ old('broj_indeksa', $student->broj_indeksa ?? '') }}">
    </div>

    <div>
        <label>Email</label><br>
        <input type="email" name="email" value="{{ old('email', $student->email ?? '') }}">
    </div>

    <div>
        <label>Telefon</label><br>
        <input name="telefon" value="{{ old('telefon', $student->telefon ?? '') }}">
    </div>

    <div>
        <label>Datum rođenja</label><br>
        <input type="date" name="datum_rodjenja"
               value="{{ old('datum_rodjenja', isset($student) && $student->datum_rodjenja ? $student->datum_rodjenja->format('Y-m-d') : '') }}">
    </div>

    <div>
        <label>Godina studija</label><br>
        <input type="number" name="godina_studija" min="1" max="8"
               value="{{ old('godina_studija', $student->godina_studija ?? '') }}">
    </div>

    <div>
        <label>Napomena</label><br>
        <textarea name="napomena">{{ old('napomena', $student->napomena ?? '') }}</textarea>
    </div>

    <button type="submit">Sačuvaj</button>
    <a href="{{ route('studenti.index') }}">Otkaži</a>
</form>
