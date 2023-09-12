@extends('index')

@section('konten')

<!-- <style>
    button {
        border: none;
        outline: none;
        padding: 8px;
        width: 252px;
        color: black;
        font-size: 16;
        cursor: pointer;
        margin-top: 20px;
        border-radius: 5px;
    }

    button:hover {
        background: gray;
    }

</style>

    <h1>Transaksi</h1>

    <a href="/formtr">
        <button>Tambah Data</button>
    </a> -->

    <table align="center" id="example" class="table table-striped" style="width:97%" height="100px">
        <tr>
            <th>ID.</th>
            <th>Informasi</th>
            <th>Gambar</th>
            <th>Harga</th>
            <th>Tanggal</th>
            <th>Kondisi</th>
            <th>Nama barang</th>
            <th>Nama Toko</th>
            <th>Nama Ruangan</th>
            <th>Aksi</th>
        </tr>
        @foreach ($data as $transaction)
        <tr>
            <td align="center">{{$transaction['id']}}</td>
            <td>{{$transaction['information']}}</td>
            <td>{{$transaction['image']}}</td>
            <td>{{$transaction['price']}}</td>
            <td>{{$transaction['date']}}</td>
            <td>{{$transaction['borrow']['name']}}</td>
            <td>{{$transaction['condition']['name']}}</td>
            <td>{{$transaction['good']['name']}}</td>
            <td>{{$transaction['supplier']['name']}}</td>
            <td>{{$transaction['room']['name']}}</td>
            <form action="{{url('transaction', ['id'=>$transaction->id])}}" method="POST">
                @method('delete')
                @csrf
                <td align="center"><button><a href="{{url('transaction/' . $transaction->id . '/edit')}}">Edit</a></button> | 
                <button onclick="return confirm('Apakah anda yakin?')">delete</button></td>
            </form>
        </tr>
        @endforeach
    </table>
    <div class="a">
    <a href="{{ url('transaction/add') }}">tambahkan data</a>
    </div>
@endsection



<!-- NAVBAR -->
@section('tittle')

    <!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Transaksi</h1>
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