<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Calls</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Your JavaScript code goes here -->

</head>
<body>
    
    <button class="btn btn-primary" onclick="makeRestCall()">GET</button>

    <script>
        function makeRestCall() {
            $.ajax({
                url: 'calls.php',
                method: 'GET',
                dataType: 'json', // Specify the response data type as JSON
                success: function(response) {
                    $('textarea').val(JSON.stringify(response, null, 4));
                    $('textarea').css('font-family', 'monospace');
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        }
    </script>

    <textarea rows="10" style="width: 100%;"></textarea>

</body>
</html>