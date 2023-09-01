@extends('index')

@section('konten')
    <table border="2px" align="center" id="example" class="table table-striped" style="width:97%" height="100px">
        <tr align="center">
            <th>ID</th>
            <th>Nama</th>
            <th>Aksi</th>
        </tr>
        @foreach ($data as $category)
        <tr>
            <td align="center" width="5%">{{$category['id']}}</td>
            <td>{{$category['name']}}</td>
            <form action="{{url('category', ['id'=>$category->id])}}" method="POST">
                @method('delete')
                @csrf
                <td align="center"><button><a href="{{url('category/' . $category->id . '/edit')}}">Edit</a></button> | 
                <button onclick="return confirm('Apakah anda yakin?')">delete</button></td>
            </form>
            <!-- <td><a href="{{url('category/' . $category->id . '/edit')}}">tes</a></td> -->
        </tr>
        @endforeach
    </table>
    <a href="{{ url('category/add') }}">tambahkan data</a>
@endsection


<!-- NAVBAR -->
@section('tittle')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Kategory</h1>
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
