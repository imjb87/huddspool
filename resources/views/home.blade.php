@extends('layouts.app')

@section('content')
    <section data-home-hero
        class="overflow-hidden bg-linear-to-br from-green-950 via-green-800 to-green-600 pt-[72px] text-white lg:pt-[80px]">
        <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 sm:py-12 lg:px-6 lg:py-14">
            <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                <div class="flex justify-center lg:justify-start">
                    <div class="relative">
                        <div class="absolute inset-0 rounded-full bg-white/10 blur-3xl"></div>
                        <img class="relative w-36 drop-shadow-2xl sm:w-40 lg:w-44"
                            src="{{ asset('images/logo.png') }}" alt="Huddersfield Pool League logo" />
                    </div>
                </div>

                <div class="space-y-3 text-center lg:col-span-2 lg:text-left">
                    <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl lg:text-[2.75rem]">
                        Everything for league night, in one place.
                    </h1>
                    <p class="text-sm leading-7 text-green-50 sm:text-base">
                        Tables, fixtures, results and averages for every section, with the latest league information
                        always close at hand.
                    </p>
                    <p class="text-sm leading-6 text-green-100" data-home-hero-account-link>
                        @auth
                            Keep your details, knockouts and team tools together in your
                            <a href="{{ route('account.show') }}" class="font-semibold text-white underline decoration-white/40 underline-offset-3 transition hover:decoration-white">
                                account
                            </a>.
                        @else
                            Already involved in the league?
                            <a href="{{ route('login') }}" class="font-semibold text-white underline decoration-white/40 underline-offset-3 transition hover:decoration-white">
                                Log in
                            </a>
                            to access your account.
                        @endauth
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-8 dark:bg-zinc-900 sm:py-10" data-home-live-scores>
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
            <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                <div class="space-y-2">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Live scores</h2>
                    <p class="text-sm leading-6 text-gray-600 dark:text-gray-400">
                        Results currently being added across the league.
                    </p>
                </div>
                <div class="lg:col-span-2">
                    @if ($liveScores->isEmpty())
                        <div class="mt-6 rounded-[1.75rem] border border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center dark:border-zinc-700 dark:bg-zinc-900/70">
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">No current matches in progress right now.</p>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Check back during league night to follow the latest scores as they come in.
                            </p>
                        </div>
                    @else
                        <div class="max-h-80 divide-y divide-gray-200 overflow-y-auto overscroll-contain dark:divide-zinc-800/80"
                            data-home-live-scores-shell
                            data-home-live-scores-list>
                            @foreach ($liveScores as $result)
                                @php
                                    $rowMeta = collect([
                                        $result->fixture?->fixture_date?->format('j M Y'),
                                        $result->section?->name,
                                        $result->section?->ruleset?->name,
                                    ])->filter()->implode(' / ');
                                @endphp
                                <div class="py-4" data-home-live-score-row>
                                    <a href="{{ route('result.show', $result) }}" class="block rounded-lg">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $result->home_team_name }} <span class="font-normal text-gray-400 dark:text-gray-500">vs</span> {{ $result->away_team_name }}
                                                </p>
                                                @if ($rowMeta !== '')
                                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $rowMeta }}</p>
                                                @endif
                                            </div>

                                            <div class="ml-auto flex shrink-0 self-center items-center text-right">
                                                <div class="inline-flex h-7 w-[60px] overflow-hidden rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10"
                                                    data-home-live-score-pill>
                                                    <div class="flex w-1/2 items-center justify-center tabular-nums pl-1">
                                                        {{ $result->home_score }}
                                                    </div>
                                                    <div class="w-px bg-white/25"></div>
                                                    <div class="flex w-1/2 items-center justify-center tabular-nums pr-1">
                                                        {{ $result->away_score }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-10 dark:bg-zinc-900 sm:py-12" data-home-news>
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
            <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                <div class="space-y-2">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Latest news</h2>
                    <p class="max-w-sm text-sm leading-6 text-gray-600 dark:text-gray-400">
                        Important updates, date changes and notices from across the league in one place.
                    </p>
                </div>

                <div class="lg:col-span-2">
                    @if ($news->isEmpty())
                        <div class="border-t border-gray-200 pt-6 dark:border-zinc-800" data-home-news-empty>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">No league news has been published yet.</p>
                            <p class="mt-2 max-w-prose text-sm text-gray-500 dark:text-gray-400">
                                Updates from the league committee will appear here when they are posted.
                            </p>
                        </div>
                    @else
                        @php
                            $featuredArticle = $news->first();
                            $secondaryArticles = $news->slice(1);
                            $featuredParagraphs = collect(preg_split('/\r\n|\r|\n/', trim((string) $featuredArticle->content)))
                                ->filter()
                                ->take(3);
                        @endphp

                        <div class="border-t border-gray-200 pt-6 dark:border-zinc-800" data-home-news-grid>
                            <article data-home-news-featured>
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    <time datetime="{{ $featuredArticle->created_at?->toDateString() }}">
                                        {{ $featuredArticle->created_at?->format('j F Y') }}
                                    </time>
                                </div>

                                <h3 class="mt-3 text-lg font-semibold tracking-tight text-gray-900 dark:text-gray-100 sm:text-xl">
                                    {{ $featuredArticle->title }}
                                </h3>

                                <div class="mt-4 space-y-4 text-sm leading-6 text-gray-600 dark:text-gray-400">
                                    @foreach ($featuredParagraphs as $paragraph)
                                        <p>{{ $paragraph }}</p>
                                    @endforeach
                                </div>
                            </article>

                            @if ($secondaryArticles->isNotEmpty())
                                <div class="mt-6 divide-y divide-gray-200 border-t border-gray-200 dark:divide-zinc-800 dark:border-zinc-800" data-home-news-list>
                                    @foreach ($secondaryArticles as $article)
                                        <article class="py-5 first:pt-4 last:pb-0" data-home-news-item>
                                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                <time datetime="{{ $article->created_at?->toDateString() }}">
                                                    {{ $article->created_at?->format('j F Y') }}
                                                </time>
                                            </div>

                                            <h3 class="mt-3 text-base font-semibold tracking-tight text-gray-900 dark:text-gray-100">
                                                {{ $article->title }}
                                            </h3>

                                            <p class="mt-3 line-clamp-4 text-sm leading-6 text-gray-600 dark:text-gray-400">
                                                {{ \Illuminate\Support\Str::limit($article->content, 180) }}
                                            </p>
                                        </article>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <x-logo-clouds class="pt-10 sm:pt-12"  />
@endsection
