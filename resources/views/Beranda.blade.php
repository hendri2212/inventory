@extends('index')

@section('konten')

<!-- Main content -->
    <section class="content">
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          	<div class="col-lg-3 col-6">
            	<!-- small box -->
            	<div class="small-box bg-light">
              		<div class="inner">
                		<h3>150</h3>

                	<p>Data Barang</p>
              	</div>
              	<div class="icon">
                	<i class="ion ion-bag"></i>
              	</div>
              	<a href="/good" class="small-box-footer text-dark bg-success">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
		  
        <!-- ./col -->
    	<div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-light">
              	<div class="inner">
                	<h3>53<sup style="font-size: 20px">%</sup></h3>

                	<p>Transaksi</p>
              	</div>
              	<div class="icon">
                	<i class="ion ion-stats-bars"></i>
              	</div>
              	<a href="/transaction" class="small-box-footer bg-success ">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-light">
              	<div class="inner">
                	<h3>44</h3>

                	<p>Supply</p>
              	</div>
              	<div class="icon">
                	<i class="ion ion-person-add"></i>
              	</div>
              	<a href="/supplier" class="small-box-footer bg-success">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-light">
              	<div class="inner">
                	<h3>65</h3>

                	<p>Room</p>
              	</div>
              	<div class="icon">
                	<i class="ion ion-pie-graph"></i>
              	</div>
              	<a href="{{url('room')}}" class="small-box-footer bg-success">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
    </div>

@endsection

@section('tittle')
    <!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid shadow bg-success">
        <div class="row mb-2">
          	<div class="col-sm-6">
            	<h1 class="m-3 text-dark">Beranda</h1>
          	</div><!-- /.col -->
          	<div class="col-sm-6 d-flex align-items-center justify-content-end">
            	<ol class="breadcrumb float-sm-right d-flex align-items-stretch">
              		<li class="breadcrumb-item"><a style="text-decoration:none" href="#">Home</a></li>
              		<li class="breadcrumb-item text-dark">Dashboard v1</li>
           	 	</ol>
          	</div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->

    <!-- /.content-header -->
@endsection


