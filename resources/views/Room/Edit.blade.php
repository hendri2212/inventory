@extends('index')

@section('konten')
    <form action="{{url('room', ['id'=>$room->id])}}" method="POST">
        @method('put')
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name" value="{{$room->name}}">
        <br>
        <label for="company_id">Nama Company :</label>
        <select name="company_id" id="">
            @foreach ($company as $company)
                <option value="{{$company['id']}}">{{$company['name']}}</option>
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
        <input type="submit" value="Simpan">
    </form>
@endsection