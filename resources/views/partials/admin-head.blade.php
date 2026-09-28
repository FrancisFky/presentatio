<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="robots" content="noindex, nofollow">
<title>@yield('title', isset($title) ? $title . ' - Admin Ambassade' : 'Admin Ambassade')</title>
<link rel="icon" href="{{ asset('images/armoiries.svg') }}">
@vite(['resources/css/app.css', 'resources/js/admin.js'])
@stack('styles')
@livewireStyles
