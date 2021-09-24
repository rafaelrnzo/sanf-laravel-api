<table role="presentation" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td height="15"></td>
    </tr>
    <tr>
        @isset($approvalContent)
            @foreach($approvalContent as $ac)
                <td align="center"><a class="action-button" href="{{ $ac[1] }}" target="_blank">{!! $ac[0] !!}</a></td>
            @endforeach
        @endisset
    </tr>
</table>
