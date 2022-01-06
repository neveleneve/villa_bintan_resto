@extends('layouts.main')

@section('title')
    Reservation
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <h1 class="display-2 text-default text-center">Reservation</h1>
        </div>
    </div>
    <hr>
    @if (Session::has('alert'))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-default alert-dismissible fade show" role="alert">
                    <span class="alert-icon"><i class="fa fa-exclamation-circle"></i></span>
                    <span class="alert-text">{{ session('alert') }}</span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('reserve') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="nama" class="font-weight-bold">Full Name</label>
                                <input class="form-control" type="text" name="nama" id="nama"
                                    value="{{ Session::has('nama') ? session('nama') : null }}"
                                    placeholder="Insert Your Full Name Here..." required>
                            </div>
                            <div class="col-6">
                                <label for="kontak" class="font-weight-bold">Contact</label>
                                <input class="form-control" type="text" name="kontak" id="kontak"
                                    value="{{ Session::has('kontak') ? session('kontak') : null }}"
                                    placeholder="Insert Your Contact Here..." required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-3">
                                <label for="tanggal" class="font-weight-bold">Date</label>
                                <input class="form-control" type="date" name="tanggal" id="tanggal"
                                    value="{{ Session::has('tanggal') ? session('tanggal') : date('Y-m-d', strtotime(date(now()) . '+1 days')) }}"
                                    placeholder="Select date..." min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-3">
                                <label for="waktu" class="font-weight-bold">Time</label>
                                <input class="form-control" type="time" name="waktu" id="waktu"
                                    placeholder="Select time..."
                                    value="{{ Session::has('waktu') ? session('waktu') : '13:00' }}" min="13:00"
                                    max="22:30" step="1800">
                            </div>
                            <div class="col-3">
                                <label for="seat" class="font-weight-bold">Capacity</label>
                                <select class="form-control" id="seat" name="seat">
                                    @if (Session::has('seat'))
                                        <option value="1" {{ session('seat') == 1 ? 'selected' : null }}>1 Person</option>
                                        <option value="2" {{ session('seat') == 2 ? 'selected' : null }}>2 People</option>
                                        <option value="3" {{ session('seat') == 3 ? 'selected' : null }}>3 People
                                        </option>
                                        <option value="4" {{ session('seat') == 4 ? 'selected' : null }}>4 People
                                        </option>
                                        <option value="5" {{ session('seat') == 5 ? 'selected' : null }}>5 People
                                        </option>
                                        <option value="6" {{ session('seat') == 6 ? 'selected' : null }}>More than 5
                                            People
                                        </option>
                                    @else
                                        <option value="1">1 Person</option>
                                        <option value="2">2 People</option>
                                        <option value="3">3 People</option>
                                        <option value="4">4 People</option>
                                        <option value="5">5 People</option>
                                        <option value="6">More than 5 People</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-3">
                                <label for="">&nbsp;</label>
                                <button type="button" class="btn btn-outline-default btn-block" onclick="checkSeat()">Check
                                    Seat</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <button type="submit" class="btn btn-default btn-block" onclick="return confirm('Book Now?')">Book
                                    Now!</button>
                            </div>
                        </div>
                        <div class="row mt-5" id="loading" style="display: none">
                            <div class="col text-center">
                                <i id="spinner" style="display: none" class="fa fa-spinner fa-spin fa-4x"></i>
                            </div>
                        </div>
                        <div class="row mt-5" id="tabel" style="display: none">
                            <div class="col">
                                <table class="table table-hover table-bordered table-dark text-center" id="tabelmeja">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Table Number</th>
                                            <th>Capacity</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabelbody">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custjs')
    <script>
        $('#date').bind('load', function() {
            $(this).datepicker();
        });
        var loading = $('#loading');
        var spinner = $('#spinner');
        var tabel = $('#tabel');
        var tabelmeja = $('#tabelmeja');
        var tabelbody = $('#tabelbody');

        function checkSeat() {
            var tanggal = $('#tanggal').val();
            var jam = $('#waktu').val();
            var jumlah = $('#seat').val();
            tabel.hide();
            tabel.animate({
                height: tabelmeja.height()
            }, 200);
            loading.show();
            loading.animate({
                height: spinner.height()
            }, 200);
            spinner.animate({
                opacity: "show"
            }, 800);
            spinner.animate({
                opacity: "hide"
            }, 1000);
            $.ajax({
                type: 'GET',
                url: '{{ route('tableCheck') }}',
                data: {
                    'tanggal': tanggal,
                    'jam': jam,
                    'jumlah': jumlah
                },
                success: function(datax) {
                    tabelbody.empty();
                    tabelbody.html(datax);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                },
            });
            setTimeout(function() {
                loading.animate({
                    height: '0'
                }, 200);
                spinner.hide();
                loading.hide();
                tabel.show();
                tabel.animate({
                    height: tabelmeja.height()
                }, 500);
            }, 2000);
        }
    </script>
@endsection