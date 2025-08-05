@extends('layouts.main')

@section('content')
    @foreach($translations as $translation)
        <textarea>{{$translation}}</textarea>
    @endforeach
@endsection
