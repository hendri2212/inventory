@extends('Main')

@section('konten')

<!-- <style>
    button {
        border: none;
        outline: none;
        padding: 8px;
        width: 252px;
        color: black;
        font-size: 16;
        cursor: pointer;
        margin-top: 20px;
        border-radius: 5px;
    }

    button:hover {
        background: gray;
    }

</style> -->

<table border="1" cellspacing="0">
        <tr>
            <th>ID.</th>
            <th>Nama</th>
            <th>ID Company</th>
        </tr>
        @foreach ($data as $room)
        <tr>
            <td align="center">{{$room['id']}}</td>
            <td>{{$room['name']}}</td>
            <td>{{$room['company_id']}}</td>
            <td>----</td>
        </tr>
        @endforeach
    </table>

    <button><a href="{{ url('category/add') }}">tambahkan data</a>
@endsection