<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Prepis</title>
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

        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Create New Prepis
                </h2>
            </div>
        </header>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-6">
                            <a href="index.blade.php" class="text-blue-600 hover:text-blue-800 font-semibold">
                                &larr; Back to Prepis Management
                            </a>
                        </div>

                        <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Form submitted (Static Demo)');">
                            <div class="mb-4">
                                <label for="student_id" class="block text-sm font-medium text-gray-700">Student</label>
                                <select name="student_id" id="student_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    <option value="">Select Student</option>
                                    <option value="1">John Doe (IB200001)</option>
                                    <option value="2">Jane Smith (IB200002)</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="fakultet_id" class="block text-sm font-medium text-gray-700">Faculty</label>
                                <select name="fakultet_id" id="fakultet_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    <option value="">Select Faculty</option>
                                    <option value="1">Faculty of Engineering</option>
                                    <option value="2">Faculty of Economics</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="datum" class="block text-sm font-medium text-gray-700">Date</label>
                                <input type="date" name="datum" id="datum" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            </div>

                            <div class="mb-4">
                                <h3 class="text-lg font-medium mb-2">Subjects</h3>
                                <div id="agreements-container">
                                    <div class="agreement-row flex space-x-4 mb-2">
                                        <div class="w-1/2">
                                            <label class="block text-sm font-medium text-gray-700">FIT Subject</label>
                                            <select name="agreements[0][fit_predmet_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fit-predmet-select" required>
                                                <option value="">Select FIT Subject</option>
                                                <option value="1">Programming I (6 ECTS)</option>
                                                <option value="2">Databases (6 ECTS)</option>
                                            </select>
                                        </div>
                                        <div class="w-1/2">
                                            <label class="block text-sm font-medium text-gray-700">Foreign Subject</label>
                                            <select name="agreements[0][strani_predmet_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm strani-predmet-select" required disabled>
                                                <option value="">Select Faculty First</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="add-agreement" class="mt-2 text-sm text-blue-600 hover:text-blue-900">+ Add Another Subject Pair</button>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const allSubjects = [
            { id: 101, naziv: "Intro to Engineering", ects: 6, fakultet_id: 1 },
            { id: 102, naziv: "Advanced Math", ects: 7, fakultet_id: 1 },
            { id: 201, naziv: "Microeconomics", ects: 6, fakultet_id: 2 },
            { id: 202, naziv: "Macroeconomics", ects: 6, fakultet_id: 2 }
        ];

        const agreementsContainer = document.getElementById('agreements-container');
        const fakultetSelect = document.getElementById('fakultet_id');

        function populateForeignSubjects(selectElement, facultyId) {
            selectElement.innerHTML = '<option value="">Select Foreign Subject</option>';
            if (!facultyId) {
                selectElement.disabled = true;
                selectElement.innerHTML = '<option value="">Select Faculty First</option>';
                return;
            }
            selectElement.disabled = false;
            
            const filteredSubjects = allSubjects.filter(subject => subject.fakultet_id == facultyId);
            
            filteredSubjects.forEach(subject => {
                const option = document.createElement('option');
                option.value = subject.id;
                option.textContent = `${subject.naziv} (${subject.ects} ECTS)`;
                selectElement.appendChild(option);
            });
        }

        function updateAllForeignSubjects() {
            const facultyId = fakultetSelect.value;
            const foreignSelects = document.querySelectorAll('.strani-predmet-select');
            foreignSelects.forEach(select => {
                const currentValue = select.value;
                populateForeignSubjects(select, facultyId);
                if (currentValue) {
                    let exists = false;
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].value == currentValue) {
                            exists = true;
                            break;
                        }
                    }
                    if (exists) {
                        select.value = currentValue;
                    }
                }
            });
        }

        fakultetSelect.addEventListener('change', updateAllForeignSubjects);

        document.getElementById('add-agreement').addEventListener('click', function() {
            const index = agreementsContainer.children.length;
            const row = document.createElement('div');
            row.className = 'agreement-row flex space-x-4 mb-2';
            row.innerHTML = `
                <div class="w-1/2">
                    <select name="agreements[${index}][fit_predmet_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fit-predmet-select" required>
                        <option value="">Select FIT Subject</option>
                        <option value="1">Programming I (6 ECTS)</option>
                        <option value="2">Databases (6 ECTS)</option>
                    </select>
                </div>
                <div class="w-1/2">
                    <select name="agreements[${index}][strani_predmet_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm strani-predmet-select" required>
                        <option value="">Select Faculty First</option>
                    </select>
                </div>
                <button type="button" class="text-red-600 hover:text-red-900 remove-agreement">X</button>
            `;
            agreementsContainer.appendChild(row);
            
            const newSelect = row.querySelector('.strani-predmet-select');
            populateForeignSubjects(newSelect, fakultetSelect.value);
        });

        agreementsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-agreement')) {
                e.target.closest('.agreement-row').remove();
            }
        });
    </script>
</body>
</html>
