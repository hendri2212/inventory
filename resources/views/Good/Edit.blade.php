@extends('index')

@section('konten')
    <form action="{{url('good', ['id'=>$good->id])}}" method="POST">
        @method('put')
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name" value="{{$good->name}}">
        <br>
        <select name="category_id" id="" value="{{$good->name}}">
            <option value="{{$good->category_id}}">{{$good->category->name}}</option>
            @foreach ($category as $category)
                <option value="{{$category['id']}}">{{$category['name']}}</option>
            @endforeach
        </select>
        <input type="submit" value="Simpan">
    </form>
@endsection

@section('tittle')

    <!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
    	<div class="row mb-2">
          	<div class="col-sm-6">
            	<h1 class="m-0">Edit Barang</h1>
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
