<?php
include 'config.php';
session_start();

$admin_id=$_SESSION['admin_id'];

if(!isset($admin_id)){
  header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Page</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <link rel="stylesheet" href="admin.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
include 'admin_header.php';
?>

<section class="admin_dashboard">

  <div class="admin_box_container">
    <div class="admin_box">
      <?php
        $select_products=mysqli_query($conn,"SELECT * FROM `products`") or die('query failed');
        $number_of_products=mysqli_num_rows($select_products);
      ?>
      <h3><?php echo $number_of_products; ?></h3>
      <p>Book Added</p>
    </div>

    <div class="admin_box">
      <?php
        $select_products=mysqli_query($conn,"SELECT * FROM `media_files`") or die('query failed');
        $number_of_products=mysqli_num_rows($select_products);
      ?>
      <h3><?php echo $number_of_products; ?></h3>
      <p>Media Added</p>
    </div>

    <div class="admin_box">
      <?php
        $select_users=mysqli_query($conn,"SELECT * FROM `users` WHERE role='student'") or die('query failed');
        $number_of_users=mysqli_num_rows($select_users);
      ?>
      <h3><?php echo $number_of_users; ?></h3>
      <p>User Present</p>
    </div>

    <div class="admin_box">
      <?php
        $select_admin=mysqli_query($conn,"SELECT * FROM `users` WHERE role='admin'") or die('query failed');
        $number_of_admin=mysqli_num_rows($select_admin);
      ?>
      <h3><?php echo $number_of_admin; ?></h3>
      <p>Admin Present</p>
    </div>

    <div class="admin_box">
      <?php
        $select_accounts=mysqli_query($conn,"SELECT * FROM `users`") or die('query failed');
        $number_of_accounts=mysqli_num_rows($select_accounts);
      ?>
      <h3><?php echo $number_of_accounts; ?></h3>
      <p>Total Accounts</p>
    </div>

    <div class="admin_box">
      <?php
        $select_messages=mysqli_query($conn,"SELECT * FROM `message`") or die('query failed');
        $number_of_messages=mysqli_num_rows($select_messages);
      ?>
      <h3><?php echo $number_of_messages; ?></h3>
      <p>New Messages</p>
    </div>

  </div>

</section>


<script src="admin_js.js"></script>
<script src="https://kit.fontawesome.com/eedbcd0c96.js" crossorigin="anonymous"></script>

</body>
</html>