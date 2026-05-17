<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}


if (isset($_GET['delete_id'])) {
    $del_id = (int)$_GET['delete_id'];
    $check_q = mysqli_query($conn, "SELECT pending_returns FROM users WHERE id = $del_id");
    $user_data = mysqli_fetch_assoc($check_q);

    if ($user_data['pending_returns'] > 0) {
        header("Location: student_info.php?error=has_books");
    } else {
        mysqli_query($conn, "DELETE FROM users WHERE id = $del_id");
        header("Location: student_info.php?msg=deleted");
    }
    exit();
}


$search_query = "";
$where_clause = "WHERE role = 'user'";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_clause .= " AND (name LIKE '%$search%' OR student_id LIKE '%$search%')";
    $search_query = $search;
}

$query = "SELECT id, name, student_id, email, created_at, pending_returns, total_returned, total_fine_paid 
          FROM users $where_clause ORDER BY id ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Info - Admin Dashboard</title>
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


        .search-box { display: flex; gap: 10px; margin-bottom: 25px; }
        .search-box input { flex: 1; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; }
        .btn-search { background: #0b1d51; color: white; border: none; padding: 0 25px; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        .btn-search:hover { opacity: 0.9; }


        table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #f0f0f0; }
        th { background: #0b1d51; color: white; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }
        tr:hover { background-color: #f9f9f9; }

        .id-badge { background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 13px; font-family: monospace; }
        
        .status-pill { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; display: inline-block; }
        .has-books { background: #fff3e0; color: #e65100; border: 1px solid #ffe0b2; }
        .clear { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }

        .btn-delete { color: #d32f2f; text-decoration: none; font-weight: bold; font-size: 12px; border: 1px solid #d32f2f; padding: 6px 12px; border-radius: 6px; transition: 0.3s; }
        .btn-delete:hover { background: #d32f2f; color: white; }

        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; font-size: 14px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-danger { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Library Admin</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="add_book.php">Add New Book</a>
        <a href="view_books.php">Book List</a>
        <a href="requests.php">Borrow Requests</a>
        <a href="return_requests.php">Return Requests</a>
        <a href="student_info.php" class="active">Student Info</a>
        
        <div style="margin-top: auto;">
            <a href="../logout.php" style="background: #d32f2f; color: white; text-align: center;">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Student Info</h1>
            <p>Manage and search registered student details</p>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success">✔ Student record deleted successfully.</div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger">✖ Cannot delete student with pending returns.</div>
        <?php endif; ?>

        <form class="search-box" method="GET">
            <input type="text" name="search" placeholder="Search by Name or Student ID..." value="<?= htmlspecialchars($search_query) ?>">
            <button type="submit" class="btn-search">Search</button>
            <?php if($search_query): ?>
                <a href="student_info.php" style="padding: 12px; color: #d32f2f; text-decoration: none; font-weight: bold;">Clear</a>
            <?php endif; ?>
        </form>
        
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Student ID</th>
                    <th>Books Held</th>
                    <th>Fine Paid</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): 
                    $held = $row['pending_returns'];
                ?>
                <tr>
                    <td><strong><?= $row['name'] ?></strong><br><small style="color: #7f8c8d;"><?= $row['email'] ?></small></td>
                    <td><span class="id-badge"><?= $row['student_id'] ?></span></td>
                    <td>
                        <span class="status-pill <?= ($held > 0) ? 'has-books' : 'clear' ?>">
                            <?= $held ?> Pending
                        </span>
                    </td>
                    <td style="font-weight: bold; color: #0b1d51;">৳ <?= number_format($row['total_fine_paid'], 2) ?></td>
                    <td>
                        <a href="student_info.php?delete_id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Permanently delete this student?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; if(mysqli_num_rows($result) == 0) echo "<tr><td colspan='5' style='text-align:center; padding:30px; color:#999;'>No students found.</td></tr>"; ?>
            </tbody>
        </table>
    </div>

</body>
</html>