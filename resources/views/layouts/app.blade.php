<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('dark') === 'true', sidebarOpen: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Professional ERP') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-slate-900 text-gray-900 dark:text-white transition-colors duration-300">

<div class="flex h-screen overflow-hidden">

    @include('layouts.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">

        @include('layouts.header')

        <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-6">
            {{ $slot }}
        </main>

        <footer class="bg-white dark:bg-slate-800 border-t dark:border-slate-700 py-3 px-6 text-sm flex justify-between items-center">
            <div>v1.0.2 | Developer: <a href="https://moniruddin.com" target="_blank" class="text-blue-500 font-bold italic">Monir.dev</a></div>
            <div class="hidden md:block italic text-gray-400">© {{ date('Y') }} All Rights Reserved.</div>
        </footer>
    </div>
</div>

<script>
    window.addEventListener('swal', event => {
        Swal.fire({
            title: event.detail.title,
            text: event.detail.text,
            icon: event.detail.icon,
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false
        });
    });
</script>
</body>
</html>
