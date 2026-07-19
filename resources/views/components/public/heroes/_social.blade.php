{{-- Shared "Follow us on" row for hero variants. Set $tone to 'dark' when rendering on a dark background. --}}
@php($tone = $tone ?? 'light')
@if(($socialFacebook && $socialFacebook !== '#') || ($socialInstagram && $socialInstagram !== '#') || ($socialTwitter && $socialTwitter !== '#'))
    <div class="flex items-center gap-3">
        <span class="text-xs sm:text-sm font-medium {{ $tone === 'dark' ? 'text-white/60' : 'text-zinc-500 dark:text-zinc-400' }}">{{ __('Follow us on') }}:</span>
        <div class="flex items-center gap-2">
            @if($socialTwitter && $socialTwitter !== '#')
                <a href="{{ $socialTwitter }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full flex items-center justify-center hover:-translate-y-0.5 hover:shadow-md motion-reduce:transform-none transition-all duration-200 shadow-sm {{ $tone === 'dark' ? 'bg-white/10 ring-1 ring-white/15 text-white/80 hover:text-white hover:bg-white/20' : 'bg-white dark:bg-zinc-700 ring-1 ring-zinc-900/[0.05] dark:ring-white/[0.08] text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white' }}" aria-label="Twitter">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.9 2H22l-7.6 8.7L23 22h-6.9l-5.4-6.8L4.4 22H1.3l8.1-9.3L1 2h7.1l4.9 6.2L18.9 2zm-1.2 18h1.9L7.4 4H5.4l12.3 16z"/></svg>
                </a>
            @endif
            @if($socialInstagram && $socialInstagram !== '#')
                <a href="{{ $socialInstagram }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full flex items-center justify-center hover:-translate-y-0.5 hover:shadow-md motion-reduce:transform-none transition-all duration-200 shadow-sm {{ $tone === 'dark' ? 'bg-white/10 ring-1 ring-white/15 text-white/80 hover:text-white hover:bg-white/20' : 'bg-white dark:bg-zinc-700 ring-1 ring-zinc-900/[0.05] dark:ring-white/[0.08] text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white' }}" aria-label="Instagram">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2c-2.72 0-3.06.01-4.12.06-1.06.05-1.79.22-2.43.47a4.9 4.9 0 00-1.77 1.15A4.9 4.9 0 002.53 5.45c-.25.64-.42 1.37-.47 2.43C2.01 8.94 2 9.28 2 12s.01 3.06.06 4.12c.05 1.06.22 1.79.47 2.43a4.9 4.9 0 001.15 1.77 4.9 4.9 0 001.77 1.15c.64.25 1.37.42 2.43.47C9.94 21.99 10.28 22 13 22s3.06-.01 4.12-.06c1.06-.05 1.79-.22 2.43-.47a4.9 4.9 0 001.77-1.15 4.9 4.9 0 001.15-1.77c.25-.64.42-1.37.47-2.43.05-1.06.06-1.4.06-4.12s-.01-3.06-.06-4.12c-.05-1.06-.22-1.79-.47-2.43a4.9 4.9 0 00-1.15-1.77A4.9 4.9 0 0018.55 2.53c-.64-.25-1.37-.42-2.43-.47C15.06 2.01 14.72 2 12 2zm0 5.35A4.65 4.65 0 1012 16.65 4.65 4.65 0 0012 7.35zm0 7.67A3.02 3.02 0 1112 8.98a3.02 3.02 0 010 6.04zm4.84-8.9a1.08 1.08 0 11-2.16 0 1.08 1.08 0 012.16 0z"/></svg>
                </a>
            @endif
            @if($socialFacebook && $socialFacebook !== '#')
                <a href="{{ $socialFacebook }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full flex items-center justify-center hover:-translate-y-0.5 hover:shadow-md motion-reduce:transform-none transition-all duration-200 shadow-sm {{ $tone === 'dark' ? 'bg-white/10 ring-1 ring-white/15 text-white/80 hover:text-white hover:bg-white/20' : 'bg-white dark:bg-zinc-700 ring-1 ring-zinc-900/[0.05] dark:ring-white/[0.08] text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white' }}" aria-label="Facebook">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9.198 21.5h4v-8.01h3.604l.396-3.98h-4V7.5a1 1 0 011-1h3v-4h-3a5 5 0 00-5 5v2.01h-2l-.396 3.98h2.396v8.01z"/></svg>
                </a>
            @endif
        </div>
    </div>
@endif
