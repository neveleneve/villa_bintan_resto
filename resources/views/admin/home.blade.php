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
                                <div class="card-header bg-default text-white font-weight-bold">Today Reservation</div>
                                <div class="card-body">
                                    {{ $reservationtoday }} Reservation(s)
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-header bg-default text-white font-weight-bold">Today's Booking Preparation
                                </div>
                                <div class="card-body">
                                    {{ $bookingtoday }} Preparation(s)
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-header bg-default text-white font-weight-bold">Completed Reservation</div>
                                <div class="card-body">
                                    {{ $completedreservation }} Reservation(s)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
