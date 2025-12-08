<x-app-layout>
    @if(session('success'))
        <div class="mb-4 bg-green-100 text-green-800 p-3 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <div class="py-10 max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Students Management</h1>
            <a href="{{ route('students.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg">
                Add Student
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Student List</h2>
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ count($students) }} Total</span>
            </div>

            <div class="px-6 py-4 flex justify-start mb-2">
                <button type="button" id="deleteSelectedBtn" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg">
                    Delete Selected
                </button>
            </div>

            <div class="overflow-x-auto px-6 pb-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Index Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($students as $student)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" class="student-checkbox" value="{{ $student->id }}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $student->ime }} {{ $student->prezime }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->br_indexa }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('students.edit', $student->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition-colors">
                                        Edit
                                    </a>
                                    <form action="{{ route('students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const selectAll = document.getElementById('selectAll');
        selectAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.student-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });

        const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
        deleteSelectedBtn.addEventListener('click', function() {
            const selected = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);
            if(selected.length && confirm('Are you sure you want to delete selected students?')) {
                fetch("{{ route('students.bulkDelete') }}", {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ids: selected })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) location.reload();
                    else alert(data.message);
                });
            }
        });
    </script>
</x-app-layout>
