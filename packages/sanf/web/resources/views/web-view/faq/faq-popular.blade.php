@extends('web::web-view.faq.master')

@section('title', 'Semua Pertanyaan')

@section('content')
    <div class="header">
        <div class="welcome-text">Selamat Datang</div>
        <div class="question-text mb-2">Anda Mengalami Kendala?</div>
        <form action="" method="get" class="d-block w-100">
            <div class="input-group">
                <input type="text" name="keyword" class="form-control"
                       aria-describedby="inputGroupSearch"
                       placeholder="Masukkan pertanyaan atau kategori" value="{{ $keyword }}">
                <button class="btn btn-search" type="submit" id="inputGroupSearch">
                    <img src="{{ asset('assets/faq/search-icon.svg') }}" alt="">
                </button>
            </div>
        </form>
    </div>

    @if(count($faqs) === 0)
        <div class="container pt-5 text-center">
            <img src="{{ asset('assets/faq/not-found-icon.svg') }}" alt="">
            <p class="mt-4">
                Pertanyaan dan Kategori yang Anda cari tidak ditemukan.
                Silakan ganti kata pencarian Anda.
            </p>
        </div>
    @else
        <div class="container">
            @if(!$keyword)
                <h1 class="container-title">Pertanyaan</h1>
            @endif
            <div class="accordion" id="accordionFaq">
                @foreach($faqs as $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeading{{$faq->id}}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faqCollapse{{$faq->id}}" aria-expanded="false"
                                    aria-controls="flush-collapseOne">
                                {{ $faq->title }}
                            </button>
                        </h2>
                        <div id="faqCollapse{{$faq->id}}" class="accordion-collapse collapse"
                             aria-labelledby="faqHeading{{$faq->id}}" data-bs-parent="#accordionFaq">
                            <div class="divider-wrapper">
                                <div class="divider"></div>
                            </div>
                            <div class="accordion-body">{{ $faq->description }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if(isset($personalAssistant) and isset($personalAssistant->msisdn))
                <div class="card mt-4" style="border-radius: 1rem; overflow: hidden">
                    <div class="card-body p-0">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <img src="{{ asset('assets/faq/bottom-logo.svg') }}" alt="">
                            </div>
                            <div class="flex-grow-1 ms-3 text-center">
                                <p class="card-text mb-2">Anda menemui kendala?</p>
                                @php $msisdn = (string)preg_replace('/^0/', '+62', $personalAssistant->msisdn) @endphp
                                <a href='{{"https://wa.me/{$msisdn}"}}' class="btn btn-custom py-2 px-4">Hub Admin SANF</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
@endsection
