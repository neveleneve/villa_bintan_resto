@extends('layouts.main')

@section('title')
    Report | Administrator
@endsection

@section('content')
    @include('layouts.include.adminnav')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-default">
                    <h1 class="text-center text-white font-weight-bold">Report</h1>
                </div>
                <form action="{{ route('adminreportpost') }}" method="post" target="__blank">
                    @csrf
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="bulan" class="font-weight-bold">Bulan</label>
                                <select name="bulan" id="bulan" class="form-control" required>
                                    <option selected disabled hidden value="">Pilih Bulan</option>
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="tahun" class="font-weight-bold">Tahun</label>
                                <select name="tahun" id="tahun" class="form-control" required>
                                    <option selected disabled hidden value="">Pilih Tahun</option>
                                    @for ($i = 0; $i < 5; $i++)
                                        <option value="{{ date('Y') - $i }}">{{ date('Y') - $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <button class="btn btn-default btn-block">Preview</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
