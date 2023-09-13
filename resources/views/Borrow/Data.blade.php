@extends('index')

@section('konten')
    <table border="2px" align="center" id="example" class="table table-striped" style="width:97%" height="100px">
        <tr align="center">
            <th>ID</th>
            <th>Nama</th>
            <th>Aksi</th>
        </tr>
        @foreach ($data as $borrow)
        <tr>
            <td align="center" width="5%">{{$borrow['id']}}</td>
            <td>{{$borrow['name']}}</td>
            <form action="{{url('borrow', ['id'=>$borrow->id])}}" method="POST">
                @method('delete')
                @csrf
                <td align="center"><button><a href="{{url('borrow/' . $borrow->id . '/edit')}}">Edit</a></button> | 
                <button onclick="return confirm('Apakah anda yakin?')">delete</button></td>
            </form>
            <!-- <td><a href="{{url('borrow/' . $borrow->id . '/edit')}}">tes</a></td> -->
        </tr>
        @endforeach
    </table>
	<div class="a">
    <a href="{{ url('borrow/add') }}">tambahkan data</a>
	</div>
@endsection


<!-- NAVBAR -->
@section('tittle')


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


@endsection