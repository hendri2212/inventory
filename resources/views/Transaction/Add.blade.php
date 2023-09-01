@extends('main')

@section('konten')
    <form action="{{url('transaction')}}" method="POST">
        @csrf
        <label for="information">Informasi :</label>
        <input type="text" name="information">
        <br>
        <label for="image">Gambar :</label>
        <input type="image" name="image">
        <br>
        <label for="price">Harga :</label>
        <input type="text" name="price">
        <br>
        <label for="date">Tanggal :</label>
        <input type="date" name="date">
        <br>
        <label for="condition">Kondisi :</label>
        <select name="condition" id="">
            <option value="sangat_baik">Sangat Baik</option>
            <option value="kurang_baik">Kurang Baik</option>
            <option value="rusak">Rusak</option>
        </select>
        <br>
        <label for="goods_id">Nama Barang :</label>
        <select name="goods_id" id="">
            <option value=""></option>
            @foreach ($good as $good)
                <option value="{{$good['id']}}">{{$good['name']}}</option>
            @endforeach
        </select>
        <br>
        <label for="shop_id">Nama Supplier :</label>
        <select name="shop_id" id="">
            <option value=""></option>
            @foreach ($supplier as $supplier)
                <option value="{{$supplier['id']}}">{{$supplier['name']}}</option>
            @endforeach
        </select>
        <br>
        <label for="room_id">Nama Ruangan :</label>
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