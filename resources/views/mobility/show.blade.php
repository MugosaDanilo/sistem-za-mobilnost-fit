<x-app-layout>
    <div class="py-10 max-w-6xl mx-auto px-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Mobility Details</h1>
            <a href="{{ route('adminDashboardShow') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-4 py-2 rounded-lg">
                Back to Dashboard
            </a>
        </div>

        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-700">Student Information</h2>
                    <p class="mt-2 text-gray-600"><span class="font-medium">Name:</span> {{ $mobilnost->student->ime }} {{ $mobilnost->student->prezime }}</p>
                    <p class="text-gray-600"><span class="font-medium">Index:</span> {{ $mobilnost->student->br_indexa }}</p>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-700">Mobility Information</h2>
                    <p class="mt-2 text-gray-600"><span class="font-medium">Faculty:</span> {{ $mobilnost->fakultet->naziv }}</p>
                    <p class="text-gray-600"><span class="font-medium">Period:</span> {{ \Carbon\Carbon::parse($mobilnost->datum_pocetka)->format('d.m.Y') }} - {{ \Carbon\Carbon::parse($mobilnost->datum_kraja)->format('d.m.Y') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Learning Agreements</h2>
            </div>
            <div class="overflow-x-auto">
                <form id="gradesForm">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">FIT Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foreign Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ECTS</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($mobilnost->learningAgreements as $la)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $la->fitPredmet ? $la->fitPredmet->naziv : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $la->straniPredmet ? $la->straniPredmet->naziv : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $la->straniPredmet ? $la->straniPredmet->ects : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <select name="grades[{{ $la->id }}]" 
                                            class="w-24 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="">-</option>
                                            @foreach(['A', 'B', 'C', 'D', 'E', 'F'] as $grade)
                                                <option value="{{ $grade }}" {{ $la->ocjena == $grade ? 'selected' : '' }}>
                                                    {{ $grade }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end items-center gap-4">
                        <span id="saveMessage" class="text-sm font-medium"></span>
                        <button type="button" onclick="saveAllGrades()" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition duration-150 ease-in-out">
                            Save All Grades
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function saveAllGrades() {
            const form = document.getElementById('gradesForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            const msgSpan = document.getElementById('saveMessage');
            
            // Convert formData to nested object structure for 'grades' array
            const grades = {};
            for (let [key, value] of formData.entries()) {
                const match = key.match(/grades\[(\d+)\]/);
                if (match) {
                    grades[match[1]] = value;
                }
            }

            msgSpan.textContent = 'Saving...';
            msgSpan.className = 'text-sm font-medium text-gray-500';

            fetch(`/admin/mobility/{{ $mobilnost->id }}/grades`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ grades: grades })
            })
            .then(res => res.json())
            .then(data => {
                msgSpan.textContent = 'All grades saved successfully!';
                msgSpan.className = 'text-sm font-medium text-green-600';
                setTimeout(() => {
                    msgSpan.textContent = '';
                }, 3000);
            })
            .catch(err => {
                msgSpan.textContent = 'Error saving grades.';
                msgSpan.className = 'text-sm font-medium text-red-600';
                console.error(err);
            });
        }
    </script>
</x-app-layout>
