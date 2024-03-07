<?php
#bg-primary blue, bg-warning yellow, bg-success green, bg-danger red
function assistanceCard($patient_ID, $patient_Name, $room_Number, $birth_Date, $reason_Admission, $admission_Status, $nurse_ID, $assistance_Status, $gloves_ID)
{
    $cardClasses = $assistance_Status == "Unassigned" ? "rgba(220,53,69, 0.25)" : "rgba(14,202,240, 0.25)";
    $bgClasses = $assistance_Status == "Unassigned" ? "bg-danger" : "bg-primary";
    $btnClasses = $assistance_Status == "Unassigned" ? "btn-danger" : "btn-primary";

    // Check if admission status is "On the way"
    $changeStatusButton = "<button type=\"button\" href=\"#\" class=\"btn btn-secondary\" disabled>Change Status</button>";

    if ($assistance_Status != "On The Way") {
        $changeStatusButton = "<button type=\"button\" href=\"#\" class=\"btn btn-info\" data-toggle=\"modal\" data-target=\"#changeStatusModal-{$patient_ID}\">Change Status</button>";
    }

    $element = "
    <div class=\"col-lg-4 my-3\">
    <div class=\"card px-0\" style=\"color: black; background: $cardClasses\">
        <img src=\"./Images/room.jpg\" class=\"card-img-top\" alt=\"...\">
        <div class=\"card-body\">
            <h5 class=\"font-weight-bold\">Patient Name: <span class=\"font-weight-normal\">$patient_Name</span> <span class=\"badge $bgClasses text-white\">$assistance_Status</span></h5>
            <h5 class=\"font-weight-bold\">Room #: $room_Number</h5>
            <form action=\"./assistanceCard.php\">
                <div class=\"d-flex align-items-center justify-content-center\">
                    <h5 class=\"me-2 mb-0\">Remarks: </h5>
                    <div class=\"input-group\">
                        <input type=\"text\" class=\"form-control\" placeholder=\"Enter Remarks\" aria-describedby=\"button-addon\" required>
                        <button class=\"btn $btnClasses\" type=\"submit\" id=\"button-addon\">Submit</button>
                    </div>
                </div>
            </form>
            <br>
            $changeStatusButton
            <!-- Change Status Modal -->
                <div class=\"modal fade\" tabindex=\"-1\" id=\"changeStatusModal-{$patient_ID}\" role=\"dialog\" aria-labelledby=\"changeStatusModalLabel-{$patient_ID}\" aria-hidden=\"true\">
                    <!-- Modal content -->
                </div>
                <!-- Submit Status Modal -->
                <div class=\"modal fade\" tabindex=\"-1\" id=\"submitAssistanceModal-{$patient_ID}\" role=\"dialog\" aria-labelledby=\"changeStatusModalLabel-{$patient_ID}\" aria-hidden=\"true\">
                    <!-- Modal content -->
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
