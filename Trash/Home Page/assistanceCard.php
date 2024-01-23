<?php
#bg-primary blue, bg-warning yellow, bg-success green, bg-danger red
function assistanceCard($patient_ID, $patient_Name, $room_Number, $age, $admission_Status, $nurse_ID, $assistance_Status, $gloves_ID)
{
    $cardClasses = $assistance_Status == "Unassigned" ? "bg-danger" : "bg-info";
    $bgClasses = $assistance_Status == "Unassigned" ? "bg-danger" : "bg-primary";
    $btnClasses = $assistance_Status == "Unassigned" ? "btn-danger" : "btn-primary";

    $element = "
    <div class=\"col-xl-3 col-md-6\">
        <div class=\"card px-0 text-black $cardClasses bg-opacity-25\" style=\"width: 25rem;\">
            <img src=\"./Images/room.jpg\" class=\"card-img-top\" alt=\"...\">
            <div class=\"card-body\">
                <h5 class=\"card-title\">Patient Name: <span class=\"fw-normal\">$patient_Name</span> <span class=\"badge $bgClasses\">$assistance_Status</span></h5>
                <h5 class=\"card-title\">Room #: $room_Number</h5>
                <form action=\"index.php\">
                    <div class=\"d-flex align-items-center justify-content-center\">
                        <h5 class=\"me-2 mb-0\">Remarks: </h5>
                        <div class=\"input-group\">
                            <input type=\"text\" class=\"form-control\" placeholder=\"Assistance Given\" aria-describedby=\"button-addon\">
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
