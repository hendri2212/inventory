@extends('Main')

@section('konten')
    <form action="{{url('category')}}" method="POST">
        @csrf
        <label for="nama">Nama :</label>
        <input type="text" name="nama">
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection