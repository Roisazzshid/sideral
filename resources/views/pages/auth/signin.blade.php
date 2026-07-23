@extends('layouts.fullscreen-layout')

@section('content')
    <div class="relative z-1 bg-white p-6 sm:p-0 dark:bg-gray-900">
        <div class="relative flex h-screen w-full flex-col justify-center sm:p-0 lg:flex-row dark:bg-gray-900">
            <!-- Form Container -->
            <div class="flex w-full flex-1 flex-col lg:w-1/2">
                <div class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center py-12">
                    <div>
                        <div class="mb-6">
                            <h1 class="text-3xl font-bold text-teal-800 dark:text-teal-400">
                                SIDERAL
                            </h1>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Silakan masuk dengan email dan password Anda.
                            </p>
                        </div>

                        <!-- Flash & Error Messages -->
                        @if(session('success'))
                            <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-700 border border-green-200">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-700 border border-red-200">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-700 border border-red-200">
                                @foreach($errors->all() as $err)
                                    <p>{{ $err }}</p>
                                @endforeach
                            </div>
                        @endif

                        <!-- Quick Demo Credentials Selector -->
                        <div class="mb-6 rounded-lg bg-slate-50 border border-slate-200 p-4 dark:bg-gray-800 dark:border-gray-700">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pilih Akun Demo (1-Click Login):</p>
                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('autologin', 'admin') }}" class="rounded-lg border border-teal-200 bg-teal-50 px-3 py-2 text-xs font-semibold text-teal-800 hover:bg-teal-100 transition text-left block">
                                    <div class="font-bold">🔑 Admin</div>
                                    <div class="text-[11px] text-teal-600 font-normal">Full Akses Semua Fitur</div>
                                </a>
                                <a href="{{ route('autologin', 'teknisi') }}" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-800 hover:bg-blue-100 transition text-left block">
                                    <div class="font-bold">🔧 Teknisi</div>
                                    <div class="text-[11px] text-blue-600 font-normal">Khusus Fitur Maintenance</div>
                                </a>
                            </div>
                        </div>

                        <!-- Login Form -->
                        <form id="loginForm" method="POST" action="{{ route('signin.post') }}">
                            @csrf
                            <div class="space-y-5">
                                <!-- Email -->
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Email<span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="admin@sideral.com"
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-teal-500 focus:ring-2 focus:ring-teal-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                </div>

                                <!-- Password -->
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Password<span class="text-red-500">*</span>
                                    </label>
                                    <div x-data="{ showPassword: false }" class="relative">
                                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required placeholder="Masukkan password"
                                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-11 pl-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-teal-500 focus:ring-2 focus:ring-teal-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                        <span @click="showPassword = !showPassword" class="absolute top-1/2 right-4 z-30 -translate-y-1/2 cursor-pointer text-gray-500 dark:text-gray-400">
                                            <svg x-show="!showPassword" class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM10.0002 4.04297C6.48191 4.04297 3.49489 6.30917 2.4155 9.4593C2.3615 9.61687 2.3615 9.78794 2.41549 9.94552C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C13.5184 15.3619 16.5055 13.0957 17.5849 9.94555C17.6389 9.78797 17.6389 9.6169 17.5849 9.45932C16.5055 6.30919 13.5184 4.04297 10.0002 4.04297ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z" fill="#98A2B3" />
                                            </svg>
                                            <svg x-show="showPassword" class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.63803 3.57709C4.34513 3.2842 3.87026 3.2842 3.57737 3.57709C3.28447 3.86999 3.28447 4.34486 3.57737 4.63775L4.85323 5.91362C3.74609 6.84199 2.89363 8.06395 2.4155 9.45936C2.3615 9.61694 2.3615 9.78801 2.41549 9.94558C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C11.255 15.3619 12.4422 15.0737 13.4994 14.5598L15.3625 16.4229C15.6554 16.7158 16.1302 16.7158 16.4231 16.4229C16.716 16.13 16.716 15.6551 16.4231 15.3622L4.63803 3.57709ZM12.3608 13.4212L10.4475 11.5079C10.3061 11.5423 10.1584 11.5606 10.0064 11.5606H9.99151C8.96527 11.5606 8.13333 10.7286 8.13333 9.70237C8.13333 9.5461 8.15262 9.39434 8.18895 9.24933L5.91885 6.97923C5.03505 7.69015 4.34057 8.62704 3.92328 9.70247C4.86803 12.1373 7.23361 13.8619 10.0002 13.8619C10.8326 13.8619 11.6287 13.7058 12.3608 13.4212ZM16.0771 9.70249C15.7843 10.4569 15.3552 11.1432 14.8199 11.7311L15.8813 12.7925C16.6329 11.9813 17.2187 11.0143 17.5849 9.94561C17.6389 9.78803 17.6389 9.61696 17.5849 9.45938C16.5055 6.30925 13.5184 4.04303 10.0002 4.04303C9.13525 4.04303 8.30244 4.17999 7.52218 4.43338L8.75139 5.66259C9.1556 5.58413 9.57311 5.54303 10.0002 5.54303C12.7667 5.54303 15.1323 7.26768 16.0771 9.70249Z" fill="#98A2B3" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>

                                <!-- Checkbox -->
                                <div class="flex items-center justify-between">
                                    <label class="flex items-center text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
                                        <input type="checkbox" name="remember" class="mr-2 rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                        Ingat saya
                                    </label>
                                </div>

                                <!-- Submit Button -->
                                <div>
                                    <button type="submit" class="flex w-full items-center justify-center rounded-lg bg-teal-700 px-4 py-3 text-sm font-semibold text-white transition hover:bg-teal-800 shadow-md">
                                        Masuk
                                    </button>
                                </div>

                                <!-- Troubleshooting Note -->
                                <p class="text-xs text-center text-gray-500 mt-2">
                                    💡 Kendala masuk? Gunakan tombol <strong>1-Click Login</strong> di atas untuk masuk langsung secara instan.
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Visual Banner -->
            <div class="bg-teal-900 relative hidden h-full w-full items-center lg:grid lg:w-1/2 dark:bg-gray-800">
                <div class="z-1 flex flex-col items-center justify-center text-center p-8 text-white">
                    <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-md">
                        <svg class="w-10 h-10 text-teal-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold">SIDERAL</h2>
                    <p class="mt-2 max-w-sm text-sm text-teal-200">
                        Sistem Informasi Spatial & Control Lighting Management
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
