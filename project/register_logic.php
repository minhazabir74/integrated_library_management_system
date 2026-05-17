<?php
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $student_id = $_POST['student_id'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    
    $check_query = "SELECT * FROM users WHERE student_id = '$student_id'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        echo "Student ID already exists! Please login.";
    } else {
        
        $sql = "INSERT INTO users (name, student_id, email, password, role) 
                VALUES ('$name', '$student_id', '$email', '$password', 'user')";

        if (mysqli_query($conn, $sql)) {
            
            header("Location: login.php?registration=success");
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
}
?>