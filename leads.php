<?php require_once 'header.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <!--
      Setting the page title and viewport to ensure responsiveness on mobile devices.
    -->
    <title>Leads</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!--
      Include jQuery Mobile CSS for mobile-friendly UI components.
      Include Bootstrap CSS for layout and additional styling.
      Include FontAwesome for icon support.
    -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-mobile/1.4.5/jquery.mobile.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">
    
    
    <!--
      Include jQuery library and jQuery Mobile JS.
    -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mobile/1.4.5/jquery.mobile.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>

    <script>
        // -------------------------
        // TEST DATA SETUP
        // -------------------------
        // Array of test call data simulating call records. 
        // var testCallsJSON = [{"id":16,"phone_number":4444444448,"caller_id_name":"Ava Martin","call_datetime":"2025-02-01 23:00:00","customer_id":1600},{"id":15,"phone_number":3333333336,"caller_id_name":"James Anderson","call_datetime":"2025-02-01 22:00:00","customer_id":1500},{"id":30,"phone_number":9999999999,"caller_id_name":"Lucas Davis","call_datetime":"2025-02-01 21:30:00","customer_id":3000},{"id":14,"phone_number":2222222224,"caller_id_name":"Sophia Thompson","call_datetime":"2025-02-01 21:00:00","customer_id":1400},{"id":29,"phone_number":8888888888,"caller_id_name":"Scarlett Martin","call_datetime":"2025-02-01 20:30:00","customer_id":2900},{"id":13,"phone_number":1111111112,"caller_id_name":"Daniel Wilson","call_datetime":"2025-02-01 20:00:00","customer_id":1300},{"id":28,"phone_number":7777777776,"caller_id_name":"Sebastian Anderson","call_datetime":"2025-02-01 19:30:00","customer_id":2800},{"id":12,"phone_number":9999999990,"caller_id_name":"Olivia Davis","call_datetime":"2025-02-01 19:00:00","customer_id":1200},{"id":27,"phone_number":6666666664,"caller_id_name":"Elizabeth Thompson","call_datetime":"2025-02-01 18:30:00","customer_id":2700},{"id":11,"phone_number":8888888888,"caller_id_name":"Alex Johnson","call_datetime":"2025-02-01 18:00:00","customer_id":1100}];
        
        // Array of test call flags. 
        // Each flag has properties: id, parent_id (for hierarchical grouping), flag_name, specify (indicating if extra details are needed), and followup (indicating if a follow-up is required).
        var callFlags = [{"id":1,"parent_id":0,"flag_name":"Spam","display_order":null,"specify":0,"followup":0},{"id":2,"parent_id":0,"flag_name":"New Customer","display_order":null,"specify":0,"followup":0},{"id":3,"parent_id":2,"flag_name":"New job","display_order":null,"specify":1,"followup":1},{"id":4,"parent_id":3,"flag_name":"Crack","display_order":null,"specify":0,"followup":0},{"id":5,"parent_id":3,"flag_name":"Bend","display_order":null,"specify":0,"followup":0},{"id":6,"parent_id":3,"flag_name":"Recon","display_order":null,"specify":0,"followup":0},{"id":7,"parent_id":3,"flag_name":"Other","display_order":null,"specify":0,"followup":0},{"id":8,"parent_id":7,"flag_name":"Specify","display_order":null,"specify":1,"followup":0},{"id":9,"parent_id":2,"flag_name":"Other","display_order":null,"specify":0,"followup":0},{"id":10,"parent_id":9,"flag_name":"Specify","display_order":null,"specify":1,"followup":0},{"id":11,"parent_id":0,"flag_name":"Existing Customer","display_order":null,"specify":0,"followup":0},{"id":12,"parent_id":11,"flag_name":"New job","display_order":null,"specify":1,"followup":1},{"id":13,"parent_id":12,"flag_name":"Crack","display_order":null,"specify":0,"followup":0},{"id":14,"parent_id":12,"flag_name":"Bend","display_order":null,"specify":0,"followup":0},{"id":15,"parent_id":12,"flag_name":"Recon","display_order":null,"specify":0,"followup":0},{"id":16,"parent_id":12,"flag_name":"Other","display_order":null,"specify":0,"followup":0},{"id":17,"parent_id":16,"flag_name":"Specify","display_order":null,"specify":1,"followup":0},{"id":18,"parent_id":11,"flag_name":"Other","display_order":null,"specify":0,"followup":0},{"id":19,"parent_id":18,"flag_name":"Specify","display_order":null,"specify":1,"followup":0},{"id":20,"parent_id":11,"flag_name":"Status check","display_order":null,"specify":0,"followup":0},{"id":21,"parent_id":0,"flag_name":"Other N/A","display_order":null,"specify":0,"followup":0},{"id":22,"parent_id":21,"flag_name":"Specify","display_order":null,"specify":1,"followup":0},{"id":24,"parent_id":0,"flag_name":"Force Followup","display_order":null,"specify":0,"followup":1}];
        
    </script>
    
    <script>


        // --------------------------------------------------
        // Function: createCheckbox
        // Purpose: Generates HTML for a single checkbox element with a label.
        //           If the flag object's "specify" property equals 1,
        //           it also creates a hidden text input for further details.
        // Parameters: flag - an object representing a call flag.
        // Returns: A string containing the generated HTML.
        // --------------------------------------------------
        function createCheckbox(flag) {
            let checkboxHtml = `<div class="form-check">
                <input class="form-check-input flag-checkbox" type="checkbox" value="${flag.id}" id="flag_${flag.id}" data-followup="${flag.followup}">
                <label class="form-check-label" for="flag_${flag.id}">
                    ${flag.flag_name}
                </label>
            </div>`;
            // If flag requires additional details ("specify" equals 1), add a hidden text input.
            // removed specify 
            // if (flag.specify === 1) {
            //     checkboxHtml += `<div id="specify_${flag.id}" style="display: none; margin-left: 20px;">
            //                         <input type="text" class="form-control" placeholder="Specify details">
            //                      </div>`;
            // }
            return checkboxHtml;
        }

        // --------------------------------------------------
        // Function: renderCallFlags
        // Purpose: Recursively generates HTML for a list of call flags,
        //          including any child flags for parent-child hierarchies.
        // Parameters:
        //   flags - an array of flag objects.
        //   parentId - the parent flag id to filter by (default is 0 for top-level).
        //   level - an indentation level used for styling nested flags.
        // Returns: A string containing the generated HTML.
        // --------------------------------------------------
        function renderCallFlags(flags, parentId = 0, level = 0) {
            let html = '';
            // Filter flags by the current parent id.
            flags.filter(flag => flag.parent_id === parentId).forEach(flag => {
                // Create a div containing the checkbox. Use left margin for nesting.
                html += `<div style="margin-left: ${level * 20}px;">${createCheckbox(flag)}</div>`;
                // Create a container for child flags (initially hidden).
                html += `<div id="children_${flag.id}" style="display: none;">${renderCallFlags(flags, flag.id, level + 1)}</div>`;
            });
            return html;
        }

        // --------------------------------------------------
        // Function: toggleChildren
        // Purpose: Toggles the visibility of child flags when a parent flag is changed.
        // Parameters: flagId - the numeric id of the parent flag.
        // --------------------------------------------------
        function toggleChildren(flagId) {
            const childrenDiv = document.getElementById(`children_${flagId}`);
            if (childrenDiv) {
                childrenDiv.style.display = childrenDiv.style.display === 'none' ? 'block' : 'none';
            }
        }

        // --------------------------------------------------
        // Function: updateFollowupVisibility
        // Purpose: Checks if any selected checkbox has the followup property set to 1.
        //          If so, it reveals the follow-up date input field, automatically
        //          populates it with today's date, and sets the minimum allowed
        //          date to today; otherwise, it hides the field.
        // --------------------------------------------------
        function updateFollowupVisibility() {
            let show = true;
            // document.querySelectorAll('.form-check-input:checked').forEach(function(checkbox) {
            //     if (checkbox.getAttribute('data-followup') === "1") {
            //         show = true;
            //     }
            // });
            
            document.getElementById('followupDateContainer').style.display = show ? 'block' : 'none';
            
            if (show) {
                setDefaultFollowupDate();
            }
        }

        function setDefaultFollowupDate() {
            // Get current date in Central Time by subtracting 6 hours
            let centralNow = new Date(new Date().getTime() - 6 * 60 * 60 * 1000);
            // Set to tomorrow
            let tomorrow = new Date(centralNow.getTime() + 24 * 60 * 60 * 1000);
            
            // Format as YYYY-MM-DD
            let yyyy = tomorrow.getFullYear();
            let mm = String(tomorrow.getMonth() + 1).padStart(2, '0');
            let dd = String(tomorrow.getDate()).padStart(2, '0');
            let followupDefaultDate = `${yyyy}-${mm}-${dd}`;
            
            let followupInput = document.getElementById('followupDate');
            followupInput.value = followupDefaultDate;
            followupInput.min = followupDefaultDate;
        }

        document.addEventListener('DOMContentLoaded', function() {
            setDefaultFollowupDate();
        });
    </script>
</head>
<body>

    <?php include 'nav.php'; ?>

    <!--
      jQuery Mobile page container.
    -->
    <div data-role="page">
        <!--
          Header section for the call screen.
        -->
        <div data-role="header">
            <h1>Leads</h1>
        </div>
        <!--
          Content section contains the form elements.
        -->
        <div data-role="content" class="container-fluid">
            <div>
                <!--
                  Input group for entering a phone number.
                  Uses Bootstrap classes for styling.
                -->
                <hr/>

                <div class="input-group">
                    <input type="tel" name="phone_number" id="phone_number" 
                        class="form-control" placeholder="Enter phone number">
                    <div class="input-group-append">
                        <!--
                          Search button with a FontAwesome search icon.
                        -->
                        <button id="searchcalls" class="btn btn-primary" 
                            type="button"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                
                <hr/>

                <div class="form-group">
                    <!-- <label for="caller_id_name">Caller Name</label> -->
                    <input type="text" class="form-control" id="caller_id_name" name="caller_id_name" placeholder="Enter caller name">
                </div>

                <!-- Insert this snippet right after the phone number input group and before the call flags container -->
                <div id="callOriginContainer" class="mt-3">
                    <!-- <label class="d-block">Origin:</label> -->
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="callOrigin" id="originIncoming" value="in" checked>
                        <label class="form-check-label" for="originIncoming">In</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="callOrigin" id="originOutgoing" value="out">
                        <label class="form-check-label" for="originOutgoing">Out</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="callOrigin" id="originOutgoingText" value="walkin">
                        <label class="form-check-label" for="originOutgoingText">Walk-in</label> 
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="callOrigin" id="originIncomingText" value="intxt">
                        <label class="form-check-label" for="originIncomingText">Text In</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="callOrigin" id="originOutgoingText" value="outtxt">
                        <label class="form-check-label" for="originOutgoingText">Text Out</label>
                    </div>
                </div>

                <!--
                  Container to render the call flags checkboxes.
                  This container's innerHTML is filled by the renderCallFlags function.
                -->
                <div id="callFlagsContainer" class="mt-3"></div>
                
                <hr/>

                <script>
                    // Render the call flags inside the container.
                    document.getElementById('callFlagsContainer').innerHTML = renderCallFlags(callFlags);
                    
                    // Add event listeners to all checkboxes for change events.
                    // On change: toggle children, show/hide spescify input, and update follow-up input.
                    document.querySelectorAll('.form-check-input').forEach(checkbox => {
                        checkbox.addEventListener('change', function () {
                            // Toggle the display of child flags (if any) based on this checkbox's value.
                            toggleChildren(this.value);
                            // Check if there is a specify input linked to this flag.
                            // remove specify fields
                            // const specifyDiv = document.getElementById(`specify_${this.value}`);
                            // if (specifyDiv) {
                            //     // Display the specify input if the checkbox is checked, otherwise hide it.
                            //     specifyDiv.style.display = this.checked ? 'block' : 'none';
                            // }
                            // Remove this line since we don't need to toggle visibility anymore
                            // updateFollowupVisibility();
                        });
                    });
                </script>

                <!--
                  Follow-Up Date Input:
                  This date input is displayed only if a flag with followup == 1 is selected.
                  Uses Bootstrap's form-control styling and jQuery Mobile's data-role.
                -->
                <!-- Change this div to always be visible -->
                <div id="followupDateContainer" style="margin-top: 20px;">
                    <label for="followupDate">Follow-Up Date</label>
                    <input type="date" id="followupDate" class="form-control" data-role="date">
                </div>
                
                <!--
                  Notes Textarea:
                  This is an optional field for call notes. It spans full width
                  and is 3 rows tall using Bootstrap's form-control.
                -->
                <textarea id="callnotes" class="form-control mb-3" rows="3" placeholder="Call notes" 
                name="callnotes"></textarea>
                    
                <!--
                New row for marking a To-do item complete.
                Only visible when the querystring "todo" equals "1".
                -->
                <div id="todoRow" class="row align-items-center" style="display: none;">
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="todoComplete" name="todoComplete" value="1">
                            <label class="form-check-label" for="todoComplete">Mark To-do item complete for today</label>
                        </div>
                    </div>
                </div>
                
                <!-- <div class="container mt-3"> -->
                    <div class="row align-items-center">
                      <div class="col-12">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="close_lead" name="close_lead" value="1">
                          <label class="form-check-label" for="close_lead">Close Lead</label>
                        </div>
                        <div id="leadResultContainer" class="mt-2" style="display: none;">
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="lead_result" id="sale" value="1">
                            <label class="form-check-label" for="sale">Sale</label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="lead_result" id="dead" value="2">
                            <label class="form-check-label" for="dead">Dead</label>
                          </div>
                        </div>
                      </div>
                    </div>
                  <!-- </div> -->

                <!--
                  Action buttons for saving or resetting the form.
                -->
                <button id="saveButton" class="btn btn-success mt-3">Save</button>
                <script>
                    // Toggle the display of the lead result radio buttons when "Close Lead" is checked.
                    document.getElementById('close_lead').addEventListener('change', function() {
                        const leadResultContainer = document.getElementById('leadResultContainer');
                        leadResultContainer.style.display = this.checked ? 'block' : 'none';
                    });

                    // Check if the URL has a query string variable named 'todo' and its value is '1'.
                    // Remove button, moving to checkbox method
                    // const urlParamsTodo = new URLSearchParams(window.location.search);
                    // const todoParam = urlParamsTodo.get('todo');
                    // if (todoParam === '1') {
                    //     document.write('<button id="saveDoneButton" class="btn btn-warning mt-3">Save & Mark Done</button>');
                    // }
                </script>
                <button id="resetButton" class="btn btn-danger mt-3">Reset</button>
            </div>
            

            <!-- Tabs for Call History and Invoice History -->
            <div data-role="tabs" id="tabs">
                <ul>
                    <li><a href="#callHistory" data-toggle="tab">Calls</a></li>
                    <li><a href="#invoiceHistory" data-toggle="tab">Invoices</a></li>
                </ul>
                <div id="callHistory" class="tab-content">
                    <!-- <h3>Calls</h3> -->
                    <!-- Content for Call Log tab -->
                    <p class="text-secondary">Enter a phone number to view call history</p>
                </div>
                <div id="invoiceHistory" class="tab-content">
                    <h3>Invoice History</h3>
                    <!-- Content for Invoice History tab -->
                    <p>Invoice history details will be displayed here.</p>
                </div>
            </div>

            <script>
                $(document).ready(function() {
                    // Initialize the tabs
                    $('#tabs').tabs();
                });
            </script>

            <!--
              Script for clearing the form data.
              This function resets the phone number input,
              unchecks all call flags, hides any specify inputs as well as child flag containers,
              clears the follow-up date input, clears the call notes textarea,
              and updates the follow-up visibility.
            -->
            <script>
                function clearFormData() {
                    // Clear the content in the calls tab.
                    document.getElementById('callHistory').innerHTML = '';
                    // Hide the saveDoneButton if it exists.
                    // const saveDoneButton = document.getElementById('saveDoneButton');
                    // if (saveDoneButton) {
                    //     saveDoneButton.style.display = 'none';
                    // }
                    document.getElementById('caller_id_name').value = '';
                    // Clear the phone number field.
                    document.getElementById('phone_number').value = '';
                    
                    // Clear all checkboxes (except call origin), specify inputs, and hide child containers.
                    document.querySelectorAll('.form-check-input').forEach(checkbox => {
                        // Skip call origin radio buttons
                        if (!checkbox.name.includes('callOrigin')) {
                            checkbox.checked = false;
                            // const specifyDiv = document.getElementById(`specify_${checkbox.value}`);
                            // if (specifyDiv) {
                            //     specifyDiv.style.display = 'none';
                            //     specifyDiv.querySelector('input').value = '';
                            // }
                            const childrenDiv = document.getElementById(`children_${checkbox.value}`);
                            if (childrenDiv) {
                                childrenDiv.style.display = 'none';
                            }
                        }
                    });

                    // Reset the follow-up date to tomorrow but keep container visible
                    setDefaultFollowupDate();
                    
                    // Clear the call notes textarea.
                    document.getElementById('callnotes').value = '';
                }
                // When the Reset button is clicked, call clearFormData.
                document.getElementById('resetButton').addEventListener('click', clearFormData);
            </script>
            
            <!--
              Script for saving the form data.
              This collects the phone number, checked flags,
              and any specify input values associated with flags.
              The resulting data is logged as JSON.
            -->
            <script>
                // Updated Save function accepts an optional clearTodo parameter (default is 0)
                function saveFormData(clearTodo = 0) {

                    const todoCompleteCheckbox = document.getElementById('todoComplete');
                    if (todoCompleteCheckbox && todoCompleteCheckbox.checked) {
                        clearTodo = 1;
                    }

                    // Remove non-digit characters from the phone number.
                    const phoneNumberField = document.getElementById('phone_number');
                    const phoneNumber = phoneNumberField.value.replace(/[^\d]/g, '');

                    // Validate the phone number has exactly 10 digits.
                    if (phoneNumber.length !== 10) {
                        let errorDiv = document.getElementById('errorAlert');
                        if (!errorDiv) {
                            errorDiv = document.createElement('div');
                            errorDiv.id = 'errorAlert';
                            errorDiv.className = 'alert alert-danger mt-3';
                            errorDiv.innerText = 'Please enter a valid 10-digit phone number prior to submitting.';
                            phoneNumberField.parentNode.insertBefore(errorDiv, phoneNumberField);
                        } else {
                            errorDiv.innerText = 'Please enter a valid 10-digit phone number prior to submitting.';
                            errorDiv.style.display = 'block';
                        }
                        return;
                    } else {
                        let errorDiv = document.getElementById('errorAlert');
                        if (errorDiv) {
                            errorDiv.style.display = 'none';
                        }
                    }

                    // Validate that at least one call flag checkbox is selected.
                    const selectedFlagsElements = document.querySelectorAll('.flag-checkbox:checked');
                    if (selectedFlagsElements.length === 0) {
                        let errorDiv = document.getElementById('errorAlert');
                        const callFlagsContainer = document.getElementById('callFlagsContainer');
                        if (!errorDiv) {
                            errorDiv = document.createElement('div');
                            errorDiv.id = 'errorAlert';
                            errorDiv.className = 'alert alert-danger mt-3';
                            errorDiv.innerText = 'Please select at least one call flag before submitting.';
                            callFlagsContainer.parentNode.insertBefore(errorDiv, callFlagsContainer);
                        } else {
                            errorDiv.innerText = 'Please select at least one call flag before submitting.';
                            errorDiv.style.display = 'block';
                        }
                        return;
                    } else {
                        let errorDiv = document.getElementById('errorAlert');
                        if (errorDiv) {
                            errorDiv.style.display = 'none';
                        }
                    }

                    // Validate that a call origin is selected
                    const callOriginSelected = document.querySelector('input[name="callOrigin"]:checked');
                    if (!callOriginSelected) {
                        let errorDiv = document.getElementById('callOriginError');
                        const callOriginContainer = document.getElementById('callOriginContainer');
                        if (!errorDiv) {
                            errorDiv = document.createElement('div');
                            errorDiv.id = 'callOriginError';
                            errorDiv.className = 'alert alert-danger mt-2';
                            errorDiv.style.fontSize = '0.875rem';
                            errorDiv.innerText = 'Please select a call origin before submitting.';
                            callOriginContainer.appendChild(errorDiv);
                        } else {
                            errorDiv.style.display = 'block';
                        }
                        return;
                    } else {
                        let errorDiv = document.getElementById('callOriginError');
                        if (errorDiv) {
                            errorDiv.style.display = 'none';
                        }
                    }

                    // Process checked flags.
                    const selectedFlags = [];
                    selectedFlagsElements.forEach(checkbox => {
                        if (checkbox.id.startsWith('flag_')) {
                            const flagId = checkbox.value;
                            selectedFlags.push({ id: flagId });
                        }
                    });
            
                    // Retrieve the follow-up date and call notes values.
                    const followupDate = document.getElementById('followupDate').value;
                    const callNotes = document.getElementById('callnotes').value;
                    
                    // Retrieve the selected call origin value.
                    const callOrigin = document.querySelector('input[name="callOrigin"]:checked').value;
                    
                    // Get the caller ID name value.
                    const callerIdNameField = document.getElementById('caller_id_name');
                    const callerIdName = callerIdNameField ? callerIdNameField.value : '';
            
                    // Retrieve the "Close Lead" value.
                    const closeLeadCheckbox = document.getElementById('close_lead');
                    const closeLead = closeLeadCheckbox.checked ? 1 : 0;
            
                    // If Close Lead is selected, get the lead result value.
                    let leadResult = null;
                    if (closeLead) {
                        const leadResultRadio = document.querySelector('input[name="lead_result"]:checked');
                        if (leadResultRadio) {
                            leadResult = parseInt(leadResultRadio.value, 10);
                        }
                    }
            
                    // Create an object with the collected form data.
                    const formData = {
                        phone_number: phoneNumber,
                        flags: selectedFlags,
                        followup_date: followupDate,
                        call_notes: callNotes,
                        call_origin: callOrigin,
                        caller_id_name: callerIdName,
                        close_lead: closeLead,
                        lead_result: leadResult,
                        clearTodo: 0
                    };
            
                    // Add the clearTodo property if clearTodo parameter is provided.
                    if (clearTodo) {
                        formData.clearTodo = clearTodo;
                    }
                    
                    // Output the JSON data to the console before posting it.
                    console.log("JSON Data:", JSON.stringify(formData, null, 2));
            
                    // Send an AJAX request to callscreen.php with the JSON data.
                    $.ajax({
                        url: 'callscreen.php',
                        type: 'POST',
                        data: {
                            data: JSON.stringify(formData)
                        },
                        success: function(response) {
                            console.log('Server response:', response);
                            // Clear the form after a successful save.
                            clearFormData();
                            if (clearTodo === 1) {
                                window.location.href = 'todos.php';
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log('AJAX error:', status, error);
                        }
                    });
                }
            
                // Event listener for saveButton.
                document.getElementById('saveButton').addEventListener('click', function() {
                    saveFormData(0);
                });
            
                // Function to fetch the most recent 10 calls based on a partial phone number
                function fetchRecentCalls(partialPhoneNumber) {
                    if (partialPhoneNumber.length < 3) {
                        // Clear the dropdown if the input is less than 3 characters
                        $("#recentCallsDropdown").empty().hide();
                        return;
                    }
            
                    $.ajax({
                        url: 'callscreen_getcalls2.php',
                        type: 'GET',
                        data: {
                            partial_number: partialPhoneNumber // Pass the partial phone number
                        },
                        success: function(response) {
                            const data = JSON.parse(response);
                            displayRecentCallsDropdown(data);
                        },
                        error: function(xhr, status, error) {
                            console.log('AJAX error:', status, error);
                        }
                    });
                }
            
                // Function to display the dropdown with recent calls
                // Function to display the dropdown with recent calls
                function displayRecentCallsDropdown(calls) {
                    const dropdown = $("#recentCallsDropdown");
                    const phoneNumberInput = $("#phone_number");
                    dropdown.empty(); // Clear existing items
                
                    if (calls.length === 0) {
                        dropdown.hide(); // Hide the dropdown if no results
                        return;
                    }
                
                    // Position the dropdown below the phone_number input field
                    const inputOffset = phoneNumberInput.offset();
                    const inputHeight = phoneNumberInput.outerHeight();
                    dropdown.css({
                        top: inputOffset.top + inputHeight, // Position below the input field
                        left: inputOffset.left, // Align with the left edge of the input field
                        width: phoneNumberInput.outerWidth(), // Match the width of the input field
                        display: 'block', // Ensure the dropdown is visible
                    });
                
                    // Populate the dropdown with the recent calls
                    calls.forEach(call => {
                        const formattedPhoneNumber = formatPhoneNumber(call.phone_number);
                        const listItem = $(`<div class="dropdown-item">${formattedPhoneNumber} - ${call.caller_id_name || 'Unknown'}</div>`);
                        listItem.on('click', function() {
                            // Populate the phone_number field and trigger searchAllCalls
                            phoneNumberInput.val(formattedPhoneNumber);
                            searchAllCalls(call.phone_number);
                            dropdown.empty().hide(); // Clear and hide the dropdown
                        });
                        dropdown.append(listItem);
                    });
                }
            
                // Event listener for the phone_number input field
                $("#phone_number").on('input', function(e) {
                    const partialPhoneNumber = e.target.value.replace(/[^\d]/g, ''); // Remove non-digit characters
                    fetchRecentCalls(partialPhoneNumber); // Fetch recent calls based on the partial number
                });
            </script>
            
            <!-- Add a dropdown container below the phone_number input field -->
            <div id="recentCallsDropdown" class="dropdown-menu" style="display: none; position: absolute; z-index: 1000;">
                <style>
                    #recentCallsDropdown {
                        max-height: 200px; /* Limit the height of the dropdown */
                        overflow-y: auto; /* Add a scrollbar if the content exceeds the height */
                        border: 1px solid #ccc; /* Add a border for better visibility */
                        background-color: #fff; /* Ensure the background is white */
                        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Add a subtle shadow */
                        z-index: 1000; /* Ensure it appears above other elements */
                    }
                </style>                
            </div>
        </div>
        
        <!--
          Footer section of the page (currently empty).
        -->
        <div data-role="footer">
            <h4>&nbsp;</h4>
        </div>
    </div>
    
    <!--
      Additional scripts to handle phone number formatting and searching for calls.
    -->
    <script>
        // --------------------------------------------------
        // Function: formatPhoneNumber
        // Purpose: Formats a text input value into a US phone number format.
        //          Examples: "123" remains "123", "1234567" becomes "(123) 456-7".
        // Parameters: value - a string representing the phone number.
        // Returns: The formatted phone number string.
        // --------------------------------------------------
        function formatPhoneNumber(value) {
            console.log('formatPhoneNumber called with:', value);
            if (!value) return value;
            const phoneNumber = String(value).replace(/[^\d]/g, ''); // Remove non-digit chars.
            const phoneNumberLength = phoneNumber.length;
            if (phoneNumberLength < 4) return phoneNumber;
            if (phoneNumberLength < 7) {
                return `(${phoneNumber.slice(0, 3)}) ${phoneNumber.slice(3)}`;
            }
            return `(${phoneNumber.slice(0, 3)}) ${phoneNumber.slice(3, 6)}-${phoneNumber.slice(6, 10)}`;
        }
    
        // --------------------------------------------------
        // Function: searchAllCalls
        // Purpose: Logs a message for demo purposes indicating it is searching.
        // Parameters: phoneNumber - a string containing a clean phone number.
        // --------------------------------------------------
        function searchAllCalls(phoneNumber) {
            console.log(`Searching for calls with phone number: ${phoneNumber}`);
            $.ajax({
                url: 'callscreen_getcalls2.php',
                type: 'GET',
                data: {
                    number: phoneNumber
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    searchCallsResults(data);
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error:', status, error);
                }
            });
    
            function searchCallsResults(data) {
                console.log('Search results:', data);
                // Clear any existing results inside the Call Log tab content area
                document.getElementById('caller_id_name').value = '';
                $("#callHistory").empty();
    
                // Create a collapsibleset container to hold the collapsible items
                let collapsibleSet = $('<div data-role="collapsibleset" data-theme="a" data-content-theme="a"></div>');
                
                // Loop through each call record in the data array
                data.forEach(call => {
                    // Process the flags to auto-check the checkboxes.
                    if (call.flags && Array.isArray(call.flags)) {
                        call.flags.forEach(flag => {
                            // flag.flag_id is the field from the AJAX response.
                            let checkbox = document.getElementById(`flag_${flag.flag_id}`);
                            if (checkbox) {
                                checkbox.checked = true;
                                // Optionally, show the specify input if needed:
                                // const specifyDiv = document.getElementById(`specify_${flag.flag_id}`);
                                // if (specifyDiv) {
                                //     specifyDiv.style.display = 'block';
                                // }
                                // Show the children container if it exists to un-collapse any child flags.
                                const childrenDiv = document.getElementById(`children_${flag.flag_id}`);
                                if (childrenDiv) {
                                    childrenDiv.style.display = 'block';
                                }
                            }
                        });
                    }
                    // Construct header text using call date/time and origin
                    let callDate = new Date(call.call_datetime);
                    let formattedDate = `${(callDate.getMonth() + 1).toString().padStart(2, '0')}/${callDate.getDate().toString().padStart(2, '0')}/${callDate.getFullYear()} ${callDate.getHours().toString().padStart(2, '0')}:${callDate.getMinutes().toString().padStart(2, '0')}`;
                    let headerText = `Date: ${formattedDate} - ${call.call_origin}`;
                    function searchCallsResults(data) {
                        console.log('Search results:', data);
                        // Clear any existing results inside the Call Log tab content area
                        $("#callHistory").empty();

                        // Create a collapsibleset container to hold the collapsible items
                        let collapsibleSet = $('<div data-role="collapsibleset" data-theme="a" data-content-theme="a"></div>');
                        
                        // Loop through each call record in the data array
                        data.forEach(call => {
                            // Construct header text using call date/time and origin
                            let callDate = new Date(call.call_datetime);
                            let formattedDate = `${(callDate.getMonth() + 1).toString().padStart(2, '0')}/${callDate.getDate().toString().padStart(2, '0')}/${callDate.getFullYear()} ${callDate.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true })}`;
                            let headerText = `Date: ${formattedDate} - ${call.call_origin}`;
                            
                            let callerIdNameElement = document.getElementById('caller_id_name');
                            if (call.caller_id_name && callerIdNameElement && !callerIdNameElement.value.trim()) {
                                callerIdNameElement.value = call.caller_id_name;
                            }
                            // Build the collapsible element with full call details
                            let collapsible = $(`
                                <div data-role="collapsible" style="font-size: 10px;">
                                    <hr/>
                                    <strong>${headerText}</strong><br/>
                                    ${call.notes ? `<strong>Notes:</strong> ${call.notes}<br/>` : ''}
                                    ${call.message ? `<strong>Message:</strong> ${call.message}<br/>` : ''}
                                </div>
                            `);
                            
                            // If one or more flags exist for the call, create a list of flags
                            if (call.flags && Array.isArray(call.flags) && call.flags.length > 0) {
                                let flagList = $('<ul></ul>');
                                call.flags.forEach(flag => {
                                    // removed specify
                                    //  ${flag.flag_specify ? `${flag.flag_specify}` : ''}
                                    flagList.append(`<li>
                                        <strong>${flag.flag_name}</strong>
                                    </li>`);
                                });
                                collapsible.append('<p><strong>Flags:</strong></p>');
                                collapsible.append(flagList);
                            }
                            
                            // Append this collapsible item into the set
                            collapsibleSet.append(collapsible);
                        });

                        // Append the collapsibleset to the callHistory element
                        $("#callHistory").append(collapsibleSet);
                    }
                    if (call.caller_id_name && !document.getElementById('caller_id_name').value.trim()) {
                        document.getElementById('caller_id_name').value = call.caller_id_name;
                    }
                    // Build the collapsible element with full call details
                    let collapsible = $(`
                        <div data-role="collapsible" style="font-size: 10px;">
                            <hr/>
                            <strong>${headerText}</strong><br/>
                            ${call.notes ? `<strong>Notes:</strong> ${call.notes}<br/>` : ''}
                            ${call.message ? `<strong>Message:</strong> ${call.message}<br/>` : ''}
                        </div>
                    `);
                    
                    // If one or more flags exist for the call, create a list of flags
                    if (call.flags && Array.isArray(call.flags) && call.flags.length > 0) {
                        let flagList = $('<ul></ul>');
                        call.flags.forEach(flag => {
                            // remove specify
                            // ${flag.flag_specify ? `${flag.flag_specify}` : ''}
                            flagList.append(`<li>
                                <strong>${flag.flag_name}</strong>
                            </li>`);
                        });
                        collapsible.append('<p><strong>Flags:</strong></p>');
                        collapsible.append(flagList);
                    }
                    
                    // Append this collapsible item into the set
                    collapsibleSet.append(collapsible);
                });

                // Append the collapsibleset to the "Call Log" tab area and enhance it with jQuery Mobile features
                $("#callHistory").append(collapsibleSet).trigger("create");
            }
        }

        // --------------------------------------------------
        // Event Listener: Phone Number Input
        // Purpose: Formats the phone number field as the user types.
        //          When exactly 10 digits are entered, it triggers a search.
        // --------------------------------------------------
        document.getElementById('phone_number').addEventListener('input', function (e) {
            const formattedPhoneNumber = formatPhoneNumber(e.target.value);
            e.target.value = formattedPhoneNumber;
            const phoneNumber = e.target.value.replace(/[^\d]/g, '');
            if (phoneNumber.length === 10) {
                // Call search function when a valid 10-digit number is entered.
                searchAllCalls(phoneNumber);
            }
        });

        // --------------------------------------------------
        // Event Listener: Search Button
        // Purpose: When clicked, retrieves the phone number value (only digits)
        //          and calls the searchAllCalls function if 10 digits are present.
        //          Otherwise, alerts the user.
        // --------------------------------------------------
        document.getElementById('searchcalls').addEventListener('click', function () {
            const phoneNumber = document.getElementById('phone_number').value.replace(/[^\d]/g, '');
            if (phoneNumber.length === 10) {
                searchAllCalls(phoneNumber);
            } else {
                alert('Please enter a valid 10-digit phone number.');
            }
        });

        // --------------------------------------------------
        // Function: getCallFlags
        // Purpose: Placeholder that returns the callFlags array.
        //          In a real application, this might retrieve flags from a database.
        // --------------------------------------------------
        function getCallFlags() {
            return callFlags;
        }

        
        // Check if the URL has a query string variable named 'number' and store its value in a variable named 'phonenumber'.
        const urlParams = new URLSearchParams(window.location.search);
        const phonenumber = urlParams.get('number');
        // If the 'phonenumber' query string variable exists, insert it into the phone number input field and run the search calls method.
        if (phonenumber) {
            document.getElementById('originOutgoing').checked = true;
            const formattedPhoneNumber = formatPhoneNumber(phonenumber);
            document.getElementById('phone_number').value = formattedPhoneNumber;
            searchAllCalls(phonenumber);
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('todo') === '1') {
                document.getElementById('todoRow').style.display = 'flex';
            }
        });
        
        // Existing event listener for saveButton.
        // document.getElementById('saveButton').addEventListener('click', function() {
        //     saveFormData(0);
        // });
        
    </script>
</body>
</html>