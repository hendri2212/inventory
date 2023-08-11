@extends('Menu.Main')

@section('konten')
    <form action="{{url('category')}}" method="POST">
        @csrf
        <input type="text" name="nama">
        <input type="submit" value="Simpan">
    </form>
@endsection