@extends('layouts.main')

@section('title')
    Choose Your Menu
@endsection

@section('custstyle')
    <style>
        .inline-group {
            max-width: 8rem;
        }

        .inline-group .form-control {
            text-align: center;
        }

        .form-control[type="number"]::-webkit-inner-spin-button,
        .form-control[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <h1 class="display-2 text-default text-center">Choose Your Menu</h1>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (Session::has('alert'))
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-sm alert-default alert-dismissible fade show" role="alert">
                                    <span class="alert-icon"><i class="fa fa-exclamation-circle"></i></span>
                                    <span class="alert-text">{{ session('alert') }}</span>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                    <form action="{{ route('reservemenu') }}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $id }}">
                        <div class="row mb-3">
                            <div class="col-12 mb-1">
                                <button class="btn btn-default btn-block">
                                    Book Now!
                                </button>
                            </div>
                            <div class="col-12">
                                <strong>*Choose one of the menus so you won't be charged a table reservation fee
                                </strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-items-center">
                                        <thead class="bg-default text-white">
                                            <tr>
                                                <th>No</th>
                                                <th>Name</th>
                                                <th>Description</th>
                                                <th>Category</th>
                                                <th>Price</th>
                                                <th>Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = 1;
                                                $numberdata = 0;
                                            @endphp
                                            @forelse ($data as $item)
                                                <tr>
                                                    <td>{{ $no++ }}</td>
                                                    <td>
                                                        <div class="media align-items-center">
                                                            <a class="avatar rounded-circle mr-3">
                                                                @if (File::exists(public_path('images/menu/' . $item->id . '.jpg')))
                                                                    <img
                                                                        src="{{ asset('images/menu/' . $item->id . '.jpg') }}">
                                                                @else
                                                                    <img src="{{ asset('images/default.jpg') }}">
                                                                @endif
                                                            </a>
                                                            <div class="media-body">
                                                                <span
                                                                    class="name mb-0 text-sm">{{ ucwords(strtolower($item->name)) }}</span>
                                                            </div>
                                                        </div>

                                                    </td>
                                                    <td>{{ ucfirst(strtolower($item->description)) }}</td>
                                                    <td>{{ ucfirst(strtolower($item->category_name)) }}</td>
                                                    <td>IDR {{ number_format($item->price, 0, ',', '.') }}</td>
                                                    <td>
                                                        <div class="input-group inline-group">
                                                            <div class="input-group-prepend">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-default"
                                                                    onclick="kurang('{{ $item->id }}')">
                                                                    <i class="fa fa-xs fa-minus"></i>
                                                                </button>
                                                            </div>
                                                            <input type="hidden" name="menu_id[]"
                                                                value="{{ $item->id }}">
                                                            <input class="form-control form-control-sm" min="0"
                                                                id="quantity{{ $item->id }}" name="quantity[]"
                                                                value="0" type="number">
                                                            <div class="input-group-append">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-default"
                                                                    onclick="tambah('{{ $item->id }}')">
                                                                    <i class="fa fa-xs fa-plus"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6">
                                                        <h1 class="text-center">Data Menu Kosong</h1>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
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
        function kurang(id) {
            var jumlah = parseInt($('#quantity' + id).val());
            $('#quantity' + id).val(jumlah - 1);
        }

        function tambah(id) {
            var jumlah = parseInt($('#quantity' + id).val());
            $('#quantity' + id).val(jumlah + 1);
        }
    </script>
@endsection
