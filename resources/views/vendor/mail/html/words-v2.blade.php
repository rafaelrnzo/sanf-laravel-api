<div class="content-block">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <p class="outro-message">
                    @if(count($textWithUrl) !== 0)
                        @foreach($textWithUrl as $key => $value)
                            {!! htmlspecialchars_decode($textWithUrl[$key][0]) !!}
                            <a href="{{ $textWithUrl[$key][1][1] }}">{{ $textWithUrl[$key][1][0]}}</a>
                        @endforeach
                    @endif
                </p>
            </td>
        </tr>
    </table>
</div>
