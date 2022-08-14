@extends('web::web-view.faq.master')

@section('title', 'Pertanyaan Popular')

@section('content')
    <div class="container mt-4">
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
                        <div class="accordion-body">{{ $faq->description }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card mt-4" style="border-radius: 1rem; overflow: hidden">
            <div class="card-body p-0">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <img src="{{ asset('assets/faq/bottom-logo.svg') }}" alt="">
                    </div>
                    <div class="flex-grow-1 ms-3 text-center">
                        <p class="card-text mb-2">Anda menemui kendala?</p>
                        <a href="#" class="btn btn-custom py-2 px-4">Hub Admin SANF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
