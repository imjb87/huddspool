@extends('layouts.app')

@section('content')
    @php
        $hasRulesetContent = filled(trim(strip_tags((string) $ruleset->content)));
    @endphp

    <div class="ui-page-shell ui-document-page" data-ruleset-content-page>
        <x-ui-document-header :breadcrumbs="[
            ['label' => 'Rulesets'],
            ['label' => $ruleset->name, 'url' => route('ruleset.show', $ruleset)],
            ['label' => 'Rules', 'current' => true],
        ]" :title="$ruleset->name" />

        <div class="ui-document-body">
            @if ($hasRulesetContent)
                <section class="ui-card ui-document-card ui-section" data-ruleset-content-section>
                    <div class="ui-document-card-body">
                        <div class="ui-document-prose" data-ruleset-content>
                            {!! $ruleset->content !!}
                        </div>
                    </div>
                </section>
            @else
                <section class="ui-card ui-document-card ui-document-empty ui-section" data-ruleset-content-empty>
                    <x-ui-empty-state title="No ruleset content has been published yet." />
                </section>
            @endif
        </div>

        <x-logo-clouds />
    </div>
@endsection
