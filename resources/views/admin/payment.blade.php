@extends('layouts.main')

@section('title')
Payments | Administrator
@endsection

@section('content')
@include('layouts.include.adminnav')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-default">
                <h1 class="text-center text-white font-weight-bold">Payments</h1>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-4">
                        <input class="form-control" type="text" name="cari" id="cari" placeholder="Search...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="bg-default text-white">
                            <tr>
                                <th>No</th>
                                <th>Reservation Code</th>
                                <th>Order Id</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @forelse($payments as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>
                                        <a class="text-dark" href="{{ route('adminreservationdetail', ['id' => $item->reservation_code]) }}">
                                            <u>
                                                {{ $item->reservation_code }}
                                            </u>
                                        </a>
                                    </td>
                                    <td>
                                        <a class="text-dark" href="{{ route('paymentstatus', ['id' => $item->order_id]) }}">
                                            <u>
                                                {{ $item->order_id }}
                                            </u>
                                        </a>
                                    </td>
                                    <td class="text-dark">
                                        {{ ucfirst( $item->transaction_status) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <h4>Payment Data is Empty</h4>
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
