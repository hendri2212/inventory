@extends('index')

@section('konten')
    <form action="{{url('supplier')}}" method="POST">
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name">
        <br>
        <label for="owner">Pemilik :</label>
        <input type="text" name="owner">
        <br>
        <label for="telephone">Telepon :</label>
        <input type="number" name="telephone">
        <br>
        <label for="addres">Alamat :</label>
        <input type="text" name="addres">
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection