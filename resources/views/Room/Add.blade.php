@extends('Main')

@section('konten')
    <form action="{{url('room')}}" method="POST">
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name">
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection