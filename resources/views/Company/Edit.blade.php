@extends('index')

@section('konten')
    <form action="{{url('company', ['id'=>$company->id])}}" method="POST">
        @method('put')
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name" value="{{$company->name}}">
        <br>
        <label for="address">Alamat :</label>
        <input type="text" name="address" value="{{$company->address}}">
        <br>
        <label for="telephone">Telepon :</label>
        <input type="number" name="telephone" value="{{$company->telephone}}">
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection


<!-- NAVBAR -->
@section('tittle')

<div class="content-header">
    <div id="Head" class="container-fluid rounded shadow bg-success">
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