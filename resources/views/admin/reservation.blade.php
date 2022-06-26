@extends('layouts.main')

@section('title')
    Reservations | Administrator
@endsection

@section('content')
    @include('layouts.include.adminnav')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-default">
                    <h1 class="text-center text-white font-weight-bold">Reservations</h1>
                </div>
                <div class="card-body">
                    {{-- <div class="row mb-3">
                        <div class="col-4">
                            <input class="form-control" type="text" name="cari" id="cari" placeholder="Search...">
                        </div>
                    </div> --}}
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead class="bg-default text-white">
                                <tr>
                                    <th>No.</th>
                                    <th>Reservation Code</th>
                                    <th>Reservation Date</th>
                                    <th>Table Number</th>
                                    <th>Order By</th>
                                    <th>Reservation Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tablemenu">
                                @forelse($datareservasi as $item)
                                    <tr>
                                        <td class="align-middle">{{ $no++ }}</td>
                                        <td class="align-middle">{{ $item->codereservation }}</td>
                                        <td class="align-middle">
                                            {{ date('d/m/Y H:i', strtotime($item->reservationtime)) }}
                                        </td>
                                        <td class="align-middle">{{ $item->nomeja }}</td>
                                        <td class="align-middle">{{ $item->pemesan }}</td>
                                        <td class="text-left align-middle">
                                            <ul class="list-group">
                                                @if ($item->reservasistatus == 0)
                                                    <li
                                                        class="list-group-item d-flex justify-content-between align-items-center">
                                                        Table Reserved. Menu Not Reserved Yet {{-- keterangan 1 --}}
                                                    </li>
                                                @elseif($item->reservasistatus == 1)
                                                    <li
                                                        class="list-group-item d-flex justify-content-between align-items-center">
                                                        Table and Menu Reserved {{-- keterangan 1 --}}
                                                    </li>
                                                    @if ($item->jumlahpembayaran == 0)
                                                        <li
                                                            class="list-group-item d-flex justify-content-between align-items-center">
                                                            Payments not initiated {{-- keterangan 2 --}}
                                                        </li>
                                                    @elseif($item->jumlahpembayaran > 0)
                                                        @if ($item->status_code == 200)
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center">
                                                                Payment succeed {{-- keterangan 2 --}}
                                                            </li>
                                                        @elseif($item->status_code == 404)
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center">
                                                                Payment process is pending {{-- keterangan 2 --}}
                                                            </li>
                                                        @elseif($item->status_code == 407)
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center">
                                                                Payment is expired {{-- keterangan 2 --}}
                                                            </li>
                                                        @endif
                                                    @endif
                                                @endif
                                                @if ((($item->status_code != 200 || $item->jumlahpembayaran == 0) && date('Y-m-d H:i:s') > date('Y-m-d H:i:s', strtotime($item->reservationtime . '- 2 hours'))) || $item->bookingstatus == 2)
                                                    <li
                                                        class="list-group-item d-flex justify-content-between align-items-center">
                                                        Expired reservation {{-- keterangan 3 --}}
                                                    </li>
                                                @elseif($item->bookingstatus == 1)
                                                    <li
                                                        class="list-group-item d-flex justify-content-between align-items-center">
                                                        Transaction Done {{-- keterangan 3 --}}
                                                    </li>
                                                @endif
                                            </ul>
                                        </td>
                                        <td class="align-middle">
                                            <a class="btn btn-sm btn-outline-default"
                                                href="{{ route('adminreservationdetail', ['id' => $item->codereservation]) }}">Detail</a>
                                            @if ($item->bookingstatus == 0)
                                                <a class="btn btn-sm btn-outline-success"
                                                    onclick="return confirm('Tandai reservasi telah selesai?')"
                                                    href="{{ route('bookedin', ['id' => $item->codereservation]) }}">Booked
                                                    In</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <h4>Reservation Data is Empty</h4>
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
@endsection

@section('custjs')
    <script>
        function search(key) {
            var tabelbody = $('#tablemenu');
            $.ajax({
                type: 'GET',
                url: '{{ route('adminreservationssearch') }}',
                data: {
                    'key': key
                },
                success: function(data) {
                    tabelbody.empty();
                    tabelbody.html(data);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                },
            });
        }
    </script>
@endsection
