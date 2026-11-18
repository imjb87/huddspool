@extends('layouts.app')

@section('title', 'Privacy')
@section('meta_description', 'How Huddspool uses and protects personal information.')

@section('content')
    <div class="ui-page-shell ui-document-page" data-privacy-page>
        <x-ui-document-header
            :breadcrumbs="[['label' => 'Privacy', 'current' => true]]"
            title="Privacy"
            description="How Huddspool uses and protects personal information."
        />

        <div class="ui-document-body">
            <article class="ui-card ui-document-card ui-section" data-privacy-content>
                <div class="ui-document-card-body">
                    <div class="ui-document-prose">
                        <p class="text-xs text-muted-foreground">Last updated 29 July 2026</p>
                        <h2>Who operates Huddspool</h2>
                        <p>Huddspool is the website and administration service for the Huddersfield &amp; District Tuesday Night Pool League. League administrators manage the information held through this service.</p>

                        <h2>Information we use</h2>
                        <p>We may hold account and contact information, team and venue membership, league entries, fixtures, results, disciplinary records, support requests, notification preferences, and technical security information such as IP addresses and browser details.</p>

                        <h2>Why we use it</h2>
                        <p>We use this information to operate the league, publish competition information, manage accounts and entries, communicate with participants, provide support, prevent misuse, and maintain an accurate administrative record.</p>

                        <h2>Sharing and retention</h2>
                        <p>We disclose information only where needed to operate the league and website, use service providers, comply with legal obligations, or protect people and the service. Public league information may remain in historical results. Account, support, security, and administrative information is retained only for as long as reasonably required for administration, accountability, dispute handling, and legal obligations.</p>

                        <h2>Your choices and rights</h2>
                        <p>You may ask a league administrator to correct your information or raise a privacy concern. Depending on applicable law, you may have rights to access, correct, erase, restrict, or object to the use of your personal information.</p>

                        <h2>Contact</h2>
                        <p>Contact the league administrators through your usual league contact. Signed-in users can also use the <a href="{{ route('support.tickets') }}">Huddspool support form</a>.</p>
                    </div>
                </div>
            </article>
        </div>
    </div>
@endsection
