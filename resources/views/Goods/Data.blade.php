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
<body>
    
<table height="100" align="center" border="1" cellspacing="0" cellpadding="0">
    <tr align="center" height="25">
        <th width="50" align="center">ID</th>
        <th width="100">ID Category</th>
        <th width="100">Anjayani</th>
    </tr>
    @foreach ($data as $goods)
    <tr align="center">
        <td>{{$goods['id']}}</td>
        <td>{{$goods['category_id']}}</td>
    </tr>
    @endforeach
</table>
<a href="{{ url('category/add') }}">tambahkan data</a>
</body>
@endsection