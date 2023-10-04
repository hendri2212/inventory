@extends('index')

@section('konten')
   
<form class="ml-4" action="{{url('good')}}" method="POST">
    @csrf
    <label for="name" class="form-label">Nama</label>
    <input type="text" name="name" style="width: 50%;" class="form-control" aria-describedby="passwordHelpBlock">
    <div id="passwordHelpBlock" class="form-text pb-3">
        Isi Nama Barang
    </div>

    <div class="mb-3">
      <label for="category_id" class="form-label">Kategori</label>

      <select id="select1" name="category_id" style="width: 50%;" class="form-select">
        <option value=""></option>
        @foreach ($data as $category)
            <option value="{{$category['id']}}">{{$category['name']}}</option>
        @endforeach
      </select>
    </div>
    <input type="submit" class=" mt-2 btn btn-success" value="Simpan">
</form>

    <!-- <form action="{{url('good')}}" method="POST">
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name">
        <br>
        <label for="category_id">Kategory :</label>
        <select name="category_id" id="">
            <option value=""></option>
        @foreach ($data as $category)
            <option value="{{$category['id']}}">{{$category['name']}}</option>
        @endforeach
        </select>
        <input type="submit" value="Simpan">
    </form> -->
@endsection

<!-- NAVBAR -->
@section('tittle')

    <!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid mb-3 rounded shadow bg-success">
    	<div class="row mb-2">
          	<div class="col-sm-6">
            	<h1 class="m-3 text-dark">Tambah Barang</h1>
          	</div><!-- /.col -->
          	<div class="col-sm-6 d-flex align-items-center justify-content-end">
              <ol class="breadcrumb float-sm-right d-flex align-items-stretch">
              		<li class="breadcrumb-item"><a style="text-decoration:none" href="#">Home</a></li>
              		<li class="breadcrumb-item text-dark">Dashboard v1</li>
           	 	</ol>
          	</div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->

@endsection