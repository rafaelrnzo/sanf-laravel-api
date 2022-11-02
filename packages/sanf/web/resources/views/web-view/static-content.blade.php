<html>
<title>{{ $content->heading }}</title>
<body>
<img src="{{ $content->imageHeader }}" alt="{{ $content->heading }}">
<h1>{{ $content->heading }}</h1>
<h4>{{ $content->date }}</h4>
<h3>{{ $content->subHeading }}</h3>
<section>{!! $content->body !!}</section>
<img src="{{ $content->imageFooter }}" alt="{{ $content->heading }}">
</body>
</html>