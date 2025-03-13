<!-- filepath: /c:/dev/repos/zapity/dev/import_calls.php -->
<?php
// At the very top of the file
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include session checker to ensure user is logged in
require_once 'checksession.php';
// Include database connection
require_once 'db_connect.php';

// Initialize variables
$message = '';
$alertClass = '';
$importCount = 0;
$duplicateCount = 0;
$errorCount = 0;
$skippedZeroDurationCount = 0;
$skippedNonIncomingCount = 0;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csvFile'])) {
    // Check if file was uploaded without errors
    if ($_FILES['csvFile']['error'] === 0) {
        $file = $_FILES['csvFile']['tmp_name'];
        
        // Check if file is a CSV
        $fileInfo = pathinfo($_FILES['csvFile']['name']);
        if (strtolower($fileInfo['extension']) === 'csv') {
            
            // Open the CSV file
            if (($handle = fopen($file, 'r')) !== FALSE) {
                // Read the header row
                $header = fgetcsv($handle);
                
                // Verify CSV structure has required columns
                $requiredColumns = ['User', 'Type', 'Date', 'Time', 'From', 'To', 'Cost', 'Duration'];
                $missingColumns = array_diff($requiredColumns, $header);
                
                if (empty($missingColumns)) {
                    // Process each row in the CSV
                    while (($data = fgetcsv($handle)) !== FALSE) {
                        if (count($data) >= 8) { // Ensure we have at least the required fields
                            $user = $data[0];
                            $callType = $data[1];
                            
                            // Skip calls that are not incoming ("In")
                            if ($callType !== "In") {
                                $skippedNonIncomingCount++;
                                continue; // Skip to the next record in the loop
                            }
                            
                            // Parse date and time together and adjust by +5 hours
                            $dateStr = $data[2]; // e.g. "Mar 13 2025"
                            $timeStr = $data[3]; // e.g. "7:36 AM"
                            $combinedDateTimeStr = $dateStr . ' ' . $timeStr; // e.g. "Mar 13 2025 7:36 AM"

                            // Create DateTime object from combined string
                            $originalDateTime = DateTime::createFromFormat('M d Y g:i A', $combinedDateTimeStr);

                            if ($originalDateTime) {
                                // Add 5 hours to the datetime
                                $adjustedDateTime = clone $originalDateTime;
                                $adjustedDateTime->modify('+5 hours');
                                
                                // Extract the adjusted values for storage
                                $formattedDate = $adjustedDateTime->format('Y-m-d');
                                $formattedTime = $adjustedDateTime->format('H:i:s');
                                $callDatetime = $adjustedDateTime->format('Y-m-d H:i:s');
                            } else {
                                // Handle parsing error
                                $errorCount++;
                                continue; // Skip this record
                            }
                            
                            $sourceNumber = preg_replace('/[^0-9]/', '', $data[4]); // Clean phone number
                            $destNumber = preg_replace('/[^0-9]/', '', $data[5]); // Clean phone number
                            
                            // Extract numeric value from cost string (remove $ and other chars)
                            $costStr = $data[6];
                            $cost = preg_replace('/[^0-9.]/', '', $costStr);
                            
                            // Parse duration (format: HH:MM:SS)
                            $durationStr = $data[7];
                            $duration = $durationStr;
                            
                            // Get description (if exists)
                            $description = isset($data[8]) ? $data[8] : '';
                            
                            // Skip calls with zero duration (00:00:00)
                            if ($duration == "00:00:00") {
                                $skippedZeroDurationCount++;
                                continue; // Skip to the next record in the loop
                            }
                            
                            // Check if this record already exists in the database
                            $stmt = $conn->prepare("SELECT id FROM call_history_import 
                                                  WHERE call_type = ? 
                                                  AND call_datetime = ?
                                                  AND source_number = ?");
                                                  
                            $stmt->bind_param("sss", $callType, $callDatetime, $sourceNumber);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            
                            if ($result->num_rows === 0) {
                                // Insert the record - it doesn't exist yet
                                $insertStmt = $conn->prepare("INSERT INTO call_history_import 
                                                          (user, call_type, source_number, 
                                                           destination_number, cost, duration, 
                                                           description, call_datetime) 
                                                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                                                          
                                $insertStmt->bind_param("ssssdsss", 
                                              $user, $callType, $sourceNumber, $destNumber, 
                                              $cost, $duration, $description, $callDatetime);
                                              
                                if ($insertStmt->execute()) {
                                    $importCount++;
                                } else {
                                    $errorCount++;
                                }
                                $insertStmt->close();
                            } else {
                                $duplicateCount++;
                            }
                            $stmt->close();
                        }
                    }
                    
                    // Set success message
                    $message = "Import completed: $importCount records imported, $duplicateCount duplicates skipped, $skippedZeroDurationCount zero-duration calls skipped, $skippedNonIncomingCount non-incoming calls skipped, $errorCount errors.";
                    $alertClass = 'alert-success';
                    
                } else {
                    $message = "CSV file is missing required columns: " . implode(", ", $missingColumns);
                    $alertClass = 'alert-danger';
                }
                fclose($handle);
            } else {
                $message = "Could not open the CSV file.";
                $alertClass = 'alert-danger';
            }
        } else {
            $message = "Please upload a valid CSV file.";
            $alertClass = 'alert-danger';
        }
    } else {
        $message = "Error uploading file: " . $_FILES['csvFile']['error'];
        $alertClass = 'alert-danger';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Call History Import</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-mobile/1.4.5/jquery.mobile.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        // Disable jQuery Mobile's Ajax navigation
        $(document).bind("mobileinit", function() {
            $.mobile.ajaxEnabled = false;
            $.mobile.hashListeningEnabled = false;
            $.mobile.pushStateEnabled = false;
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mobile/1.4.5/jquery.mobile.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
    
    <style>
        .custom-file-label::after {
            content: "Browse";
        }
        .result-panel {
            margin-top: 20px;
        }
        .import-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .instructions {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div data-role="page" id="import-page">
        <div data-role="header" data-position="fixed">
            <h1>Call History Import</h1>
        </div>

        <div role="main" class="ui-content">
            <div class="import-container">
                <div class="instructions">
                    <h2>Import Call History</h2>
                    <p>Upload a CSV file with call history data. The file should contain these columns:</p>
                    <p>User, Type (In/Out), Date (Month Day Year), Time (H:MM AM/PM), From (phone number), To (phone number), Cost (e.g. $0.00), Duration (HH:MM:SS), Description (optional)</p>
                </div>
                
                <?php if (!empty($message)): ?>
                    <div class="alert <?php echo $alertClass; ?>" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="form-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="csvFile" name="csvFile" accept=".csv">
                            <label class="custom-file-label" for="csvFile">Choose CSV file</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                        <i class="fas fa-upload mr-2"></i> Import Call History
                    </button>
                </form>
                
                <?php if ($importCount > 0): ?>
                <div class="result-panel card mt-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Import Results</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <h3><?php echo $importCount; ?></h3>
                                <p>Records Imported</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <h3><?php echo $duplicateCount; ?></h3>
                                <p>Duplicates Skipped</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <h3><?php echo $errorCount; ?></h3>
                                <p>Errors</p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div data-role="footer" data-position="fixed">
        </div>
    </div>

    <script>
        // Update file input label with selected filename
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
    </script>
</body>
</html>