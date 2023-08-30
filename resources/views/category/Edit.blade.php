@extends('index')

@section('konten')
    <form action="{{url('category', ['id'=>$category->id])}}" method="POST">
        @method('put')
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name" value="{{$category->name}}">
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection