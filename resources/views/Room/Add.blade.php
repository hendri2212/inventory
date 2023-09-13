@extends('index')

@section('konten')
    <form action="{{url('room')}}" method="POST">
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name">
        <br>
        <label for="company_id">Nama Company :</label>
        <select name="company_id" id="">
            <option value=""></option>
            @foreach ($company as $company)
                <option value="{{$company['id']}}">{{$company['name']}}</option>
            @endforeach
        </select>
        <br>
        <label for="good_id">Nama barang :</label>
        <select name="good_id" id="">
            <option value=""></option>
            @foreach ($good as $good)
                <option value="{{$good['id']}}">{{$good['name']}}</option>
            @endforeach
        </select>
        <br>
        <label for="supplier_id">Nama Supplier :</label>
        <select name="supplier_id" id="">
            <option value=""></option>
            @foreach ($supplier as $supplier)
                <option value="{{$supplier['id']}}">{{$supplier['name']}}</option>
            @endforeach
        </select>
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection


<!-- NAVBAR -->
@section('tittle')

<div class="content-header">
    <div class="container-fluid">
    	<div class="row mb-2">
          	<div class="col-sm-6">
            	<h1 class="m-0"></h1>
          	</div><!-- /.col -->
          	<div class="col-sm-6">
            	<ol class="breadcrumb float-sm-right">
              		<li class="breadcrumb-item"><a href="/beranda">Home</a></li>
              		<li class="breadcrumb-item active">Dashboard v1</li>
            	</ol>
          	</div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
@endsection