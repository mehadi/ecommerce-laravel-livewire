<!DOCTYPE html>
<html lang="en" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('Store Unavailable') }}</title>
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen flex items-center justify-center bg-white dark:bg-zinc-900 px-6">
        <div class="max-w-md text-center space-y-4">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/20">
                <svg class="h-7 w-7 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                </svg>
            </div>
            <h1 class="text-xl font-semibold text-zinc-900 dark:text-white">{{ __('This store is temporarily unavailable') }}</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('The owner of this store has been notified. Please check back later.') }}
            </p>
        </div>
    </body>
</html>
