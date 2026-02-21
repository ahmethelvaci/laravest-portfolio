<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-slate-100 leading-tight tracking-tight">
                {{ __('Portföy Özeti') }}
            </h2>
            <a href="{{ route('transactions.create') }}"
                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-lg shadow-indigo-500/30 transition-all font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                Yeni İşlem Gir
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-400 bg-green-900/30 border border-green-800 rounded-xl"
                role="alert">
                {{ session('success') }}
            </div>
            @endif

            <!-- Özet Kartları -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Toplam Değer -->
                <div
                    class="bg-slate-800/80 backdrop-blur-md rounded-3xl p-6 border border-slate-700/50 shadow-xl relative overflow-hidden group hover:border-indigo-500/50 transition-all">
                    <div
                        class="absolute -right-10 -top-10 w-32 h-32 bg-indigo-500/20 rounded-full blur-2xl group-hover:bg-indigo-500/30 transition-all">
                    </div>
                    <div class="relative z-10">
                        <p class="text-sm font-medium text-slate-400 uppercase tracking-wider mb-2">Toplam Portföy
                            Değeri</p>
                        <h3 class="text-4xl font-extrabold text-white tracking-tight">
                            ₺{{ number_format($summary['total_current_value'], 2, ',', '.') }}
                        </h3>
                    </div>
                </div>

                <!-- Toplam Maliyet -->
                <div
                    class="bg-slate-800/80 backdrop-blur-md rounded-3xl p-6 border border-slate-700/50 shadow-xl relative overflow-hidden group hover:border-blue-500/50 transition-all">
                    <div
                        class="absolute -right-10 -top-10 w-32 h-32 bg-blue-500/20 rounded-full blur-2xl group-hover:bg-blue-500/30 transition-all">
                    </div>
                    <div class="relative z-10">
                        <p class="text-sm font-medium text-slate-400 uppercase tracking-wider mb-2">Toplam Maliyet</p>
                        <h3 class="text-3xl font-bold text-slate-200 tracking-tight">
                            ₺{{ number_format($summary['total_cost'], 2, ',', '.') }}
                        </h3>
                    </div>
                </div>

                <!-- Toplam Kar/Zarar -->
                <div
                    class="bg-slate-800/80 backdrop-blur-md rounded-3xl p-6 border border-slate-700/50 shadow-xl relative overflow-hidden group {{ $summary['total_profit'] >= 0 ? 'hover:border-green-500/50' : 'hover:border-red-500/50' }} transition-all">
                    <div
                        class="absolute -right-10 -top-10 w-32 h-32 {{ $summary['total_profit'] >= 0 ? 'bg-green-500/20 group-hover:bg-green-500/30' : 'bg-red-500/20 group-hover:bg-red-500/30' }} rounded-full blur-2xl transition-all">
                    </div>
                    <div class="relative z-10">
                        <p class="text-sm font-medium text-slate-400 uppercase tracking-wider mb-2">Net Kar / Zarar</p>
                        <div class="flex items-baseline gap-3">
                            <h3
                                class="text-3xl font-bold {{ $summary['total_profit'] >= 0 ? 'text-green-400' : 'text-red-400' }} tracking-tight">
                                {{ $summary['total_profit'] >= 0 ? '+' : '' }}₺{{
                                number_format($summary['total_profit'], 2, ',', '.') }}
                            </h3>
                            <span
                                class="px-2.5 py-1 text-sm font-semibold rounded-full {{ $summary['total_profit'] >= 0 ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                {{ $summary['total_profit'] >= 0 ? '+' : '' }}{{
                                number_format($summary['total_profit_percentage'], 2, ',', '.') }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Varlık Listesi -->
            <div
                class="bg-slate-800/80 backdrop-blur-md border border-slate-700/50 rounded-3xl overflow-hidden shadow-2xl mt-8">
                <div class="px-6 py-5 border-b border-slate-700/50 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Varlık Dağılımı ve Detaylar</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-900/50 text-slate-400 text-sm font-semibold tracking-wider">
                                <th class="py-4 px-6 border-b border-slate-700/50">Varlık Tipi</th>
                                <th class="py-4 px-6 border-b border-slate-700/50">Kod/İsim</th>
                                <th class="py-4 px-6 border-b border-slate-700/50">Miktar</th>
                                <th class="py-4 px-6 border-b border-slate-700/50">Maliyet Ort.</th>
                                <th class="py-4 px-6 border-b border-slate-700/50">Güncel Fiyat</th>
                                <th class="py-4 px-6 border-b border-slate-700/50">Güncel Değer</th>
                                <th class="py-4 px-6 border-b border-slate-700/50 text-right">Kar/Zarar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            @forelse($summary['assets'] as $item)
                            <tr class="hover:bg-slate-700/30 transition-colors">
                                <td class="py-4 px-6 text-sm">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-medium {{ $item['asset']->type === 'GOLD' ? 'bg-yellow-500/20 text-yellow-500' : 'bg-indigo-500/20 text-indigo-400' }}">
                                        {{ $item['asset']->type }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-medium text-slate-200">{{ $item['asset']->code ?? 'N/A' }}</div>
                                    <div class="text-xs text-slate-400">{{ $item['asset']->name }}</div>
                                </td>
                                <td class="py-4 px-6 font-medium text-slate-300">
                                    {{ number_format($item['quantity'], 6, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 text-slate-300">
                                    ₺{{ number_format($item['avg_cost'], 4, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 font-medium text-slate-200">
                                    ₺{{ number_format($item['current_price'], 4, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 font-bold text-white">
                                    ₺{{ number_format($item['current_value'], 2, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div
                                        class="font-bold {{ $item['profit'] >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $item['profit'] >= 0 ? '+' : '' }}₺{{ number_format($item['profit'], 2, ',',
                                        '.') }}
                                    </div>
                                    <div
                                        class="text-xs font-medium {{ $item['profit_percentage'] >= 0 ? 'text-green-500' : 'text-red-500' }}">
                                        {{ $item['profit_percentage'] >= 0 ? '+' : '' }}{{
                                        number_format($item['profit_percentage'], 2, ',', '.') }}%
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-600"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p>Portföyünüzde henüz hiç varlık bulunmuyor.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>