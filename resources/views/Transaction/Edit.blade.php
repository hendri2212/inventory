@extends('index')

@section('konten')
    <form action="{{url('transaction', ['id'=>$transaction->id])}}" method="POST">
        @method('put')
        @csrf
        <label for="information">Informasi :</label>
        <input type="text" name="information"  value="{{$transaction->information}}">
        <br>
        <label for="image">Gambar :</label>
        <input type="image" name="image" value="{{$transaction->image}}">
        <br>
        <label for="price">Harga :</label>
        <input type="text" name="price" value="{{$transaction->price}}">
        <br>
        <label for="date">Tanggal :</label>
        <input type="date" name="date" value="{{$transaction->date}}">
        <br>
        <label for="borrow_id">Borrow :</label>
        <select name="borrow_id" id="">
            @foreach ($borrow as $borrow)
                <option value="{{$borrow['id']}}">{{$borrow['name']}}</option>
            @endforeach
        </select>
        <br>
        <label for="condition_id">Kondisi :</label>
        <select name="condition_id" id="">
            @foreach ($condition as $condition)
                <option value="{{$condition['id']}}">{{$condition['name']}}</option>
            @endforeach
        </select>
        <br>
        <label for="good_id">Nama Barang :</label>
        <select name="good_id" id="">
            @foreach ($good as $good)
                <option value="{{$good['id']}}">{{$good['name']}}</option>
            @endforeach
        </select>
        <br>
        <label for="supplier_id">Nama Supplier :</label>
        <select name="supplier_id" id="">
            @foreach ($supplier as $supplier)
                <option value="{{$supplier['id']}}">{{$supplier['name']}}</option>
            @endforeach
        </select>
        <br>
        <label for="room_id">Nama Ruangan :</label>
        <select name="room_id" id="">
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
    <div class="container-fluid">
    	<div class="row mb-2">
          	<div class="col-sm-6">
            	<h1 class="m-0"></h1>
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