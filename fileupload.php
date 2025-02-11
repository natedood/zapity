
<!DOCTYPE html>
<html>
<head>
    <title>Image Upload</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Image Upload</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="image">Select Image:</label>
                <input type="file" name="image" id="image" accept="image/*" capture="camera" class="form-control-file">
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>

    <img id="preview" src="" alt="Preview" class="img-fluid">
    
    <script>
    document.getElementById('image').addEventListener('change', resizeImage);

    function resizeImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    console.log('Onload called...');
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    const maxWidth = 1024;
                    const ratio = maxWidth / img.width;
                    const width = img.width * ratio;
                    const height = img.height * ratio;

                    canvas.width = width;
                    canvas.height = height;

                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob(function(blob) {
                        const resizedFile = new File([blob], file.name, { type: file.type });
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(resizedFile);
                        event.target.files = dataTransfer.files;

                        // Display the resized image on the screen
                        const preview = document.getElementById('preview');
                        preview.src = URL.createObjectURL(resizedFile);
                    }, file.type, 1.0);
                };
                img.src = e.target.result;
                
            };
            reader.readAsDataURL(file);
        }
    }
</script>

    <?php
    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    if (isset($_POST['submit'])) {
        $targetDir = __DIR__;
        $originalFileName = basename($_FILES['image']['name']);
        $targetFile = $targetDir . '/' . $originalFileName;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check if file already exists and rename if necessary
        if (file_exists($targetFile)) {
            $uniqueId = time(); // You can also use uniqid() for a unique identifier
            $targetFile = $targetDir . '/' . pathinfo($originalFileName, PATHINFO_FILENAME) . '_' . $uniqueId . '.' . $imageFileType;
        }

        // Check file size
        if ($_FILES['image']['size'] > 50000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                echo "The file " . basename($targetFile) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }
    ?>

</body>
</html>