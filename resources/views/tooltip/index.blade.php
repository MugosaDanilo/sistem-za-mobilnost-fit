<x-app-layout>
    <div class="pt-16 max-w-4xl mx-auto px-6">

        <h1 class="text-2xl font-bold mb-6">Faculty Tooltip Management</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('confirm_overwrite'))
            <div class="bg-yellow-100 p-4 rounded mb-4">
                <p class="font-semibold">This faculty already has a tooltip. Do you want to overwrite it?</p>

                <form action="{{ route('tooltip.overwrite') }}" method="POST">
                    @csrf
                    <input type="hidden" name="faculty_id" value="{{ session('faculty_id') }}">
                    <textarea name="new_text" class="hidden">{{ session('new_text') }}</textarea>

                    <button type="submit" class="mt-3 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                        Yes — Overwrite Tooltip
                    </button>
                </form>
            </div>
        @endif

        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Add or Overwrite Tooltip</h2>

            <form action="{{ route('tooltip.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <label class="block font-medium mb-2">Select Faculty:</label>
                <select name="faculty_id" class="border-gray-300 rounded w-full mb-4" required>
                    @foreach($fakulteti as $f)
                        <option value="{{ $f->id }}">{{ $f->naziv }}</option>
                    @endforeach
                </select>

                <label class="block font-medium mb-2">Document (.txt or .docx):</label>
                <input type="file" name="tooltip_file" accept=".txt,.docx" class="mb-4" required>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Upload Tooltip
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
