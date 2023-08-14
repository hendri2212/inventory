@extends('Main')

@section('konten')
    <table border="1" cellspacing="0">
        <tr>
            <th>ID.</th>
            <th>Nama</th>
            <th>Addres</th>
            <th>Telephone</th>
        </tr>
        @foreach ($data as $company)
        <tr>
            <td align="center">{{$company['id']}}</td>
            <td>{{$company['name']}}</td>
            <td>{{$company['addres']}}</td>
            <td>{{$company['telephone']}}</td>
        </tr>
        @endforeach
    </table>
    <a href="{{ url('company/add') }}">tambahkan data</a>
@endsection
