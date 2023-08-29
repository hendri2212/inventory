@extends('index')

@section('konten')
    <form action="{{url('room')}}" method="POST">
        @csrf
        <label for="nama">Nama :</label>
        <input type="text" name="nama">
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection