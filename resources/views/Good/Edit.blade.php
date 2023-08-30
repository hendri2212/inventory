@extends('Main')

@section('konten')
    <form action="{{url('good', ['id'=>$good->id])}}" method="POST">
        @method('put')
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name" value="{{$good->name}}">
        <br>
        <select name="category_id" id="" value="{{$good->name}}">
            {{-- <option value="{{$good->id}}">{{$good->name}}</option> --}}
        @foreach ($category as $category)
            <option value="{{$category['id']}}">{{$category['name']}}</option>
        @endforeach
        </select>
        <input type="submit" value="Simpan">
    </form>
@endsection
