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
                        @if (Auth::user()->role == 0)
                            <div class="col-lg-4">
                                <div class="card text-center">
                                    <div class="card-header bg-default text-white font-weight-bold">
                                        Pemesanan Hari Ini
                                    </div>
                                    <div class="card-body">
                                        {{ $reservationtoday }} Reservation(s)
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card text-center">
                                    <div class="card-header bg-default text-white font-weight-bold">
                                        Persiapan Pemesanan Hari Ini
                                    </div>
                                    <div class="card-body">
                                        {{ $bookingtoday }} Preparation(s)
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card text-center">
                                    <div class="card-header bg-default text-white font-weight-bold">
                                        Reservasi Selesai
                                    </div>
                                    <div class="card-body">
                                        {{ $completedreservation }} Reservation(s)
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-lg-4">
                                <div class="card text-center">
                                    <div class="card-header bg-default text-white font-weight-bold">
                                        Jumlah Transaksi Reservasi Kamu
                                    </div>
                                    <div class="card-body">
                                        {{ $completedreservation }}
                                        Reservation(s)
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
