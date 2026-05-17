<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { 
    header("Location: ../login.php"); 
    exit(); 
}


if (isset($_GET['approve_id'])) {
    $req_id = (int)$_GET['approve_id']; 
    
    $info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT user_id, book_id FROM borrow_requests WHERE id = $req_id"));
    $u_id = $info['user_id'];
    $b_id = $info['book_id'];

    $approve_sql = "UPDATE borrow_requests SET status = 'accepted', issue_date = CURDATE(), return_date = DATE_ADD(CURDATE(), INTERVAL 7 DAY) WHERE id = $req_id";
    
    if (mysqli_query($conn, $approve_sql)) {
        mysqli_query($conn, "UPDATE books SET stock = stock - 1 WHERE id = $b_id");
        mysqli_query($conn, "UPDATE users SET pending_returns = pending_returns + 1, total_borrowed = total_borrowed + 1 WHERE id = $u_id");
        header("Location: requests.php?msg=approved"); exit();
    }
}


if (isset($_GET['reject_id'])) {
    $req_id = (int)$_GET['reject_id'];
    mysqli_query($conn, "UPDATE borrow_requests SET status = 'rejected' WHERE id = $req_id");
    header("Location: requests.php?msg=rejected"); exit();
}


$sql = "SELECT br.*, u.name as student_name, u.student_id, u.total_borrowed, u.pending_returns, b.title as book_title, b.stock 
        FROM borrow_requests br 
        JOIN users u ON br.user_id = u.id 
        JOIN books b ON br.book_id = b.id 
        WHERE br.status = 'pending' ORDER BY br.id ASC";
$result = mysqli_query($conn, $sql);


$history_sql = "SELECT br.*, u.name as student_name, b.title as book_title FROM borrow_requests br 
                JOIN users u ON br.user_id = u.id JOIN books b ON br.book_id = b.id 
                WHERE br.status IN ('accepted', 'rejected') ORDER BY br.id DESC LIMIT 10";
$history_result = mysqli_query($conn, $history_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow Requests - Admin Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; height: 100vh; background: #f4f7f6; }


        .sidebar { width: 260px; background: #0b1d51; color: white; padding: 25px; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar h2 { font-size: 22px; margin-bottom: 35px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; }
        .sidebar a { display: block; color: #bdc3c7; text-decoration: none; padding: 14px 18px; margin-bottom: 8px; border-radius: 8px; transition: 0.3s; font-size: 15px; }
        .sidebar a:hover { background: rgba(255,255,255,0.1); color: white; }
        .active { background: #d32f2f !important; color: white !important; font-weight: bold; }


        .main-content { flex: 1; padding: 40px; overflow-y: auto; }
        .header { margin-bottom: 30px; }
        .header h1 { color: #0b1d51; font-size: 28px; }


        table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 40px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #f0f0f0; }
        th { background: #0b1d51; color: white; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }
        
        tr:hover { background-color: #f9f9f9; }


        .btn { padding: 8px 14px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: bold; transition: 0.3s; display: inline-block; }
        .btn-approve { background: #27ae60; color: white; margin-right: 5px; }
        .btn-reject { background: #d32f2f; color: white; }
        .btn:hover { opacity: 0.8; transform: translateY(-2px); }


        .stats-tag { font-size: 11px; padding: 3px 8px; border-radius: 4px; background: #ebf5ff; color: #007bff; font-weight: 600; display: inline-block; margin-top: 4px; }
        .status-badge { padding: 5px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .status-accepted { background: #e8f5e9; color: #2e7d32; }
        .status-rejected { background: #ffebee; color: #c62828; }

        .section-title { color: #34495e; font-size: 20px; margin-bottom: 15px; border-left: 5px solid #d32f2f; padding-left: 15px; }
        .msg { padding: 15px; background: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Library Admin</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="add_book.php">Add New Book</a>
        <a href="view_books.php">Book List</a>
        <a href="requests.php" class="active">Borrow Requests</a>
        <a href="return_requests.php">Return Requests</a>
        <a href="student_info.php">Student Info</a>
        
        <div style="margin-top: auto;">
            <a href="../logout.php" style="background: #d32f2f; color: white; text-align: center;">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Borrow Management</h1>
            <p>Review and process book borrow requests</p>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="msg">✔ Request processed successfully and statistics updated!</div>
        <?php endif; ?>

        <h2 class="section-title">Pending Requests</h2>
        <table>
            <thead>
                <tr>
                    <th>Student Info</th>
                    <th>History Stats</th>
                    <th>Book Details</th>
                    <th>In Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><strong><?= $row['student_name'] ?></strong><br><small style="color:#7f8c8d;">ID: <?= $row['student_id'] ?></small></td>
                    <td>
                        <span class="stats-tag">Total: <?= $row['total_borrowed'] ?></span><br>
                        <span class="stats-tag" style="background: <?= ($row['pending_returns'] >= 3) ? '#ffebee; color: #d32f2f' : '#e8f5e9; color: #27ae60' ?>;">
                            Pending: <?= $row['pending_returns'] ?>
                        </span>
                    </td>
                    <td><strong><?= $row['book_title'] ?></strong></td>
                    <td><span style="font-weight: 600; color: <?= ($row['stock'] > 0) ? '#2c3e50' : '#d32f2f' ?>;"><?= $row['stock'] ?> pcs</span></td>
                    <td>
                        <?php if($row['stock'] > 0): ?> 
                            <a href="requests.php?approve_id=<?= $row['id'] ?>" class="btn btn-approve">Approve</a> 
                        <?php else: ?> 
                            <span style="color:#d32f2f; font-size:12px; font-weight:bold;">OUT OF STOCK</span> 
                        <?php endif; ?>
                        <a href="requests.php?reject_id=<?= $row['id'] ?>" class="btn btn-reject" onclick="return confirm('Are you sure you want to reject this request?')">Reject</a>
                    </td>
                </tr>
                <?php endwhile; if(mysqli_num_rows($result) == 0): ?>
                    <tr><td colspan="5" style="text-align: center; color: #95a5a6; padding: 40px;">No pending borrow requests found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h2 class="section-title">Recent History (Last 10)</h2>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Book Title</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($h = mysqli_fetch_assoc($history_result)): ?>
                <tr>
                    <td><?= $h['student_name'] ?></td>
                    <td><?= $h['book_title'] ?></td>
                    <td>
                        <span class="status-badge status-<?= $h['status'] ?>">
                            <?= ucfirst($h['status']) ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>
</html>