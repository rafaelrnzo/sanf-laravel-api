@component('mail::layout')
    {{--START HEADER--}}
    @slot('header')
        @component('mail::header', ['url' => config('mail.url')])
        @endcomponent
    @endslot
    {{--END HEADER--}}

    {{--START BODY--}}

    {{--START GREETING--}}
    @isset($greeting)
        @component('mail::greeting')
            {!! $greeting !!}
        @endcomponent
    @endisset
    {{--END GREETING--}}

    {{--START INTRO MESSAGE--}}
    @isset($introLines)
        @component('mail::words')
            @foreach ($introLines as $line)
                {!! $line !!}
            @endforeach
        @endcomponent
    @endisset
    {{--END INTRO MESSAGE--}}

    {{--START MESSAGE--}}
    {{ $slot ?? '' }}
    {{--END MESSAGE--}}

    {{--START ACTION--}}
    @isset($actionText)
        @component('mail::button', ['url' => $actionUrl, 'helps' => $actionHelp])
            {{ $actionText }}
        @endcomponent
    @endisset
    {{--END ACTION--}}

    {{--START OUTRO MESSAGE--}}
    @isset($outroLines)
        @component('mail::words')
            @foreach ($outroLines as $line)
                {!! $line !!}
            @endforeach
        @endcomponent
    @endisset
    {{--END OUTRO MESSAGE--}}

    {{--END BODY--}}

    {{--START FOOTER--}}
    @slot('footer')
        @component('mail::footer', ['url' => config('mail.url'), 'social_account' => config('mail.social_account', [])])
        @endcomponent
    @endslot
    {{--END FOOTER--}}
@endcomponent
