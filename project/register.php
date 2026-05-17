<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Primeasia Central Library</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>

        body.register-page {

            background: linear-gradient(135deg, #1a5e3a 0%, #0b1d51 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .auth-container {
            animation: slideUp 0.5s ease-out;
            border-top: 5px solid #d32f2f; 
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .input-group {
            position: relative;
            margin-bottom: 15px;
            text-align: left;
        }

        .input-group label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }


        .btn-form {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            text-transform: uppercase;
            color: #fff;
            margin-top: 10px;
            letter-spacing: 1px;
        }

        .btn-red { 
            background: #d32f2f; 
            box-shadow: 0 4px 12px rgba(211, 47, 47, 0.2);
        }

        .btn-red:hover {
            background: #b71c1c;
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(211, 47, 47, 0.3);
        }
    </style>
</head>
<body class="register-page"> 

<div class="auth-container">
    <a href="index.php">
        <img src="logo.jpg" alt="Logo" class="logo">
    </a>
    <h2>PRIMEASIA</h2>
    <h4>LIBRARY REGISTRATION</h4>

    <form action="register_logic.php" method="POST">
        <div class="input-group">
            <label>Full Name</label>
            <input type="text" name="name" placeholder="Enter your full name" required autofocus>
        </div>

        <div class="input-group">
            <label>Student ID</label>
            <input type="text" name="student_id" placeholder="9-digit ID (e.g. 2110110XX)" maxlength="9" minlength="9" required>
        </div>

        <div class="input-group">
            <label>University Email</label>
            <input type="email" name="email" placeholder="example@primeasia.edu.bd" required>
        </div>

        <div class="input-group">
            <label>Create Password</label>
            <input type="password" name="password" placeholder="Choose 8-digit password" maxlength="8" minlength="8" required>
        </div>

        <button type="submit" class="btn-form btn-red">Create Account</button>
    </form>

    <div class="footer-link">
        Already have an account? <a href="login.php">Log In Here</a>
    </div>
    <div class="footer-link" style="margin-top: 15px;">
        <a href="index.php" style="color: #94a3b8; font-size: 12px; text-decoration: none;">← Back to Home</a>
    </div>
</div>

</body>
</html>