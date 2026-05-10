@extends('layouts.app')
@section('content')
@php
    $reviewCount = $business->reviews->count();
    $trustScore = min(98, 72 + ($business->average_review * 4) + min($reviewCount, 6));
@endphp

<section class="supplier-profile">
    <div class="supplier-hero">
        <div>
            <a class="supplier-back" href="/businesses">← Terug naar leveranciers</a>
            <p class="eyay-eyebrow"><span></span> EyAy leveranciersprofiel</p>
            <h1>{{ $business->name }}</h1>
            <p class="supplier-lead">{{ $business->description }}</p>
            <div class="supplier-categories">
                @foreach ($business->categories as $category)
                <span class="category-chip">{{ $category->name }}</span>
                @endforeach
            </div>
            <div class="supplier-actions">
                <a class="eyay-button primary" href="mailto:{{ $business->email }}">Plan intake</a>
                <a class="eyay-button" href="#reviews">Bekijk reviews</a>
            </div>
        </div>
        <aside class="supplier-visual">
            <img src="{{ asset($business->image()) }}" alt="{{ $business->name }}">
            <div class="supplier-score">
                <strong>{{ $trustScore }}%</strong>
                <span>EyAy matchscore</span>
                <p>Gebaseerd op categorie-fit, reviewkwaliteit en profielvolledigheid.</p>
            </div>
        </aside>
    </div>

    <div class="supplier-layout">
        <section class="supplier-main">
            <div class="supplier-card">
                <div class="supplier-section-heading">
                    <p class="kicker">Profiel</p>
                    <h2>Waar deze leverancier sterk in is</h2>
                </div>
                <div class="supplier-points">
                    <div>
                        <strong>AI-oplossingen</strong>
                        <p>{{ $business->categories->pluck('name')->take(3)->join(', ') }}</p>
                    </div>
                    <div>
                        <strong>Geschikt voor</strong>
                        <p>MKB, mid-market en teams die aantoonbare AI in productie willen brengen.</p>
                    </div>
                    <div>
                        <strong>Implementatie</strong>
                        <p>Start met intake, scopebepaling, pilot en meetbare productievalidatie.</p>
                    </div>
                </div>
            </div>

            <showcasedreviews :business-slug="'{{ $business->slug }}'"
                :current-user-is-owner="{{Auth::check() && Auth::user()->ownerOf($business) ? 'true' : 'false' }}">
            </showcasedreviews>

            <div class="supplier-card">
                <div class="supplier-section-heading">
                    <p class="kicker">Locatie</p>
                    <h2>Werkgebied en contact</h2>
                </div>
                @include('business.components.map')
            </div>

            @can('addReview', $business)
            <add-review :url-path="'{{ "/businesses/". $business->slug ."/review" }}'"></add-review>
            @endcan

            <div id="reviews">
                <reviews :current-user-is-owner="{{Auth::check() && Auth::user()->ownerOf($business) ? 'true' : 'false' }}">
                </reviews>
            </div>
        </section>

        <aside class="supplier-aside">
            @include('business.components.info-card')
            <x-business-rating-card :business="$business" />
        </aside>
    </div>
</section>
@endsection
