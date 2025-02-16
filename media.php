<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
  header('location:login.php');
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>View Media</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your CSS file -->
    <style>
        .media-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px; 
        }
        .media-item {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
            width: 50%;
            text-align: center;
        }
        .media-item img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
             object-fit: cover;
        }
        .media-item h4 {
            margin: 10px 0;
        }
        .media-item audio, .media-item video {
            width: 100%;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php include 'user_header.php'; ?> 

    <div class="media-container">
        <?php
            // Fetch media files from the database
            $sql = "SELECT * FROM media_files";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $filePath = htmlspecialchars($row['file_path']);
                    $thumbnailPath = htmlspecialchars($row['thumbnail_path']);
                    $title = htmlspecialchars($row['title']);
                    $fileType = $row['file_type'];

                    echo "
                    <div class='media-item'>
                        <h4>$title</h4>
                        <img src='$thumbnailPath' alt='Thumbnail'>
                    ";

                    if ($fileType == 'audio') {
                        echo "<audio controls>
                                <source src='$filePath' type='audio/mp3'>
                                Your browser does not support the audio element.
                            </audio>";
                    } else if ($fileType == 'video') {
                        echo "<video controls width='100%'>
                                <source src='$filePath' type='video/mp4'>
                                Your browser does not support the video element.
                            </video>";
                    }

                    echo "</div>";
                }
            } else {
                echo "<p>No media files found.</p>";
            }

            // Close the database connection
            $conn->close();
        ?>
    </div>
    <script src="https://kit.fontawesome.com/eedbcd0c96.js" crossorigin="anonymous"></script>
    <script src="script.js"></script>
    <?php include 'footer.php';?>
</body>
</html>