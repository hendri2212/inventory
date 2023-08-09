@extends('')

@section('konten')
    <table border="1" cellspacing="0">
        <tr>
            <th>ID.</th>
            <th>Nama</th>
        </tr>
        @foreach ($data as $category)
        <tr>
            <td align="center">{{$category['id']}}</td>
            <td>{{$category['name']}}</td>
        </tr>
        @endforeach
    </table>
@endsection
