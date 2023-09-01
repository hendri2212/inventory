@extends('Main')

@section('konten')
    <form action="{{url('supplier', ['id'=>$supplier->id])}}" method="POST">
        @method('put')
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name" value="{{$supplier->name}}">
        <br>
        <label for="owner">Pemilik :</label>
        <input type="text" name="owner" value="{{$supplier->owner}}">
        <br>
        <label for="telephone">Telepon :</label>
        <input type="number" name="telephone" value="{{$supplier->telephone}}">
        <br>
        <label for="addres">Alamat :</label>
        <input type="text" name="addres" value="{{$supplier->addres}}">
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection