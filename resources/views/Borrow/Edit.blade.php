@extends('index')

@section('konten')
    <form action="{{url('category', ['id'=>$category->id])}}" method="POST">
        @method('put')
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name" value="{{$category->name}}">
        <br>
        <label for="good_id">good :</label>
        <select name="good_id" id="">
            @foreach ($good as $good)
                <option value="{{$good['id']}}">{{$good['name']}}</option>
            @endforeach
        </select>
        <br>
        <label for="room_id">room :</label>
        <select name="room_id" id="">
            @foreach ($room as $room)
                <option value="{{$room['id']}}">{{$room['name']}}</option>
            @endforeach
        </select>
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection