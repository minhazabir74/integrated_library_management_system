<?php
session_start();
include '../includes/db.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}


$total_books_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM books");
$total_books = mysqli_fetch_assoc($total_books_query)['total'];

$pending_req_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM borrow_requests WHERE status = 'pending'");
$pending_requests = mysqli_fetch_assoc($pending_req_query)['total'];


$pending_return_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM borrow_requests WHERE status = 'return_pending'");
$pending_returns = mysqli_fetch_assoc($pending_return_query)['total'];

$total_students_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$total_students = mysqli_fetch_assoc($total_students_query)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Primeasia Library</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; height: 100vh; background: #f4f7f6; }

        
        .sidebar { width: 260px; background: #0b1d51; color: white; padding: 25px; display: flex; flex-direction: column; }
        .sidebar h2 { font-size: 22px; margin-bottom: 35px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; }
        
        .sidebar a { 
            display: block; 
            color: #bdc3c7; 
            text-decoration: none; 
            padding: 14px 18px; 
            margin-bottom: 8px; 
            border-radius: 8px; 
            transition: 0.3s; 
            font-size: 15px;
        }
        
        .sidebar a:hover { background: rgba(255,255,255,0.1); color: white; }
        .active { background: #d32f2f !important; color: white !important; font-weight: bold; }

        
        .main-content { flex: 1; padding: 40px; overflow-y: auto; }
        .header { margin-bottom: 30px; }
        .header h1 { color: #0b1d51; font-size: 28px; }

        
        .stats-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .card { 
            background: white; 
            padding: 25px; 
            border-radius: 12px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); 
            border-left: 5px solid #0b1d51;
            transition: transform 0.3s;
        }
        .card:hover { transform: translateY(-5px); }
        .card h3 { font-size: 14px; color: #7f8c8d; text-transform: uppercase; margin-bottom: 10px; }
        .card .value { font-size: 32px; font-weight: bold; color: #2c3e50; }
        
        .card.red { border-left-color: #d32f2f; }
        .card.green { border-left-color: #27ae60; }             
        .card.orange { border-left-color: #f39c12; } /* Style for Return Requests */

        .welcome-msg { margin-top: 40px; padding: 20px; background: #e8f4fd; border-radius: 10px; color: #2980b9; font-weight: 500; }
    </style>
</head>
<body>

    
    <div class="sidebar">
        <h2>Library Admin</h2>
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="add_book.php">Add New Book</a>
        <a href="view_books.php">Book List</a>
        <a href="requests.php">Borrow Requests</a>
        <a href="return_requests.php">Return Requests</a> 
        <a href="student_info.php">Student Info</a>
        
        <div style="margin-top: auto;">
            <a href="../logout.php" style="background: #d32f2f; color: white; text-align: center;">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Welcome Back, Admin</h1>
            <p>Library Management System Overview</p>
        </div>

        <div class="stats-container">
            <div class="card">
                <h3>Total Books</h3>
                <div class="value"><?php echo $total_books; ?></div>
            </div>
            
            <div class="card red">
                <h3>Borrow Requests</h3>
                <div class="value"><?php echo $pending_requests; ?></div>
            </div>

  
            <div class="card orange">
                <h3>Return Requests</h3>
                <div class="value"><?php echo $pending_returns; ?></div>
            </div>
            
            <div class="card green">
                <h3>Registered Students</h3>
                <div class="value"><?php echo $total_students; ?></div>
            </div>
        </div>

        <div class="welcome-msg">
            Hello! You are currently managing the PrimeAsia University Library System. Use the sidebar to manage books, borrow requests, and the new student return requests.
        </div>
    </div>

</body>
</html>