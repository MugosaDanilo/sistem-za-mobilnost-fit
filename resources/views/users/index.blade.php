<x-app-layout>

    @if(session('success'))
        <div class="mb-4 bg-green-100 text-green-800 p-3 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Whoops!</strong>
            <span class="block">There were some problems with your input:</span>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="py-10 max-w-6xl mx-auto px-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">User Management</h1>
            <button id="addUserBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg">
                Add User
            </button>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="min-w-full border border-gray-200" id="userTable">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">ID</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Name</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Email</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Type</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="userTableBody">
                    @foreach ($users as $user)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $user->id }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $user->email }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                @if ((int)$user->type === 0)
                                    Admin
                                @elseif((int)$user->type === 1)
                                    Profesor
                                @endif
                           </td>
                            <td class="px-4 py-3">
                                <div class="flex space-x-2">
                                    <button
                                        onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}')"
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-3 py-1 rounded-md">
                                        Edit
                                    </button>

                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this user?')">
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
        </div>
    </div>

    <div id="userModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <h2 id="modalTitle" class="text-xl font-semibold mb-4">Add User</h2>

            <form id="userForm" action="{{ route('users.store') }}" method="POST" >
                @csrf
                <input type="hidden" name="id" id="userId">

                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-medium mb-1">Name</label>
                    <input type="text" id="name" name="name"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           required>
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
                    <input type="email" id="email" name="email"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           required>
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
                    <input type="password" id="password" name="password"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
                      <input type="password" id="password_confirmation" name="password_confirmation"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">User Type</label>
                    <select name="type" class="border p-2 w-full">
                        <option value="0">Admin</option>
                        <option value="1">Profesor</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancelModal"
                            class="px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-100">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('userModal');
        const addUserBtn = document.getElementById('addUserBtn');
        const cancelModal = document.getElementById('cancelModal');
        const form = document.getElementById('userForm');
        const title = document.getElementById('modalTitle');

        addUserBtn.addEventListener('click', () => {
            form.action = "{{ route('users.store') }}";
            form.reset();
            document.getElementById('password').required = true;
            title.textContent = 'Add User';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });

        cancelModal.addEventListener('click', () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });

    function openEditModal(id, name, email) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        title.textContent = 'Edit User';
        form.action = `/admin/users/${id}`;
        form.method = 'POST';

        const existingMethod = form.querySelector('input[name="_method"]');
        if (existingMethod) existingMethod.remove();

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        form.appendChild(methodInput);

        document.getElementById('userId').value = id;
        document.getElementById('name').value = name;
        document.getElementById('email').value = email;
        document.getElementById('password').required = false;
    }

    </script>
</x-app-layout>
