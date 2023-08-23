<!-- DATA -->
@extends('index')

@section('konten')
<body>
    
<table align="center" id="example" class="table table-striped" style="width:97%" height="100px">
        <tr>
            <th>ID.</th>
            <th>Nama</th>
            <th>ID Category</th>
            <th>Aksi</th>
        </tr>
        @foreach ($data as $goods)
        <tr>
            <td>{{$goods['id']}}</td>
            <td>{{$goods['name']}}</td>
            <td>{{$goods['category_id']}}</td>
            <form action="{{url('goods', ['id'=>$goods->id])}}" method="POST">
                @method('delete')
                @csrf
                <td><button onclick="return confirm('Apakah anda yakin?')">delete</button></td>
            </form>
        </tr>
        @endforeach
    </table>
    <a href="{{ url('goods/add') }}">tambahkan data</a>
@endsection



<!-- NAVBAR -->
@extends('index')

@section('tittle')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">DATA BARANG</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard v1</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

@endsection