@extends('index')

@section('konten')
    <form action="{{url('supplier', ['id'=>$supplier->id])}}" method="POST">
        @method('put')
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name" value="{{$supplier->name}}">
        <br>
        <label for="company_id">Company :</label>
        <input type="text" name="company_id" value="{{$supplier->company_id}}">
        <br>
        <label for="image">Gambar :</label>
        <input type="text" name="image" value="{{$supplier->image}}">
        <br>
        <label for="telephone">Telepon :</label>
        <input type="number" name="telephone" value="{{$supplier->telephone}}">
        <br>
        <label for="address">Alamat :</label>
        <input type="text" name="address" value="{{$supplier->address}}">
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection