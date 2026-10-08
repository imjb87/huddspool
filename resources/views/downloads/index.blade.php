@extends('layouts.app')

@section('content')
    <div class="ui-page-shell ui-document-page" data-downloads-page>
        <x-ui-document-header
            title="Downloads"
            description="Printable score cards for league and knockout nights."
        />

        <div class="ui-document-body">
            <section class="ui-card ui-document-card" data-downloads-list>
                <div class="ui-document-card-body">
                    <div class="flex flex-col gap-1">
                        <h2 class="text-base font-semibold tracking-tight text-foreground">Printable forms</h2>
                        <p class="m-0 text-sm leading-6 text-muted-foreground">Download a blank PDF, print the copies you need, and complete them on the night.</p>
                    </div>

                    <div class="mt-6 rounded-lg border border-border bg-muted/30 p-4 transition-colors hover:bg-muted/60 sm:flex sm:items-center sm:justify-between sm:gap-6">
                        <div class="min-w-0">
                            <h3 class="text-sm font-medium text-foreground">Score card</h3>
                            <p class="mt-1 text-sm leading-6 text-muted-foreground">Blank match score card for recording frames and the final result.</p>
                        </div>
                        <a href="{{ asset('downloads/scorecard.pdf') }}" download="scorecard.pdf" class="ui-button-primary mt-4 shrink-0 sm:mt-0" data-download-link="scorecard">
                            Download scorecard
                        </a>
                    </div>
                </div>
            </section>
        </div>

        <x-logo-clouds />
    </div>
@endsection
