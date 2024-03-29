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
                            <h1 class="h1 fw-bold">Tabel Meja Pesanan</h1>
                            <table class="table table-bordered table-hover align-items-center text-center mb-3">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor Meja</th>
                                        <th>Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($reservation_data as $item)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $item->no_meja }}</td>
                                            <td>{{ $item->kapasitas }} Orang</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3">
                                                <h5 class="font-weight-bold">Table Reservation is Empty</h5>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <h1 class="h1 fw-bold">Tabel Menu Pesanan</h1>
                            <table class="table table-bordered table-hover align-items-center text-center">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Qty</th>
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
                                @if (count($reservation_menu_data) != null)
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-right font-weight-bold"><strong>Total</strong>
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
                        <div class="col-4" style="border: 1px solid">
                            <h2><u><strong>Payment Details</strong></u></h2>
                            <div class="row">
                                <div class="col-6">
                                    <p>Menu</p>
                                </div>
                                <div class="col-6">
                                    <p class="text-right">
                                        <strong class="font-weight-bold">Rp. </strong>
                                        {{ number_format($total, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <p>Table Reservation Fee</p>
                                </div>
                                <div class="col-6" style="border-bottom: 1px solid">
                                    <p class="text-right">
                                        <strong class="font-weight-bold">Rp. </strong>
                                        {{ number_format($reservation_fee_data[0]['fee'], 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                            <div class="row pt-2">
                                <div class="col-6 ">
                                    <p>Total</p>
                                </div>
                                <div class="col-6">
                                    <p class="text-right">
                                        <strong class="font-weight-bold">Rp.</strong>
                                        {{ number_format($total + $reservation_fee_data[0]['fee'], 0, ',', '.') }}
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
                                        file_put_contents('images/barcode/' . $id . '.png', $generator->getBarcode($id, $generator::TYPE_CODE_39, 3, 50, [0, 0, 0]));
                                    }
                                    if (!File::exists(public_path('images/scan_barcode/' . $id . '.png'))) {
                                        $src = imagecreatefrompng(asset('images/barcode/' . $id . '.png'));
                                        $dest = imagecreatefrompng(asset('images/scan_here.png'));
                                        imagecopymerge($dest, $src, 4, 50, 0, 0, 870, 50, 100); //have to play with these numbers for it to work for you, etc.
                                        imagepng($dest, 'images/scan_barcode/' . $id . '.png');
                                        imagedestroy($dest);
                                        imagedestroy($src);
                                    }
                                @endphp
                                <img class="img-thumbnail" src="{{ asset('images/scan_barcode/' . $id . '.png') }}"
                                    alt="barcode">
                            </div>
                            <div class="col-12 text-center mb-3">
                                <a onclick="return confirm('Download barcode pemesanan?')"
                                    href="{{ route('downloadbarcode', ['id' => $id]) }}"
                                    class="btn btn-sm btn-primary">Download</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custjs')
    <script>
        $(document).ready(function() {
            setInterval(function() {
                $.ajax({
                    type: 'GET',
                    url: '{{ route('reservationcheck') }}',
                    data: {
                        'id': '{{ $id }}'
                    },
                    success: function(data) {
                        if (data.berubah == true) {
                            location.reload();
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                    },
                });
                console.log('halo');
            }, 1000);
        });
    </script>
@endsection
