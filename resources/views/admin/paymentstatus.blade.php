@extends('layouts.main')

@section('title')
Payment Detail and Status | Administrator
@endsection

@section('content')
@include('layouts.include.adminnav')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-default">
                <h1 class="text-center text-white font-weight-bold">Payment Detail and Status</h1>
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
                @if($payment_data['status_code'] == 200)
                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="font-weight-bold">Order ID</label>
                        </div>
                        <div class="col-1">
                            <label>:</label>
                        </div>
                        <div class="col-3">
                            <label>{{ $payment_data['order_id'] }}</label>
                        </div>
                        <div class="col-2">
                            <label class="font-weight-bold">Transaction Status</label>
                        </div>
                        <div class="col-1">
                            <label>:</label>
                        </div>
                        <div class="col-3">
                            <label>{{ ucfirst($payment_data['transaction_status']) }}</label>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="font-weight-bold">Payment Method</label>
                        </div>
                        <div class="col-1">
                            <label>:</label>
                        </div>
                        <div class="col-3">
                            <label>{{ ucwords(str_replace('_', ' ', $payment_data['payment_type'])) }}</label>
                        </div>
                        <div class="col-2">
                            <label class="font-weight-bold">Payment Media</label>
                        </div>
                        <div class="col-1">
                            <label>:</label>
                        </div>
                        <div class="col-3">
                            <label>{{ strtoupper($payment_data['va_numbers'][0]['bank']) }}</label>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-2">
                            <label class="font-weight-bold">Amount</label>
                        </div>
                        <div class="col-1">
                            <label>:</label>
                        </div>
                        <div class="col-3">
                            <label>{{ number_format($payment_data['payment_amounts'][0]['amount'],0,',', '.') }} <strong>IDR</strong></label>
                        </div>
                        <div class="col-2">
                            <label class="font-weight-bold">Payment Date</label>
                        </div>
                        <div class="col-1">
                            <label>:</label>
                        </div>
                        <div class="col-3">
                            <label>{{ date('d M Y, H:i:s', strtotime($payment_data['payment_amounts'][0]['paid_at'])) }}</label>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-12">
                            @if($payment_data['status_code'] == 201)
                                <h4 class="text-center">
                                    Payment Pending
                                </h4>
                            @elseif($payment_data['status_code'] == 404)
                                <h4 class="text-center">
                                    Payment not Initiate.
                                </h4>
                            @elseif($payment_data['status_code'] == 407)
                                <h4 class="text-center">
                                    Payment Expired
                                </h4>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
