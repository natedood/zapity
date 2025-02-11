<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intake</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
 
<body>
    <div class="container">
        <h1>Intake</h1>
        <form>
            <div class="form-group">
                <label for="location">Location:</label>
                <div class="input-group">
                    <select class="form-control" id="location" name="location"></select>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-secondary" onclick="loadLocations()"><i class="fas fa-map-marker-alt"></i></button>
                        <button type="button" class="btn btn-secondary" onclick="loadLocationsAlphaSorted()"><i class="fas fa-sort-alpha-down"></i></button>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script>
        var lon;
        var lat;
        //$(document).ready(function() {
        function loadLocationsLonLat(lon,lat) {
            locationurl = 'locations.php?lon=' + lon + '&lat=' + lat;
            loadLocationsUrl(locationurl);
        }
        function loadLocationsUrl(urlToLoad){
            console.log(urlToLoad);
            $.ajax({
                url: urlToLoad,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    loadLocationsSelect(response);
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                }
            });
        }
        function loadLocationsSelect(response){
            var locationSelect = $('#location');
            console.log(response);
            locationSelect.empty();
            $.each(response, function(index, location) {
                var option = $('<option>').val(location.id).text(location.location_name + ', ' + location.city + ' ' + location.state);
                locationSelect.append(option);
            });
        }
        function loadLocationsAlphaSorted(){
            locationurl = 'locations.php?alphasort=1';
            loadLocationsUrl(locationurl);
        }
        function loadLocations() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var lon = position.coords.longitude;
                    var lat = position.coords.latitude;
                    // Use lon and lat variables as needed
                    loadLocationsLonLat(lon,lat)
                });
            } else {
                console.log('Geolocation is not supported by this browser.');
                loadLocationsUrl('locations.php');
            }
        }

        $(document).ready(function() {
            loadLocations();
        });
        //});


    </script>

</body>

</html>