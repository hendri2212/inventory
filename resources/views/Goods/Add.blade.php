@extends('Menu.Main')

@section('konten')
    <h1>FORM</h1>

    <form action="" Method="POST">
        <label for="">Nama Barang</label> <br>
        <input type="text">
<br><br>

        <label for="">Kualitas</label> <br>
        <select name="Kualitas" id="">
            <option value="Baik">Baik</option>
            <option value="Buruk">Buruk</option>
        </select>

    </form>
@endsection