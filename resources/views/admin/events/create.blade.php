<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tambah Event') }}
            </h2>
            <a href="{{ route('events.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition border border-gray-300 shadow-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Event
            </a>
        </div>
    </x-slot>

    <div class="py-12 max-w-3xl mx-auto sm:px-6 lg:px-8">
        {{-- Alert Notifikasi Error Validasi --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg text-red-700 shadow-sm">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-bold">Gagal menambahkan event:</span>
                </div>
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-gray-200">
            <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- Nama Event --}}
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Event <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" class="w-full border border-gray-300 rounded-lg p-2.5 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition @error('name') border-red-500 @enderror" placeholder="Contoh: Konser Musik Elektronik" required>
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kategori --}}
                <div>
                    <label for="category" class="block text-sm font-semibold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select id="category" name="category" class="w-full border border-gray-300 rounded-lg p-2.5 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition @error('category') border-red-500 @enderror" required>
                        <option value="Musik" {{ old('category') == 'Musik' ? 'selected' : '' }}>Musik</option>
                        <option value="Festival" {{ old('category') == 'Festival' ? 'selected' : '' }}>Festival</option>
                        <option value="Seminar" {{ old('category') == 'Seminar' ? 'selected' : '' }}>Seminar</option>
                    </select>
                    @error('category')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Harga --}}
                <div>
                    <label for="price" class="block text-sm font-semibold text-gray-700 mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-semibold text-sm">Rp</span>
                        <input type="number" id="price" name="price" value="{{ old('price') }}" class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2.5 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition @error('price') border-red-500 @enderror" placeholder="25000" min="0" required>
                    </div>
                    @error('price')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Event <span class="text-red-500">*</span></label>
                    <textarea id="description" name="description" rows="3" class="w-full border border-gray-300 rounded-lg p-2.5 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition @error('description') border-red-500 @enderror" placeholder="Jelaskan tentang event..." required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Gambar Event --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Foto Event</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/webp" class="w-full border border-gray-300 rounded-lg p-2 text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('image') border-red-500 @enderror">
                    <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, WEBP (Maksimal 2MB).</p>
                    @error('image')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tombol Aksi --}}
                <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold text-sm rounded-lg shadow-sm transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Data
                    </button>
                    <a href="{{ route('events.index') }}" class="inline-flex items-center px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-lg transition border border-gray-300">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>