@extends('index')

@section('konten')
    <form action="{{url('borrow')}}" method="POST">
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name">
        <br>
        <label for="good_id">good :</label>
        <select name="good_id" id="">
            <option value=""></option>
            @foreach ($good as $good)
                <option value="{{$good['id']}}">{{$good['name']}}</option>
            @endforeach
        </select>
        <br>
        <label for="room_id">room :</label>
        <select name="room_id" id="">
            <option value=""></option>
            @foreach ($room as $room)
                <option value="{{$room['id']}}">{{$room['name']}}</option>
            @endforeach
        </select>
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection

<!-- NAVBAR -->
@section('tittle')

<div class="content-header">
    <div class="container-fluid rounded shadow bg-success">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-3 text-dark">Borrow</h1>
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