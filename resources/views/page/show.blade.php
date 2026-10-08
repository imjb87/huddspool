@extends('layouts.app')

@section('content')
    <div class="ui-page-shell ui-document-page" data-page-show>
        <x-ui-document-header :breadcrumbs="[
            ['label' => 'Pages'],
            ['label' => $page->title, 'current' => true],
        ]" :title="$page->title" />

        <div class="ui-document-body">
            <section class="ui-card ui-document-card ui-section" data-page-content-section>
                <div class="ui-document-card-body">
                    <div class="ui-document-prose dark:prose-invert" data-page-content>
                        {!! $page->content !!}
                    </div>
                </div>
            </section>
        </div>

        <x-logo-clouds />
    </div>
@endsection
