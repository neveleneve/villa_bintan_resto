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
                    @if (Auth::user()->role == 0)
                        <div class="row mb-3">
                            <div class="col-4">
                                <input class="form-control" type="text" name="cari" id="cari"
                                    placeholder="Search reservation code or order ID" oninput="search(this.value)">
                            </div>
                            <div class="col-4"></div>
                            <div class="col-4">
                                <input type="button" value="Refresh Page" onClick="confirmrefresh()"
                                    class="btn btn-outline-default btn-block">
                            </div>
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead class="bg-default text-white">
                                <tr>
                                    <th>Reservation Code</th>
                                    <th>Order Id</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="tablemenu">
                                @forelse($payments as $item)
                                    <tr>
                                        <td>
                                            <a class="text-dark"
                                                href="{{ route('adminreservationdetail', ['id' => $item->reservation_code]) }}">
                                                <u>
                                                    {{ $item->reservation_code }}
                                                </u>
                                            </a>
                                        </td>
                                        <td>
                                            <a class="text-dark"
                                                href="{{ route('paymentstatus', ['id' => $item->order_id]) }}">
                                                <u>
                                                    {{ $item->order_id }}
                                                </u>
                                            </a>
                                        </td>
                                        <td class="text-dark">
                                            {{ ucfirst($item->transaction_status) }}
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
                    {{-- <div class="row mt-3">
                    <div class="col-12">
                        {{ $payments->onEachSide(1)->links('layouts.bootstrap-4') }}
                    </div>
                </div> --}}
                </div>
            </div>
        </div>
    </div>
@endsection
@if (Auth::user()->role == 0)
    @section('custjs')
        <script>
            function search(key) {
                var tabelbody = $('#tablemenu');
                $.ajax({
                    type: 'GET',
                    url: '{{ route('adminpaymentssearch') }}',
                    data: {
                        'key': key,
                        'role': '{{ Auth::user()->role }}',
                        'id': '{{ Auth::user()->id }}',
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

            function confirmrefresh() {
                if (confirm('Refresh page?')) {
                    document.location.reload(true);
                }
            }
        </script>
    @endsection
@endif
