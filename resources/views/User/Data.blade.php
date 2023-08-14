@extends('Main')

@section('konten')
    <table border="1" cellspacing="0">
        <tr>
            <th>ID.</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Password</th>
            <th>ID Company</th>
        </tr>
        @foreach ($data as $user)
        <tr>
            <td align="center">{{$user['id']}}</td>
            <td>{{$user['name']}}</td>
            <td>{{$user['username']}}</td>
            <td>{{$user['password']}}</td>
            <td>{{$user['company_id']}}</td>
        </tr>
        @endforeach
    </table>
    <a href="{{ url('category/add') }}">tambahkan data</a>
@endsection
