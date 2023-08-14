@extends('Main')

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

    <table border="1" cellspacing="0">
        <tr>
            <th>ID.</th>
            <th>Informasi</th>
            <th>Gambar</th>
            <th>Harga</th>
            <th>Tanggal</th>
            <th>Kondisi</th>
            <th>ID barang</th>
            <th>ID Toko</th>
            <th>ID Ruangan</th>
        </tr>
        @foreach ($data as $transaction)
        <tr>
            <td align="center">{{$transaction['id']}}</td>
            <td>{{$transaction['information']}}</td>
            <td>{{$transaction['image']}}</td>
            <td>{{$transaction['price']}}</td>
            <td>{{$transaction['date']}}</td>
            <td>{{$transaction['condition']}}</td>
            <td>{{$transaction['goods_id']}}</td>
            <td>{{$transaction['shop_id']}}</td>
            <td>{{$transaction['room_id']}}</td>
        </tr>
        @endforeach
    </table>
    <a href="{{ url('category/add') }}">tambahkan data</a>
@endsection