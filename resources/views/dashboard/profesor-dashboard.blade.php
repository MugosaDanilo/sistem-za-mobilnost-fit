<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Dashboard') }}
    </h2>
    </x-slot> --}}

    <div class="py-10 max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Pregled profesora</h1>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-8" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
<<<<<<< Updated upstream
                <h2 class="text-lg font-semibold text-gray-800">Transfer Requests</h2>
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $agreements->count() }} Total</span>
=======
                <h2 class="text-lg font-semibold text-gray-800">Zahtjevi za povezivanje</h2>
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $mappingRequests->count() }} Total</span>
>>>>>>> Stashed changes
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
<<<<<<< Updated upstream
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transfer Subject</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">FIT Subject</th>
=======
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fakultet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Datum slanja</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Broj predmeta</th>
>>>>>>> Stashed changes
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Akcije</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
<<<<<<< Updated upstream
                        @forelse($agreements as $agreement)
                            <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                            {{ substr($agreement->prepis->student->ime, 0, 1) }}{{ substr($agreement->prepis->student->prezime, 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $agreement->prepis->student->ime }} {{ $agreement->prepis->student->prezime }}</div>
                                            <div class="text-sm text-gray-500">{{ $agreement->prepis->student->br_indexa }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium">{{ $agreement->straniPredmet->naziv }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium">{{ $agreement->fitPredmet->naziv }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($agreement->status == 'odobren') bg-green-100 text-green-800 
                                        @elseif($agreement->status == 'odbijen') bg-red-100 text-red-800 
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ $agreement->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($agreement->status == 'u procesu')
                                        <div class="flex justify-end space-x-2">
                                            <form action="{{ route('prepis-agreement.accept', $agreement->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-3 rounded text-xs transition-colors">
                                                    Accept
                                                </button>
                                            </form>
                                            <form action="{{ route('prepis-agreement.reject', $agreement->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-3 rounded text-xs transition-colors">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-gray-500 text-sm">Completed</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                    No active agreement requests found.
                                </td>
                            </tr>
=======
                        @forelse($mappingRequests as $request)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $request->fakultet?->naziv ?? 'Unknown Faculty' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $request->created_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $request->subjects->where('professor_id', auth()->id())->count() }} / {{ $request->subjects->count() }} subjects
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                // Global status
                                $status = $request->status;
                                $color = '';
                                $text = ucfirst($status);

                                if ($status == 'accepted') {
                                $color = 'bg-green-100 text-green-800';
                                } elseif ($status == 'rejected') {
                                $color = 'bg-red-100 text-red-800';
                                } else {
                                // Request is pending globally. Check MY status.
                                $mySubjects = $request->subjects->where('professor_id', auth()->id());
                                $total = $mySubjects->count();
                                $matched = $mySubjects->whereNotNull('fit_predmet_id')->count();
                                $rejected = $mySubjects->where('is_rejected', true)->count();
                                $processed = $matched + $rejected;

                                if ($total > 0 && $processed == $total) {
                                // I have finished my part
                                if ($matched == 0 && $rejected > 0) {
                                // I rejected everything
                                $text = 'Rejected';
                                $color = 'bg-red-100 text-red-800';
                                } else {
                                // I matched some (or all)
                                $text = 'Submitted';
                                $color = 'bg-blue-100 text-blue-800';
                                }
                                } else {
                                // Still have work to do
                                $text = 'Action Required';
                                $color = 'bg-yellow-100 text-yellow-800';
                                }
                                }
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                    {{ $text }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('mapping-request.show', $request->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition-colors">
                                    Pregled i povezivanje
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                Nema zahtjeva za povezivanje
                            </td>
                        </tr>
>>>>>>> Stashed changes
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
