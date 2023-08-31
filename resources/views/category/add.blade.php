@extends('index')

@section('konten')
    <form action="{{url('category')}}" method="POST">
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name">
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection