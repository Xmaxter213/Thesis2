<?php
#bg-primary blue, bg-warning yellow, bg-success green, bg-danger red
function assistanceCard($patient_ID, $patient_Name, $room_Number, $birth_Date, $reason_Admission, $admission_Status, $nurse_ID, $assistance_Status, $gloves_ID, $nurse_name, $contact_No, $assistance_Type, $IMMEDIATE_calls, $ADL_calls, $assigned_Ward)
{
    $cardClasses = $assistance_Status == "Unassigned" ? "rgba(220,53,69, 0.25)" : "rgba(14,202,240, 0.25)";
    $bgClasses = $assistance_Status == "Unassigned" ? "bg-danger" : "bg-primary";
    $btnClasses = $assistance_Status == "Unassigned" ? "btn-danger" : "btn-primary";
    // $requestBadge = $assistance_Status == "Unassigned" ? "badge-danger" : "badge-primary";
    // $requestCounts = "";

    // if ($assistance_Type != "IMMEDIATE") {
    //     $requestCounts = "<h6>Call counts <span class=\"badge $requestBadge\">$ADL_calls</span></h6>";
    // } else {
    //     $requestCounts = "<h6>Call counts <span class=\"badge $requestBadge\">$IMMEDIATE_calls</span></h6>";
    // }

    $element = "
    <div class=\"col-lg-4\" style=\"max-width: 25rem\">
    <div class=\"card px-0\" style=\"color: black; background: $cardClasses\">
        <div class=\"card-body\">
            <h5 class=\"font-weight-bold\">Patient Name: <span class=\"font-weight-normal\">$patient_Name</span> <span class=\"badge $bgClasses text-white\">$assistance_Status</span></h5>
            <h5 class=\"font-weight-bold\">Room #: $room_Number</h5>
            <br>
            <div class=\"d-flex justify-content-between align-items-center\">
                <button type=\"button\" class=\"btn $btnClasses\" data-toggle=\"modal\" data-target=\"#view-{$patient_ID}\" onclick=\"turnOffRefreshImmediate(); turnOffRefreshADL();\">View Details</button>
            </div>

            <div class=\"modal fade\" tabindex=\"-1\" id=\"view-{$patient_ID}\" role=\"dialog\" aria-labelledby=\"viewModalLabel\" aria-hidden=\"true\">
                <div class=\"modal-dialog\" role=\"document\">
                    <div class=\"modal-content\">
                        <div class=\"modal-header\">
                            <h5 class=\"modal-title\" id=\"exampleModalLabel\">Patient Details</h5>
                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                <span aria-hidden=\"true\">&times;</span>
                            </button>
                        </div>
                        <div class=\"modal-body\">
                            <h6 class=\"font-weight-bold\">Patient Name: <span class=\"font-weight-normal\">$patient_Name</span></h6>
                            <h6 class=\"font-weight-bold\">Room Number: <span class=\"font-weight-normal\">$room_Number</span></h6>
                            <h6 class=\"font-weight-bold\">Age: <span class=\"font-weight-normal\">$birth_Date</h6>
                            <h6 class=\"font-weight-bold\">Reason for Admission: <span class=\"font-weight-normal text-justify\">$reason_Admission</span></h6>
                            <h6 class=\"font-weight-bold\">Assistance Status: <span class=\"font-weight-normal\">$assistance_Status</span></h6>
                            <h6 class=\"font-weight-bold\">Nurse Name: <span class=\"font-weight-normal\">$nurse_name</span></h6>
                            <h6 class=\"font-weight-bold\">Contact Number: <span class=\"font-weight-normal\">$contact_No</span></h6>
                            <h6 class=\"font-weight-bold\">Nurse Assigned Ward: <span class=\"font-weight-normal\">$assigned_Ward</span></h6>
                        </div>
                        <div class=\"modal-footer\">
                            <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    ";

    echo $element;
}
?>

<!-- JavaScript for AJAX request and handling form submission -->
<script>
    function changeStatus(patientID) {
        $.ajax({
            url: 'statusChange.php',
            method: 'POST',
            data: { patientID: patientID },
            success: function (response) {
                // Handle response if needed
                console.log(response);
            },
            error: function (xhr, status, error) {
                // Handle error if needed
                console.error(xhr.responseText);
            }
        });
    }

    function submitAssistanceForm(patientID) {
        var remarks = $('#remarksInput-' + patientID).val().trim();
        if (remarks !== '') {
            $('#submitAssistanceModal-' + patientID).modal('show');
        } else {
            alert('Please enter remarks before submitting.');
        }
    }

    function confirmAssistanceSubmission(patientID) {
        var remarks = $('#remarksInput-' + patientID).val().trim();
        $.ajax({
            url: 'submitAssistance.php',
            method: 'POST',
            data: { patientID: patientID, remarks: remarks },
            success: function (response) {
                // Handle response if needed
                console.log(response);
                // Reload the page after successful submission
                location.reload();
            },
            error: function (xhr, status, error) {
                // Handle error if needed
                console.error(xhr.responseText);
            }
        });
    }
</script>