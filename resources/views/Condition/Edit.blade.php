@extends('index')

@section('konten')
    <form action="{{url('condition', ['id'=>$condition->id])}}" method="POST">
        @method('put')
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name" value="{{$condition->name}}">
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection