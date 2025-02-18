<?php
include 'config.php';

if(isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $cpassword = mysqli_real_escape_string($conn, $_POST['cpassword']);
    $user_type = $_POST['user_type'];

    // Check if email already exists
    $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email='$email'") or die('Query failed');

    if(mysqli_num_rows($select_users) > 0){
        $message[] = 'User already exists!';
    } else {
        // Check if passwords match
        if ($password !== $cpassword) {
            $message[] = 'Confirm password does not match!';
        } else {
            // Hash the password before inserting
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            // Insert user data into `users` table
            $insert_query = "INSERT INTO `users` (name, email, password, role) VALUES ('$name', '$email', '$hashed_password', '$user_type')";
            mysqli_query($conn, $insert_query) or die('Query failed');

            $message[] = 'Registered Successfully!';
            header('location:login.php');
            exit(); 
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Form</title>

    <!-- Font awesome Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">

    <!-- Css File Link -->
    <link rel="stylesheet" href="login.css">
</head>

<body>

<?php
if(isset($message)){
    foreach($message as $msg){
        echo '
        <div class="message">
            <span>'.$msg.'</span>
            <i class="fa-solid fa-xmark" onclick="this.parentElement.remove();"></i>
        </div>
    ';    
    } 
}
?>

<div class="box">
    <span class="borderline"></span>
    <form action="" method="post">
        <h2>Register</h2>

        <div class="inputbox">
            <input type="text" name="name" required>
            <span>Name</span>
            <i></i>
        </div>

        <div class="inputbox">
            <input type="email" name="email" required>
            <span>Email</span>
            <i></i>
        </div>

        <div class="inputbox">
            <input type="password" name="password" required>
            <span>Password</span>
            <i></i>
        </div>

        <div class="inputbox">
            <input type="password" name="cpassword" required>
            <span>Confirm Password</span>
            <i></i>
        </div>
        
        <div class="inputbox">
            <select name="user_type">
                <option value="student">Student</option>
                <option value="admin">Admin</option>
            </select>
        <i></i>
        </div>
        
        <div class="links">
            <a href="#">Forgot Password</a>
            <a href="login.php">Login</a>
        </div>

        <input type="submit" value="Sign Up" name="submit">
    </form>
</div>

<script src="https://kit.fontawesome.com/eedbcd0c96.js" crossorigin="anonymous"></script>
</body>
</html>
