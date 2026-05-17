<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Primeasia Central Library</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>

        .auth-container {
            animation: slideUp 0.5s ease-out;
            position: relative;
            z-index: 10;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }


        .btn-form {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            text-transform: uppercase;
            color: #fff;
            margin-top: 10px;
            letter-spacing: 1px;
        }

        .btn-green { 
            background: #1a5e3a; 
            box-shadow: 0 4px 12px rgba(26, 94, 58, 0.2);
        }

        .btn-green:hover {
            background: #144d2f;
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(26, 94, 58, 0.3);
        }
    </style>
</head>
<body class="hero"> 

<div class="auth-container">
    <a href="index.php">
        <img src="logo.jpg" alt="Logo" class="logo">
    </a>
    <h2>PRIMEASIA</h2>
    <h4>CENTRAL LIBRARY</h4>

    <?php if(isset($_GET['error'])): ?>
        <div class="error-msg">
            <?php 
                if($_GET['error'] == 'notfound') {
                    echo "<strong>INVALID LOGIN!</strong><br>Account not found. Please <a href='register.php'>register first</a>.";
                } elseif($_GET['error'] == 'wrongpass') {
                    echo "<strong>WRONG PASSWORD!</strong><br>Please check your 8-digit password.";
                }
            ?>
        </div>
    <?php endif; ?>

    <form action="login_logic.php" method="POST">
        <div class="input-group">
            <label>Student ID</label>
            <input type="text" name="student_id" placeholder="Enter 9-digit ID" maxlength="9" minlength="9" required autofocus>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter 8-digit password" maxlength="8" minlength="8" required>
        </div>

        <button type="submit" class="btn-form btn-green">Log In</button>
    </form>

    <div class="footer-link">
        Don't have an account? <a href="register.php">Register Now</a>
    </div>
    <div class="footer-link" style="margin-top: 15px;">
        <a href="index.php" style="color: #94a3b8; font-size: 12px; text-decoration: none;">← Back to Home</a>
    </div>
</div>

</body>
</html>