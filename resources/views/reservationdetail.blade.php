@extends('layouts.main')

@section('title')
    Reservation
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <h1 class="display-2 text-default text-center">Reservation Details</h1>
        </div>
    </div>
    <hr>
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
                            {{ $reservation_data[0]['nama_pemesan'] }}
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
                            {{ $reservation_data[0]['table_id'] }}
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
                            {{ date('d/m/Y H:i', strtotime($reservation_data[0]['time'])) }}
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="row">
                        <div class="col-8">
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
                                    @forelse($reservation_menu_data as $item)
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
                                @if (count($reservation_menu_data) != null)
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
                        <div class="col-4" style="border: 1px solid">
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
                                        {{ number_format($reservation_fee_data[0]['fee'], 0, ',', '.') }}
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
                                        {{ number_format($total + $reservation_fee_data[0]['fee'], 0, ',', '.') }}
                                        <strong class="font-weight-bold">IDR</strong>
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 pt-3" style="border-top: 1px solid">
                                    @if ($status_pembayaran['status_code'] == 404)
                                        @if (date('Y-m-d H:i:s') > date('Y-m-d H:i:s', strtotime($reservation_data[0]['time'] . '- 2 hours')))
                                            <h5>
                                                @if (count($datapembayaran) > 0)
                                                    @if ($datapembayaran[0]['status_code'] == 200 && $datapembayaran[0]['transaction_status'] == 'settlement')
                                                        Your payment is done. You can check the payment
                                                        details
                                                        <a href="{{ route(' paymentsstatus', ['id' => $id]) }}">here</a>
                                                    @else
                                                        Payment process cannot be continued. Payment
                                                        processing is allowed no
                                                        later than 2 hours before the reservation time!
                                                        (Your Reservation Time :
                                                        {{ date('d/m/Y H:i', strtotime($reservation_data[0]['time'])) }}
                                                        Indonesian Eastern Time). Make a new reservation on reservation
                                                        page <a href="{{ route('reservation') }}">here</a>.
                                                    @endif
                                                @else
                                                    Payment process cannot be continued. Payment processing
                                                    is allowed no
                                                    later than 2 hours before the reservation time!
                                                    (Your Reservation Time :
                                                    {{ date('d/m/Y H:i', strtotime($reservation_data[0]['time'])) }}
                                                    Indonesian Eastern Time). Make a new reservation on reservation page <a
                                                        href="{{ route('reservation') }}">here</a>.
                                                @endif
                                            </h5>
                                        @else
                                            <a href="{{ $payments_url }}" target="__blank"
                                                class="btn btn-outline-default btn-block">
                                                Pay Now!
                                            </a>
                                            <small>* Payment expiration is 1 hour</small>
                                            <br>
                                            <small>** Make the payment 2 hours before reservation time</small>
                                            <br>
                                            <small>*** If you miss the payment after 2 hours before reservation time, you
                                                should make a new reservation</small>
                                        @endif
                                    @else
                                        <a href="{{ url('/payments/finish?order_id=' . $random) }}"
                                            class="btn btn-outline-default btn-block mb-3">
                                            Lihat Detail Pembayaran
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($jumlahpembayaran > 0)
                        <hr>
                        <div class="row justify-content-center">
                            <div class="col-12 mb-3 text-center">
                                Save barcode for easy access to payment status and transction's proof
                            </div>
                            <div class="col-12 text-center mb-3">
                                @php
                                    $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
                                    if (!File::exists(public_path('images/barcode/' . $id . '.png'))) {
                                        file_put_contents('images/barcode/' . $id . '.png', $generator->getBarcode($id, $generator::TYPE_CODE_128, 3, 50, [0, 0, 0]));
                                    }
                                @endphp
                                <img class="img-thumbnail" src="{{ asset('images/barcode/' . $id . '.png') }}"
                                    alt="barcode">
                            </div>
                            <div class="col-12 text-center mb-3">
                                <button class="btn btn-sm btn-primary">Download</button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
