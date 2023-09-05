@extends('index')

@section('konten')
    <table align="center" id="example" class="table table-striped" style="width:97%" height="100px">
        <tr>
            <th>ID.</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Password</th>
            <th>ID Company</th>
            <th>Aksi</th>
        </tr>
        @foreach ($data as $user)
        <tr>
            <td align="center">{{$user['id']}}</td>
            <td>{{$user['name']}}</td>
            <td>{{$user['username']}}</td>
            <td>{{$user['password']}}</td>
            <td>{{$user['company_id']}}</td>
            <form action="{{url('user', ['id'=>$user->id])}}" method="POST">
                @method('delete')
                @csrf
                <td align="center"><button><a href="{{url('user/' . $user->id . '/edit')}}">Edit</a></button> | 
                <button onclick="return confirm('Apakah anda yakin?')">delete</button></td>
            </form>
        </tr>
        @endforeach
    </table>

    <div class="a">
    <a href="{{ url('user/add') }}">tambahkan data</a>
    </div>
@endsection

<!-- NAVBAR -->
@section('tittle')

    <!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">User</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard v1</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->


@endsection
