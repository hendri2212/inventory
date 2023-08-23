@extends('Main')

@section('konten')
    <form action="{{url('user')}}" method="POST">
        @csrf
        <label for="nama">Nama :</label>
        <input type="text" name="nama">
        <br>
        <label for="username">Username :</label>
        <input type="text" name="username">
        <br>
        <label for="password">Password :</label>
        <input type="password" name="password">
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection