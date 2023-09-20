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


<div class="content-header">
    <div class="container-fluid rounded shadow bg-success">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-3 text-dark">Supplier</h1>
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