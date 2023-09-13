@extends('index')

@section('konten')
<table border="1" align="center" id="example" class="table table-striped" style="width:97%" height="100px">
        <tr>
            <th>ID.</th>
            <th>Nama</th>
            <th>Nama Company</th>
            <th>Gambar</th>
            <th>Telepon</th>
            <th>Alamat</th>
            <th>Aksi</th>
        </tr>
        @foreach ($data as $supplier)
        <tr>
            <td align="center">{{$supplier['id']}}</td>
            <td>{{$supplier['name']}}</td>
            <td>{{$supplier['company']['name']}}</td>
            <td>{{$supplier['image']}}</td>
            <td>{{$supplier['telephone']}}</td>
            <td>{{$supplier['address']}}</td>
            <form action="{{url('supplier', ['id'=>$supplier->id])}}" method="POST">
                @method('delete')
                @csrf
                <td align="center"><button><a href="{{url('supplier/' . $supplier->id . '/edit')}}">Edit</a></button> | 
                <button onclick="return confirm('Apakah anda yakin?')">delete</button></td>
            </form>
        </tr>
        @endforeach
    </table>
	<div class="a">
    <a href="{{ url('supplier/add') }}">tambahkan data</a>
	</div>
@endsection




<!-- NAVBAR -->

@section('tittle')


    <!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
          	<div class="col-sm-6">
            	<h1 class="m-0">Supplier</h1>
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