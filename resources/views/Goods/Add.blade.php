
<<<<<<< HEAD
<body>
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
        
<br><br>
        <label for="">Kualitas</label> <br>
        <select name="Kualitas" id="">
            <option value="Baik">Baik</option>
            <option value="Buruk">Buruk</option>
        </select>

    </form>
    </body>
=======
@section('konten')
    <form action="{{url('goods')}}" method="POST">
        @csrf
        <label for="nama">Nama :</label>
        <input type="text" name="nama">
        <br>
        <input type="submit" value="Simpan">
    </form>
@endsection
>>>>>>> 740b8ed1d7098f6105d7d287a5772392d33b269c
