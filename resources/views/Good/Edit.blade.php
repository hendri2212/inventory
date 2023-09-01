@extends('index')

@section('konten')
    <form action="{{url('good', ['id'=>$good->id])}}" method="POST">
        @method('put')
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name" value="{{$good->name}}">
        <br>
        <select name="category_id" id="">
        @foreach ($data as $good)
            <option value="{{$good['id']}}">{{$category['name']}}</option>
        @endforeach
        </select>
        <input type="submit" value="Simpan">
    </form>
@endsection

