@extends('Main')

@section('konten')
<body>
    
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

    <h1>SUPPLY</h1>

    <table border="1" cellspacing="0" cellpadding="0">
        <Tr align="center" bgcolor="lightgray">
            <td width="50" >No</td>
            <td width="250">Supliers</td>
            <td width="250">Instansi</td>
            <td width="250">Kategori</td>
        </Tr>
        
        <tr align="center">
            <th>1</th>
            <th>----</th>
            <th>----</th>
            <th>----</th>
        </tr>

    </table>

    <a href="/formspl">
        <button>Tambah Data</button>
    </a>

    <br>
</body>
@endsection