@extends('Main')

@section('konten')
    <form action="{{url('supplier')}}" method="POST">
        @csrf
        <label for="nama">Nama :</label>
        <input type="text" name="nama">
        <br>
        <label for="pemilik">Pemilik :</label>
        <input type="text" name="pemilik">
        <br>
        <label for="telepon">Telepon :</label>
        <input type="number" name="telepon">
        <br>
        <label for="alamat">Alamat :</label>
        <input type="text" name="alamat">
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection