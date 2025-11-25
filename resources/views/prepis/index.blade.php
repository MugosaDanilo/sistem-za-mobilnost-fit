<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prepis Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen">
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <a href="index.blade.php" class="font-bold text-xl text-gray-800">MyApp</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <div class="py-10 max-w-6xl mx-auto px-6">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Prepis Management</h1>
                    <a href="create.blade.php" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg">
                        Add Prepis
                    </a>
                </div>

                <div class="overflow-x-auto bg-white shadow rounded-lg">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">ID</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Student</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Index</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Faculty</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Date</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-800">1</td>
                                <td class="px-4 py-3 text-sm text-gray-800">John Doe</td>
                                <td class="px-4 py-3 text-sm text-gray-800">IB200001</td>
                                <td class="px-4 py-3 text-sm text-gray-800">Faculty of Engineering</td>
                                <td class="px-4 py-3 text-sm text-gray-800">24.11.2025</td>
                                <td class="px-4 py-3 text-sm text-gray-800">Pending</td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        <a href="edit.blade.php" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-3 py-1 rounded-md">
                                            Edit
                                        </a>
                                        <button class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded-md" onclick="confirm('Are you sure you want to delete this prepis?')">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-800">2</td>
                                <td class="px-4 py-3 text-sm text-gray-800">Jane Smith</td>
                                <td class="px-4 py-3 text-sm text-gray-800">IB200002</td>
                                <td class="px-4 py-3 text-sm text-gray-800">Faculty of Economics</td>
                                <td class="px-4 py-3 text-sm text-gray-800">23.11.2025</td>
                                <td class="px-4 py-3 text-sm text-gray-800">Approved</td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        <a href="edit.blade.php" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-3 py-1 rounded-md">
                                            Edit
                                        </a>
                                        <button class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded-md" onclick="confirm('Are you sure you want to delete this prepis?')">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
