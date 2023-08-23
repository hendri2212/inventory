@extends('index')

@section('konten')

<style>
    button {
        border: none;
        outline: none;
        width: 30px;
        color: black;
        font-size: 16;
    }
</style>


<table border="4px" id="example" class="table table-striped" style="width:100%" height="100px">
        <thead align="center">
            <tr>
                <th>ID</th>
                <th>ID Category</th>
                <th>Kosong</th>
            </tr>
        </thead>

        @foreach ($data as $goods)
        <tbody align="center">
             <tr align="center">
        <td>{{$goods['id']}}</td>
        <td>{{$goods['category_id']}}</td>
        <td>----</td>
    	</tr>
        </tfoot>
        @endforeach
    </table>
<br>
<button><a href="{{ url('category/add') }}"></a></button>
</body>


    @endsection