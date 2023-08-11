@extends('Main')

@section('konten')

<style>
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

    <h1>Rooms</h1>

    <a href="/formr">
        <button>Tambah Data</button>
    </a>
@endsection