<!DOCTYPE html>
<html lang="vi" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="dark">

    <title>@yield('title', 'Ticket System')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#222222] text-gray-100">

    @include('components.header')

    @include('components.toast')

    <main>
        @yield('content')
    </main>

    @include('components.footer')

</body>

</html>