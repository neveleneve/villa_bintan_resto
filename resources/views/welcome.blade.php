@extends('layouts.main')

@section('title')
    Home
@endsection

@section('content')
    <div class="jumbotron rounded-0" style="background-image: url('{{ asset('images/home_bg.jpg') }}'); height: 90vh;">
        <h1 class="h1 font-weight-bold text-white text-center">VILLA BINTAN RESTO</h1>
        <h3 class="h2 font-weight-bold text-white text-center">Making People Happy</h3>
        <div class="row justify-content-center">
            <div class="col-8">
                <hr class="bg-white">
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-6">
                <p class="lead text-white text-center">
                    Deliciousness jumping into the mouth
                </p>
            </div>
        </div>
    </div>
@endsection
