@extends('layouts.main')

@section('title')
Menus | Administrator
@endsection

@section('content')
@include('layouts.include.adminnav')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-default">
                <h1 class="text-center text-white font-weight-bold">Menus</h1>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-4">
                        <input class="form-control" type="text" name="cari" id="cari" placeholder="Search..." oninput="search(this.value)">
                    </div>
                    <div class="col-8">
                        <button class="btn btn-outline-default btn-block" type="button" data-toggle="modal" data-target="#modalTambahMenu">
                            Add Menu
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        All Menu : {{ count($menu) }}
                    </div>
                    <div class="col-4">
                        Menu Available : {{ $menuavail }}
                    </div>
                    <div class="col-4">
                        Menu Not Available : {{ $menunot }}
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="bg-default text-white">
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tablemenu">
                            @forelse($menu as $item)
                            <tr>
                                <td class="align-middle">
                                    <div class="media align-items-center">
                                        <a class="avatar mr-3">
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
                                <td class="align-middle">{{ ucfirst(strtolower($item->description)) }}</td>
                                <td class="align-middle">{{ ucfirst(strtolower($item->category_name)) }}</td>
                                <td class="align-middle">{{ number_format($item->price, 0, ',', '.') }} <strong>IDR</strong></td>
                                <td class="align-middle">
                                    @if($item->deleted_at == null)
                                    <i class="fas fa-check" title="Available"></i>
                                    @else
                                    <i class="fas fa-times" title="Not Available"></i>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <input type="hidden" name="id_menu" value="{{ $item->id }}">
                                    <button type="button" class="btn btn-sm btn-outline-default" type="button" onclick="getMenuData({{ $item->id }})" data-toggle="modal" data-target="#modalViewMenu">View</button>
                                    @if($item->deleted_at == null)
                                    <a href="{{ route('adminmenudelete', ['id' => $item->id]) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deactivate this menu?')">Deactive</a>
                                    @else
                                    <a href="{{ route('adminmenurestore', ['id' => $item->id]) }}" class="btn btn-sm btn-outline-success" onclick="return confirm('Activate this menu?')">Activate</a>
                                    @endif
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
<div class="modal fade" id="modalViewMenu" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-secondary modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title" id="exampleModalLabel">View Menu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-dark">&times;</span>
                </button>
            </div>
            <form action="{{ route('adminmenuedit') }}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="viewid" id="viewid" value="">
                <div class="modal-body">
                    <label for="viewname" class="font-weight-bold h5">
                        Name
                    </label>
                    <input type="text" class="form-control mb-3" id="viewname" name="viewname" required placeholder="Menu Name">
                    <label for="viewdesc" class="font-weight-bold h5">
                        Description
                    </label>
                    <textarea class="form-control mb-3" id="viewdesc" name="viewdesc" required placeholder="Menu Description"></textarea>
                    <label for="viewcat" class="font-weight-bold h5">
                        Category
                    </label>
                    <select name="viewcat" id="viewcat" class="form-control mb-3" required>
                        @if(count($cat) > 0)
                        <option value="" selected disabled hidden>Select Menu Category</option>
                        @else
                        <option value="" selected disabled>Empty Category</option>
                        @endif
                        @foreach($cat as $item)
                        <option value="{{ $item->id }}">{{ ucfirst($item->name) }}</option>
                        @endforeach
                    </select>
                    <label for="viewprice" class="font-weight-bold h5">
                        Price
                    </label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <strong>
                                    IDR
                                </strong>
                            </span>
                        </div>
                        <input type="number" class="form-control" id="viewprice" name="viewprice" required placeholder="Menu Price">
                    </div>
                    <label for="image" class="font-weight-bold h5">
                        Image
                    </label>
                    <div class="custom-file mb-3">
                        <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                        <label class="custom-file-label" for="image">Select Image</label>
                    </div>
                    <div class="text-center">
                        <label for="imagenow" class="font-weight-bold h5">Image</label>
                        <br>
                        <img id="imagenow" class="img-fluid img-thumbnail" style="width: 25%">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-danger" data-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-sm btn-outline-default">
                        Edit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalTambahMenu" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-secondary modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title h4" id="exampleModalLabel">Add Menu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-dark">&times;</span>
                </button>
            </div>
            <form action="{{ route('adminmenuadd') }}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-body">
                    <label for="name" class="font-weight-bold h5">
                        Name
                    </label>
                    <input type="text" class="form-control mb-3" id="name" name="name" required placeholder="Menu Name">
                    <label for="desc" class="font-weight-bold h5">
                        Description
                    </label>
                    <textarea class="form-control mb-3" id="desc" name="desc" required placeholder="Menu Description"></textarea>
                    <label for="cat" class="font-weight-bold h5">
                        Category
                    </label>
                    <select name="cat" id="cat" class="form-control mb-3" required>
                        @if(count($cat) > 0)
                        <option value="" selected disabled hidden>Select Menu Category</option>
                        @else
                        <option value="" selected disabled>Empty Category</option>
                        @endif
                        @foreach($cat as $item)
                        <option value="{{ $item->id }}">{{ ucfirst($item->name) }}</option>
                        @endforeach
                    </select>
                    <label for="price" class="font-weight-bold h5">
                        Price
                    </label>
                    <input type="number" class="form-control mb-3" id="price" name="price" required placeholder="Menu Price">
                    <label for="image" class="font-weight-bold h5">
                        Image
                    </label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                        <label class="custom-file-label" for="image">Select Image</label>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-sm btn-outline-danger" data-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-sm btn-outline-default">
                        Add
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('custjs')
<script>
    function getMenuData(id) {
        $.ajax({
            type: 'GET',
            url: '{{ route("adminmenuview") }}',
            data: {
                'id': id
            },
            success: function (data) {
                $('#viewid').val(data.id);
                $('#viewname').val(data.name);
                $('#viewdesc').val(data.desc);
                $('#viewcat').val(data.cat);
                $('#viewprice').val(data.price);
                // make a function to check if image is exist
                $("#imagenow").attr("src", data.src);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
        });
    }

    function search(key) {
        var tabelbody = $('#tablemenu');
        // var key = $('#cari').val();
        $.ajax({
            type: 'GET',
            url: '{{ route("adminmenusearch") }}',
            data: {
                'key': key
            },
            success: function (data) {
                tabelbody.empty();
                tabelbody.html(data);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
        });
    }

</script>
@endsection
