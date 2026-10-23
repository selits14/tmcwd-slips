<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TMCWD Slip System') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#102b46] font-sans text-slate-900 antialiased">
        <main class="min-h-screen lg:grid lg:grid-cols-[1.05fr_0.95fr]">
            <section class="relative flex min-h-[32vh] flex-col justify-between overflow-hidden bg-[#102b46] px-7 py-8 text-white sm:px-12 lg:min-h-screen lg:px-16 lg:py-12">
                <div class="absolute -right-24 -top-28 h-80 w-80 rounded-full border border-white/10"></div>
                <div class="absolute -right-10 -top-14 h-52 w-52 rounded-full border border-white/10"></div>
                <div class="relative flex items-center gap-4">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white p-2 shadow-lg shadow-sky-900/10 sm:h-[4.5rem] sm:w-[4.5rem]">
                        <x-application-logo class="h-full w-full object-contain" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-200">Trece Martires City</p>
                        <h1 class="mt-1 text-lg font-bold leading-tight sm:text-xl">Water District</h1>
                    </div>
                </div>

                <div class="relative my-12 max-w-xl lg:my-auto lg:py-20">
                    <p class="text-sm font-semibold uppercase tracking-[0.22em] text-cyan-200">TMCWD</p>
                    <h2 class="mt-4 text-3xl font-semibold leading-tight tracking-tight sm:text-4xl lg:text-5xl">Slips Management System</h2>
                    <p class="mt-5 max-w-md text-base leading-7 text-slate-300">Manage and track official slips in one secure place.</p>
                </div>

                <p class="relative hidden text-xs tracking-wide text-slate-400 lg:block">TMCWD &nbsp;·&nbsp; OFFICIAL SYSTEM</p>
            </section>

            <section class="flex items-center justify-center bg-[#102b46] px-5 py-12 sm:px-10 lg:px-12">
                <div class="w-full max-w-md">
                    <div class="mb-8 lg:hidden">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Trece Martires City Water District</p>
                    </div>
                    <div class="rounded-2xl border border-sky-200 bg-white p-7 shadow-xl shadow-sky-900/10 sm:p-10">
                        <div class="mb-8">
                            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-sky-700">Secure sign in</p>
                            <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Welcome back</h2>
                            <p class="mt-2 text-sm text-slate-500">Enter your account details to continue.</p>
                        </div>
                        {{ $slot }}
                    </div>
                    <p class="mt-6 text-center text-xs text-slate-600">Authorized personnel only</p>
                </div>
            </section>
        </main>
    </body>
</html>
