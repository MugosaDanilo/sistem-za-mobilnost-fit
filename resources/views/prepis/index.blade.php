<x-app-layout>
    @if(session('success'))
    <div class="mb-4 bg-green-100 text-green-800 p-3 rounded-md">
        {{ session('success') }}
    </div>
    @endif

    <div class="py-10 max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between mb-6">
<<<<<<< Updated upstream
            <h1 class="text-3xl font-bold text-gray-900">Prepis Management</h1>
            <a href="{{ route('prepis.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow-lg transform transition hover:scale-105">
                Add Prepis
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Prepis List</h2>
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ count($prepisi) }} Total</span>
=======
            <h1 class="text-3xl font-bold text-gray-900">Upravljanje prepisima</h1>
            <div class="flex space-x-4">
                <a href="{{ route('prepis.match') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-4 py-2 rounded-lg shadow-lg transform transition hover:scale-105">
                    Dodaj prepis
                </a>
            </div>
        </div>



        <!-- Mapping Requests Table -->
        <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200 mt-8">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Zahtjevi za povezivanje profesora</h2>
                <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ count($mappingRequests) }}
                    Ukupno</span>
>>>>>>> Stashed changes
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
<<<<<<< Updated upstream
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Faculty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($prepisi as $prepis)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                        {{ substr($prepis->student->ime, 0, 1) }}{{ substr($prepis->student->prezime, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $prepis->student->ime }} {{ $prepis->student->prezime }}</div>
                                        <div class="text-sm text-gray-500">{{ $prepis->student->br_indexa }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $prepis->fakultet->naziv }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $prepis->datum->format('d.m.Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('prepis.show', $prepis->id) }}" class="hover:opacity-80 transition-opacity">
                                    @php
                                        $status = $prepis->derived_status;
                                        $colorClass = match($status) {
                                            'odobren' => 'bg-green-100 text-green-800',
                                            'odbijen' => 'bg-red-100 text-red-800',
                                            default => 'bg-yellow-100 text-yellow-800',
                                        };
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('prepis.edit', $prepis->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition-colors">
                                        Edit
                                    </a>
                                    <form action="{{ route('prepis.destroy', $prepis->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this prepis?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors">
                                            Delete
=======
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Student / Profesor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Predmet (Student -> Profesor)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Akcije</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($mappingRequests as $request)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col space-y-1">
                                    @if($request->student)
                                    <div class="flex items-center text-sm font-medium text-gray-900">
                                        <span class="text-gray-500 mr-1">Student:</span> {{ $request->student->ime }}
                                        {{ $request->student->prezime }}
                                    </div>
                                    @endif
                                    <div class="flex items-center text-sm text-gray-500">
                                    </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="mb-2 text-xs text-gray-500">
                                    <span class="font-medium">Profesori:</span>
                                    {{ $request->subjects->pluck('professor.name')->unique()->filter()->join(', ') ?: 'None assigned' }}
                                </div>
                                <ul class="list-disc pl-4 space-y-1">
                                    @foreach($request->subjects as $subject)
                                    <li>
                                        <span class="font-medium">{{ $subject->straniPredmet->naziv }}</span>
                                        <span class="text-xs text-gray-400">({{ $subject->professor->name ?? 'Unassigned' }})</span>
                                        @if($subject->fitPredmet)
                                        <span class="text-green-600 font-bold">->
                                            {{ $subject->fitPredmet->naziv }}</span>
                                        @elseif($subject->is_rejected)
                                        <span class="text-red-500 font-bold">-> (Odbijeno)</span>
                                        @else
                                        <span class="text-yellow-500 italic">-> (Čekanje potvrde)</span>
                                        @endif
                                    </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                // Determine status color logic
                                $color = match ($request->status) {
                                'accepted' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                default => 'bg-yellow-100 text-yellow-800',
                                };
                                $statusText = match ($request->status) {
                                'accepted' => 'Accepted',
                                'rejected' => 'Rejected',
                                default => 'Pending',
                                };

                                $totalSubjects = $request->subjects->count();
                                $matchedSubjects = $request->subjects->whereNotNull('fit_predmet_id')->count();
                                $rejectedSubjects = $request->subjects->where('is_rejected', true)->count();
                                $processedSubjects = $matchedSubjects + $rejectedSubjects;

                                $allProcessed = ($totalSubjects > 0 && $processedSubjects == $totalSubjects);
                                $allRejected = ($totalSubjects > 0 && $rejectedSubjects == $totalSubjects);
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                    {{ $statusText }}
                                </span>
                                @if($request->status == 'pending')
                                @if($allRejected)
                                <div class="text-xs text-red-600 mt-1 font-bold">Profesor je odbio sve</div>
                                @elseif($allProcessed)
                                <div class="text-xs text-green-600 mt-1 font-bold">Spremno za pregled</div>
                                @else
                                <div class="text-xs text-yellow-600 mt-1">Čeka se profesor
                                    ({{ $processedSubjects }}/{{ $totalSubjects }})</div>
                                @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('prepis.mapping-request.show', $request->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition-colors">
                                        Prikaži zahtjev
                                    </a>
                                    <form action="{{ route('prepis.mapping-request.destroy', $request->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this mapping request?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors">
                                            Izbriši
>>>>>>> Stashed changes
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
<<<<<<< Updated upstream
                    @endforeach
                </tbody>
            </table>

            </div>
        </div>
=======
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">
                                Nema zahtjeva za povezivanje
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
>>>>>>> Stashed changes
    </div>
</x-app-layout>
