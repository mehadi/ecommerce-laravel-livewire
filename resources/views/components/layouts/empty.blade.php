<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @stack('head')
</head>
<body>
    {{ $slot }}
    @stack('scripts')
</body>
</html>
