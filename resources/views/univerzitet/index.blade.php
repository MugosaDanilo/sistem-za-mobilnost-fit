<x-app-layout>
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="py-10 max-w-7xl mx-auto px-6">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Universities</h1>
            <div class="flex space-x-3">
                <button id="addUniversityBtn"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg">
                    Add University
                </button>
            </div>
        </div>

        <form id="bulkDeleteForm" action="{{ route('univerzitet.bulkDelete') }}" method="POST" class="mb-4">
            @csrf
            @method('DELETE')
            <input type="hidden" id="selectedIds" name="ids">
            <button type="submit"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg"
                onclick="return confirm('Delete selected universities?')">
                Delete Selected
            </button>
        </form>

        <div class="mb-4">
            <input 
                type="text" 
                id="searchUniversity" 
                placeholder="Pretrazi.." 
                class="w-full max-w-md border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2"
            >
        </div>

        <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">

            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">University List</h2>
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                    {{ count($univerziteti) }} Total
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Country</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">City</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($univerziteti as $u)
                            <tr class="university-row hover:bg-gray-50 transition-colors duration-150 ease-in-out"
                                data-search="{{ strtolower($u->naziv . ' ' . $u->drzava . ' ' . $u->grad . ' ' . $u->email) }}">
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" class="rowCheckbox" value="{{ $u->id }}">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $u->naziv }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $u->drzava }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $u->grad }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $u->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center space-x-2">
                                        <button
                                            class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md openEditModal"
                                            data-id="{{ $u->id }}"
                                            data-naziv="{{ $u->naziv }}"
                                            data-drzava="{{ $u->drzava }}"
                                            data-grad="{{ $u->grad }}"
                                            data-email="{{ $u->email }}">
                                            Edit
                                        </button>
                                        <form action="{{ route('univerzitet.destroy', $u->id) }}" method="POST"
                                              onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md">
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

    <div id="editUniversityModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <h2 class="text-xl font-semibold mb-4">Edit University</h2>

            <form id="editUniversityForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="editUniversityId" name="id">

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">University Name</label>
                    <input type="text" id="editName" name="naziv" class="w-full border-gray-300 rounded-lg" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Country</label>
                    <input type="text" id="editCountry" name="drzava" class="w-full border-gray-300 rounded-lg" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">City</label>
                    <input type="text" id="editCity" name="grad" class="w-full border-gray-300 rounded-lg" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Email</label>
                    <input type="email" id="editEmail" name="email" class="w-full border-gray-300 rounded-lg" required>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancelEditModal" class="px-4 py-2 rounded-md border">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <div id="addUniversityModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <h2 class="text-xl font-semibold mb-4">Add University</h2>

            <form id="addUniversityForm" action="{{ route('univerzitet.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">University Name</label>
                    <input type="text" id="addName" name="naziv" class="w-full border-gray-300 rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Country</label>
                    <input type="text" id="addCountry" name="drzava" class="w-full border-gray-300 rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">City</label>
                    <input type="text" id="addCity" name="grad" class="w-full border-gray-300 rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Email</label>
                    <input type="email" id="addEmail" name="email" class="w-full border-gray-300 rounded-lg" required>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancelAddModal" class="px-4 py-2 rounded-md border">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md">Save</button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('editUniversityModal');
    const cancelBtn = document.getElementById('cancelEditModal');
    const form = document.getElementById('editUniversityForm');

    document.querySelectorAll('.openEditModal').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.dataset.id;
            document.getElementById('editUniversityId').value = id;
            document.getElementById('editName').value = button.dataset.naziv;
            document.getElementById('editCountry').value = button.dataset.drzava;
            document.getElementById('editCity').value = button.dataset.grad;
            document.getElementById('editEmail').value = button.dataset.email;
            form.action = `{{ route('univerzitet.index') }}/${id}`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    cancelBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });

    const addModal = document.getElementById('addUniversityModal');
    document.getElementById('addUniversityBtn').addEventListener('click', () => {
        addModal.classList.remove('hidden');
        addModal.classList.add('flex');
    });
    document.getElementById('cancelAddModal').addEventListener('click', () => {
        addModal.classList.add('hidden');
        addModal.classList.remove('flex');
    });

    const searchInput = document.getElementById('searchUniversity');
    const rows = document.querySelectorAll('.university-row');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        rows.forEach(row => {
            const searchText = row.dataset.search;
            row.style.display = searchText.includes(searchTerm) ? '' : 'none';
        });
    });

    const selectAll = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');
    const selectedIdsInput = document.getElementById('selectedIds');

    selectAll.addEventListener('change', function () {
        rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
    });

    bulkDeleteForm.addEventListener('submit', function (e) {
        const selected = [...rowCheckboxes].filter(cb => cb.checked).map(cb => cb.value);
        if (selected.length === 0) { alert('No rows selected.'); e.preventDefault(); return; }
        selectedIdsInput.value = selected.join(',');
    });
});
</script>
