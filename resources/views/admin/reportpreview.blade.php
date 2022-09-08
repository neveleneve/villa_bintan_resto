<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Report Pemasukan Restoran</title>
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
    <div class="row">
        <div class="col-12">
            <h2 class="text-center">Laporan Pemasukan Restoran</h2>
            <h4 class="text-center">Bulan {{ $bulan }} Tahun {{ $tahun }}</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>ID Reservasi</th>
                        <th>Nama</th>
                        <th>Tanggal Pesan</th>
                        <th>Sub Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total = 0;
                    @endphp
                    @forelse ($data as $item)
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $item->reservation_code }}</td>
                            <td>{{ $item->nama_pemesan }}</td>
                            <td>{{ date('d-m-Y H:i', strtotime($item->time)) }}</td>
                            <td class="text-right">
                                Rp. {{ number_format($item->totalmenu + $item->totalfee, 0, ',', '.') }}
                                @php
                                    $total += $item->totalmenu + $item->totalfee;
                                @endphp
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <h3 class="text-center">Data Kosong</h3>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if (count($data) > 0)
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-right font-weight-bold">Total</td>
                            <td class="text-right"> Rp. {{ number_format($total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</body>

</html>
