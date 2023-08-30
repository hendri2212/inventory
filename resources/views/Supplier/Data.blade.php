@extends('index')

@section('konten')
<table border="1" cellspacing="0">
        <tr>
            <th>ID.</th>
            <th>Nama</th>
            <th>Owner</th>
            <th>Telepon</th>
            <th>Alamat</th>
            <th>Aksi</th>
        </tr>
        @foreach ($data as $supplier)
        <tr>
            <td align="center">{{$supplier['id']}}</td>
            <td>{{$supplier['name']}}</td>
            <td>{{$supplier['owner']}}</td>
            <td>{{$supplier['telephone']}}</td>
            <td>{{$supplier['addres']}}</td>
            <form action="{{url('supplier', ['id'=>$supplier->id])}}" method="POST">
                @method('delete')
                @csrf
                <td><button onclick="return confirm('Apakah anda yakin?')">delete</button></td>
            </form>
        </tr>
        @endforeach
    </table>
    <a href="{{ url('supplier/add') }}">tambahkan data</a>
@endsection




<!-- NAVBAR -->

@section('tittle')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Supplier</h1>
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