<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if password parameter exists in URL
$password = isset($_GET['password']) ? $_GET['password'] : '';
$hash = '';

// Generate hash if password is provided
if (!empty($password)) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Hash Generator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .result {
            margin-top: 20px;
            word-break: break-all;
        }
        .hash-value {
            background-color: #e9f5f9;
            padding: 10px;
            border-left: 4px solid #4a90e2;
        }
    </style>
</head>
<body>
    <h1>Password Hash Generator</h1>
    
    <div class="container">
        <p>Enter a password in the URL as <code>?password=yourpassword</code> or use the form below:</p>
        
        <form method="GET" action="hash.php">
            <div class="form-group">
                <label for="password">Password to hash:</label>
                <input type="text" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>">
            </div>
            <div class="form-group">
                <button type="submit">Generate Hash</button>
            </div>
        </form>
        
        <?php if (!empty($hash)): ?>
        <div class="result">
            <h3>Generated Hash:</h3>
            <p class="hash-value"><?php echo htmlspecialchars($hash); ?></p>
            <p><small>Password: <?php echo htmlspecialchars($password); ?></small></p>
            <p><small>Hash Length: <?php echo strlen($hash); ?> characters</small></p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>