<?php
session_start();
include '../includes/db.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') { 
    header("Location: ../login.php"); 
    exit(); 
}

$user_id = $_SESSION['user_id'];


$user_res = mysqli_query($conn, "SELECT name, student_id, total_borrowed, total_returned, total_fine_paid FROM users WHERE id = '$user_id'");
$user_data = mysqli_fetch_assoc($user_res);


$my_issued = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM borrow_requests WHERE user_id = '$user_id' AND status = 'accepted'"))['total'];
$my_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM borrow_requests WHERE user_id = '$user_id' AND status = 'pending'"))['total'];
$avail_books = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM books WHERE stock > 0"))['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Primeasia Library</title>
    <style>

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; height: 100vh; background: #f4f7f6; }


        .sidebar { width: 260px; background: #0b1d51; color: white; padding: 25px; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar h2 { font-size: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; margin-bottom: 30px; }
        .sidebar a { display: block; color: #bdc3c7; text-decoration: none; padding: 14px 18px; margin-bottom: 8px; border-radius: 8px; transition: 0.3s; font-size: 15px; }
        .sidebar a:hover { background: rgba(255,255,255,0.1); color: white; }
        .active { background: #d32f2f !important; color: white !important; font-weight: bold; }

        .main-content { flex: 1; padding: 40px; overflow-y: auto; }
        

        .header { background: white; padding: 30px; border-radius: 15px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 35px; border-top: 5px solid #0b1d51; }
        .header h1 { color: #0b1d51; font-size: 26px; }
        .header p { color: #64748b; font-size: 14px; font-weight: 500; }


        .section-title { margin-bottom: 20px; color: #0b1d51; font-size: 18px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 40px; }
        
        .stat-card { background: white; padding: 25px; border-radius: 12px; border-left: 5px solid #0b1d51; box-shadow: 0 4px 12px rgba(0,0,0,0.04); transition: 0.3s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
        .stat-card.red { border-left-color: #d32f2f; }
        .stat-card.green { border-left-color: #27ae60; }
        .stat-card.orange { border-left-color: #f39c12; }
        
        .stat-card h3 { font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .stat-card p { font-size: 28px; font-weight: bold; color: #1e293b; }
        .stat-card .unit { font-size: 14px; color: #64748b; font-weight: 500; margin-left: 5px; }


        .action-box { background: linear-gradient(135deg, #0b1d51 0%, #1a3070 100%); color: white; padding: 40px; border-radius: 20px; text-align: center; box-shadow: 0 10px 25px rgba(11, 29, 81, 0.2); }
        .action-box h2 { font-size: 24px; margin-bottom: 10px; }
        .action-box p { color: #cbd5e0; margin-bottom: 25px; }
        .btn-browse { background: #d32f2f; color: white; padding: 12px 35px; border-radius: 8px; text-decoration: none; font-weight: bold; transition: 0.3s; display: inline-block; }
        .btn-browse:hover { background: #b71c1c; transform: scale(1.05); }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Student Portal</h2>
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="browse_books.php">Search & Borrow</a>
        <a href="my_history.php">Borrowing History</a>
        <a href="profile.php">My Profile</a>
        
        <div style="margin-top: auto;">
            <a href="../logout.php" style="background: #d32f2f; color: white; text-align: center; border-radius: 8px; padding: 10px;">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <div>
                <h1>Welcome Back, <?= htmlspecialchars($user_data['name']) ?></h1>
                <p>Primeasia University Student | ID: <?= htmlspecialchars($user_data['student_id']) ?></p>
            </div>
            <!-- Dynamic Initial Icon if Logo missing -->
            <div style="width: 50px; height: 50px; background: #0b1d51; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                <?= substr($user_data['name'], 0, 1) ?>
            </div>
        </div>

        <div class="section-title"> Account Summary</div>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Lifetime Borrowed</h3>
                <p><?= $user_data['total_borrowed'] ?><span class="unit">Books</span></p>
            </div>
            <div class="stat-card green">
                <h3>Successfully Returned</h3>
                <p><?= $user_data['total_returned'] ?><span class="unit">Books</span></p>
            </div>
            <div class="stat-card orange">
                <h3>Total Fine Paid</h3>
                <p><span class="unit">৳</span> <?= number_format($user_data['total_fine_paid'], 2) ?></p>
            </div>
        </div>

        <div class="section-title"> Library Real-time Status</div>
        <div class="stats-grid">
            <div class="stat-card" style="border-left-color: #3498db;">
                <h3>Currently with you</h3>
                <p><?= $my_issued ?><span class="unit">Active</span></p>
            </div>
            <div class="stat-card" style="border-left-color: #9b59b6;">
                <h3>Pending Requests</h3>
                <p><?= $my_pending ?><span class="unit">Awaiting</span></p>
            </div>
            <div class="stat-card red">
                <h3>Library Stock</h3>
                <p><?= $avail_books ?><span class="unit">Available</span></p>
            </div>
        </div>

        <div class="action-box">
            <h2>Ready for your next read?</h2>
            <p>Search through thousands of titles and manage your borrows effortlessly.</p>
            <a href="browse_books.php" class="btn-browse">Explore Catalog</a>
        </div>
    </div>

</body>
</html>