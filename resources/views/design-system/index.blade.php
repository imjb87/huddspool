@extends('layouts.app')

@section('content')
    <div class="ui-page-shell" data-design-system-page>
        <div class="ui-page-header">
            <div class="min-w-0 space-y-2">
                <p class="ui-kicker">Design system</p>
                <h1 class="ui-display-title">Core components</h1>
                <p class="max-w-3xl text-sm leading-7 text-gray-600 dark:text-gray-400">
                    Build the system one primitive at a time. Card defines the surface. Button defines the action language.
                </p>
            </div>
        </div>

        <div class="ui-page-body space-y-10">
            <section class="ui-shell-grid" data-design-system-card>
                <div class="space-y-2">
                    <p class="ui-kicker">Component 01</p>
                    <h2 class="ui-section-title">Definition</h2>
                    <p class="ui-copy">
                        Use card for grouped content that needs clear separation from the page background without heavy decoration. Prefer it for forms, summaries, and contained workflow blocks.
                    </p>
                    <div class="ui-subtle-panel mt-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500 dark:text-gray-400">Class</p>
                        <p class="mt-2 font-mono text-sm text-gray-900 dark:text-gray-100">ui-card</p>
                    </div>
                </div>

                <div class="space-y-6 lg:col-span-2">
                    <div class="ui-card" data-design-system-card-example>
                        <div class="ui-card-body">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div class="space-y-2">
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">League summary</h3>
                                    <p class="max-w-2xl text-sm text-gray-600 dark:text-gray-400">
                                        Card content should start immediately inside the body. If a title or status is needed, it belongs in the body layout rather than a dedicated card header shell.
                                    </p>
                                </div>
                                <span class="ui-pill ui-pill-success">Healthy</span>
                            </div>
                        </div>

                        <div class="ui-card-column-headings" data-design-system-card-column-headings>
                            <div class="min-w-[4.5rem] text-right">
                                <p class="ui-card-column-header">Total</p>
                            </div>
                        </div>

                        <div class="ui-card-rows" data-design-system-card-rows>
                            <a href="#" class="ui-card-row-link">
                                <div class="ui-card-row">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Open sections</p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Live league tables currently visible</p>
                                    </div>
                                    <div class="min-w-[4.5rem] text-right">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">12</p>
                                    </div>
                                </div>
                            </a>
                            <div class="ui-card-row">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Pending results</p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Matches that still need score confirmation</p>
                                </div>
                                <div class="min-w-[4.5rem] text-right">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">4</p>
                                </div>
                            </div>
                            <div class="ui-card-row">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Avg latency</p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Healthy websocket round trip</p>
                                </div>
                                <div class="min-w-[4.5rem] text-right">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">0.4s</p>
                                </div>
                            </div>
                        </div>

                        <div class="ui-card-footer">
                            <div class="flex flex-wrap items-center gap-3">
                                <button type="button" class="ui-button-primary">Primary action</button>
                                <button type="button" class="ui-button-secondary">Secondary action</button>
                            </div>
                        </div>
                    </div>

                    <div class="ui-card-branded" data-design-system-card-branded>
                        <div class="ui-shell-grid items-center px-5 py-5 lg:px-6 lg:py-6">
                            <div class="space-y-3">
                                <h3 class="text-base font-semibold text-white">Branded card</h3>
                                <p class="max-w-xl text-sm leading-6 text-green-50/90">
                                    Use the branded card when the surface itself should carry league identity. It spans the full available width and splits content left-right instead of nesting into smaller utility rows.
                                </p>
                                <div class="flex flex-wrap items-center gap-3">
                                    <button type="button" class="ui-button-primary">Register now</button>
                                    <button type="button" class="ui-button-secondary">View handbook</button>
                                </div>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-3 lg:col-span-2 lg:grid-cols-1">
                                <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15 backdrop-blur-sm">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-green-100/80">Surface</p>
                                    <p class="mt-2 text-sm text-white">Full-width, gradient-led, and brand-forward.</p>
                                </div>
                                <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15 backdrop-blur-sm">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-green-100/80">Layout</p>
                                    <p class="mt-2 text-sm text-white">Split content left-right with one clear action cluster.</p>
                                </div>
                                <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15 backdrop-blur-sm">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-green-100/80">Use case</p>
                                    <p class="mt-2 text-sm text-white">Hero support, registrations, and branded calls to action.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ui-panel">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Rules</h3>
                        <ul class="mt-4 space-y-2 text-sm leading-6 text-gray-600 dark:text-gray-400">
                            <li>Keep the radius at the Filament-style rounded-xl scale.</li>
                            <li>Use a subtle ring and soft shadow instead of a heavy border.</li>
                            <li>Cards do not have a dedicated header container.</li>
                            <li>Put title, status, and lead copy directly inside the body layout.</li>
                            <li>Rows inside the card should be separated with standard dividers, using neutral-800 in dark mode.</li>
                            <li>If a card row is linked, it gets a desktop hover state, resolving to neutral-800 in dark mode.</li>
                            <li>If a row set includes data columns, the columns need a single header row using the same typography as the row sub-description.</li>
                            <li>Column headers appear once above the first row and are never repeated per row.</li>
                            <li><code>ui-card-branded</code> is the full-width gradient variant with a left-right split layout.</li>
                            <li>Do not stack decorative gradients or tinted fills inside the card by default.</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="ui-shell-grid" data-design-system-button>
                <div class="space-y-2">
                    <p class="ui-kicker">Component 02</p>
                    <h2 class="ui-section-title">Button</h2>
                    <p class="ui-copy">
                        Buttons are rounded pills. Primary actions use the league green gradient. Secondary actions stay quiet and structural.
                    </p>
                    <div class="ui-subtle-panel mt-4 space-y-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500 dark:text-gray-400">Primary class</p>
                            <p class="mt-2 font-mono text-sm text-gray-900 dark:text-gray-100">ui-button-primary</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500 dark:text-gray-400">Secondary class</p>
                            <p class="mt-2 font-mono text-sm text-gray-900 dark:text-gray-100">ui-button-secondary</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-6 lg:col-span-2">
                    <div class="ui-card" data-design-system-button-example>
                        <div class="ui-card-body space-y-5">
                            <div class="space-y-2">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Action set</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Primary should be visually obvious at a glance. Secondary should support the path without competing with it.
                                </p>
                            </div>

                            <div class="flex flex-wrap items-center gap-3">
                                <button type="button" class="ui-button-primary">Submit result</button>
                                <button type="button" class="ui-button-secondary">Save draft</button>
                            </div>
                        </div>
                    </div>

                    <div class="ui-panel">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Rules</h3>
                        <ul class="mt-4 space-y-2 text-sm leading-6 text-gray-600 dark:text-gray-400">
                            <li>Buttons use a rounded pill silhouette, not a square or card-like radius.</li>
                            <li>Primary actions use the green gradient and should appear once per action group.</li>
                            <li>Secondary actions stay neutral and never use the gradient treatment.</li>
                            <li>Keep button copy short and operational.</li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
