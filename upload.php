<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "books";

// Create connection
// $conn = new mysqli($servername, $username, $password, $dbname);
$conn = new mysqli($servername, $username, $password, $dbname, 3306); // add port if necessary

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Upload logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_products_btn'])) {
    // Check if files are uploaded before proceeding
    if (isset($_FILES["mediaFile"]) && isset($_FILES["thumbnail"])) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES["mediaFile"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        $title = $_POST['title'];

        // Handle thumbnail upload
        $thumbnailName = basename($_FILES["thumbnail"]["name"]);
        $thumbnailPath = $targetDir . $thumbnailName;
        $thumbnailType = pathinfo($thumbnailPath, PATHINFO_EXTENSION);

        // Validate media file type
        $allowedMediaTypes = ["mp3", "wav", "mp4", "mov", "avi"];
        $allowedImageTypes = ["jpg", "jpeg", "png"];

        if (in_array($fileType, $allowedMediaTypes) && in_array($thumbnailType, $allowedImageTypes)) {
            // Check if the file already exists
            if (file_exists($targetFilePath)) {
                echo "<div class='alert error'>File already exists. <button class='close-btn'>&times;</button></div>";
            } else {
                // Proceed with file upload
                if (move_uploaded_file($_FILES["mediaFile"]["tmp_name"], $targetFilePath) && move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $thumbnailPath)) {
                    $type = (in_array($fileType, ["mp3", "wav"])) ? 'audio' : 'video';
                    $sql = "INSERT INTO media_files (filename, file_path, thumbnail_path, file_type, title) 
                            VALUES ('$fileName', '$targetFilePath', '$thumbnailPath', '$type', '$title')";

                    if ($conn->query($sql) === TRUE) {
                        echo "<div class='alert success'>File and thumbnail uploaded successfully. <button class='close-btn'>&times;</button></div>";
                    } else {
                        echo "<div class='alert error'>Error: " . $conn->error . " <button class='close-btn'>&times;</button></div>";
                    }
                } else {
                    echo "<div class='alert error'>Error uploading files. <button class='close-btn'>&times;</button></div>";
                }
            }
        } else {
            echo "<div class='alert error'>Invalid file or thumbnail type. <button class='close-btn'>&times;</button></div>";
        }
    } else {
        echo "<div class='alert error'>No files uploaded. <button class='close-btn'>&times;</button></div>";
    }
}
// Handle delete request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_media_btn'])) {
    $media_id = $_POST['media_id'];

    // Fetch file paths before deleting
    $sql = "SELECT file_path, thumbnail_path FROM media_files WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $media_id);
    $stmt->execute();
    $stmt->bind_result($file_path, $thumbnail_path);
    $stmt->fetch();
    $stmt->close();

    // Delete files from the server
    if (file_exists($file_path)) {
        unlink($file_path);
    }
    if (file_exists($thumbnail_path)) {
        unlink($thumbnail_path);
    }

    // Delete from database
    $sql = "DELETE FROM media_files WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $media_id);
    if ($stmt->execute()) {
        echo "<div class='alert success'>Media deleted successfully. <button class='close-btn'>&times;</button></div>";
    } else {
        echo "<div class='alert error'>Error deleting media. <button class='close-btn'>&times;</button></div>";
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload, View & Update Media</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="uploadMedia.css">

    <style>
        
    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>

<div class="upload-container">
    <form action="" method="post" enctype="multipart/form-data">
        <h3>Upload Media</h3>
        <label>Title:</label>
        <input type="text" name="title" class="admin_input" placeholder="Enter Title" required><br>

        <label>Media File:</label>
        <input type="file" name="mediaFile" class="admin_input" accept="audio/*,video/*" required><br>

        <label>Thumbnail Image:</label>
        <input type="file" name="thumbnail" accept="image/*" required onchange="previewThumbnail()"><br>
        <img id="thumbnailPreview" src="#" alt="Thumbnail Preview" style="display: none;">

        <input type="submit" name="add_products_btn" class="admin_input" value="Add Media">
    </form>
</div>

<!-- View & Update Media Section -->
<?php
// Fetch and display the media files
$sql = "SELECT * FROM media_files";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "
        <div class='media-item'>
            <h4>" . htmlspecialchars($row['title']) . "</h4>
            <img src='" . htmlspecialchars($row['thumbnail_path']) . "' alt='Thumbnail' width='150'>
            <audio controls>
                <source src='" . htmlspecialchars($row['file_path']) . "' type='" . ($row['file_type'] == 'audio' ? 'audio/mp3' : 'video/mp4') . "'>
                Your browser does not support the audio element.
            </audio>
            <form action='' method='post' enctype='multipart/form-data'>
                <input type='hidden' name='media_id' value='" . $row['id'] . "'>
                <label>Title:</label>
                <input type='text' name='title' value='" . htmlspecialchars($row['title']) . "' required><br>
                <label>Media File:</label>
                <input type='file' name='mediaFile' accept='audio/*,video/*'><br>
                <label>Thumbnail Image:</label>
                <input type='file' name='thumbnail' accept='image/*'><br>
                <input type='submit' name='update_media_btn' value='Update Media'>
                <button type='submit' name='delete_media_btn' style='background-color: red; color: white;'>Delete</button>
            </form>
        </div>";
    }
} else {
    echo "<p>No media files found.</p>";
}
?>
<script>
    // Thumbnail Preview
    function previewThumbnail() {
        const fileInput = document.querySelector('input[name="thumbnail"]');
        const preview = document.getElementById('thumbnailPreview');

        if (fileInput.files && fileInput.files[0]) {
            const reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };

            reader.readAsDataURL(fileInput.files[0]);
        }
    }

    // Close Alert
    document.querySelectorAll('.alert .close-btn').forEach(button => {
        button.addEventListener('click', function () {
            this.parentElement.style.display = 'none';
        });
    });
</script>

</body>
</html>
<?php
$conn->close();
?>