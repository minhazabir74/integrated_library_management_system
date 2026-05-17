<?php
session_start();
include '../includes/db.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";


if (isset($_GET['borrow_id'])) {
    $book_id = (int)$_GET['borrow_id'];


    $check_request = mysqli_query($conn, "SELECT * FROM borrow_requests WHERE user_id = '$user_id' AND book_id = '$book_id' AND status IN ('pending', 'accepted')");
    
    if (mysqli_num_rows($check_request) > 0) {
        $message = "<div style='color: #856404; padding:12px; background:#fff3cd; border:1px solid #ffeeba; border-radius:8px; margin-bottom:25px;'>⚠️ Request failed: You already have this book or a pending request for it!</div>";
    } else {

        $stock_check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stock FROM books WHERE id = $book_id"));
        
        if ($stock_check['stock'] > 0) {
            $query = "INSERT INTO borrow_requests (user_id, book_id, status, request_date) VALUES ('$user_id', '$book_id', 'pending', NOW())";
            if (mysqli_query($conn, $query)) {
                $message = "<div style='color: #155724; padding:12px; background:#d4edda; border:1px solid #c3e6cb; border-radius:8px; margin-bottom:25px;'>✅ Borrow request sent! Please wait for Admin approval.</div>";
            }
        } else {
            $message = "<div style='color: #721c24; padding:12px; background:#f8d7da; border:1px solid #f5c6cb; border-radius:8px; margin-bottom:25px;'>❌ Sorry, this book just went out of stock!</div>";
        }
    }
}


$search_query = "";
if (isset($_POST['search'])) {
    $term = mysqli_real_escape_string($conn, $_POST['search_term']);
    $search_query = " WHERE title LIKE '%$term%' OR author LIKE '%$term%' OR category LIKE '%$term%'";
}

$books = mysqli_query($conn, "SELECT * FROM books $search_query ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search & Borrow - Primeasia Library</title>
    <style>

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; height: 100vh; background: #f4f7f6; }
        
        .sidebar { width: 260px; background: #0b1d51; color: white; padding: 25px; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar h2 { font-size: 20px; margin-bottom: 35px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; }
        .sidebar a { display: block; color: #bdc3c7; text-decoration: none; padding: 14px 18px; margin-bottom: 8px; border-radius: 8px; transition: 0.3s; font-size: 15px; }
        .sidebar a:hover { background: rgba(255,255,255,0.1); color: white; }
        .active { background: #d32f2f !important; color: white !important; font-weight: bold; }

        .main-content { flex: 1; padding: 40px; overflow-y: auto; }
        .header-section { margin-bottom: 30px; }
        .header-section h1 { color: #0b1d51; font-size: 28px; }


        .search-container { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 35px; display: flex; gap: 10px; }
        .search-container input { flex: 1; padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 15px; outline: none; transition: 0.3s; }
        .search-container input:focus { border-color: #0b1d51; box-shadow: 0 0 0 3px rgba(11, 29, 81, 0.1); }
        .btn-search { background: #0b1d51; color: white; border: none; padding: 0 30px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .btn-search:hover { background: #1a3070; }


        .book-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 25px; }
        .book-card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-top: 5px solid #0b1d51; display: flex; flex-direction: column; justify-content: space-between; transition: 0.3s; }
        .book-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        
        .book-info h3 { color: #0b1d51; font-size: 18px; margin-bottom: 12px; height: 50px; overflow: hidden; }
        .book-info p { font-size: 13px; color: #64748b; margin-bottom: 6px; }
        .book-info .cat-tag { display: inline-block; background: #f0f4ff; color: #0b1d51; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; margin-bottom: 15px; text-transform: uppercase; }

        .stock-status { font-size: 13px; font-weight: bold; margin: 15px 0; }
        .in-stock { color: #27ae60; }
        .out-stock { color: #d32f2f; }

        .btn-borrow { display: block; text-align: center; background: #0b1d51; color: white; text-decoration: none; padding: 12px; border-radius: 8px; font-size: 14px; font-weight: bold; transition: 0.3s; }
        .btn-borrow:hover { background: #d32f2f; }
        .btn-disabled { background: #e0e0e0; color: #a0a0a0; pointer-events: none; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Student Portal</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="browse_books.php" class="active">Search & Borrow</a>
        <a href="my_history.php">Borrowing History</a>
        <a href="profile.php">My Profile</a>
        
        <div style="margin-top: auto;">
            <a href="../logout.php" style="background: #d32f2f; color: white; text-align: center; border-radius: 8px; padding: 10px;">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="header-section">
            <h1>Library Catalog</h1>
            <p style="color: #64748b;">Browse our collection and request books for your study.</p>
        </div>


        <?= $message ?>


        <form method="POST" class="search-container">
            <input type="text" name="search_term" placeholder="Search by title, author, or category..." value="<?= isset($_POST['search_term']) ? htmlspecialchars($_POST['search_term']) : '' ?>">
            <button type="submit" name="search" class="btn-search">Search</button>
        </form>

        <div class="book-grid">
            <?php while($row = mysqli_fetch_assoc($books)): ?>
                <div class="book-card">
                    <div class="book-info">
                        <span class="cat-tag"><?= htmlspecialchars($row['category']) ?></span>
                        <h3><?= htmlspecialchars($row['title']) ?></h3>
                        <p><strong>Author:</strong> <?= htmlspecialchars($row['author']) ?></p>
                        
                        <div class="stock-status <?= ($row['stock'] > 0) ? 'in-stock' : 'out-stock' ?>">
                            ● <?= ($row['stock'] > 0) ? "Available (In Stock: ".$row['stock'].")" : "Currently Unavailable" ?>
                        </div>
                    </div>

                    <?php if($row['stock'] > 0): ?>
                        <a href="browse_books.php?borrow_id=<?= $row['id'] ?>" class="btn-borrow" onclick="return confirm('Send borrow request for: <?= addslashes($row['title']) ?>?')">Request Borrow</a>
                    <?php else: ?>
                        <a href="#" class="btn-borrow btn-disabled">Out of Stock</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; if(mysqli_num_rows($books) == 0): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 50px; color: #95a5a6;">
                    No books found matching your search.
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>