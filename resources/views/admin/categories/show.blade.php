@extends('layouts.base')

@section('content')
<div class="container pt-5">
    <h1 class="text-uppercase">{{$category->name}}</h1>
    <p>{{$category->description}}</p>
</div>
@endsection