<?php include 'header.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Todo</title>
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
    
    <script>
      
      function openCallScreen(phoneNumber) {
        window.location.href = `callscreen.html?number=${phoneNumber}&todo=1`;
      }

      function setTodoStatus(todoId, statusId = 1) {
        console.log("setTodoStatus called with:", { todoId, statusId });
        $.ajax({
          url: 'todos_status.php',
          type: 'GET',
          data: {
            id: todoId,
            status: statusId
          },
          success: function(response) {
            console.log("Status updated:", response);
            // Optionally, refresh the calls list after updating the status
            getCalls();
          },
          error: function(xhr, status, error) {
            console.error("Error updating status:", error);
          }
        });
      }

      // Define and call getCalls function to fetch and log calls JSON data.
      function getCalls() {
        // Get the date range values.
        var startDate = $("#startDate").val();
        var endDate = $("#endDate").val();

        // Collect all checked status values (e.g., Open (0) and/or Done (1)).
        var statuses = [];
        $(".status-check:checked").each(function(){
          statuses.push($(this).val());
        });
        var statusParam = statuses.join(",");

        console.log("Sending data to server:", {
          startDate: startDate,
          endDate: endDate,
          status: statusParam
        });

        $.ajax({
          url: 'todos_calls.php',
          type: 'GET',
          dataType: 'json',
          data: {
            startDate: startDate,
            endDate: endDate,
            status: statusParam
          },
          success: function(data) {
            console.log("Calls fetched:", data);
            
            // If the new "Filter to unique phone numbers only" checkbox is checked,
            // consolidate the data so that only the most recent record for each unique phone number is kept.
            if ($("#filterByPhone").prop("checked")) {
              var uniqueCalls = {};
              data.forEach(function(call) {
                // Convert call.id to a number for proper comparison.
                var callId = parseInt(call.id, 10);
                if (uniqueCalls[call.phone_number]) {
                  // If the existing record has a lower id, update it.
                  if (callId > parseInt(uniqueCalls[call.phone_number].id, 10)) {
                    uniqueCalls[call.phone_number] = call;
                  }
                } else {
                  uniqueCalls[call.phone_number] = call;
                }
              });
              // Replace data with the unique values.
              data = Object.values(uniqueCalls);
            }
            
            var tbody = $("#calls tbody");
            tbody.empty(); // Clear existing rows
            data.forEach(function(call) {
              var row = `<tr>
                <td>
                  <button class="btn btn-success">
                    <i class="fas fa-folder-open" onclick="openCallScreen('${call.phone_number}')"></i>
                  </button>
                </td>
                <td>${String(call.phone_number).replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3')}</td>
                <td>${call.caller_id_name}</td>
                <td>${new Date(call.due_datetime).toLocaleDateString()}</td>
                <td>
                      ${call.status === 0 ? `<button class="btn btn-primary" onclick="setTodoStatus(${call.id}, 1)">Done</button>` : ''}
                </td>
              </tr>`;
              
              // New row for displaying call notes, spanning across all columns (assuming 5 columns)
              var notesRow = `<tr>
                <td colspan="5" class="text-muted">
                  <small>${call.call_notes ? call.call_notes : ''}</small>
                </td>
              </tr>`;
              
              tbody.append(row);
              tbody.append(notesRow);
            });

            // Update result count
            $("#resultCountTop, #resultCountBottom").text(`Total results: ${data.length}`);
          },
          error: function(xhr, status, error) {
            console.error("Error fetching calls:", error);
          }
        });
      }
      
      $(function(){
            $("#tabs").tabs();

            // Call getCalls when the page loads.
            getCalls();
            
            // Attach an event handler for the refresh button next to the Calls title.
            // Assuming the refresh button is the btn-info within the #calls container.
            $("#calls button.btn-info").on("click", function() {
                getCalls();
            });    
            // Register a change event for startDate, endDate, and status checkboxes to re-run getCalls.
            $("#startDate, #endDate").on("change", getCalls);
            $(".status-check").on("change", getCalls);
            $("#filterByPhone").on("change", getCalls);
        });
    </script>
</head>
<body>

<?php include 'nav.php'; ?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Get current date in Central Time (-6 hours)
    var centralNow = new Date(new Date().getTime() - 6 * 60 * 60 * 1000);
    
    // Create date for one week ago
    //var oneWeekAgo = new Date(centralNow.getTime() - (7 * 24 * 60 * 60 * 1000));
    
    // Format dates as YYYY-MM-DD
    var today = centralNow.toISOString().split('T')[0];
    //var startDate = oneWeekAgo.toISOString().split('T')[0];
    
    // changed to always pull status 0 items on server call function
    // Set the input values
    document.getElementById('startDate').value = today;
    document.getElementById('endDate').value = today;
  });
</script>

 
<div data-role="header">
  <h1>To-do List</h1>
</div>

    <!-- <div id="tabs">
        <ul>
            <li><a href="#calls">Calls</a></li>
            <li><a href="#tires">Tires</a></li>
            <li><a href="#supplies">Supplies</a></li>
            <li><a href="#other">Other</a></li>
        </ul> -->
        <div id="calls">
            <!-- <h2>
                <button class="btn btn-info"><i class="fas fa-sync-alt"></i></button> Calls
            </h2> -->

                        
            <div class="container mt-5">
            <div class="row">
              <div class="col-6">
                <input type="date" id="startDate" class="form-control start-date">
              </div>
              <div class="col-6">
                <input type="date" id="endDate" class="form-control end-date">
              </div>
            </div>
            </div>

            <div class="container mt-3">
              <div class="row">
                <div class="col">
                  <div class="form-check form-check-inline">
                    <input class="form-check-input status-check" type="checkbox" id="statusOpen" value="0" checked>
                    <label class="form-check-label" for="statusOpen">Open</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input status-check" type="checkbox" id="statusDone" value="1">
                    <label class="form-check-label" for="statusDone">Done</label>
                  </div>
                </div>
              </div>
            </div>

            <!-- New row for "Filter to unique phone numbers only" -->
            <div class="container mt-3">
              <div class="row">
                <div class="col">
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="filterByPhone" id="filterByPhone" value="1">
                    <label class="form-check-label" for="filterByPhone">Filter to unique phone numbers only</label>
                  </div>
                </div>
              </div>
            </div>

            <hr/>

            <!-- Result count above the table -->
            <div class="container mt-3">
              <div class="row">
                  <div id="resultCountTop">Total results: 0</div>
              </div>
            </div>

            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Open</th>
                  <th>Phone</th>
                  <th>Name</th>
                  <th>Due Date</th>
                  <th>Save</th>
                </tr>
              </thead>
              <tbody>
                <!-- template for todo call items -->
                <tr>
                  <td>
                    <button class="btn btn-success">
                      <i class="fas fa-folder-open"></i>
                    </button>
                  </td>
                  <td>Name Here</td>
                  <td>123-456-7890</td>
                  <td>01/01/2023-12:00</td>
                  <td>
                    <button class="btn btn-primary">Done</button>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Result count below the table -->
            <div class="container mt-3">
              <div class="row">
                  <div id="resultCountBottom">Total results: 0</div>
              </div>
            </div>
        </div>
        <!-- <div id="tires">
            <h2>Tires</h2>
            <p>Content for Tires tab.</p>
        </div>
        <div id="supplies">
            <h2>Supplies</h2>
            <p>Content for Supplies tab.</p>
        </div>
        <div id="other">
            <h2>Other</h2>
            <p>Content for Other tab.</p>
        </div> -->
    <!-- </div> -->
    <div data-role="footer">
      <h4>&nbsp;</h4>
    </div>

    <script>
            window.addEventListener('pageshow', function(event) {
              // Call getCalls on every page show, including when coming back from cache.
              getCalls();
            });
    </script>

</body>
</html>