@extends('Main')

@section('konten')
    <form action="{{url('goods')}}" method="POST">
        @csrf
        <label for="nama">Nama :</label>
        <input type="text" name="nama">
        <br>
        <select name="category_id" id="">
        @foreach ($data as $category)
            <option value="{{$category['id']}}">{{$category['name']}}</option>
        @endforeach
        </select>
        <input type="submit" value="Simpan">
    </form>
@endsection
