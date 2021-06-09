@foreach($social_account as $account)
{{ $account['name'] . ': ' . $account['link'] }}
@endforeach

{{ strtoupper(config('app.name')) }} . copyright {{ date('Y') }}
