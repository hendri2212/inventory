@extends('index')

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
        <label for="borrow_id">Borrow :</label>
        <select name="borrow_id" id="">
            <option value=""></option>
            @foreach ($borrow as $borrow)
                <option value="{{$borrow['id']}}">{{$borrow['name']}}</option>
            @endforeach
        </select>
        <br>
        <label for="condition_id">Kondisi :</label>
        <select name="condition_id" id="">
            <option value=""></option>
            @foreach ($condition as $condition)
                <option value="{{$condition['id']}}">{{$condition['name']}}</option>
            @endforeach
        </select>
        <br>
        <label for="good_id">Nama Barang :</label>
        <select name="good_id" id="">
            <option value=""></option>
            @foreach ($good as $good)
                <option value="{{$good['id']}}">{{$good['name']}}</option>
            @endforeach
        </select>
        <br>
        <label for="supplier_id">Nama Supplier :</label>
        <select name="supplier_id" id="">
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