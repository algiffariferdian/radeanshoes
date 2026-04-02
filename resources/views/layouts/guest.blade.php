<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'RadeanShoes') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-stone-900 antialiased bg-[#f5efe6]">
        <div class="absolute inset-x-0 top-0 -z-10 h-72 bg-gradient-to-r from-amber-200/70 via-orange-100/30 to-teal-100/70 blur-3xl"></div>
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div>
                <a href="/">
                    <x-application-logo class="w-24 h-24 fill-current text-stone-900" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-5 bg-white shadow-sm overflow-hidden rounded-[1.75rem] border border-stone-200">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
