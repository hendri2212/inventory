<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Halaman Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <video id="bg-video" autoplay loop muted>
            <source src="./Avatar/Blue_Galaxy.mp4" type="video/mp4" />
        </video>
        
<!-- <div class="foto">
<img src="../../public/Avatar/pxfuel.jpg" alt="">
</div> -->
        
    
<div class="Bg">
<div class="container">
    <div class="login">
        <form action="/login" method="POST">
            @csrf
            <H1>LOGIN</H1>
            <hr>
            <p>Gatau Mau Nulis Apa</p>
            <label for="email">EMAIL</label>
            <input type="email" name="email" id="email" placeholder="Example@gmail.com" required>

            <label for="">PASSWORD</label>
            <input type="email" name="email" id="email" placeholder="Password" required>

            <button>LOGIN</button>

            <a href="#">Lupa Password</a>
        </form>
    </div>
    <div class="right">
        <img src="./Avatar/Ryo.jpeg" alt="">
</div>
</div>
</header>
</body>
</html>