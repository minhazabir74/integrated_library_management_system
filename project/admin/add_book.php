<?php
session_start();
include '../includes/db.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $stock = (int)$_POST['stock'];

    $sql = "INSERT INTO books (title, author, category, stock) VALUES ('$title', '$author', '$category', '$stock')";
    if (mysqli_query($conn, $sql)) {
        $message = "<div class='msg success'>✔ Book Added Successfully!</div>";
    } else {
        $message = "<div class='msg error'>✘ Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Book - Admin</title>
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


        .form-card { background: white; padding: 35px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); max-width: 600px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 10px; font-weight: 600; color: #34495e; font-size: 14px; }
        .form-group input, .form-group select { width: 100%; padding: 12px 15px; border: 1px solid #dfe6e9; border-radius: 8px; font-size: 15px; transition: 0.3s; }
        .form-group input:focus, .form-group select:focus { border-color: #0b1d51; outline: none; box-shadow: 0 0 0 3px rgba(11, 29, 81, 0.1); }
        
        .btn-submit { background: #27ae60; color: white; border: none; padding: 14px; border-radius: 8px; cursor: pointer; width: 100%; font-weight: bold; font-size: 16px; transition: 0.3s; margin-top: 10px; }
        .btn-submit:hover { background: #219150; transform: translateY(-2px); box-shadow: 0 5px 10px rgba(39, 174, 96, 0.2); }


        .msg { padding: 15px; border-radius: 8px; margin-bottom: 25px; font-size: 14px; font-weight: 600; }
        .success { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .error { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Library Admin</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="add_book.php" class="active">Add New Book</a>
        <a href="view_books.php">Book List</a>
        <a href="requests.php">Borrow Requests</a>
        <a href="return_requests.php">Return Requests</a>
        <a href="student_info.php">Student Info</a>
        
        <div style="margin-top: auto;">
            <a href="../logout.php" style="background: #d32f2f; color: white; text-align: center;">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <h1>Add New Book to Inventory</h1>
        
        <div class="form-card">
            <?php echo $message; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Book Title</label>
                    <input type="text" name="title" placeholder="e.g. Database Systems" required>
                </div>
                
                <div class="form-group">
                    <label>Author Name</label>
                    <input type="text" name="author" placeholder="e.g. Navathe" required>
                </div>
                
                <div class="form-group">
                    <label>Category</label>
                    <select name="category">
                        <option>Computer Science</option>
                        <option>Business</option>
                        <option>Engineering</option>
                        <option>Arts</option>
                        <option>Mathematics</option>
                        <option>Literature</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Initial Stock Count</label>
                    <input type="number" name="stock" value="1" min="1" required>
                </div>
                
                <button type="submit" class="btn-submit">Add to Inventory</button>
            </form>
        </div>
    </div>

</body>
</html>