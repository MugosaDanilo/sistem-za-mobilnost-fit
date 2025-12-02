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

    <div class="py-10 max-w-6xl mx-auto px-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Universities</h1>
            <button id="addUniversityBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg">
                Add University
            </button>
        </div>

        <div class="mb-4">
            <input 
                type="text" 
                id="searchUniversity" 
                placeholder="Pretrazi.." 
                class="w-full max-w-md border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2"
            >
        </div>

        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Name</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Country</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">City</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Email</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($univerziteti as $u)
                        <tr class="bg-white university-row" data-search="{{ strtolower($u->naziv . ' ' . $u->drzava . ' ' . $u->grad . ' ' . $u->email) }}">
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $u->naziv }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $u->drzava }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $u->grad }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $u->email }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center space-x-2">
                                   <button
    class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-3 py-1 rounded-md openEditModal"
    data-id="{{ $u->id }}"
    data-naziv="{{ $u->naziv }}"
    data-drzava="{{ $u->drzava }}"
    data-grad="{{ $u->grad }}"
    data-email="{{ $u->email }}">
    Edit
</button>
                                    <form action="{{ route('univerzitet.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded-md">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Edit University Modal -->
<div id="editUniversityModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <h2 id="modalTitle" class="text-xl font-semibold mb-4">Edit University</h2>

        <form id="editUniversityForm" method="POST">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="id" id="editUniversityId">

            <div class="mb-4">
                <label for="editName" class="block text-gray-700 font-medium mb-1">University Name</label>
                <input type="text" id="editName" name="naziv" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label for="editCountry" class="block text-gray-700 font-medium mb-1">Country</label>
                <input type="text" id="editCountry" name="drzava" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label for="editCity" class="block text-gray-700 font-medium mb-1">City</label>
                <input type="text" id="editCity" name="grad" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label for="editEmail" class="block text-gray-700 font-medium mb-1">Email</label>
                <input type="email" id="editEmail" name="email" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" id="cancelEditModal" class="px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-100">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
<!-- Add University -->
<div id="addUniversityModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <h2 id="modalTitleAdd" class="text-xl font-semibold mb-4">Add University</h2>

        <form id="addUniversityForm" action="{{ route('univerzitet.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="addName" class="block text-gray-700 font-medium mb-1">University Name</label>
                <input type="text" id="addName" name="naziv" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label for="addCountry" class="block text-gray-700 font-medium mb-1">Country</label>
                <input type="text" id="addCountry" name="drzava" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label for="addCity" class="block text-gray-700 font-medium mb-1">City</label>
                <input type="text" id="addCity" name="grad" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label for="addEmail" class="block text-gray-700 font-medium mb-1">Email</label>
                <input type="email" id="addEmail" name="email" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" id="cancelAddModal" class="px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-100">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>


        </div>
    </div>


    <script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('editUniversityModal');
    const cancelBtn = document.getElementById('cancelEditModal');
    const form = document.getElementById('editUniversityForm');

    // Otvori modal i popuni polja
    document.querySelectorAll('.openEditModal').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            document.getElementById('editUniversityId').value = id;
            document.getElementById('editName').value = button.getAttribute('data-naziv');
            document.getElementById('editCountry').value = button.getAttribute('data-drzava');
            document.getElementById('editCity').value = button.getAttribute('data-grad');
            document.getElementById('editEmail').value = button.getAttribute('data-email');

            form.action = `{{ route('univerzitet.index') }}/${id}`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    // Zatvori modal
    cancelBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const addModal = document.getElementById('addUniversityModal');
    const addBtn = document.getElementById('addUniversityBtn');
    const cancelAdd = document.getElementById('cancelAddModal');

    addBtn.addEventListener('click', () => {
        addModal.classList.remove('hidden');
        addModal.classList.add('flex');
    });

    cancelAdd.addEventListener('click', () => {
        addModal.classList.add('hidden');
        addModal.classList.remove('flex');
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchUniversity');
    const rows = document.querySelectorAll('.university-row');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        rows.forEach(row => {
            const searchText = row.getAttribute('data-search');
            if (searchText.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>


</x-app-layout>
