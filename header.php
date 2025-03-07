<?php
// Get the current file name
$current_file = basename($_SERVER['SCRIPT_NAME']);

// List of files to exclude from session checking
$excluded_files = ['login.php', 'logout.php', 'hash.php'];

// Only include checksession.php if the current file is not in the excluded list
if (!in_array($current_file, $excluded_files)) {
    require_once 'checksession.php';
}

// You can include other common elements here (navigation, etc.)
?>