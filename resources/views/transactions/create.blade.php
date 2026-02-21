<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="font-bold text-2xl text-slate-100 leading-tight tracking-tight">
                {{ __('Yeni İşlem Ekle') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-slate-800/80 backdrop-blur-md rounded-3xl p-8 border border-slate-700/50 shadow-2xl relative overflow-hidden">
                <div
                    class="absolute -right-20 -top-20 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none">
                </div>

                <form method="POST" action="{{ route('transactions.store') }}" class="space-y-6 relative z-10"
                    x-data="transactionForm()">
                    @csrf

                    @if($errors->any())
                    <div class="p-4 bg-red-900/30 border border-red-800 text-red-400 rounded-xl">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Date -->
                        <div class="col-span-1 border-b border-slate-700/50 pb-4">
                            <label for="date" class="block text-sm font-medium text-slate-300 mb-2">İşlem Tarihi</label>
                            <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" required
                                class="w-full bg-slate-900/50 border border-slate-600 rounded-xl px-4 py-2 text-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none" />
                        </div>

                        <!-- Direction -->
                        <div class="col-span-1 border-b border-slate-700/50 pb-4">
                            <label class="block text-sm font-medium text-slate-300 mb-2">İşlem Yönü</label>
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="direction" value="BUY"
                                        class="text-indigo-600 bg-slate-900 border-slate-600 focus:ring-indigo-500" {{
                                        old('direction', 'BUY' )==='BUY' ? 'checked' : '' }} />
                                    <span class="text-slate-200 font-medium">Alış (BUY)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="direction" value="SELL"
                                        class="text-red-500 bg-slate-900 border-slate-600 focus:ring-red-500" {{
                                        old('direction')==='SELL' ? 'checked' : '' }} />
                                    <span class="text-slate-200 font-medium">Satış (SELL)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Asset Type -->
                        <div
                            class="col-span-1 md:col-span-2 rounded-xl bg-slate-900/40 p-5 mt-2 border border-slate-700/50">
                            <label class="block text-sm font-medium text-slate-300 mb-3 block">Varlık Tipi</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="FUND" x-model="assetType"
                                        class="peer sr-only" />
                                    <div
                                        class="p-4 rounded-xl border border-slate-600 peer-checked:border-indigo-500 peer-checked:bg-indigo-500/10 hover:bg-slate-700/30 transition-all text-center">
                                        <span
                                            class="block text-lg font-bold text-slate-200 peer-checked:text-indigo-400">Yatırım
                                            Fonu (TEFAS)</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="GOLD" x-model="assetType"
                                        class="peer sr-only" />
                                    <div
                                        class="p-4 rounded-xl border border-slate-600 peer-checked:border-yellow-500 peer-checked:bg-yellow-500/10 hover:bg-slate-700/30 transition-all text-center">
                                        <span
                                            class="block text-lg font-bold text-slate-200 peer-checked:text-yellow-400">Altın</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Fund Code (Only if FUND) -->
                        <div class="col-span-1" x-show="assetType === 'FUND'" style="display: none;">
                            <label for="code" class="block text-sm font-medium text-slate-300 mb-2">Fon Kodu
                                (TEFAS)</label>
                            <input type="text" name="code" id="code" x-bind:required="assetType === 'FUND'"
                                value="{{ old('code') }}" placeholder="Örn: MAC"
                                class="w-full bg-slate-900/50 border border-slate-600 rounded-xl px-4 py-2 text-slate-200 focus:ring-2 focus:ring-indigo-500 transition-all outline-none uppercase" />
                            <p class="text-xs text-slate-400 mt-1">Fon daha önce portföye eklenmediyse, otomatik olarak
                                kaydedilir.</p>
                        </div>

                        <!-- Asset Name -->
                        <div class="col-span-1" :class="assetType === 'GOLD' ? 'md:col-span-2' : ''">
                            <label for="name" class="block text-sm font-medium text-slate-300 mb-2">Varlık Adı /
                                Türü</label>

                            <!-- Gold Options -->
                            <select x-show="assetType === 'GOLD'" name="name"
                                class="w-full bg-slate-900/50 border border-slate-600 rounded-xl px-4 py-2 text-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none"
                                :disabled="assetType !== 'GOLD'">
                                <option value="Gram Altın">Gram Altın</option>
                                <option value="Çeyrek Altın">Çeyrek Altın</option>
                                <option value="Yarım Altın">Yarım Altın</option>
                                <option value="Tam Altın">Tam Altın</option>
                                <option value="Ata Altın">Ata Altın</option>
                            </select>

                            <!-- Fund Input -->
                            <input x-show="assetType === 'FUND'" type="text" name="name" id="name_fund"
                                value="{{ old('name') }}" placeholder="Marmara Capital Hisse Senedi Fonu"
                                class="w-full bg-slate-900/50 border border-slate-600 rounded-xl px-4 py-2 text-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none"
                                :disabled="assetType !== 'FUND'" />
                        </div>

                        <!-- Quantity -->
                        <div class="col-span-1">
                            <label for="quantity" class="block text-sm font-medium text-slate-300 mb-2">Miktar
                                (Adet/Pay/Gram)</label>
                            <input type="number" step="0.000001" name="quantity" id="quantity" x-model="quantity"
                                value="{{ old('quantity') }}" required
                                class="w-full bg-slate-900/50 border border-slate-600 rounded-xl px-4 py-2 text-slate-200 focus:ring-2 focus:ring-indigo-500 transition-all outline-none" />
                        </div>

                        <!-- Price -->
                        <div class="col-span-1">
                            <label for="price" class="block text-sm font-medium text-slate-300 mb-2">Birim Fiyat
                                (₺)</label>
                            <input type="number" step="0.000001" name="price" id="price" x-model="price"
                                value="{{ old('price') }}" required
                                class="w-full bg-slate-900/50 border border-slate-600 rounded-xl px-4 py-2 text-slate-200 focus:ring-2 focus:ring-indigo-500 transition-all outline-none" />
                        </div>

                        <!-- Total calculation (JS) -->
                        <div
                            class="col-span-1 md:col-span-2 bg-gradient-to-r from-indigo-900/40 to-slate-900/40 border border-indigo-500/20 rounded-xl p-5 mt-4 flex justify-between items-center">
                            <span class="text-slate-300 font-medium">Toplam İşlem Tutarı:</span>
                            <span class="text-2xl font-bold tracking-tight text-white">₺<span
                                    x-text="calculateTotal()"></span></span>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit"
                            class="w-full py-4 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 focus:ring-4 focus:ring-indigo-900 focus:outline-none text-white font-bold rounded-xl text-lg shadow-xl shadow-indigo-600/30 transition-all transform hover:-translate-y-1">
                            İşlemi Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('transactionForm', () => ({
                assetType: '{{ old('type', 'FUND') }}',
                quantity: '{{ old('quantity', '') }}',
                price: '{{ old('price', '') }}',

                calculateTotal() {
                    const q = parseFloat(this.quantity);
                    const p = parseFloat(this.price);
                    if (isNaN(q) || isNaN(p)) return '0,00';
                    return (q * p).toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }))
        })
    </script>
    @endpush
</x-app-layout>