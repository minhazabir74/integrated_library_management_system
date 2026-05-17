<?php
session_start();
include '../includes/db.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


if (isset($_GET['return_req_id'])) {
    $req_id = (int)$_GET['return_req_id']; 
    

    $verify = mysqli_query($conn, "SELECT id FROM borrow_requests WHERE id = '$req_id' AND user_id = '$user_id' AND status = 'accepted'");
    
    if (mysqli_num_rows($verify) > 0) {
        $update_sql = "UPDATE borrow_requests SET status = 'return_pending' WHERE id = '$req_id'";
        if (mysqli_query($conn, $update_sql)) {
            header("Location: my_history.php?msg=req_sent");
            exit();
        }
    }
}


$sql = "SELECT br.*, b.title, b.author 
        FROM borrow_requests br 
        JOIN books b ON br.book_id = b.id 
        WHERE br.user_id = '$user_id' 
        ORDER BY br.id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowing History - Primeasia Library</title>
    <style>

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; height: 100vh; background: #f4f7f6; }

        .sidebar { width: 260px; background: #0b1d51; color: white; padding: 25px; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar h2 { font-size: 20px; margin-bottom: 35px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; }
        .sidebar a { display: block; color: #bdc3c7; text-decoration: none; padding: 14px 18px; margin-bottom: 8px; border-radius: 8px; transition: 0.3s; font-size: 15px; }
        .sidebar a:hover { background: rgba(255,255,255,0.1); color: white; }
        .active { background: #d32f2f !important; color: white !important; font-weight: bold; }

        .main-content { flex: 1; padding: 40px; overflow-y: auto; }
        h1 { color: #0b1d51; margin-bottom: 25px; font-size: 26px; }


        .table-container { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #0b1d51; color: white; padding: 15px; text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 15px; border-bottom: 1px solid #f0f2f5; font-size: 14px; color: #334155; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: #fafbfc; }


        .status { padding: 5px 12px; border-radius: 15px; font-size: 10px; font-weight: bold; display: inline-block; text-transform: uppercase; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-accepted { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        .status-return_pending { background: #e2e8f0; color: #475569; }
        .status-completed { background: #d1ecf1; color: #0c5460; }

        .fine-amount { color: #d32f2f; font-weight: 700; }
        .btn-return { background: #d32f2f; color: white; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 11px; font-weight: bold; transition: 0.3s; display: inline-block; }
        .btn-return:hover { background: #b71c1c; box-shadow: 0 4px 10px rgba(211,47,47,0.2); }

        .alert-success { background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #28a745; font-size: 14px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Student Portal</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="browse_books.php">Search & Borrow</a>
        <a href="my_history.php" class="active">Borrowing History</a>
        <a href="profile.php">My Profile</a>
        
        <div style="margin-top: auto;">
            <a href="../logout.php" style="background: #d32f2f; color: white; text-align: center; border-radius: 8px; padding: 10px;">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <h1>Borrowing History</h1>
        
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'req_sent'): ?>
            <div class="alert-success">✔ Return request submitted. Admin will verify the book and clear any fines.</div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Book Information</th>
                        <th>Borrowed On</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Fine Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($row['title']) ?></strong><br>
                                <span style="font-size: 12px; color: #64748b;"><?= htmlspecialchars($row['author']) ?></span>
                            </td>
                            <td><?= $row['issue_date'] ? date('d M, Y', strtotime($row['issue_date'])) : '---' ?></td>
                            <td><?= $row['return_date'] ? date('d M, Y', strtotime($row['return_date'])) : '---' ?></td>
                            <td>
                                <span class="status status-<?= $row['status'] ?>">
                                    <?= str_replace('_', ' ', $row['status']) ?>
                                </span>
                            </td>
                            <td class="fine-amount">
                                <?php 
                                if(in_array($row['status'], ['accepted', 'return_pending']) && $row['return_date']) {
                                    $today = date('Y-m-d');
                                    if($today > $row['return_date']) {
                                        $days_late = floor((strtotime($today) - strtotime($row['return_date'])) / 86400);
                                        echo "৳ " . ($days_late * 10);
                                    } else { echo "৳ 0"; }
                                } else { echo "---"; }
                                ?>
                            </td>
                            <td>
                                <?php if($row['status'] == 'accepted'): ?>
                                    <a href="my_history.php?return_req_id=<?= $row['id'] ?>" class="btn-return" onclick="return confirm('Initiate return request for this book?')">Return Book</a>
                                <?php elseif($row['status'] == 'return_pending'): ?>
                                    <span style="font-size: 11px; color: #94a3b8; font-style: italic;">Verification in progress</span>
                                <?php else: ?>
                                    <span style="color: #cbd5e0;">N/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 50px; color: #94a3b8;">No borrowing records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>