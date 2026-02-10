<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>InvestTax</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-zinc-50 text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
        <div class="relative overflow-x-clip">
            <div class="pointer-events-none absolute inset-x-0 top-[-12rem] -z-10 flex justify-center blur-3xl">
                <div class="h-72 w-72 rounded-full bg-cyan-400/20 dark:bg-cyan-500/15"></div>
            </div>

            {{-- Navbar --}}
            <header class="sticky top-0 z-50 border-b border-zinc-200/70 bg-white/80 shadow-sm backdrop-blur-xl dark:border-zinc-800/70 dark:bg-zinc-950/75">
                <nav class="mx-auto flex w-full max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8" aria-label="Nawigacja główna">
                    <a href="/" class="text-lg font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">InvestTax</a>

                    <div class="hidden items-center gap-6 text-sm text-zinc-600 md:flex dark:text-zinc-300">
                        <a href="#how-it-works" class="transition hover:text-zinc-900 dark:hover:text-zinc-100">Jak to działa</a>
                        <a href="#features" class="transition hover:text-zinc-900 dark:hover:text-zinc-100">Funkcje</a>
                        <a href="#faq" class="transition hover:text-zinc-900 dark:hover:text-zinc-100">FAQ</a>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-3">
                        @guest
                            <a href="/login" class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-zinc-100">
                                Zaloguj się
                            </a>
                            <a href="/register" class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-zinc-700 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-300">
                                Załóż konto
                            </a>
                        @endguest

                        @auth
                            <a href="/dashboard" class="rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800 dark:hover:text-zinc-100">
                                {{ auth()->user()->name }}
                            </a>
                        @endauth
                    </div>
                </nav>
            </header>

            <main>
                {{-- Hero --}}
                <section class="mx-auto grid w-full max-w-7xl gap-12 px-4 py-16 sm:px-6 md:py-20 lg:grid-cols-2 lg:items-center lg:gap-14 lg:px-8 xl:py-24 2xl:gap-20">
                    <div class="space-y-7">
                        <p class="inline-flex items-center rounded-full border border-cyan-200 bg-cyan-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-cyan-700 dark:border-cyan-900/70 dark:bg-cyan-950/40 dark:text-cyan-300">
                            Podatki inwestycyjne bez chaosu
                        </p>

                        <div class="space-y-4">
                            <h1 class="text-balance text-3xl font-semibold leading-tight sm:text-4xl md:text-5xl xl:text-6xl">
                                Oblicz podatek od giełdy i kryptowalut w jednym miejscu
                            </h1>
                            <p class="max-w-2xl text-sm leading-relaxed text-zinc-600 sm:text-base md:text-lg dark:text-zinc-300">
                                Dodawaj transakcje, śledź zrealizowane zyski i przygotuj dane do PIT-38 zgodnie z polskim prawem.
                            </p>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            @guest
                                <a href="/register" class="inline-flex items-center justify-center rounded-xl bg-zinc-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-zinc-700 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-300">
                                    Załóż darmowe konto
                                </a>
                            @endguest
                            @auth
                                <a href="/dashboard" class="inline-flex items-center justify-center rounded-xl bg-zinc-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-zinc-700 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-300">
                                    Przejdź do dashboardu
                                </a>
                            @endauth
                            <a href="#how-it-works" class="inline-flex items-center justify-center rounded-xl border border-zinc-200 bg-white px-5 py-3 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800 dark:hover:text-zinc-100">
                                Jak to działa
                            </a>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="absolute -inset-4 -z-10 rounded-3xl bg-gradient-to-br from-cyan-400/20 via-sky-300/10 to-transparent blur-2xl dark:from-cyan-500/15 dark:via-sky-400/10"></div>
                        <div class="rounded-3xl border border-zinc-200/80 bg-white/75 p-4 shadow-xl backdrop-blur-xl dark:border-zinc-700/80 dark:bg-zinc-900/65 sm:p-6 xl:p-7">
                            <div class="rounded-2xl border border-zinc-200/70 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-900/80">
                                <div class="mb-5 flex items-center justify-between">
                                    <h2 class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Podgląd dashboardu</h2>
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">PIT-38 ready</span>
                                </div>
                                <div class="space-y-3">
                                    <div class="rounded-xl border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-800/70">
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Zrealizowany wynik 2026</p>
                                        <p class="mt-1 text-lg font-semibold text-zinc-900 dark:text-zinc-50">+ 18 420,50 PLN</p>
                                    </div>
                                    <div class="grid gap-3 sm:grid-cols-2">
                                        <div class="rounded-xl border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-800/70">
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Akcje</p>
                                            <p class="mt-1 text-sm font-semibold text-zinc-800 dark:text-zinc-100">214 transakcji</p>
                                        </div>
                                        <div class="rounded-xl border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-800/70">
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Krypto</p>
                                            <p class="mt-1 text-sm font-semibold text-zinc-800 dark:text-zinc-100">96 transakcji</p>
                                        </div>
                                    </div>
                                    <div class="rounded-xl border border-dashed border-zinc-300 bg-white/70 p-3 text-xs text-zinc-600 dark:border-zinc-600 dark:bg-zinc-900/60 dark:text-zinc-300">
                                        System automatycznie agreguje koszty i przychody, aby ułatwić roczne rozliczenie podatkowe.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Jak to działa --}}
                <section id="how-it-works" class="mx-auto w-full max-w-7xl px-4 py-14 sm:px-6 md:py-16 lg:px-8 xl:py-20">
                    <div class="mb-8 max-w-2xl space-y-3 md:mb-10">
                        <h2 class="text-2xl font-semibold tracking-tight sm:text-3xl">Jak to działa</h2>
                        <p class="text-sm text-zinc-600 sm:text-base dark:text-zinc-300">Trzy kroki od historii transakcji do gotowego podsumowania podatkowego.</p>
                    </div>

                    <div class="grid gap-4 sm:gap-5 md:grid-cols-3 md:gap-6">
                        <article class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-cyan-700 dark:text-cyan-300">Krok 1</p>
                            <h3 class="mt-2 text-base font-semibold">Dodaj transakcje (akcje i krypto)</h3>
                            <p class="mt-2 text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">Wprowadź transakcje z różnych brokerów i giełd kryptowalut w jednym miejscu.</p>
                        </article>
                        <article class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-cyan-700 dark:text-cyan-300">Krok 2</p>
                            <h3 class="mt-2 text-base font-semibold">Automatyczne liczenie zysków i strat</h3>
                            <p class="mt-2 text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">Aplikacja oblicza wynik zrealizowany i porządkuje dane przez cały rok podatkowy.</p>
                        </article>
                        <article class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-cyan-700 dark:text-cyan-300">Krok 3</p>
                            <h3 class="mt-2 text-base font-semibold">Podsumowanie podatkowe do PIT</h3>
                            <p class="mt-2 text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">Na koniec otrzymujesz przejrzyste podsumowanie przydatne do przygotowania PIT-38.</p>
                        </article>
                    </div>
                </section>

                {{-- Funkcje --}}
                <section id="features" class="mx-auto w-full max-w-7xl px-4 py-14 sm:px-6 md:py-16 lg:px-8 xl:py-20">
                    <div class="mb-8 max-w-2xl space-y-3 md:mb-10">
                        <h2 class="text-2xl font-semibold tracking-tight sm:text-3xl">Funkcje</h2>
                        <p class="text-sm text-zinc-600 sm:text-base dark:text-zinc-300">Najważniejsze moduły, które wspierają codzienne zarządzanie inwestycjami i podatkami.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <article class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                            <h3 class="text-base font-semibold">My Wallets</h3>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Organizuj portfele inwestycyjne i porównuj wyniki między rachunkami.</p>
                        </article>
                        <article class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                            <h3 class="text-base font-semibold">My Transactions</h3>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Rejestruj transakcje akcji i kryptowalut z zachowaniem pełnej historii.</p>
                        </article>
                        <article class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                            <h3 class="text-base font-semibold">Śledzenie zrealizowanych zysków</h3>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Monitoruj przychody, koszty i wynik netto w czasie rzeczywistym.</p>
                        </article>
                        <article class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                            <h3 class="text-base font-semibold">Zgodność z polskim prawem podatkowym</h3>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Projektowane pod rozliczenia inwestycyjne zgodne z wymaganiami PIT-38.</p>
                        </article>
                        <article class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                            <h3 class="text-base font-semibold">Bezpieczne przechowywanie danych</h3>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Dane użytkowników są chronione i dostępne tylko dla autoryzowanych kont.</p>
                        </article>
                    </div>
                </section>

                {{-- FAQ --}}
                <section id="faq" class="mx-auto w-full max-w-7xl px-4 py-14 sm:px-6 md:py-16 lg:px-8 xl:py-20">
                    <div class="mb-8 max-w-2xl space-y-3 md:mb-10">
                        <h2 class="text-2xl font-semibold tracking-tight sm:text-3xl">FAQ</h2>
                        <p class="text-sm text-zinc-600 sm:text-base dark:text-zinc-300">Najczęstsze pytania dotyczące korzystania z aplikacji InvestTax.</p>
                    </div>

                    <div class="space-y-4">
                        <article class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                            <h3 class="text-base font-semibold">Czy aplikacja jest darmowa?</h3>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Tak, możesz założyć konto i rozpocząć pracę z podstawowymi funkcjami bez opłat.</p>
                        </article>
                        <article class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                            <h3 class="text-base font-semibold">Czy łączy się z giełdą?</h3>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Na start dane dodajesz ręcznie, dzięki czemu masz pełną kontrolę nad importowanymi transakcjami.</p>
                        </article>
                        <article class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                            <h3 class="text-base font-semibold">Czy moje dane są bezpieczne?</h3>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Tak. Stosujemy bezpieczne przechowywanie danych i standardowe mechanizmy ochrony kont.</p>
                        </article>
                        <article class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                            <h3 class="text-base font-semibold">Czy jest zgodna z polskim prawem podatkowym?</h3>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Tak, widok i logika aplikacji są projektowane pod potrzeby rozliczenia PIT-38 w Polsce.</p>
                        </article>
                    </div>
                </section>
            </main>

            {{-- Footer --}}
            <footer class="border-t border-zinc-200 bg-white/80 dark:border-zinc-800 dark:bg-zinc-950/80">
                <div class="mx-auto flex w-full max-w-7xl flex-col gap-4 px-4 py-8 text-sm text-zinc-600 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8 dark:text-zinc-300">
                    <div class="flex flex-wrap gap-x-5 gap-y-2">
                        <a href="#" class="transition hover:text-zinc-900 dark:hover:text-zinc-100">Regulamin</a>
                        <a href="#" class="transition hover:text-zinc-900 dark:hover:text-zinc-100">Polityka prywatności</a>
                        <a href="#" class="transition hover:text-zinc-900 dark:hover:text-zinc-100">Kontakt</a>
                        <a href="#" class="transition hover:text-zinc-900 dark:hover:text-zinc-100">GitHub</a>
                    </div>
                    <p class="text-xs sm:text-sm">Copyright © 2026 InvestTax</p>
                </div>
            </footer>
        </div>
    </body>
</html>
