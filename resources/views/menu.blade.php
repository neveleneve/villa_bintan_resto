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
                    <th>Category</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @php
                $no = 1;
                @endphp
                @forelse ($data as $item)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>
                        <div class="media align-items-center">
                            <a class="avatar rounded-circle mr-3">
                                @if(File::exists(public_path('images/menu/' . $item->id . '.jpg')))
                                <img src="{{ asset('images/menu/' . $item->id . '.jpg') }}">
                                @else
                                <img src="{{ asset('images/default.jpg') }}">
                                @endif
                            </a>
                            <div class="media-body">
                                <span class="name mb-0 text-sm">{{ ucwords(strtolower($item->name)) }}</span>
                            </div>
                        </div>

                    </td>
                    <td>{{ ucfirst(strtolower($item->description)) }}</td>
                    <td>{{ ucfirst(strtolower($item->category_name)) }}</td>
                    <td>IDR {{ number_format($item->price, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <h1 class="text-center">Data Menu Kosong</h1>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
