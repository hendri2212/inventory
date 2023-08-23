@extends('Main')

@section('konten')
    <form action="{{url('transaction')}}" method="POST">
        @csrf
        <label for="informasi">Informasi :</label>
        <input type="text" name="informasi">
        <br>
        <label for="gambar">Gambar :</label>
        <input type="image" name="gambar">
        <br>
        <label for="harga">Harga :</label>
        <input type="text" name="harga">
        <br>
        <label for="tanggal">Tanggal :</label>
        <input type="date" name="tanggal">
        <br>
        <label for="kondisi">Kondisi :</label>
        <select name="kondisi" id="">
            <option value="sangat_baik">Sangat Baik</option>
            <option value="kurang_baik">Kurang Baik</option>
            <option value="rusak">Rusak</option>
        </select>
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection