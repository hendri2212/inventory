@extends('Main')

@section('konten')
    <form action="{{url('room', ['id'=>$room->id])}}" method="POST">
        @method('put')
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name" value="{{$room->name}}">
        <br>
        <label for="company_id">Nama Company :</label>
        <select name="company_id" id="">
            @foreach ($company as $company)
                <option value="{{$company['id']}}">{{$company['name']}}</option>
            @endforeach
        </select>
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection