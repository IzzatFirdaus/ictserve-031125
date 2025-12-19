<!DOCTYPE html>
<html lang="ms" class="h-full bg-gray-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ICTServe Admin') }} - Log Masuk</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full font-sans antialiased text-gray-900">
    <main class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
        <!-- Logo Section -->
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <img class="mx-auto h-12 w-auto" src="{{ asset('images/motac-logo.png') }}" alt="Logo MOTAC">
            <h1 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
                Log Masuk Pentadbir
            </h1>
            <p class="mt-2 text-center text-sm text-gray-600">
                Sila log masuk untuk mengakses papan pemuka
            </p>
        </div>

        <!-- Login Card -->
        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
            <div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12">
                <form class="space-y-6" action="#" method="POST">
                    @csrf

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
                            Emel
                        </label>
                        <div class="mt-2">
                            <input id="email" name="email" type="email" autocomplete="email" required
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6"
                                placeholder="nama@motac.gov.my"
                                aria-describedby="email-error">
                        </div>
                        @error('email')
                        <p class="mt-2 text-sm text-red-600" id="email-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">
                            Kata Laluan
                        </label>
                        <div class="mt-2">
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6">
                        </div>
                        @error('password')
                        <p class="mt-2 text-sm text-red-600" id="password-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember" type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-primary-500 focus:ring-primary-500">
                            <label for="remember-me" class="ml-3 block text-sm leading-6 text-gray-900">
                                Ingat saya
                            </label>
                        </div>

                        <div class="text-sm leading-6">
                            <a href="#" class="font-semibold text-primary-500 hover:text-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-sm">
                                Lupa kata laluan?
                            </a>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                            class="flex w-full justify-center rounded-md bg-primary-500 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-primary-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-500 transition duration-150 ease-in-out">
                            Log Masuk
                        </button>
                    </div>
                </form>

                <!-- Footer / Help -->
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="bg-white px-2 text-gray-500">
                                Perlukan bantuan?
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4">
                        <a href="#"
                            class="flex w-full items-center justify-center gap-3 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                            </svg>
                            <span class="text-sm font-medium">Hubungi Meja Bantuan</span>
                        </a>
                    </div>
                </div>
            </div>

            <p class="mt-10 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} Kementerian Pelancongan, Seni dan Budaya (MOTAC). <br>Hak Cipta Terpelihara.
            </p>
        </div>
    </main>
</body>

</html>
