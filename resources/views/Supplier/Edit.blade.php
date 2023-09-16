@extends('index')

@section('konten')
    <form action="{{url('supplier', ['id'=>$supplier->id])}}" method="POST">
        @method('put')
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name" value="{{$supplier->name}}">
        <br>
        <label for="company_id">Company :</label>
        <select name="company_id" id="" value="{{$supplier->name}}">
            <option value="{{$supplier->company_id}}">{{$supplier->company->name}}</option>
            @foreach ($company as $company)
                <option value="{{$company['id']}}">{{$company['name']}}</option>
            @endforeach
        </select>
        <br>
        <label for="image">Gambar :</label>
        <input type="text" name="image" value="{{$supplier->image}}">
        <br>
        <label for="telephone">Telepon :</label>
        <input type="number" name="telephone" value="{{$supplier->telephone}}">
        <br>
        <label for="address">Alamat :</label>
        <input type="text" name="address" value="{{$supplier->address}}">
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