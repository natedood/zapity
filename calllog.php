
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Call Log</title>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* Optional: reduce padding/margins to better integrate with your design */
        #tabs .ui-tabs-nav { margin: 0; padding: 0.5em; }
        #tabs .ui-tabs-panel { padding: 1em; }
        table.table thead th.sortable {
          cursor: pointer;
        }
        /* Prevent phone column data from wrapping */
        table.table th[data-type="phone"],
        table.table td:nth-child(2) {
          white-space: nowrap;
        }
    </style>
</head>
<body>

<?php include 'nav.php'; ?>
  
<div style="margin-top: 70px;"></div>

<div class="container-fluid mt-4">
  <div class="row">
    <div class="col-12">
      <form id="dateFilterForm" class="form-inline">
        <label for="startDate" class="mr-2">Dates:</label>
        <div class="form-group mb-2">
          <input type="date" class="form-control" id="startDate" placeholder="mm/dd/yyyy">
        </div>
        <div class="form-group mx-sm-3 mb-2">
          <input type="date" class="form-control" id="endDate" placeholder="mm/dd/yyyy">
        </div>
      </form>
    </div>
  </div>
</div>

<div class="container-fluid mt-4">
  <div class="row">
    <div class="col-12">
      <form id="originFilterForm" class="form-inline">
        <label for="origin" class="mr-2">Origin:</label>
        <div class="form-check form-check-inline">
          <input class="form-check-input origin-check" type="checkbox" name="origin" value="in" id="in" checked>
          <label class="form-check-label" for="in">In</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input origin-check" type="checkbox" name="origin" value="out" id="out" checked>
          <label class="form-check-label" for="out">Out</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input origin-check" type="checkbox" name="origin" value="walkin" id="walkin" checked>
          <label class="form-check-label" for="walkin">Walkin</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input origin-check" type="checkbox" name="origin" value="textin" id="textin" checked>
          <label class="form-check-label" for="textin">Textin</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input origin-check" type="checkbox" name="origin" value="textout" id="textout" checked>
          <label class="form-check-label" for="textout">Textout</label>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="container-fluid mt-4">
  <div class="row">
    <div class="col-12">
      <form id="followUpFilterForm" class="form-inline">
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="followuponly" id="followuponly" value="1">
            <label class="form-check-label" for="followuponly">Follow-up only</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" name="nofollowup" id="nofollowup" value="1">
            <label class="form-check-label" for="nofollowup">No follow-up</label>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- New row for "Filter to phone numbers only" -->
<div class="container-fluid mt-4">
  <div class="row">
    <div class="col-12">
      <form id="phoneFilterForm" class="form-inline">
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="filterByPhone" id="filterByPhone" value="1">
          <label class="form-check-label" for="filterByPhone">Filter to unique phone numbers only</label>
        </div>
      </form>
    </div>
  </div>
</div>

<br/>

<div class="container-fluid mt-4">
  <!-- Bootstrap Nav Tabs -->
  <ul class="nav nav-tabs" id="callLogTabs" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="calls-tab" data-toggle="tab" href="#calls" role="tab" aria-controls="calls" aria-selected="true">Calls</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="stats-tab" data-toggle="tab" href="#stats" role="tab" aria-controls="stats" aria-selected="false">Stats</a>
    </li>
  </ul>

  <!-- Tab Content -->
  <div class="tab-content" id="callLogTabsContent">
    <!-- Calls Tab -->
    <div class="tab-pane fade show active" id="calls" role="tabpanel" aria-labelledby="calls-tab">
      <!-- Result count at the top -->
      <div id="resultCountTop" class="container-fluid mb-2 text-center">Total results: 0</div>

      <!-- Results Table -->
      <table class="table table-striped container-fluid">
        <thead>
          <tr>
            <th>Open</th>
            <th class="sortable" data-type="phone">Phone</th>
            <th class="sortable" data-type="string">Name</th>
            <th class="sortable" data-type="date">Date</th>
            <th class="sortable" data-type="string">Origin</th>
            <th class="sortable" data-type="string">User</th>
            <th class="sortable" data-type="string">Flags</th>
          </tr>
        </thead>
        <tbody>
          <!-- Template for call log items will be dynamically injected here -->
        </tbody>
      </table>

      <!-- Result count at the bottom -->
      <div id="resultCountBottom" class="container-fluid mt-2 text-center">Total results: 0</div>
    </div>

    <!-- Stats Tab -->
    <div class="tab-pane fade" id="stats" role="tabpanel" aria-labelledby="stats-tab">
      <!-- Line chart container -->
      <div class="container-fluid mt-4">
        <div class="row">
          <div class="col-12">
            <canvas id="callChart" style="max-height:300px;"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div data-role="footer">
  <h4>&nbsp;</h4>
</div>


<script>
  var callChart; // Global chart instance

  // Function to update the chart given an array of call objects
  function updateChart(callData) {
      // Aggregate call counts by day from the call data.
      var countsByDay = {};
      callData.forEach(function(call) {
          // Extract the date portion (YYYY-MM-DD) from call_datetime.
          var callDate = new Date(call.call_datetime).toISOString().split('T')[0];
          countsByDay[callDate] = (countsByDay[callDate] || 0) + 1;
      });
      
      // Get the date range from the filter inputs.
      var startDateStr = $("#startDate").val();
      var endDateStr = $("#endDate").val();
      
      // Build an array with all days between the start and end dates, inclusive.
      var labels = [];
      if (startDateStr && endDateStr) {
          var current = new Date(startDateStr);
          var end = new Date(endDateStr);
          while (current <= end) {
              // Format the date as YYYY-MM-DD.
              var dateStr = current.toISOString().split('T')[0];
              labels.push(dateStr);
              current.setDate(current.getDate() + 1);
          }
      }
      
      // Create dataCounts array using countsByDay; if a day has no calls, default to 0.
      var dataCounts = labels.map(function(label) {
          return countsByDay[label] || 0;
      });
      
      // Get the canvas context.
      var ctx = document.getElementById('callChart').getContext('2d');

      // If the chart already exists, update it.
      if (callChart) {
          callChart.data.labels = labels;
          callChart.data.datasets[0].data = dataCounts;
          callChart.update();
      } else {
          callChart = new Chart(ctx, {
              type: 'line',
              data: {
                  labels: labels,
                  datasets: [{
                      label: 'Calls per Day',
                      data: dataCounts,
                      fill: false,
                      borderColor: 'rgba(75, 192, 192, 1)',
                      tension: 0.1
                  }]
              },
              options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  scales: {
                      x: {
                          title: {
                              display: true,
                              text: 'Day (YYYY-MM-DD)'
                          }
                      },
                      y: {
                          title: {
                              display: true,
                              text: 'Number of Calls'
                          },
                          beginAtZero: true,
                          precision: 0
                      }
                  }
              }
          });
      }
  }

  // Function to load call log data based on the current filter form values.
  function loadCallLog() {
      // Get date filters.
      var startDate = $("#startDate").val();
      var endDate   = $("#endDate").val();
      
      // Get the origin filter as a comma-delimited string.
      var origins = [];
      $(".origin-check:checked").each(function(){
          origins.push($(this).val());
      });
      var originParam = origins.join(",");
      
      // Get the followuponly value (1 if checked, else 0).
      var followUpOnly = $("#followuponly").prop("checked") ? 1 : 0;
      
      // Get the nofollowup value (1 if checked, else 0).
      var noFollowUp = $("#nofollowup").prop("checked") ? 1 : 0;
      
      console.log("Sending parameters:", {
          startDate: startDate,
          endDate: endDate,
          origin: originParam,
          followuponly: followUpOnly,
          nofollowup: noFollowUp
      });
      
      $.ajax({
          url: "calllog_calls.php",
          type: "GET",
          dataType: "json",
          data: {
              startDate: startDate,
              endDate: endDate,
              origin: originParam,
              followuponly: followUpOnly,
              nofollowup: noFollowUp
              // Note: filterByPhone is handled client-side.
          },
          success: function(data) {
              console.log("Call log data:", data);
              
              // If "Filter to unique phone numbers only" is checked, consolidate the rows.
              if ($("#filterByPhone").prop("checked")) {
                  var uniqueCalls = {};
                  // Assuming data is ordered descending by call_datetime:
                  data.forEach(function(call) {
                      if (!uniqueCalls[call.phone_number]) {
                          uniqueCalls[call.phone_number] = call;
                      }
                  });
                  data = Object.values(uniqueCalls);
              }
              
              // Update running totals.
              var totalResults = data.length;
              $("#resultCountTop").text("Total results: " + totalResults);
              $("#resultCountBottom").text("Total results: " + totalResults);
              
              var tbody = $("table.table tbody");
              tbody.empty(); // Clear existing rows.
              
              data.forEach(function(call) {
                  var row = `<tr>
                      <td>
                        <button class="btn btn-success" onclick="openCallScreen('${call.phone_number}')">
                          <i class="fas fa-folder-open"></i>
                        </button>
                      </td>
                      <td>${String(call.phone_number).replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3')}</td>
                      <td>${call.caller_id_name || ""}</td>
                      <td>${new Date(call.call_datetime).toLocaleDateString()}</td>
                      <td>${call.call_origin}</td>
                      <td>${call.first_name || ""}</td> <!-- Add the User column -->
                      <td>${call.flags ? call.flags : ""}</td>
                  </tr>`;
              
                  // Extra row for displaying notes, spanning across all 7 columns.
                  var notesRow = `<tr>
                      <td colspan="7" class="text-muted">
                        <small>${call.notes ? call.notes : ''}</small>
                      </td>
                  </tr>`;
              
                  tbody.append(row);
                  tbody.append(notesRow);
              });
              
              // Update running totals.
              var totalResults = data.length;
              $("#resultCountTop").text("Total results: " + totalResults);
              $("#resultCountBottom").text("Total results: " + totalResults);
              
              // Update the chart with the call log data.
              updateChart(data);
          },
          error: function(xhr, status, error) {
              console.error("Error fetching call log:", error);
          }
      });
  }

  // On page load.
  $(function() {
      // Set default dates to today.
      var today = new Date();
      // Optionally adjust timezone if needed.
      today.setHours(today.getHours() - 6);
      var dt = today.toISOString().split('T')[0];
      $("#startDate").val(dt);
      $("#endDate").val(dt);
      
      // Bind change events on the filter form fields.
      $("#startDate, #endDate").on("change", loadCallLog);
      $(".origin-check").on("change", loadCallLog);
      $("#followuponly, #nofollowup, #filterByPhone").on("change", loadCallLog);
      
      // Load the call log on page load.
      loadCallLog();
  });

  // Function to open the call screen.
  function openCallScreen(phoneNumber) {
      window.location.href = "leads.php?number=" + phoneNumber;
  }
</script>


</body>
</html>