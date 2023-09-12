@extends('index')

@section('konten')
    <form action="{{url('supplier')}}" method="POST">
        @csrf
        <label for="name">Nama :</label>
        <input type="text" name="name">
        <br>
        <label for="company_id">Company :</label>
        <input type="text" name="company_id">
        <br>
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