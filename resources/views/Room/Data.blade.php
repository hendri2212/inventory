@extends('index')

@section('konten')

<!-- <style>
    button {
        border: none;
        outline: none;
        padding: 8px;
        width: 252px;
        color: black;
        font-size: 16;
        cursor: pointer;
        margin-top: 20px;
        border-radius: 5px;
    }

    button:hover {
        background: gray;
    }

</style> -->

<table border="2" align="center" id="example" class="table table-striped" style="width:97%" height="100px">
        <tr>
            <th>ID.</th>
            <th>Nama</th>
            <th>Nama Company</th>
            <th>Aksi</th>
        </tr>
        @foreach ($data as $room)
        <tr>
            <td align="center">{{$room['id']}}</td>
            <td>{{$room['name']}}</td>
            <td>{{$room['company']['name']}}</td>
            <td>{{$room['good']['name']}}</td>
            <td>{{$room['supplier']['name']}}</td>
            <form action="{{url('room', ['id'=>$room->id])}}" method="POST">
                @method('delete')
                @csrf
                <td align="center"><button><a href="{{url('room/' . $room->id . '/edit')}}">Edit</a></button> | 
                <button onclick="return confirm('Apakah anda yakin?')">delete</button></td>
            </form>
        </tr>
        @endforeach
    </table>
	<div class="a">
    <a href="{{ url('room/add') }}">tambahkan data</a>
	</div>
@endsection


@section('tittle')

    <!-- Content Header (Page header) -->
    <div class="content-header">
      	<div class="container-fluid">
        	<div class="row mb-2">
          		<div class="col-sm-6">
            		<h1 class="m-0">Room</h1>
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