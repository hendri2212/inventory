@extends('Main')

@section('konten')
    <form action="{{url('company')}}" method="POST">
        @csrf
        <label for="nama">Nama :</label>
        <input type="text" name="nama">
        <br>
        <label for="alamat">Alamat :</label>
        <input type="text" name="alamat">
        <br>
        <label for="telepon">Telepon :</label>
        <input type="number" name="telepon">
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection