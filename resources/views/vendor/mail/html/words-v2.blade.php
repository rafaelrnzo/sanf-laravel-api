<table role="presentation" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <p class="outro-message">
                @if(count($textWithUrl) !== 0)
                    {!! htmlspecialchars_decode($textWithUrl[0]) !!} <a href="{{ $reportLink }}">{{ $textWithUrl[1] }}</a>
                @endif
            </p>
        </td>
    </tr>
</table>
