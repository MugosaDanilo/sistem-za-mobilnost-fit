<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Novi student</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">


</head>
<body class="p-6">
  @include('components.flash')
  <h1 class="mb-4 text-2xl font-semibold">Novi student</h1>
  @include('studenti._form')
</body>
</html>
