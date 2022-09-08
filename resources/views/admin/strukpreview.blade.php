<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Struk Pemesanan {{ $id }}</title>
    <link rel="stylesheet" href="{{ asset('argon/assets/css/argon.css?v=1.2.0') }}" type="text/css">
    <style>
        /**
        * Define the width, height, margins and position of the watermark.
        **/
        #watermark {
            position: fixed;

            bottom: 6cm;
            left: 6.5cm;

            /** Change image dimensions**/
            width: 15cm;
            height: 8.5cm;

            /** Your watermark should be behind every content**/
            z-index: -1000;
            opacity: 0.2;
        }
    </style>
</head>

<body style="background-color: white">
    <div id="watermark">
        <img src="{{ asset('argon/assets/img/brand/brand-dark.png') }}" height="100%" width="100%" />
    </div>
    <div class="row mb-0">
        <div class="col-12">
            <table class="table">
                <tbody>
                    <tr>
                        <td>
                            <h3 class="h2 text-center">BIIE VILLA RESTAURANT LOBAM</h3>
                            {{-- <h3 class="h2 text-center">Restaurant</h3> --}}
                            <h3 class="h4 text-center">Bintan Inti Industrial Estate, Lobam, Pulau Bintan 29152
                                Kepulauan Riau</h3>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <hr class="mt-0">
    <div class="row mb-3">
        <div class="col-12">
            <table class="h6">
                <tbody>
                    <tr class="mb-3">
                        <td><strong>Nama</strong></td>
                        <td><strong>:</strong></td>
                        <td>{{ $datareservasi[0]->pemesan }}</td>
                    </tr>
                    <tr>
                        <td><strong>Kode Reservasi</strong></td>
                        <td><strong>:</strong></td>
                        <td>{{ $datareservasi[0]->codereservation }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Reservasi</strong></td>
                        <td><strong>:</strong></td>
                        <td>{{ date('d-m-Y H:i', strtotime($datareservasi[0]->reservationtime)) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Fee Pemesanan</strong></td>
                        <td><strong>:</strong></td>
                        <td>Rp.
                            @if (count($datafee) > 0)
                                {{ number_format($datafee[0]['fee'], 0, ',', '.') }}
                            @else
                                {{ number_format(0, 0, ',', '.') }}
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12">
            <table class="table table-bordered text-center h6">
                <thead class="table-dark">
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
                    @forelse($datamenu as $item)
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
                @if (count($datamenu) != null)
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
</body>

</html>
