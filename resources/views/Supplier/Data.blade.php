@extends('Main')

@section('konten')
<table border="1" cellspacing="0">
        <tr>
            <th>ID.</th>
            <th>Nama</th>
            <th>Owner</th>
            <th>Telepon</th>
            <th>Alamat</th>
        </tr>
        @foreach ($data as $supplier)
        <tr>
            <td align="center">{{$supplier['id']}}</td>
            <td>{{$supplier['name']}}</td>
            <td>{{$supplier['owner']}}</td>
            <td>{{$supplier['telephone']}}</td>
            <td>{{$supplier['addres']}}</td>
        </tr>
        @endforeach
    </table>
    <a href="{{ url('category/add') }}">tambahkan data</a>
@endsection