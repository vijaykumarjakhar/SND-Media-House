<?php
/**
 * SND Media — 404 Not Found Page
 * Standard HTTP 404 error template with navigation recovery
 */

http_response_code(404);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/includes/functions.php';

$seo = [
    'title' => 'Page Not Found (404) | SND Media',
    'description' => 'The story, broadcast, or resource you requested could not be located on SND Media.',
    'url' => 'https://sndmedia.in/404',
    'noindex' => true
];

include __DIR__ . '/includes/header.php';
?>

<main class="min-h-[60vh] flex items-center justify-center py-20 px-4 bg-slate-900/60 relative overflow-hidden">
    <div class="absolute inset-0 bg-radial-gradient opacity-20 pointer-events-none"></div>
    
    <div class="max-w-xl w-full text-center relative z-10 glass-panel p-8 md:p-12 rounded-2xl border border-white/10 shadow-2xl">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-600/10 border border-red-500/20 text-red-500 mb-6 font-mono text-3xl font-extrabold">
            404
        </div>
        
        <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight uppercase mb-4">
            Story Or Page Not Found
        </h1>
        
        <p class="text-slate-300 text-sm md:text-base leading-relaxed mb-8">
            The page you are looking for may have been moved, updated, or temporarily archived by the editorial desk.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="./" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-lg shadow-red-600/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Return to Front Page
            </a>
            <a href="studio-rental" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-white/10 font-bold text-xs uppercase tracking-wider transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Studio Space Rentals
            </a>
        </div>
        
        <div class="mt-8 pt-8 border-t border-white/5 text-xs text-slate-400">
            For urgent editorial notices or corrections, contact <a href="mailto:newsdesk@sndmedia.in" class="text-red-400 hover:underline">newsdesk@sndmedia.in</a>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
