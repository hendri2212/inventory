@extends('index')

@section('konten')
    <form action="{{url('condition')}}" method="POST">
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name">
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
                <h1 class="m-3 text-dark">Condition</h1>
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