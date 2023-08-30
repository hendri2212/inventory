@extends('Main')

@section('konten')
    <form action="{{url('good')}}" method="POST">
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name">
        <br>
        <select name="category_id" id="">
            <option value=""></option>
        @foreach ($data as $category)
            <option value="{{$category['id']}}">{{$category['name']}}</option>
        @endforeach
        </select>
        <input type="submit" value="Simpan">
    </form>
@endsection
