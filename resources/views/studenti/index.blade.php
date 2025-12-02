<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Studenti</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">


</head>
<body class="p-6">
  @include('components.flash')

  <div class="mb-4 flex items-center justify-between">
    <h1 class="text-2xl font-semibold">Studenti</h1>
    <a href="{{ route('studenti.create') }}" class="rounded-xl bg-blue-600 px-4 py-2 text-white">Dodaj</a>
  </div>

  <div class="overflow-hidden rounded-xl border">
    <table class="min-w-full divide-y">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left text-sm font-medium">Indeks</th>
          <th class="px-4 py-2 text-left text-sm font-medium">Ime i prezime</th>
          <th class="px-4 py-2 text-left text-sm font-medium">Email</th>
          <th class="px-4 py-2"></th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($studenti as $s)
        <tr>
          <td class="px-4 py-2">{{ $s->broj_indeksa }}</td>
          <td class="px-4 py-2">{{ $s->puno_ime }}</td>
          <td class="px-4 py-2">{{ $s->email }}</td>
          <td class="px-4 py-2 text-right">
            <a href="{{ route('studenti.edit', $s) }}" class="rounded-lg bg-gray-100 px-3 py-1">Uredi</a>
            <form action="{{ route('studenti.destroy', $s) }}" method="POST" class="inline" onsubmit="return confirm('Obrisati ovog studenta?')">
              @csrf @method('DELETE')
              <button class="rounded-lg bg-red-600 px-3 py-1 text-white">Obriši</button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Nema zapisa.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $studenti->links() }}
  </div>
</body>
</html>
