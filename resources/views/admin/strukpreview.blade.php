<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Struk Pemesanan {{ $id }}</title>
    <link rel="stylesheet" href="{{ asset('argon/assets/css/argon.css?v=1.2.0') }}" type="text/css">
</head>

<body>
    <div class="row">
        <div class="col-12">
            <table>
                <tbody>
                    <tr>
                        <td><strong>Nama</strong></td>
                        <td><strong>:</strong></td>
                        <td>{{ $datareservasi[0]->pemesan }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Reservasi</strong></td>
                        <td><strong>:</strong></td>
                        <td>{{ date('d-m-Y H:i', strtotime($datareservasi[0]->reservationtime)) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
