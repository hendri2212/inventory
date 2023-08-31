@extends('Main')

@section('konten')
    <form action="{{url('user', ['id'=>$user->id])}}" method="POST">
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name" value="{{$user->name}}">
        <br>
        <label for="username">Username :</label>
        <input type="text" name="username" value="{{$user->username}}">
        <br>
        <label for="password">Password :</label>
        <input type="password" name="password" value="{{$user->password}}">
        <br>
        <label for="company_id">Nama Company :</label>
        <select name="company_id" id="">
            @foreach ($data as $company)
                <option value="{{$company['id']}}">{{$company['name']}}</option>
            @endforeach
        </select>
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection