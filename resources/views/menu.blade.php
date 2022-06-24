@extends('layouts.main')

@section('title')
    Menu
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <h1 class="display-2 text-default text-center">Menu</h1>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-12 table-responsive">
            <table class="table table-bg table-hover">
                <thead class="bg-default text-white">
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($data) == 0)
                        <tr class="bg-secondary  text-center">
                            <td colspan="4">
                                <h1 class="font-weight-bold h2 text-default">
                                    <strong>
                                        Data Menu Kosong
                                    </strong>
                                </h1>
                            </td>
                        </tr>
                    @else
                        @foreach ($cat as $cate)
                            <tr class="bg-secondary  text-center">
                                <td colspan="4">
                                    <h1 class="font-weight-bold h2 text-default">
                                        <strong>
                                            {{ ucwords($cate->name) }}
                                        </strong>
                                    </h1>
                                </td>
                            </tr>
                            @php
                                $no = 1;
                            @endphp
                            @foreach ($data as $item)
                                @php
                                    $i = 0;
                                @endphp
                                @if ($cate->name == $item->category_name)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>
                                            <div class="media align-items-center">
                                                <a class="avatar rounded-circle mr-3">
                                                    @if (File::exists(public_path('images/menu/' . $item->id . '.jpg')))
                                                        <img src="{{ asset('images/menu/' . $item->id . '.jpg') }}">
                                                    @else
                                                        <img src="{{ asset('images/default.jpg') }}">
                                                    @endif
                                                </a>
                                                <div class="media-body">
                                                    <span
                                                        class="name mb-0 text-sm">{{ ucwords(strtolower($item->name)) }}</span>
                                                </div>
                                            </div>

                                        </td>
                                        <td>{{ ucfirst(strtolower($item->description)) }}</td>
                                        <td>IDR {{ number_format($item->price, 0, ',', '.') }}</td>
                                        @php
                                            $i++;
                                        @endphp
                                    </tr>
                                @endif
                            @endforeach
                            @if ($i == 0)
                                <tr>
                                    <td colspan="4">
                                        <h2 class="font-weight-bold text-center">
                                            Data Menu Kosong
                                        </h2>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
