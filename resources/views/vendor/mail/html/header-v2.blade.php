<div class="header">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="left" width="50%"><a href="#">@isset($logo[0]) <img
                src="{!! $logo[0] !!}" alt="SANF Logo"/> @else {{ config('app.name') }}@endisset</a></td>
            <td align="right" width="50%"><a href="#">@isset($logo[1]) <img
                src="{!! $logo[1] !!}" alt="SANF Tagline"/> @else {{ config('app.name') }}@endisset</a></td>
        </tr>
    </table>
</div>
