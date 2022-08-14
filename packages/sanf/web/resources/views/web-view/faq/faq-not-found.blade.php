@extends('web::web-view.faq.master')

@section('content')
    <div class="container mt-5 text-center">
        <img src="{{ asset('assets/faq/not-found-icon.svg') }}" alt="">
        <p class="mt-4">
            Pertanyaan dan Kategori yang Anda cari tidak ditemukan.
            Klik <a href="{{ route('web-view.faq') }}">disini</a> untuk kembali ke halaman utama.
        </p>
    </div>
@endsection
