@extends('layouts.main')

@section('title')
    Categories | Administrator
@endsection

@section('content')
    @include('layouts.include.adminnav')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-default">
                    <h1 class="text-center text-white font-weight-bold">Categories</h1>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        {{-- <div class="col-4">
                            <input class="form-control" type="text" name="cari" id="cari" placeholder="Search...">
                        </div> --}}
                        <div class="col-12">
                            <button class="btn btn-outline-default btn-block" type="button" data-toggle="modal"
                                data-target="#exampleModal1">
                                Add Category
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead class="bg-default text-white">
                                <tr>
                                    <th>Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($category as $item)
                                    <tr>
                                        <td class="align-middle">{{ ucfirst(strtolower($item->name)) }}</td>
                                        <td class="align-middle">
                                            <button class="btn btn-sm btn-outline-default" type="button"
                                                data-toggle="modal" data-target="#exampleModal"
                                                onclick="getData({{ $item->id }})">Edit</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <h1 class="text-center">Data Menu Kosong</h1>
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
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admineditcategories') }}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="idkategori" id="idkategori" value="">
                    <div class="modal-body">
                        <label for="namakategori" class="font-weight-bold">Nama Kategori</label>
                        <input type="text" class="form-control form-control-sm" name="namakategori" id="namakategori"
                            required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-danger" data-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-sm btn-outline-default">
                            Ubah Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('adminaddcategories') }}" method="post">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <label for="nama" class="font-weight-bold">Nama Kategori</label>
                        <input type="text" class="form-control form-control-sm" name="nama" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-danger" data-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-sm btn-outline-default">
                            Tambah Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('custjs')
    <script>
        function getData(id) {
            $.ajax({
                type: 'GET',
                url: '{{ route('getcategorydata') }}',
                data: {
                    'id': id,
                },
                success: function(datax) {
                    $('#namakategori').val(datax.nama);
                    $('#idkategori').val(datax.id);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                },
            })
        }
    </script>
@endsection
