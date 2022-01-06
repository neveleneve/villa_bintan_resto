@extends('layouts.main')

@section('title')
    Reservation
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <h1 class="display-2 text-default text-center">PAYMENT STATUS</h1>
        </div>
    </div>
    <hr>
    <div class="row mb-3">
        <div class="col-3">
            <a class="btn btn-outline-default" href="{{ route('reservationdetail', ['id' => $id]) }}">
                Back to Payment Page
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-2">
                            <label class="font-weight-bold" for="nama">Name</label>
                        </div>
                        <div class="col-1">
                            :
                        </div>
                        <div class="col-3">
                            {{ $reservation[0]['nama_pemesan'] }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2">
                            <label class="font-weight-bold">Reservation ID</label>
                        </div>
                        <div class="col-1">
                            :
                        </div>
                        <div class="col-3">
                            {{ $id }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2">
                            <label class="font-weight-bold">Table ID</label>
                        </div>
                        <div class="col-1">
                            :
                        </div>
                        <div class="col-3">
                            {{ $reservation[0]['table_id'] }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2">
                            <label class="font-weight-bold">Date & Time</label>
                        </div>
                        <div class="col-1">
                            :
                        </div>
                        <div class="col-3">
                            {{ date('d/m/Y H:i', strtotime($reservation[0]['time'])) }}
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="row">
                        <div class="col-8">
                            <h2><u><strong>Menu </strong></u></h2>
                            <table class="table table-bordered table-hover align-items-center text-center">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Quantity</th>
                                        <th class="text-right">Price</th>
                                        <th class="text-right">Sub Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total = 0;
                                    @endphp
                                    @forelse($reservationmenu as $item)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->jumlah }}</td>
                                            <td class="text-right">
                                                {{ number_format($item->harga, 0, ',', '.') }}
                                                <strong>IDR</strong>
                                            </td>
                                            <td class="text-right">
                                                {{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}
                                                <strong>IDR</strong>
                                            </td>
                                            @php
                                                $total += $item->harga * $item->jumlah;
                                            @endphp
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5">
                                                <h5 class="font-weight-bold">Menu Reservation Is Empty</h5>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if (count($reservationmenu) != null)
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-right font-weight-bold"><strong>Total</strong>
                                            </td>
                                            <td class="text-right">
                                                {{ number_format($total, 0, ',', '.') }}
                                                <strong>IDR</strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                        <div class="col-4">
                            <h2><u><strong>Payment Details</strong></u></h2>
                            <div class="row">
                                <div class="col-6">
                                    <p>Menu</p>
                                </div>
                                <div class="col-6">
                                    <p class="text-right">
                                        {{ number_format($total, 0, ',', '.') }}
                                        <strong class="font-weight-bold">IDR</strong>
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <p>Table Reservation Fee</p>
                                </div>
                                <div class="col-6" style="border-bottom: 1px solid">
                                    <p class="text-right">
                                        {{ number_format($reservationfee[0]['fee'], 0, ',', '.') }}
                                        <strong class="font-weight-bold">IDR</strong>
                                    </p>
                                </div>
                            </div>
                            <div class="row pt-2">
                                <div class="col-6 ">
                                    <p>Total</p>
                                </div>
                                <div class="col-6">
                                    <p class="text-right">
                                        {{ number_format($total + $reservationfee[0]['fee'], 0, ',', '.') }}
                                        <strong class="font-weight-bold">IDR</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="row mt-3">
                        <div class="col-12">
                            <h2><u><strong>Payment History</strong></u></h2>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-items-center text-center">
                                    @php
                                        $no = 1;
                                    @endphp
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Order ID</th>
                                            <th>Payment Date</th>
                                            <th>Transaction Status Code</th>
                                            <th>Transaction Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($payment as $item)
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td>{{ $item->order_id }}</td>
                                                <td>{{ date('d/m/Y', strtotime($item->created_at)) }}</td>
                                                <td>{{ $item->status_code }}</td>
                                                <td>{{ $item->transaction_status }}</td>
                                                <td>
                                                    @if ($item->status_code == 404)
                                                        @if (date('Y-m-d H:i:s') > date('Y-m-d H:i:s', strtotime($reservation[0]['time'] . '- 2 hours')))
                                                            <h6>
                                                                Your reservation date is expired. Please set a new
                                                                reservation on <a href="{{ route('reservation') }}">this
                                                                    page</a>
                                                            </h6>
                                                        @else
                                                            <a class="btn btn-sm btn-outline-default"
                                                                href="{{ $item->url }}" target="__blank">Pay Now!</a>
                                                        @endif
                                                    @elseif($item->status_code == 200)

                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6">
                                                    <h5 class="font-weight-bold">Payments History is Empty</h5>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
