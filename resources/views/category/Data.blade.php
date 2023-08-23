@extends('Main')

@section('konten')
    <table border="1" cellspacing="0">
        <tr>
            <th>ID.</th>
            <th>Nama</th>
            <th>Aksi</th>
        </tr>
        @foreach ($data as $category)
        <tr>
            <td align="center">{{$category['id']}}</td>
            <td>{{$category['name']}}</td>
            <form action="{{url('category', ['id'=>$category->id])}}" method="POST">
                @method('delete')
                @csrf
                <td><button onclick="return confirm('Apakah anda yakin?')">delete</button></td>
            </form>
        </tr>
        @endforeach
    </table>
    <a href="{{ url('category/add') }}">tambahkan data</a>
@endsection
