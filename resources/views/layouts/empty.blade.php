<!DOCTYPE html>
<html lang="fr" class="h-full admin-page">
<head>
    @include('partials.admin-head')
</head>
<body class="h-full admin-page">
    <div class="container mx-auto">
        @include('partials.notifications')
        @yield('content')
    </div>

    @stack('scripts')
    @livewireScripts
</body>
</html>
