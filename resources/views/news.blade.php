<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Global News - SupplyGuard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/supplyguard-professional.css') }}">
    <style>
        .news-page{padding:28px}.news-hero{padding:30px;border-radius:24px;background:linear-gradient(135deg,#0b2448,#155f86);color:#fff}
        .news-stat{height:100%;padding:18px;border:1px solid #dfe7f1;border-radius:16px;background:#fff;box-shadow:0 9px 24px rgba(15,23,42,.06)}.news-stat strong{display:block;color:#1261cb;font-size:25px}
        .news-filter{padding:18px;border:1px solid #dfe7f1;border-radius:18px;background:#fff}.news-card{height:100%;overflow:hidden;border:1px solid #dfe7f1;border-radius:18px;background:#fff;box-shadow:0 10px 26px rgba(15,23,42,.06);transition:.2s ease}.news-card:hover{transform:translateY(-3px);box-shadow:0 16px 34px rgba(15,23,42,.1)}
        .news-image{width:100%;height:150px;object-fit:cover;background:#eaf1f8}.news-placeholder{height:150px;display:grid;place-items:center;background:linear-gradient(135deg,#e8f1fc,#dbe9f8);color:#4773a7;font-weight:900}.news-content{padding:17px}.news-content h2{display:-webkit-box;overflow:hidden;-webkit-box-orient:vertical;-webkit-line-clamp:2;font-size:15px;line-height:1.4}.news-summary{display:-webkit-box;overflow:hidden;-webkit-box-orient:vertical;-webkit-line-clamp:3;color:#68798d;font-size:11px;line-height:1.55}
        .sentiment{padding:4px 8px;border-radius:999px;font-size:8px;font-weight:900;text-transform:uppercase}.sentiment-positive{background:#e2f8ee;color:#087a55}.sentiment-negative{background:#ffebeb;color:#bd3030}.sentiment-neutral{background:#edf2f8;color:#60738a}.news-pagination{display:flex;align-items:center;justify-content:center;gap:12px}@media(max-width:767px){.news-page{padding:16px}.news-hero{padding:23px}}
    </style>
</head>
<body class="sg-user-sidebar-layout">
@include('user.partials.sidebar')
<main class="news-page">
    <section class="news-hero mb-4">
        <span class="badge text-bg-info mb-2">GLOBAL INTELLIGENCE FEED</span>
        <h1 class="fw-bold mb-2">{{ app()->getLocale() === 'id' ? 'Berita Rantai Pasok Global' : 'Global Supply Chain News' }}</h1>
        <p class="mb-0 opacity-75">{{ app()->getLocale() === 'id' ? 'Berita ekonomi, perdagangan, logistik, dan risiko dengan analisis sentimen.' : 'Economic, trade, logistics, and risk news with sentiment analysis.' }}</p>
    </section>
    <section class="row g-3 mb-4">
        @foreach ([['total','Total Articles'],['positive','Positive'],['neutral','Neutral'],['negative','Negative']] as [$key,$label])
            <div class="col-6 col-lg-3"><div class="news-stat"><strong>{{ number_format($statistics[$key]) }}</strong><span class="small text-secondary fw-bold">{{ $label }}</span></div></div>
        @endforeach
    </section>
    <form class="news-filter row g-2 mb-4" method="GET">
        <div class="col-lg-5"><input class="form-control" name="search" value="{{ request('search') }}" placeholder="{{ app()->getLocale() === 'id' ? 'Cari judul, ringkasan, atau sumber...' : 'Search title, summary, or source...' }}"></div>
        <div class="col-md-4 col-lg-3"><select class="form-select" name="country_id"><option value="">{{ app()->getLocale() === 'id' ? 'Semua negara' : 'All countries' }}</option>@foreach($countries as $country)<option value="{{ $country->id }}" @selected((string)request('country_id') === (string)$country->id)>{{ $country->name }}</option>@endforeach</select></div>
        <div class="col-md-4 col-lg-2"><select class="form-select" name="sentiment"><option value="">All sentiment</option>@foreach(['Positive','Neutral','Negative'] as $sentiment)<option value="{{ $sentiment }}" @selected(request('sentiment') === $sentiment)>{{ $sentiment }}</option>@endforeach</select></div>
        <div class="col-md-4 col-lg-2"><button class="btn btn-primary w-100" type="submit">{{ app()->getLocale() === 'id' ? 'Terapkan' : 'Apply' }}</button></div>
    </form>
    <section class="row g-3">
        @forelse($newsItems as $news)
            <div class="col-md-6 col-xl-4"><article class="news-card">
                @if($news->image_url)<img class="news-image" src="{{ $news->image_url }}" alt="" loading="lazy">@else<div class="news-placeholder">SUPPLYGUARD NEWS</div>@endif
                <div class="news-content">
                    <div class="d-flex justify-content-between gap-2 mb-2"><span class="sentiment sentiment-{{ strtolower($news->sentiment) }}">{{ $news->sentiment }}</span><small class="text-muted">{{ $news->published_at?->format('d M Y') ?? $news->published_date?->format('d M Y') ?? '-' }}</small></div>
                    <h2 class="fw-bold">{{ $news->title }}</h2><p class="news-summary">{{ $news->summary ?: (app()->getLocale() === 'id' ? 'Ringkasan belum tersedia.' : 'Summary is not available.') }}</p>
                    <div class="d-flex justify-content-between align-items-center gap-2"><small class="text-muted">{{ $news->country?->name ?? 'Global' }} · {{ $news->source_name ?? 'External source' }}</small><a href="{{ route('news.show',$news) }}" class="btn btn-sm btn-outline-primary">Detail</a></div>
                </div>
            </article></div>
        @empty<div class="col-12"><div class="alert alert-info">{{ app()->getLocale() === 'id' ? 'Berita tidak ditemukan.' : 'No news was found.' }}</div></div>@endforelse
    </section>
    @if($newsItems->hasPages())<nav class="news-pagination mt-4"><a class="btn btn-outline-primary {{ $newsItems->onFirstPage() ? 'disabled' : '' }}" href="{{ $newsItems->previousPageUrl() ?: '#' }}">Previous</a><span class="small fw-bold">Page {{ $newsItems->currentPage() }} / {{ $newsItems->lastPage() }}</span><a class="btn btn-outline-primary {{ $newsItems->hasMorePages() ? '' : 'disabled' }}" href="{{ $newsItems->nextPageUrl() ?: '#' }}">Next</a></nav>@endif
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
