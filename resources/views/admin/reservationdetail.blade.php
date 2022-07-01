@extends('layouts.main')

@section('title')
    Reservation Detail | Administrator
@endsection

@section('content')
    @include('layouts.include.adminnav')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-default">
                    <h1 class="text-center text-white font-weight-bold">Reservation Detail</h1>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-4">
                            <button onclick="history.back()" class="btn btn-outline-default btn-block">
                                Back
                            </button>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-2">
                                    <label class="font-weight-bold">Name</label>
                                </div>
                                <div class="col-1">
                                    <label>:</label>
                                </div>
                                <div class="col-3">
                                    <label>{{ $datareservasi[0]->pemesan }}</label>
                                </div>
                                <div class="col-2">
                                    <label class="font-weight-bold">Contact</label>
                                </div>
                                <div class="col-1">
                                    <label>:</label>
                                </div>
                                <div class="col-3">
                                    <label>{{ $datareservasi[0]->kontak }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-2">
                                    <label class="font-weight-bold">Reservation Date</label>
                                </div>
                                <div class="col-1">
                                    <label>:</label>
                                </div>
                                <div class="col-3">
                                    <label>{{ date('d/m/Y H:i', strtotime($datareservasi[0]->reservationtime)) }}</label>
                                </div>
                                <div class="col-2">
                                    <label class="font-weight-bold">Reservation Fee</label>
                                </div>
                                <div class="col-1">
                                    <label>:</label>
                                </div>
                                <div class="col-3">
                                    <label>
                                        <strong>Rp. </strong>
                                        @if (count($datafee) > 0)
                                            {{ number_format($datafee[0]['fee'], 0, ',', '.') }}
                                        @else
                                            {{ number_format(0, 0, ',', '.') }}
                                        @endif
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-2">
                                    <label class="font-weight-bold">Table Number</label>
                                </div>
                                <div class="col-1">
                                    <label>:</label>
                                </div>
                                <div class="col-3">
                                    <label>{{ $datareservasi[0]->nomeja }}</label>
                                </div>
                                <div class="col-2">
                                    <label class="font-weight-bold">Status</label>
                                </div>
                                <div class="col-1">
                                    <label>:</label>
                                </div>
                                <div class="col-3">
                                    @if ($datareservasi[0]->reservasistatus == 0)
                                        <label>
                                            Menu Not Reserved Yet {{-- keterangan 1 --}}
                                        </label>
                                        <br>
                                    @elseif($datareservasi[0]->reservasistatus == 1)
                                        <label>
                                            Menu Reserved {{-- keterangan 1 --}}
                                        </label>
                                        <br>
                                        @if ($datareservasi[0]->jumlahpembayaran == 0)
                                            <label>
                                                Payments not initiated {{-- keterangan 2 --}}
                                            </label>
                                            <br>
                                        @elseif($datareservasi[0]->jumlahpembayaran > 0)
                                            @if ($datareservasi[0]->status_code == 200)
                                                <label>
                                                    Payment succeed {{-- keterangan 2 --}}
                                                </label>
                                                <br>
                                            @elseif($datareservasi[0]->status_code == 404)
                                                <label>
                                                    Payment process is pending {{-- keterangan 2 --}}
                                                </label>
                                                <br>
                                            @elseif($datareservasi[0]->status_code == 407)
                                                <label>
                                                    Payment is expired {{-- keterangan 2 --}}
                                                </label>
                                                <br>
                                            @endif
                                        @endif
                                    @endif
                                    @if (($datareservasi[0]->status_code != 200 || $datareservasi[0]->jumlahpembayaran == 0) && date('Y-m-dH:i:s') > date('Y-m-d H:i:s', strtotime($datareservasi[0]->reservationtime . '- 2 hours')))
                                        <label>
                                            Expired reservation
                                        </label>
                                        <br>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center">
                                    <thead>
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
                                            $no = 1;
                                        @endphp
                                        @forelse($pesanan as $item)
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->jumlah }}</td>
                                                <td class="text-right">
                                                    <strong>Rp. </strong>
                                                    {{ number_format($item->harga, 0, ',', '.') }}
                                                </td>
                                                <td class="text-right">
                                                    <strong>Rp. </strong>
                                                    {{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}
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
                                    @if (count($pesanan) != null)
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" class="text-right font-weight-bold">
                                                    <strong>Total</strong>
                                                </td>
                                                <td class="text-right">
                                                    <strong>Rp. </strong>
                                                    {{ number_format($total, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
