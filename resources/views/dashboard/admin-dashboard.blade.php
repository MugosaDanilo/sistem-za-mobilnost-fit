<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot> --}}

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6">Admin Dashboard - Pregled Mobilnosti</h1>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Student</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fakultet</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Period</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Akcije</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($mobilnosti as $mobilnost)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $mobilnost->student->ime }}
                                                {{ $mobilnost->student->prezime }}</div>
                                            <div class="text-sm text-gray-500">{{ $mobilnost->student->br_indexa }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $mobilnost->fakultet->naziv }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ \Carbon\Carbon::parse($mobilnost->datum_pocetka)->format('d.m.Y') }} -
                                                {{ \Carbon\Carbon::parse($mobilnost->datum_kraja)->format('d.m.Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="openDetailsModal({{ $mobilnost->id }})"
                                                class="text-indigo-600 hover:text-indigo-900">Detalji</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="detailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden"
        style="z-index: 50;">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Detalji Mobilnosti</h3>
                    <button onclick="closeDetailsModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mt-2" id="modalContent">
                    <!-- Content will be loaded here -->
                    <div class="text-center py-4">Učitavanje...</div>
                </div>

                <div class="items-center px-4 py-3">
                    <button id="ok-btn" onclick="closeDetailsModal()"
                        class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Zatvori
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openDetailsModal(id) {
            const modal = document.getElementById('detailsModal');
            const content = document.getElementById('modalContent');
            modal.classList.remove('hidden');
            content.innerHTML = '<div class="text-center py-4">Učitavanje...</div>';

            fetch(`/admin/mobility/${id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('modalTitle').textContent = `Mobilnost: ${data.student.ime} ${data.student.prezime} - ${data.fakultet.naziv}`;

                    let html = `
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">FIT Predmet</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Strani Predmet</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ECTS</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ocjena</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Akcija</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                    `;

                    data.learning_agreements.forEach(la => {
                        html += `
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900">${la.fit_predmet ? la.fit_predmet.naziv : '-'}</td>
                                <td class="px-4 py-2 text-sm text-gray-900">${la.strani_predmet ? la.strani_predmet.naziv : '-'}</td>
                                <td class="px-4 py-2 text-sm text-gray-900">${la.strani_predmet ? la.strani_predmet.ects : '-'}</td>
                                <td class="px-4 py-2 text-sm text-gray-900">
                                    <input type="text" id="grade-${la.id}" value="${la.ocjena || ''}" 
                                        class="w-20 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900">
                                    <button onclick="saveGrade(${la.id})" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-xs">
                                        Sačuvaj
                                    </button>
                                </td>
                            </tr>
                        `;
                    });

                    html += `</tbody></table></div>`;
                    content.innerHTML = html;
                })
                .catch(err => {
                    content.innerHTML = '<div class="text-red-500 text-center py-4">Greška prilikom učitavanja podataka.</div>';
                    console.error(err);
                });
        }

        function closeDetailsModal() {
            document.getElementById('detailsModal').classList.add('hidden');
        }

        function saveGrade(id) {
            const grade = document.getElementById(`grade-${id}`).value;

            fetch(`/admin/mobility/grade/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ocjena: grade })
            })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                })
                .catch(err => {
                    alert('Greška prilikom čuvanja ocjene.');
                    console.error(err);
                });
        }
    </script>
</x-app-layout>