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
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        padding: 20px;
        width: 100%;
    }
    
    .media-item {
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s;
        width: 100%;
    }
    
    .media-item:hover {
        transform: translateY(-5px);
    }
    
    .media-thumbnail {
        position: relative;
        width: 100%;
        padding-top: 56.25%; /* 16:9 aspect ratio */
        overflow: hidden;
    }
    
    .media-thumbnail img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .media-info {
        padding: 12px;
    }
    
    .media-info h4 {
        margin: 0 0 8px 0;
        font-size: 14px;
        color: #333;
        line-height: 1.4;
        height: 40px;
        overflow: hidden;
    }
    
    .media-player {
        width: 100%;
        margin-top: 10px;
    }
    
    video {
        background: #000;
    }
    
    audio {
        width: 100%;
        height: 40px;
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
                        <div class='media-thumbnail'>
                            <img src='$thumbnailPath' alt='Thumbnail'>
                        </div>
                        <div class='media-info'>
                            <h4>$title</h4>
                    ";
                    
                    if ($fileType == 'audio') {
                        echo "<audio class='media-player' controls>
                                <source src='$filePath' type='audio/mp3'>
                            </audio>";
                    } else if ($fileType == 'video') {
                        echo "<video class='media-player' controls>
                                <source src='$filePath' type='video/mp4'>
                            </video>";
                    }
                    
                    echo "</div></div>";
                }
            } else {
                echo "<p>No media files found.</p>";
            }

            // Close the database connection
            $conn->close();
        ?>
    </div>




<?php include 'footer.php';?>


    <script src="https://kit.fontawesome.com/eedbcd0c96.js" crossorigin="anonymous"></script>
    <script src="script.js">
        document.addEventListener("DOMContentLoaded", () => {
            const mediaItems = document.querySelectorAll(".media-item");

            mediaItems.forEach((item) => {
                const video = item.querySelector("video");
                const thumbnail = item.querySelector("img");

                if (video) {
                    // Hide the thumbnail when hovering
                    item.addEventListener("mouseenter", () => {
                        thumbnail.style.opacity = "0"; // Hide thumbnail
                        thumbnail.style.pointerEvents = "none"; // Allow video interactions
                        video.play();
                    });

                    // Show the thumbnail when mouse leaves (if video is not playing)
                    item.addEventListener("mouseleave", () => {
                        if (video.paused) {
                            thumbnail.style.opacity = "1"; // Show thumbnail
                            thumbnail.style.pointerEvents = "auto"; // Block video interactions
                        }
                    });

                    // Ensure video remains interactive when played
                    video.addEventListener("play", () => {
                        thumbnail.style.opacity = "0"; // Keep thumbnail hidden
                        thumbnail.style.pointerEvents = "none"; // Allow video interactions
                    });

                    // Show thumbnail when video is paused/stopped
                    video.addEventListener("pause", () => {
                        thumbnail.style.opacity = "1"; // Show thumbnail
                        thumbnail.style.pointerEvents = "auto"; // Block video interactions
                    });

                    video.addEventListener("ended", () => {
                        thumbnail.style.opacity = "1"; // Show thumbnail
                        thumbnail.style.pointerEvents = "auto"; // Block video interactions
                    });
                }
            });
        });



    </script>
    
</body>
</html>