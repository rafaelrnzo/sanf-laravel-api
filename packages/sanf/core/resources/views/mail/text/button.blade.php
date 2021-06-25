@isset($helps['top'])
    @foreach ($helps['top'] as $help) {!! $help !!} @endforeach
@endisset

{{ $slot }}: {{ $url }}

@isset($helps['bottom'])
    @foreach ($helps['bottom'] as $help) {!! $help !!}
    @endforeach
@endisset
