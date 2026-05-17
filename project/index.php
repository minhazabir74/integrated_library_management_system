<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrimeAsia Central Library</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

        
        .hero {
            height: 100vh;
            width: 100%;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('library_hero.png');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
        }

        
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 50px;
            background: rgba(11, 29, 81, 0.8); 
        }

        .logo-section { display: flex; align-items: center; }
        .logo-section img { width: 50px; margin-right: 15px; }
        .logo-section h1 { color: white; font-size: 24px; text-transform: uppercase; }

        .auth-buttons a {
            text-decoration: none;
            color: white;
            padding: 10px 25px;
            border-radius: 5px;
            font-weight: bold;
            margin-left: 10px;
            transition: 0.3s;
            text-transform: uppercase;
        }

        .btn-login { border: 2px solid white; }
        .btn-login:hover { background: white; color: #0b1d51; }

        .btn-register { background: #d32f2f; }
        .btn-register:hover { background: #b71c1c; }

        
        .hero-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            padding: 20px;
        }

        .hero-content h2 { font-size: 50px; margin-bottom: 20px; text-shadow: 2px 2px 10px rgba(0,0,0,0.5); }
        .hero-content p { font-size: 20px; max-width: 700px; line-height: 1.6; text-shadow: 1px 1px 5px rgba(0,0,0,0.5); }

    </style>
</head>
<body>

    <div class="hero">
        <nav>
            <div class="logo-section">
                <img src="logo.jpg" alt="Logo">
                <h1>Primeasia Central Library</h1>
            </div>
            <div class="auth-buttons">
                <a href="login.php" class="btn-login">Login</a>
                <a href="register.php" class="btn-register">Register</a>
            </div>
        </nav>

        <div class="hero-content">
            <h2>Welcome to Your Digital Gateway</h2>
            <p>Access thousands of research papers, books, and journals from the heart of Primeasia University. Empower your knowledge today.</p>
        </div>
    </div>

</body>
</html>