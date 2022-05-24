@extends('layouts.base')

@section('content')
    <x-navbar />
    <div class="container p-5">
        <div class="row">
            <div class="col">
                <h1 class="text-uppercase">{{ $post->title }}</h1>
                <b>{{ $post->user->name }}</b> - <b>{{ $post->user->userInfo->phone }}</b>
                <div>
                    <b>{{ $post->category->name }}</b>
                </div>
                <p class="mt-3">{{ $post->content }}</p>
            </div>
        </div>
    </div>
@endsection