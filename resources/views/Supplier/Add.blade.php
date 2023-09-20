@extends('index')

@section('konten')
    <form action="{{url('supplier')}}" method="POST">
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name">
        <br>
        <label for="company_id">Nama Company :</label>
        <select name="company_id" id="">
            <option value=""></option>
            @foreach ($company as $company)
                <option value="{{$company['id']}}">{{$company['name']}}</option>
            @endforeach
        </select>
        <br>
        {{-- <label for="company_id">Company :</label>
        <input type="text" name="company_id">
        <br> --}}
        <label for="image">gambar :</label>
        <input type="text" name="image">
        <br>
        <label for="telephone">Telepon :</label>
        <input type="number" name="telephone">
        <br>
        <label for="address">Alamat :</label>
        <input type="text" name="address">
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection


<!-- NAVBAR -->
@section('tittle')

<div class="content-header">
    <div class="container-fluid rounded shadow bg-success">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-3 text-dark">Add Supplier</h1>
            </div><!-- /.col -->
            <div class="col-sm-6 d-flex align-items-center justify-content-end">
              <ol class="breadcrumb float-sm-right d-flex align-items-stretch">
              		<li class="breadcrumb-item"><a style="text-decoration:none" href="#">Home</a></li>
              		<li class="breadcrumb-item text-dark">Dashboard v1</li>
           	 	</ol>
          	</div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
@endsection