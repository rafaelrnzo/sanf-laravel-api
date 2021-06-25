<div class="footer">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td class="social-account">
                @foreach($social_account as $account)
                    <a href="{{ $account['link'] }}"><img src="{{ $account['icon'] }}" width="30"
                                                          alt="{{ $account['name'] }}"/></a>
            @endforeach
            <td class="copyright">&copy; {{ date('Y') }} Surya Artha Nusantara Finance</td>
        </tr>
    </table>
</div>
