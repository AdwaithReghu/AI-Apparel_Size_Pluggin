<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Search Merchant --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                Search Merchant
            </h2>
            <p class="text-sm text-gray-500 mb-4">
                View any merchant's data to assist with troubleshooting
            </p>

            <div class="flex gap-3">
                <input
                    type="email"
                    wire:model="searchEmail"
                    placeholder="merchant@example.com"
                    class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                />
                <button
                    wire:click="searchMerchant"
                    class="rounded-lg bg-red-600 px-6 py-2 text-sm font-medium text-white hover:bg-red-700">
                    🔍 Search
                </button>
            </div>

            @if($searchError)
                <p class="mt-2 text-sm text-red-600">{{ $searchError }}</p>
            @endif
        </div>

        {{-- Merchant Data --}}
        @if($merchantData)
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Merchant: {{ $merchantData['name'] }}
                </h2>
                <button
                    wire:click="resetMerchantApiCalls"
                    wire:confirm="Reset API call counts for this merchant?"
                    class="rounded-lg border border-red-300 px-3 py-1 text-sm text-red-600 hover:bg-red-50">
                    Reset API Counts
                </button>
            </div>

            {{-- Profile --}}
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 mb-6">
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">Email</p>
                    <p class="font-medium text-gray-900">{{ $merchantData['email'] }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">Company</p>
                    <p class="font-medium text-gray-900">{{ $merchantData['company'] }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">Country</p>
                    <p class="font-medium text-gray-900">{{ $merchantData['country'] }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">Joined</p>
                    <p class="font-medium text-gray-900">{{ $merchantData['joined'] }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">Total Garments</p>
                    <p class="font-bold text-green-600">{{ $merchantData['total_garments'] }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">Total Scans</p>
                    <p class="font-bold text-blue-600">{{ $merchantData['total_scans'] }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">API Calls Today</p>
                    <p class="font-bold text-purple-600">{{ $merchantData['api_calls_today'] }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">API Calls Month</p>
                    <p class="font-bold text-purple-600">{{ $merchantData['api_calls_month'] }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">API Key</p>
                    <p class="font-mono text-xs text-gray-600 truncate">
                        {{ substr($merchantData['api_key'], 0, 20) }}...
                    </p>
                </div>
            </div>

            {{-- Recent Garments --}}
            <h3 class="font-semibold text-gray-900 dark:text-white mb-3">
                Recent Garments
            </h3>
            @if(count($merchantData['recent_garments']) > 0)
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="pb-2 text-left text-gray-500">Name</th>
                        <th class="pb-2 text-left text-gray-500">Brand</th>
                        <th class="pb-2 text-left text-gray-500">Category</th>
                        <th class="pb-2 text-left text-gray-500">Status</th>
                        <th class="pb-2 text-left text-gray-500">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($merchantData['recent_garments'] as $garment)
                    <tr class="border-b border-gray-100">
                        <td class="py-2 font-medium">{{ $garment['name'] }}</td>
                        <td class="py-2 text-gray-500">{{ $garment['brand'] }}</td>
                        <td class="py-2 text-gray-500">{{ $garment['category'] }}</td>
                        <td class="py-2">
                            <span class="rounded-full px-2 py-1 text-xs
                                {{ $garment['status'] === 'completed'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-orange-100 text-orange-700' }}">
                                {{ $garment['status'] }}
                            </span>
                        </td>
                        <td class="py-2 text-gray-500">{{ $garment['created'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <p class="text-sm text-gray-500">No garments found</p>
            @endif
        </div>
        @endif

    </div>
</x-filament-panels::page>