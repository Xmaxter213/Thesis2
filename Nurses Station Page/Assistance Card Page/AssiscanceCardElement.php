<?php
#bg-primary blue, bg-warning yellow, bg-success green, bg-danger red
function assistanceCard($patient_ID, $patient_Name, $room_Number, $birth_Date, $reason_Admission, $admission_Status, $nurse_ID, $assistance_Status, $gloves_ID, $device_ID, $ADL_calls, $max_ADL_response_Time, $IMMEDIATE_calls, $max_IMMEDIATE_response_Time, $pulse_Rate, $date_called, $pulse_Rate_Status, $nurse_contact)
{
    $cardClasses = $assistance_Status == "Unassigned" ? "rgba(220,53,69, 0.25)" : "rgba(14,202,240, 0.25)";
    $bgClasses = $assistance_Status == "Unassigned" ? "bg-danger" : "bg-primary";
    $btnClasses = $assistance_Status == "Unassigned" ? "btn-danger" : "btn-primary";

    // $cardClasses = $assistance_Status == "Unassigned" ? "rgba(220,53,69, 0.25)" : ($assistance_Status == "Completed" ? "rgb(191, 249, 190)" : "rgba(14,202,240, 0.25)");
    // $bgClasses = $assistance_Status == "Unassigned" ? "bg-danger" : ($assistance_Status == "Completed" ? "bg-success" : "bg-primary");
    // $btnClasses = $assistance_Status == "Unassigned" ? "btn-danger" : ($assistance_Status == "Completed" ? "btn-success" : "btn-primary");

    $element = "
    <div class=\"col-lg-4\" style=\" max-width: 25rem \">
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
            <button type=\"button\" href=\"#\" class=\"btn $btnClasses\" data-toggle=\"modal\" data-target=\"#view-{$patient_ID}\">View Details</button>

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
                            <h6 class=\"font-weight-bold\">Age: <span class=\"font-weight-normal\">$birth_Date</span></h6><br>
                            <h6 class=\"font-weight-bold\">ADL Assistance Count: <span class=\"font-weight-normal\">$ADL_calls</span></h6>
                            <h6 class=\"font-weight-bold\">ADL Average Response Time: <span class=\"font-weight-normal\">$max_ADL_response_Time seconds</span></h6><br>
                            <h6 class=\"font-weight-bold\">Immediate Assistance Count: <span class=\"font-weight-normal\">$IMMEDIATE_calls</span></h6>
                            <h6 class=\"font-weight-bold\">Immediate Average Response Time: <span class=\"font-weight-normal\">$max_IMMEDIATE_response_Time seconds</span></h6><br>
                            <h6 class=\"font-weight-bold\">Reason for Admission: <span class=\"font-weight-normal text-justify\">$reason_Admission</span></h6>
                            <h6 class=\"font-weight-bold\">Assistance Status: <span class=\"font-weight-normal\">$assistance_Status</span></h6>
                            <h6 class=\"font-weight-bold\">Date & Time of Critical Pulse Rate: <span class=\"font-weight-normal\">$pulse_Rate_Status</span></h6>
                            <h6 class=\"font-weight-bold\">Nurse Contact No.: <span class=\"font-weight-normal\">$nurse_contact</span></h6>
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