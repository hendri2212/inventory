@extends('index')

@section('konten')
    <form action="{{url('room')}}" method="POST">
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name">
        <br>
        <label for="company_id">Nama Company :</label>
        <select name="company_id" id="">
            <option value=""></option>
            @foreach ($data as $company)
                <option value="{{$company['id']}}">{{$company['name']}}</option>
            @endforeach
        </select>
        <br>
        <label for="good_id">Nama barang :</label>
        <select name="good_id" id="">
            <option value=""></option>
            @foreach ($data as $good)
                <option value="{{$good['id']}}">{{$good['name']}}</option>
            @endforeach
        </select>
        <br>
        <label for="supplier_id">Nama Supplier :</label>
        <select name="supplier_id" id="">
            <option value=""></option>
            @foreach ($data as $supplier)
                <option value="{{$supplier['id']}}">{{$supplier['name']}}</option>
            @endforeach
        </select>
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection