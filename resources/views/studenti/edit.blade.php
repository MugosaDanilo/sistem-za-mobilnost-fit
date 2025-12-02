<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Uredi studenta</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

</head>
<body class="p-6">
  @include('components.flash')
  <h1 class="mb-4 text-2xl font-semibold">Uredi: {{ $student->puno_ime }}</h1>
  @include('studenti._form', ['student' => $student])
</body>
</html>
