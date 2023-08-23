@extends('Main')

@section('konten')

<table border="1" cellspacing="0">
        <tr>
            <th>ID.</th>
            <th>Nama</th>
            <th>ID Company</th>
            <th>Aksi</th>
        </tr>
        @foreach ($data as $room)
        <tr>
            <td align="center">{{$room['id']}}</td>
            <td>{{$room['name']}}</td>
            <td>{{$room['company_id']}}</td>
            <form action="{{url('room', ['id'=>$room->id])}}" method="POST">
                @method('delete')
                @csrf
                <td><button onclick="return confirm('Apakah anda yakin?')">delete</button></td>
            </form>
        </tr>
        @endforeach
    </table>
    <a href="{{ url('room/add') }}">tambahkan data</a>
@endsection