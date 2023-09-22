<!-- DATA -->
@extends('index')

@section('konten')
<!-- <body> -->
    
<table border="2" align="center" id="example" class="table table-bordered table-success table-hover " style="width:97%" height="100px">
        <tr align="center" class="table-active">
            <th>ID.</th>
            <th>Nama</th>
            <th>Category</th>
            <th>Aksi</th>
        </tr>
        
        @foreach ($data as $good)
        <tr>
            <td align="center">{{$good['id']}}</td>
            <td>{{$good['name']}}</td>
            <td>{{$good['category']['name']}}</td>
            <form action="{{ url('good', ['id'=>$good->id]) }}" method="POST">
                @method('delete')
                @csrf
                <td align="center"><button><a href="{{url('good/' . $good->id . '/edit')}}">Edit</a></button> | 
                <button onclick="return confirm('Apakah anda yakin?')">delete</button></td>
            </form>
        </tr>
        @endforeach
    </table>
	<div class="a">
    <a href="{{ url('good/add') }}">tambahkan data</a>
	</div>
@endsection



<!-- NAVBAR -->

@section('tittle')

    <!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid mb-3 rounded shadow bg-success">
    	<div class="row mb-2">
          	<div class="col-sm-6">
            	<h1 class="m-3 text-dark">Data Barang</h1>
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