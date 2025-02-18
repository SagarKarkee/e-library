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
    <link rel="stylesheet" href="style.css"> 
    <style>
    .media-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        padding: 20px;
    }

    .media-item {
        border-radius: 8px;
        overflow: hidden;
        background: #000;
        position: relative;
        cursor: pointer;
        aspect-ratio: 16/9;
    }

    .media-thumbnail {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: opacity 0.3s ease;
    }

    .media-player {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        transition: opacity 0.3s ease;
        background: #000;
    }

    .media-item:hover .media-thumbnail {
        opacity: 0;
    }

    .media-item:hover .media-player {
        opacity: 1;
    }

    /* Show video when playing (even if not hovering) */
    .media-player.playing {
        opacity: 1;
        z-index: 2;
    }

    .media-info {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 10px;
        background: linear-gradient(transparent, rgba(0,0,0,0.7));
        color: white;
        z-index: 3;
    }

    video.media-player {
        object-fit: cover;
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
                        <img class='media-thumbnail' src='$thumbnailPath' alt='Thumbnail'>
                        
                        <div class='media-info'>
                            <h4>$title</h4>
                        </div>";
                    
                    if ($fileType == 'audio') {
                        echo "<audio class='media-player' controls>
                                <source src='$filePath' type='audio/mp3'>
                              </audio>";
                    } else if ($fileType == 'video') {
                        echo "<video class='media-player' muted controls>
                                <source src='$filePath' type='video/mp4'>
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




<?php include 'footer.php';?>
<script src="script.js"></script>

    <script src="https://kit.fontawesome.com/eedbcd0c96.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle video interactions
            document.querySelectorAll('.media-item').forEach(item => {
                const video = item.querySelector('video');
                const thumbnail = item.querySelector('.media-thumbnail');

                if (video) {
                    // Play/pause on click
                    item.addEventListener('click', function(e) {
                        if (video.paused) {
                            video.play();
                            video.classList.add('playing');
                        } else {
                            video.pause();
                            video.classList.remove('playing');
                        }
                    });

                    // Handle video states
                    video.addEventListener('play', () => {
                        video.classList.add('playing');
                        thumbnail.style.opacity = 0;
                    });

                    video.addEventListener('pause', () => {
                        video.classList.remove('playing');
                        if (!item.matches(':hover')) {
                            thumbnail.style.opacity = 1;
                        }
                    });

                    video.addEventListener('ended', () => {
                        video.classList.remove('playing');
                        thumbnail.style.opacity = 1;
                    });
                }
            });
        });
</script>

    
</body>
</html>