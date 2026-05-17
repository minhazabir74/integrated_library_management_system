<?php
session_start();
include '../includes/db.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


$user_res = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($user_res);


$borrowed_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM borrow_requests WHERE user_id = '$user_id' AND status = 'accepted'");
$active_borrows = mysqli_fetch_assoc($borrowed_res)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Primeasia Library</title>
    <style>

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; height: 100vh; background: #f4f7f6; }

        .sidebar { width: 260px; background: #0b1d51; color: white; padding: 25px; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar h2 { font-size: 20px; margin-bottom: 35px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; }
        .sidebar a { display: block; color: #bdc3c7; text-decoration: none; padding: 14px 18px; margin-bottom: 8px; border-radius: 8px; transition: 0.3s; font-size: 15px; }
        .sidebar a:hover { background: rgba(255,255,255,0.1); color: white; }
        .active { background: #d32f2f !important; color: white !important; font-weight: bold; }

        .main-content { flex: 1; padding: 40px; display: flex; align-items: center; justify-content: center; overflow-y: auto; }
        

        .profile-card { background: white; width: 100%; max-width: 550px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        .profile-header { background: #0b1d51; height: 120px; position: relative; }
        .profile-img { 
            width: 110px; height: 110px; background: #d32f2f; color: white; 
            border-radius: 50%; display: flex; align-items: center; justify-content: center; 
            font-size: 45px; font-weight: bold; border: 5px solid white; 
            position: absolute; left: 50%; bottom: -55px; transform: translateX(-50%);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .profile-body { padding: 70px 40px 40px; text-align: center; }
        .profile-body h2 { color: #0b1d51; font-size: 26px; margin-bottom: 5px; }
        .student-tag { background: #e3f2fd; color: #1976d2; padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: bold; display: inline-block; margin-bottom: 25px; }

        .info-grid { text-align: left; border-top: 1px solid #eee; padding-top: 25px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .label { color: #64748b; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 5px; }
        .value { color: #1e293b; font-size: 15px; font-weight: 600; }

        .stats-banner { margin-top: 30px; display: flex; gap: 12px; }
        .stat-box { flex: 1; background: #f8fafc; padding: 15px 10px; border-radius: 12px; border-bottom: 3px solid #0b1d51; text-align: center; }
        .stat-box .num { display: block; font-size: 18px; font-weight: bold; color: #0b1d51; }
        .stat-box .txt { font-size: 10px; color: #64748b; font-weight: bold; text-transform: uppercase; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Student Portal</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="browse_books.php">Search & Borrow</a>
        <a href="my_history.php">Borrowing History</a>
        <a href="profile.php" class="active">My Profile</a>
        
        <div style="margin-top: auto;">
            <a href="../logout.php" style="background: #d32f2f; color: white; text-align: center; padding: 10px; border-radius: 8px;">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-img"><?= substr($user['name'], 0, 1) ?></div>
            </div>
            
            <div class="profile-body">
                <h2><?= htmlspecialchars($user['name']) ?></h2>
                <span class="student-tag">Primeasia University Student</span>

                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Student ID</span>
                        <span class="value"><?= htmlspecialchars($user['student_id']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Email Address</span>
                        <span class="value" style="font-size: 13px;"><?= htmlspecialchars($user['email']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Member Since</span>
                        <span class="value"><?= date('d M, Y', strtotime($user['created_at'])) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Total Borrowed</span>
                        <span class="value"><?= $user['total_borrowed'] ?> Books</span>
                    </div>
                </div>

                <div class="stats-banner">
                    <div class="stat-box" style="border-bottom-color: <?= ($active_borrows > 0) ? '#f39c12' : '#27ae60' ?>;">
                        <span class="num"><?= $active_borrows ?></span>
                        <span class="txt">Pending Returns</span>
                    </div>
                    <div class="stat-box" style="border-bottom-color: #d32f2f;">
                        <span class="num">৳ <?= number_format($user['total_fine_paid'], 2) ?></span>
                        <span class="txt">Total Fine Paid</span>
                    </div>
                    <div class="stat-box" style="border-bottom-color: #27ae60;">
                        <span class="num"><?= $user['total_returned'] ?></span>
                        <span class="txt">Total Returned</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>