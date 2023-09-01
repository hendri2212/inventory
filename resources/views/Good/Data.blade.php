<!-- DATA -->
@extends('index')

@section('konten')
<body>
    
<table border="2" align="center" id="example" class="table table-striped" style="width:97%" height="100px">
        <tr align="center">
            <th>ID.</th>
            <th>Nama</th>
            <th>Category</th>
            <th>Aksi</th>
        </tr>
        
        @foreach ($data as $good)
        <tr>
            <td>{{$good['id']}}</td>
            <td>{{$good['name']}}</td>
            <td>{{$good['category']['name']}}</td>
            <form action="{{ url('good', ['id'=>$good->id]) }}" method="POST">
                @method('delete')
                @csrf
<<<<<<< HEAD
                <td align="center"><button onclick="return confirm('Apakah anda yakin?')">delete</button>| 
                <button><a href="{{url('good/' . $good->id . '/edit')}}">Edit</a></td></button></td>
=======
                <td align="center"><button><a href="{{url('good/' . $good->id . '/edit')}}">Edit</a></button> | 
                <button onclick="return confirm('Apakah anda yakin?')">delete</button></td>
>>>>>>> 37f03ab1bdc11d778905576c35253de6315634b5
            </form>
        </tr>
        @endforeach
    </table>
    <a href="{{ url('good/add') }}">tambahkan data</a>
@endsection



<!-- NAVBAR -->

@section('tittle')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Data Barang</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/beranda">Home</a></li>
              <li class="breadcrumb-item active">Dashboard v1</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

@endsection