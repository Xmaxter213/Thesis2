<?php
#bg-primary blue, bg-warning yellow, bg-success green, bg-danger red
function assistanceCard($patient_ID, $patient_Name, $room_Number, $age, $admission_Status, $nurse_ID, $assistance_Status, $gloves_ID)
{
    $cardClasses = $assistance_Status == "Unassigned" ? "rgba(220,53,69, 0.25)" : "rgba(14,202,240, 0.25)";
    $bgClasses = $assistance_Status == "Unassigned" ? "bg-danger" : "bg-primary";
    $btnClasses = $assistance_Status == "Unassigned" ? "btn-danger" : "btn-primary";

    $element = "
    <div class=\"col-xl-3 col-md-6\">
        <div class=\"card px-0\" style=\"width: 25rem; color: black; background: $cardClasses\">
            <img src=\"./Images/room.jpg\" class=\"card-img-top\" alt=\"...\">
            <div class=\"card-body\">
                <h5 class=\"font-weight-bold\">Patient Name: <span class=\"font-weight-normal\">$patient_Name</span> <span class=\"badge $bgClasses text-white\">$assistance_Status</span></h5>
                <h5 class=\"font-weight-bold\">Room #: $room_Number</h5>
                <form action=\"index.php\">
                    <div class=\"d-flex align-items-center justify-content-center\">
                        <h5 class=\"me-2 mb-0\">Remarks: </h5>
                        <div class=\"input-group\">
                            <input type=\"text\" class=\"form-control\" placeholder=\"Enter Remarks\" aria-describedby=\"button-addon\">
                            <button class=\"btn $btnClasses\" type=\"submit\" id=\"button-addon\">Submit</button>
                        </div>
                    </div>
                </form>
                <br>
                <a href=\"#\" class=\"btn $btnClasses\">View Details</a>
            </div>
        </div>
    </div>
    ";

    echo $element;
}
