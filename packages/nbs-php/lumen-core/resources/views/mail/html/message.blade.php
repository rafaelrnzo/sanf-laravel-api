@component('mail::layout')
    {{--START HEADER--}}
    @slot('header')
        @component('mail::header', ['url' => config('mail.url')])
            {{ $logo }}
        @endcomponent
    @endslot
    {{--END HEADER--}}

    {{--START BANNER--}}
    @isset($banner)
        @slot('banner')
            @component('mail::banner')
                {{ $banner }}
            @endcomponent
        @endslot
    @endisset
    {{--END BANNER--}}

    {{--START BODY--}}
    <table role="presentation" class="main">
        <tr>
            <td class="wrapper">
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
                        @foreach ($introLines as $line)<p>{!! $line !!}</p>@endforeach
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
                        @foreach ($outroLines as $line)<p>{!! $line !!}</p>@endforeach
                    @endcomponent
                @endisset
                {{--END OUTRO MESSAGE--}}
            </td>
        </tr>
    </table>
    {{--END BODY--}}

    {{--START FOOTER--}}
    @slot('footer')
        @component('mail::footer', ['url' => config('mail.url'), 'social_account' => config('mail.social_account', [])])
        @endcomponent
    @endslot
    {{--END FOOTER--}}
@endcomponent
