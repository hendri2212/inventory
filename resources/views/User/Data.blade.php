@extends('Main')

@section('konten')
    <table border="1" cellspacing="0">
        <tr>
            <th>ID.</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Password</th>
            <th>ID Company</th>
            <th>Aksi</th>
        </tr>
        @foreach ($data as $user)
        <tr>
            <td align="center">{{$user['id']}}</td>
            <td>{{$user['name']}}</td>
            <td>{{$user['username']}}</td>
            <td>{{$user['password']}}</td>
            <td>{{$user['company_id']}}</td>
            <form action="{{url('user', ['id'=>$user->id])}}" method="POST">
                @method('delete')
                @csrf
                <td align="center"><button><a href="{{url('user/' . $user->id . '/edit')}}">Edit</a></button> | 
                <button onclick="return confirm('Apakah anda yakin?')">delete</button></td>
            </form>
        </tr>
        @endforeach
    </table>
    <a href="{{ url('user/add') }}">tambahkan data</a>
@endsection
