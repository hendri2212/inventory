@extends('Main')

@section('konten')
    <form action="{{url('company')}}" method="POST">
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name">
        <br>
        <label for="addres">Alamat :</label>
        <input type="text" name="addres">
        <br>
        <label for="telephone">Telepon :</label>
        <input type="number" name="telephone">
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection