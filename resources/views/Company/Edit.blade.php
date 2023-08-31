@extends('Main')

@section('konten')
    <form action="{{url('company', ['id'=>$company->id])}}" method="POST">
        @method('put')
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name" value="{{$company->name}}">
        <br>
        <label for="addres">Alamat :</label>
        <input type="text" name="addres" value="{{$company->addres}}">
        <br>
        <label for="telephone">Telepon :</label>
        <input type="number" name="telephone" value="{{$company->telephone}}">
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection