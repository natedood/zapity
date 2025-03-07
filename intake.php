<!DOCTYPE html>
<html lang="en">

<head>
    <title>Intake</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Include Bootstrap for layout and FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Include jQuery UI CSS for Tabs styling -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">
    <!-- Include jQuery and jQuery UI JS libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
    <style>
        /* Optional: reduce padding/margins to better integrate with your design */
        #tabs .ui-tabs-nav { margin: 0; padding: 0.5em; }
        #tabs .ui-tabs-panel { padding: 1em; }
    </style>
</head>
 
<body>

  <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <a class="navbar-brand" href="#">Rimspec</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="callscreen.html">Leads</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="todos.html">Todo</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="calllog.html">Call Log</a>
        </li>
      </ul>
    </div>
  </nav>

  
<div data-role="header">
  <h1>Intake</h1>
</div>

    <div class="container">
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

            <div class="form-group">
                <label for="rep">Rep:</label>
                <select class="form-control" id="rep" name="rep"></select>
            </div>

            <div class="form-group">
                <label>Type:</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="type" id="retail" value="retail">
                    <label class="form-check-label" for="retail">Retail</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="type" id="wholesale" value="wholesale">
                    <label class="form-check-label" for="wholesale">Wholesale</label>
                </div>
            </div>

            <div id="retail-info" class="form-group" style="display: none;">
                <label for="retail-info">Retail Information:</label>
            </div>

            <div id="wholesale-info" class="form-group" style="display: none;">
                <label for="wholesale-info">Wholesale Information:</label>
            </div>

            <script>
                $('input[name="type"]').change(function() {
                    $('#retail-info').toggle($('#retail').is(':checked'));
                    $('#wholesale-info').toggle($('#wholesale').is(':checked'));
                });

                $(document).ready(function() {
                    $('input[name="type"]:checked').each(function() {
                        if (this.id === 'retail') {
                            $('#retail-info').show();
                        } else if (this.id === 'wholesale') {
                            $('#wholesale-info').show();
                        }
                    });
                });
            </script>


            <script>
                function updateTypeRadioGetLocation(){
                    var locationId = $('#location').val();
                    updateTypeRadio(locationId);
                }
                function updateTypeRadio(locationId) {
                    if (locationId != 53) { // location 53 = rimspec offices - brittle, TODO: use a DB flag
                        $('#wholesale').prop('checked', true);
                    } else {
                        $('#retail').prop('checked', true);
                    }
                    $('input[name="type"]').change();
                }

                $('#location').change(function() {
                    var locationId = $(this).val();
                    loadReps(locationId);
                    updateTypeRadio(locationId);
                });

                $(document).ready(function() {
                    var initialLocationId = $('#location').val();
                    if (initialLocationId) {
                        updateTypeRadio(initialLocationId);
                    }
                });

                $('#location').change(function() {
                    var locationId = $(this).val();
                    loadReps(locationId);
                });

                function loadReps(locationId) {
                    console.log('Requesting reps for location ID:', locationId);
                    $.ajax({
                        url: 'reps.php',
                        method: 'GET',
                        data: { location_id: locationId },
                        dataType: 'json',
                        success: function(response) {
                            var repSelect = $('#rep');
                            repSelect.empty();
                            $.each(response, function(index, rep) {
                                var option = $('<option>').val(rep.id).text(rep.rep_name);
                                repSelect.append(option);
                            });
                            console.log('Reps loaded:', response);
                            updateTypeRadioGetLocation();
                        },
                        error: function(xhr, status, error) {
                            console.log('Error:', error);
                        }
                    });
                }

                $(document).ready(function() {
                    var initialLocationId = $('#location').val();
                    if (initialLocationId) {
                        loadReps(initialLocationId);
                    }
                });
            </script>
            <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
        </form>
    </div>

    <script>
        var lon;
        var lat;
        
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
                    updateTypeRadioGetLocation();
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
            // Automatically load reps for the selected location (first option by default)
            var selectedLocationId = locationSelect.val();
            if (selectedLocationId) {
                loadReps(selectedLocationId);
            }
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
        
    </script>

</body>

</html>