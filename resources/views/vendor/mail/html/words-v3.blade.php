<div class="content-block">
    <table role="presentation" class="another-email-content" border="0" cellpadding="0"
        cellspacing="0" width="100%">
    @foreach($content as $value)
        <tr style="margin-bottom: 3em;">
            @if(isset($value['separator']))
                <td align="left" colspan="2" width="100%">{!! htmlspecialchars_decode($value['separator']) !!}</td>
            @else
                <td align="left" width="50%" class="label-content">{!! htmlspecialchars_decode($value['label']) !!}</td>
                <td align="right" class="text-content">{!! htmlspecialchars_decode($value['text']) !!}</td>
            @endif
        </tr>
    @endforeach
    </table>
</div>
