@extends('index')

@section('konten')

<!-- Main content -->
    <section class="content">
    <div class="container mt-4 mb-4">
    <div class="row">
        <!-- First Photo Card -->
        <div class="col-lg-4 col-md-6">
            <div class="card">
				<a href="">
                	<img src="Avatar/gray.jpg" class="card-img-top" alt="Image 1">
				</a>
                <!-- <div class="card-body">
                    <h5 class="card-title"></h5>
                    <p class="card-text"></p>
                </div> -->
            </div>
        </div>

        <!-- Second Photo Card -->
        <div class="col-lg-4 col-md-6">
            <div class="card">
				<a href="">
				<img src="Avatar/gray.jpg" class="card-img-top" style alt="Image 2">
				</a>
                <!-- <div class="card-body">
                    <h5 class="card-title"></h5>
                    <p class="card-text"></p>
                </div> -->
            </div>
        </div>

        <!-- Third Photo Card -->
        <div class="col-lg-4 col-md-6">
            <div class="card">
				<a href="">
					<img src="Avatar/gray.jpg" class="card-img-top" alt="Image 3">
				</a>
                <!-- <div class="card-body">
                    <h5 class="card-title"></h5>
                    <p class="card-text"></p>
                </div> -->
            </div>
        </div>
    </div>
</div>



        <div class="row">

          	<div class="col-lg-3 col-6">
            	<!-- small box -->
            	<div class="small-box bg-light">
              		<div class="inner">
                		<h3>{{ $good }}</h3>

                	<p>Data Barang</p>
              	</div>
              	<div class="icon">
                	<i class="ion ion-bag"></i>
              	</div>
              	<a href="/good" id="bcg" class="small-box-footer text-dark bg-success">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
		  
        <!-- ./col -->
    	<div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-light">
              	<div class="inner">
                	<h3>{{ $transaction }}<sup style="font-size: 20px">%</sup></h3>

                	<p>Transaksi</p>
              	</div>
              	<div class="icon">
                	<i class="ion ion-stats-bars"></i>
              	</div>
              	<a href="/transaction" id="bcg" class="small-box-footer bg-success ">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-light">
              	<div class="inner">
                	<h3>{{ $supplier }}</h3>

                	<p>Supply</p>
              	</div>
              	<div class="icon">
                	<i class="ion ion-person-add"></i>
              	</div>
              	<a href="/supplier" id="bcg" class="small-box-footer bg-success">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-light">
              	<div class="inner">
                	<h3>{{ $room }}</h3>

                	<p>Room</p>
              	</div>
              	<div class="icon">
                	<i class="ion ion-pie-graph"></i>
              	</div>
              	<a href="{{url('room')}}" id="bcg" class="small-box-footer bg-success">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
    </div>

@endsection

@section('tittle')
    <!-- Content Header (Page header) -->
<div class="content-header">
    <div id="Head" class="container-fluid rounded shadow bg-success">
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


