@extends('Main')

@section('konten')

    <table border="2px" align="center" id="example" class="table table-striped" style="width:97%" height="100px">
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
                    <td align="center"><button><a href="{{url('company/' . $company->id . '/edit')}}">Edit</a></button> | 
                    <button onclick="return confirm('Apakah anda yakin?')">delete</button></td>
            </form>
        </tr>
        @endforeach
    </table>
   <button> <a href="{{ url('company/add') }}">tambahkan data</a> </button>
@endsection
