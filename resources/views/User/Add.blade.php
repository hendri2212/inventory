@extends('index')

@section('konten')
    <form action="{{url('user')}}" method="POST">
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name">
        <br>
        <label for="role_id">Nama role :</label>
        <select name="role_id" id="">
            <option value=""></option>
            @foreach ($role as $role)
                <option value="{{$role['id']}}">{{$role['name']}}</option>
            @endforeach
        </select>
        <br>
        <label for="username">Username :</label>
        <input type="text" name="username">
        <br>
        <label for="password">Password :</label>
        <input type="password" name="password">
        <br>
        <label for="company_id">Nama Company :</label>
        <select name="company_id" id="">
            <option value=""></option>
            @foreach ($company as $company)
                <option value="{{$company['id']}}">{{$company['name']}}</option>
            @endforeach
        </select>
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection


<!-- NAVBAR -->
@section('tittle')

<div class="content-header">
    <div class="container-fluid">
    	<div class="row mb-2">
          	<div class="col-sm-6">
            	<h1 class="m-0"></h1>
          	</div><!-- /.col -->
          	<div class="col-sm-6">
            	<ol class="breadcrumb float-sm-right">
              		<li class="breadcrumb-item"><a href="/beranda">Home</a></li>
              		<li class="breadcrumb-item active">Dashboard v1</li>
            	</ol>
          	</div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->

@endsection