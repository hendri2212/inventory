@extends('Main')

@section('konten')
<body>
    
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
            <th>ID Category</th>
        </tr>
        @foreach ($data as $goods)
        <tr>
            <td align="center">{{$goods['id']}}</td>
            <td>{{$goods['category_id']}}</td>
        </tr>
        @endforeach
    </table>
    <a href="{{ url('category/add') }}">tambahkan data</a>
@endsection