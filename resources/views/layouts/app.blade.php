<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">
    <nav class="bg-white shadow p-4">
        <div class="container mx-auto flex items-center gap-6">
            <a href="{{ route('opinsurance.dashboard') }}" class="font-bold">Opinsurance Dashboard</a>
        </div>
    </nav>

    <main class="container mx-auto p-6">
        @yield('content')
    </main>
</body>
</html>
