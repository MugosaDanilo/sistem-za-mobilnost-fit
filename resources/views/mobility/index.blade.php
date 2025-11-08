<x-app-layout>
    <div class="py-10 max-w-6xl mx-auto px-6">

        <div class=" p-6 mb-8">
            <form id="uploadForm"
                  action="{{ route(auth()->user()->type === 0 ? 'admin.mobility.upload' : 'profesor.mobility.upload') }}"
                  method="POST" enctype="multipart/form-data">
                @csrf

                <div class="flex items-center space-x-4">
                    <input type="file" name="word_file" accept=".doc,.docx" class="hidden" id="wordFileInput">

                    <button type="button"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg"
                            onclick="document.getElementById('wordFileInput').click()">
                        Upload Tor File
                    </button>
                </div>
            </form>
        </div>

        @if(session('courses'))
            <div class="overflow-x-auto bg-white shadow rounded-lg">
                <table class="min-w-full border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Term</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Course</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">ECTS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach(session('courses') as $course)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-800">{{ $course['Term'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-800">{{ $course['Course'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-800">{{ $course['ECTS'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <script>
        const fileInput = document.getElementById('wordFileInput');
        const form = document.getElementById('uploadForm');

        fileInput.addEventListener('change', () => {
            if(fileInput.files.length > 0){
                form.submit(); 
            }
        });
    </script>
</x-app-layout>
