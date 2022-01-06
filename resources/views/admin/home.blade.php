@extends('layouts.main')

@section('title')
    Dashboard | Administrator
@endsection

@section('content')
    @include('layouts.include.adminnav')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-default">
                    <h1 class="text-center text-white font-weight-bold">Dashboard</h1>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-header bg-primary text-white font-weight-bold">Reservation In Progress</div>
                                <div class="card-body">
                                    You are logged in!
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-header bg-primary text-white font-weight-bold">Completed Reservation</div>
                                <div class="card-body">
                                    You are logged in!
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-header bg-primary text-white font-weight-bold">Canceled Reservation</div>
                                <div class="card-body">
                                    You are logged in!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
