@extends("mobi.include.mother")

@section("content")

    {!! htmlspecialchars_decode($article->body) !!}
@endsection
