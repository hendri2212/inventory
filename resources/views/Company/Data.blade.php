@extends('index')

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
            <td>{{$company['address']}}</td>
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
	<div class="a">
   <button> <a href="{{ url('company/add') }}">tambahkan data</a> </button>
   </div>
@endsection

@section('tittle')


    <!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
          	<div class="col-sm-6">
            	<h1 class="m-3 text">Company</h1>
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
