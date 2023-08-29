@extends('Main')

@section('konten')
    <form action="{{url('transaction')}}" method="POST">
        @csrf
        <label for="information">Informasi :</label>
        <input type="text" name="information">
        <br>
        <label for="image">Gambar :</label>
        <input type="image" name="image">
        <br>
        <label for="price">Harga :</label>
        <input type="text" name="price">
        <br>
        <label for="date">Tanggal :</label>
        <input type="date" name="date">
        <br>
        <label for="condition">Kondisi :</label>
        <select name="condition" id="">
            <option value="sangat_baik">Sangat Baik</option>
            <option value="kurang_baik">Kurang Baik</option>
            <option value="rusak">Rusak</option>
        </select>
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection