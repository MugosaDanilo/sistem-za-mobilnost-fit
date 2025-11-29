<x-app-layout>
    <div class="py-10 max-w-6xl mx-auto px-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Prepis Details</h1>
            <a href="{{ route('prepis.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-4 py-2 rounded-lg">
                Back to List
            </a>
        </div>

        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-700">Student Information</h2>
                    <p class="mt-2 text-gray-600"><span class="font-medium">Name:</span> {{ $prepis->student->ime }} {{ $prepis->student->prezime }}</p>
                    <p class="text-gray-600"><span class="font-medium">Index:</span> {{ $prepis->student->br_indexa }}</p>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-700">Prepis Information</h2>
                    <p class="mt-2 text-gray-600"><span class="font-medium">Faculty:</span> {{ $prepis->fakultet->naziv }}</p>
                    <p class="text-gray-600"><span class="font-medium">Date:</span> {{ $prepis->datum->format('d.m.Y') }}</p>
                    <p class="text-gray-600 mt-1">
                        <span class="font-medium">Overall Status:</span>
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
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Linked Subjects (Agreements)</h2>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">FIT Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foreign Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($prepis->agreements as $agreement)
                        <tr class="@if($agreement->status == 'odobren') bg-green-50 @elseif($agreement->status == 'odbijen') bg-red-50 @else bg-yellow-50 @endif">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $agreement->fitPredmet->naziv }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $agreement->straniPredmet->naziv }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @php
                                    $aColorClass = match($agreement->status) {
                                        'odobren' => 'text-green-800',
                                        'odbijen' => 'text-red-800',
                                        default => 'text-yellow-800',
                                    };
                                @endphp
                                <span class="font-semibold {{ $aColorClass }}">
                                    {{ ucfirst($agreement->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No agreements found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
