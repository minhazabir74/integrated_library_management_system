<?php
session_start();
include '../includes/db.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}


if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM books WHERE id = $id");
    header("Location: view_books.php?msg=deleted");
}

$result = mysqli_query($conn, "SELECT * FROM books ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management - Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; height: 100vh; background: #f4f7f6; }


        .sidebar { width: 260px; background: #0b1d51; color: white; padding: 25px; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar h2 { font-size: 22px; margin-bottom: 35px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; }
        .sidebar a { display: block; color: #bdc3c7; text-decoration: none; padding: 14px 18px; margin-bottom: 8px; border-radius: 8px; transition: 0.3s; font-size: 15px; }
        .sidebar a:hover { background: rgba(255,255,255,0.1); color: white; }
        .active { background: #d32f2f !important; color: white !important; font-weight: bold; }

        .main-content { flex: 1; padding: 40px; overflow-y: auto; }
        h1 { color: #0b1d51; margin-bottom: 30px; }


        table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #0b1d51; color: white; text-transform: uppercase; font-size: 13px; letter-spacing: 0.5px; }
        tr:hover { background: #f9f9f9; }
        

        .stock-badge { padding: 6px 12px; border-radius: 20px; font-weight: bold; font-size: 11px; text-transform: uppercase; display: inline-block; }
        .stock-green { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .stock-red { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
        
        .btn-del { color: #d32f2f; text-decoration: none; font-weight: bold; padding: 6px 12px; border: 2px solid #d32f2f; border-radius: 6px; font-size: 12px; transition: 0.3s; }
        .btn-del:hover { background: #d32f2f; color: white; }

        .msg-alert { padding: 12px; background: #e3f2fd; color: #1976d2; border-radius: 8px; margin-bottom: 20px; font-weight: 600; font-size: 14px; }
    </style>
</head>
<body>


    <div class="sidebar">
        <h2>Library Admin</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="add_book.php">Add New Book</a>
        <a href="view_books.php" class="active">Book List</a>
        <a href="requests.php">Borrow Requests</a>
        <a href="return_requests.php">Return Requests</a>
        <a href="student_info.php">Student Info</a>
        
        <div style="margin-top: auto;">
            <a href="../logout.php" style="background: #d32f2f; color: white; text-align: center;">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <h1>Library Inventory</h1>

        <?php if(isset($_GET['msg'])) echo "<div class='msg-alert'>✔ Book removed from inventory successfully.</div>"; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Book Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Stock Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): 
                    $stock_class = ($row['stock'] > 0) ? 'stock-green' : 'stock-red';
                    $stock_text = ($row['stock'] > 0) ? $row['stock'] . " Available" : "Out of Stock";
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><strong><?php echo $row['title']; ?></strong></td>
                    <td><?php echo $row['author']; ?></td>
                    <td style="color: #7f8c8d; font-size: 14px;"><?php echo $row['category']; ?></td>
                    <td><span class="stock-badge <?php echo $stock_class; ?>"><?php echo $stock_text; ?></span></td>
                    <td>
                        <a href="view_books.php?delete=<?php echo $row['id']; ?>" class="btn-del" onclick="return confirm('Are you sure you want to delete this book from the records?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>
</html>