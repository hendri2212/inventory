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
	<div class="a">
    <a href="{{ url('category/add') }}">tambahkan data</a>
	</div>
@endsection


<!-- NAVBAR -->
@section('tittle')

<div class="content-header">
    <div class="container-fluid rounded shadow bg-success">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-3 text-dark">Category</h1>
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
