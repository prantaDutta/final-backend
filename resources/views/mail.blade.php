<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Grayscale Email</title>
    <style>
        .title {
            font-size: 2rem;
            color: cyan;
            padding: 2rem 3rem;
        }
    </style>
</head>
<body>
<section>
    <div>
        <h1 class="title">{{$data['title']}}</h1>
        <p>Hello, {{$data['name']}}</p>
        <p>{{$data['message']}}</p>
        @if($data['action'])
            <a href="{{ url($data['action']->url) }}">{{$data['action']->name}}</a>
        @endif
{{--        @if($data['from'])--}}
{{--            From, <p>{{$data['from']}}</p>--}}
{{--        @endif--}}
    </div>
</section>
</body>
</html>
