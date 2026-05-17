<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { 
    header("Location: ../login.php"); 
    exit(); 
}


if (isset($_GET['approve_id']) || isset($_GET['lost_id'])) {
    $is_lost = isset($_GET['lost_id']);
    $req_id = $is_lost ? (int)$_GET['lost_id'] : (int)$_GET['approve_id'];

    $info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT book_id, user_id, return_date FROM borrow_requests WHERE id = $req_id"));
    $b_id = $info['book_id'];
    $u_id = $info['user_id'];
    

    $fine = 0;
    $today = date('Y-m-d');
    if($today > $info['return_date']) {
        $diff = floor((strtotime($today) - strtotime($info['return_date'])) / 86400);
        $fine = $diff * 10;
    }

    if ($is_lost) {
        $fine += 500; // Lost penalty
        $status = 'lost';
    } else {
        $status = 'completed';
    }


    if (mysqli_query($conn, "UPDATE borrow_requests SET status = '$status', fine_amount = $fine WHERE id = $req_id")) {
        

        if (!$is_lost) {
            mysqli_query($conn, "UPDATE books SET stock = stock + 1 WHERE id = $b_id");
        }


        mysqli_query($conn, "UPDATE users SET total_returned = total_returned + 1, pending_returns = GREATEST(0, pending_returns - 1), total_fine_paid = total_fine_paid + $fine WHERE id = $u_id");
        
        header("Location: return_requests.php?msg=" . ($is_lost ? "lost" : "success")); exit();
    }
}


$requests = mysqli_query($conn, "SELECT br.*, u.name as student_name, u.student_id, b.title as book_title FROM borrow_requests br JOIN users u ON br.user_id = u.id JOIN books b ON br.book_id = b.id WHERE br.status = 'return_pending' ORDER BY br.id ASC");


$history = mysqli_query($conn, "SELECT br.*, u.name as student_name, b.title as book_title FROM borrow_requests br JOIN users u ON br.user_id = u.id JOIN books b ON br.book_id = b.id WHERE br.status IN ('completed', 'lost') ORDER BY br.id DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Management - Admin Dashboard</title>
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
        .btn-lost { background: #d32f2f; color: white; }
        .btn:hover { opacity: 0.8; transform: translateY(-2px); }

    
        .fine-badge { padding: 5px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .fine-none { background: #e8f5e9; color: #2e7d32; }
        .fine-due { background: #ffebee; color: #c62828; }

        .section-title { color: #34495e; font-size: 20px; margin-bottom: 15px; border-left: 5px solid #27ae60; padding-left: 15px; }
        .msg { padding: 15px; background: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Library Admin</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="add_book.php">Add New Book</a>
        <a href="view_books.php">Book List</a>
        <a href="requests.php">Borrow Requests</a>
        <a href="return_requests.php" class="active">Return Requests</a>
        <a href="student_info.php">Student Info</a>
        
        <div style="margin-top: auto;">
            <a href="../logout.php" style="background: #d32f2f; color: white; text-align: center; border-radius: 8px; padding: 10px;">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Return Management</h1>
            <p>Approve returns or mark lost books with penalty</p>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="msg">✔ <?= ($_GET['msg'] == 'lost') ? "Book marked as Lost. Penalty applied." : "Return processed successfully!" ?></div>
        <?php endif; ?>

        <h2 class="section-title">Pending Return Requests</h2>
        <table>
            <thead>
                <tr>
                    <th>Student Info</th>
                    <th>Book Details</th>
                    <th>Due Date</th>
                    <th>Current Fine</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($r = mysqli_fetch_assoc($requests)): 
                    $curr_fine = 0; $today = date('Y-m-d');
                    if($today > $r['return_date']) {
                        $curr_fine = floor((strtotime($today) - strtotime($r['return_date'])) / 86400) * 10;
                    }
                ?>
                <tr>
                    <td><strong><?= $r['student_name'] ?></strong><br><small style="color:#7f8c8d;">ID: <?= $r['student_id'] ?></small></td>
                    <td><strong><?= $r['book_title'] ?></strong></td>
                    <td><?= date('d M, Y', strtotime($r['return_date'])) ?></td>
                    <td>
                        <span class="fine-badge <?= ($curr_fine > 0) ? 'fine-due' : 'fine-none' ?>">
                            <?= ($curr_fine > 0) ? "Tk $curr_fine" : "No Fine" ?>
                        </span>
                    </td>
                    <td>
                        <a href="return_requests.php?approve_id=<?= $r['id'] ?>" class="btn btn-approve">Approve</a>
                        <a href="return_requests.php?lost_id=<?= $r['id'] ?>" class="btn btn-lost" onclick="return confirm('Mark as Lost? (Tk 500 Penalty + Late Fine)')">Mark Lost</a>
                    </td>
                </tr>
                <?php endwhile; if(mysqli_num_rows($requests) == 0): ?>
                    <tr><td colspan="5" style="text-align: center; color: #95a5a6; padding: 40px;">No pending return requests.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h2 class="section-title">History (Last 10 Returns)</h2>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Book Title</th>
                    <th>Fine Paid</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($h = mysqli_fetch_assoc($history)): ?>
                <tr>
                    <td><?= $h['student_name'] ?></td>
                    <td><?= $h['book_title'] ?></td>
                    <td><strong>Tk <?= number_format($h['fine_amount'], 2) ?></strong></td>
                    <td>
                        <span style="font-weight:bold; color: <?= ($h['status'] == 'lost') ? '#d32f2f' : '#27ae60' ?>; font-size:12px; text-transform:uppercase;">
                            ● <?= $h['status'] ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>
</html>