<table role="presentation" border="0" cellpadding="0" cellspacing="0">
    @isset($helps['top'])
        <tr>
            <td>@foreach ($helps['top'] as $help)
                    <span class="action-help-top">{!! $help !!}</span>
                @endforeach</td>
        </tr>
    @endisset
    <tr>
        <td height="15"></td>
    </tr>
    <tr>
        <td align="center"><a class="action-button" href="{{ $url }}" target="_blank"
                              rel="noopener noreferrer">{!! $slot !!}</a></td>
    </tr>
    <tr>
        <td height="15"></td>
    </tr>
    @isset($helps['bottom'])
        <tr>
            <td>
                @foreach ($helps['bottom'] as $help)
                    <span class="action-help-bottom">{!! $help !!}</span>
                @endforeach
            </td>
        </tr>
    @endisset
</table>
