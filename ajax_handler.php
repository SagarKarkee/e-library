<?php
include 'config.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'login':
            // Login handler
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $password = $_POST['password'];

            $result = mysqli_query($conn, "SELECT * FROM `users` WHERE email='$email'");
            if(mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    echo json_encode(['status' => 'success', 'redirect' => ($user['role'] == 'admin') ? 'admin_page.php' : 'home.php']);
                    exit;
                }
            }
            echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
            break;

        case 'register':
            // Registration handler
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $user_type = $_POST['user_type'];

            if (mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users WHERE email='$email'")) > 0) {
                echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
                exit;
            }

            mysqli_query($conn, "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$user_type')");
            echo json_encode(['status' => 'success', 'message' => 'Registration successful', 'redirect' => 'login.php']);
            break;

        case 'add_to_cart':
            // Add to cart handler
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Please login first']);
                exit;
            }

            $user_id = $_SESSION['user_id'];
            $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
            $product_price = (int)$_POST['product_price'];
            $product_quantity = (int)$_POST['product_quantity'];
            $product_image = mysqli_real_escape_string($conn, $_POST['product_image']);

            // Check if product exists in cart
            $check = mysqli_query($conn, "SELECT * FROM cart WHERE user_id='$user_id' AND name='$product_name'");
            if (mysqli_num_rows($check) > 0) {
                echo json_encode(['status' => 'error', 'message' => 'Product already in cart']);
                exit;
            }

            mysqli_query($conn, "INSERT INTO cart (user_id, name, price, quantity, image) VALUES ('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')");
            echo json_encode(['status' => 'success', 'message' => 'Product added to cart']);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
}