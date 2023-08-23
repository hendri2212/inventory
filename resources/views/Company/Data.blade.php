@extends('Main')

@section('konten')
    <table border="1" cellspacing="0">
        <tr>
            <th>ID.</th>
            <th>Nama</th>
            <th>Addres</th>
            <th>Telephone</th>
            <th>Aksi</th>
        </tr>
        @foreach ($data as $company)
        <tr>
            <td align="center">{{$company['id']}}</td>
            <td>{{$company['name']}}</td>
            <td>{{$company['addres']}}</td>
            <td>{{$company['telephone']}}</td>
            <form action="{{url('company', ['id'=>$company->id])}}" method="POST">
                @method('delete')
                @csrf
                <td><button onclick="return confirm('Apakah anda yakin?')">delete</button></td>
            </form>
        </tr>
        @endforeach
    </table>
    <a href="{{ url('company/add') }}">tambahkan data</a>
@endsection
