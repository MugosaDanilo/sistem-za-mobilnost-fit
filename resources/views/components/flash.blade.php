@if (session('success'))
  <div class="mb-4 rounded-lg bg-green-50 p-3 text-green-800">{{ session('success') }}</div>
@endif
@if ($errors->any())
  <div class="mb-4 rounded-lg bg-red-50 p-3 text-red-800">
    <ul class="list-inside list-disc">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
