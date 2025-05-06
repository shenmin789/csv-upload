<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
        
        <!-- Styles -->
        @vite('resources/css/app.css')
        @livewireStyles

    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div class="min-h-screen">

            <!-- Page Content -->
            <main>
            @livewire('file-uploader')
            </main>
            
        </div>

        @livewireScripts
    </body>
</html>