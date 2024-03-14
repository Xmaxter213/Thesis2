<?php
#bg-primary blue, bg-warning yellow, bg-success green, bg-danger red
function assistanceCard($patient_ID, $patient_Name, $room_Number, $birth_Date, $reason_Admission, $admission_Status, $nurse_ID, $assistance_Status, $gloves_ID)
{
    $cardClasses = $assistance_Status == "Unassigned" ? "rgba(220,53,69, 0.25)" : "rgba(14,202,240, 0.25)";
    $bgClasses = $assistance_Status == "Unassigned" ? "bg-danger" : "bg-primary";
    $btnClasses = $assistance_Status == "Unassigned" ? "btn-danger" : "btn-primary";

    // Check if admission status is "On the way" 
    // Added inProgressPage.php once clicked it will be redirected to 'On the Way' Page
    $changeStatusButton = "<button type=\"button\" href=\"./inProgressPage.php\" class=\"btn btn-secondary\" disabled>Change Status</button>";

    if ($assistance_Status != "On The Way") {
        $changeStatusButton = "<button type=\"button\" href=\"#\" class=\"btn btn-info\" data-toggle=\"modal\" data-target=\"#changeStatusModal-{$patient_ID}\">Change Status</button>";
    }

    $element = "
    <div class=\"col-lg-4\" style=\"max-width: 25rem;\">
        <div class=\"card px-0\" style=\"color: black; background: $cardClasses;\">
            <img src=\"./Images/room.jpg\" class=\"card-img-top\" alt=\"...\">
            <div class=\"card-body\">
                <h5 class=\"font-weight-bold\">Patient Name: <span class=\"font-weight-normal\">$patient_Name</span> <span class=\"badge $bgClasses text-white\">$assistance_Status</span></h5>
                <h5 class=\"font-weight-bold\">Room #: $room_Number</h5>
                <form id=\"assistanceForm-$patient_ID\" action=\"javascript:void(0);\">
                    <div class=\"d-flex align-items-center justify-content-center\">
                        <h5 class=\"me-2 mb-0\">Remarks: </h5>
                        <div class=\"input-group\">
                            <input type=\"text\" class=\"form-control\" id=\"remarksInput-$patient_ID\" placeholder=\"Enter Remarks\" aria-describedby=\"button-addon\" required " . ($assistance_Status == 'Unassigned' ? 'disabled' : '') . ">
                            <button class=\"btn $btnClasses\" type=\"submit\" id=\"button-addon\" onclick=\"submitAssistanceForm('$patient_ID')\">Submit</button>
                        </div>
                    </div>
                </form>
                <br>
                $changeStatusButton

                <!-- Change Status Modal -->
                <div class=\"modal fade\" tabindex=\"-1\" id=\"changeStatusModal-{$patient_ID}\" role=\"dialog\" aria-labelledby=\"changeStatusModalLabel-{$patient_ID}\" aria-hidden=\"true\">
                    <div class=\"modal-dialog\" role=\"document\">
                        <div class=\"modal-content\">
                            <div class=\"modal-header\">
                                <h5 class=\"modal-title\" id=\"changeStatusModalLabel-{$patient_ID}\">Change Status</h5>
                                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                    <span aria-hidden=\"true\">&times;</span>
                                </button>
                            </div>
                            <div class=\"modal-body\">
                                <p>Change status to 'On the way'?</p>
                            </div>
                            <div class=\"modal-footer\">
                                <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">No</button>
                                <button type=\"button\" class=\"btn btn-primary\" onclick=\"changeStatus('$patient_ID')\">Yes</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Status Modal -->
                <div class=\"modal fade\" tabindex=\"-1\" id=\"submitAssistanceModal-{$patient_ID}\" role=\"dialog\" aria-labelledby=\"changeStatusModalLabel-{$patient_ID}\" aria-hidden=\"true\">
                    <div class=\"modal-dialog\" role=\"document\">
                        <div class=\"modal-content\">
                            <div class=\"modal-header\">
                                <h5 class=\"modal-title\" id=\"changeStatusModalLabel-{$patient_ID}\">Change Status</h5>
                                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                    <span aria-hidden=\"true\">&times;</span>
                                </button>
                            </div>
                            <div class=\"modal-body\">
                                <p>Finished attending patient?</p>
                            </div>
                            <div class=\"modal-footer\">
                                <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">No</button>
                                <button type=\"button\" class=\"btn btn-primary\" onclick=\"confirmAssistanceSubmission('$patient_ID')\">Yes</button>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
<!-- JavaScript for AJAX request and handling form submission -->
<script>
    function changeStatus(patientID) {
        $.ajax({
            url: 'assistanceStatusChange.php',
            method: 'POST',
            data: { patientID: patientID },
            success: function(response) {
                // Handle response if needed
                console.log(response);
            },
            error: function(xhr, status, error) {
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
            url: 'assistanceSubmit.php',
            method: 'POST',
            data: { patientID: patientID, remarks: remarks },
            success: function(response) {
                // Handle response if needed
                console.log(response);
                // Reload the page after successful submission
                location.reload();
            },
            error: function(xhr, status, error) {
                // Handle error if needed
                console.error(xhr.responseText);
            }
        });
    }
</script>
