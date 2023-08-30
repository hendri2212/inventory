@extends('Main')

@section('konten')
<table border="1" cellspacing="0">
        <tr>
            <th>ID.</th>
            <th>Nama</th>
            <th>Owner</th>
            <th>Telepon</th>
            <th>Alamat</th>
            <th>Aksi</th>
        </tr>
        @foreach ($data as $supplier)
        <tr>
            <td align="center">{{$supplier['id']}}</td>
            <td>{{$supplier['name']}}</td>
            <td>{{$supplier['owner']}}</td>
            <td>{{$supplier['telephone']}}</td>
            <td>{{$supplier['addres']}}</td>
            <form action="{{url('supplier', ['id'=>$supplier->id])}}" method="POST">
                @method('delete')
                @csrf
                <td align="center"><button><a href="{{url('supplier/' . $supplier->id . '/edit')}}">Edit</a></button> | 
                <button onclick="return confirm('Apakah anda yakin?')">delete</button></td>
            </form>
        </tr>
        @endforeach
    </table>
    <a href="{{ url('supplier/add') }}">tambahkan data</a>
@endsection