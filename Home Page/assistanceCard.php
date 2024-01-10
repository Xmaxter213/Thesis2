<?php
#bg-primary blue, bg-warning yellow, bg-success green, bg-danger red
function assistanceCard($patient_ID, $patient_Name, $room_Number, $age, $admission_Status, $nurse_Name, $assistance_Status, $device_Assigned){
    if ($assistance_Status == "Unassigned")
    {
        $assistance_Color = "bg-danger";
    }
    else if ($assistance_Status == "On the way")
    {
        $assistance_Color = "bg-primary";
    } else
    {
        $assistance_Color = NULL;
    }

    $element ="<div class=\"col-xl-3 col-md-6\">
                                <div class=\"card $assistance_Color text-white mb-4\">
                                    <div class=\"card-body\">Patient Name: $patient_Name</div>
                                    <div class=\"card-body\">Room #: $room_Number</div>
                                    <form action=\"index.php\">
                                        <label style = \"margin-left: 15px\" for=\"remark\">Remark: </label>
                                        <input type=\"text\" id=\"remark\" name=\"remark\"><br><br>
                                        <input style = \"margin-left: 40%\" type=\"submit\" value=\"Submit\">
                                    </form>

                                    <div class=\"card-footer d-flex align-items-center justify-content-between\">
                                        <a class=\"small text-white\" href=\"#\">View Details</a>
                                        <div class=\"small text-white\"><i class=\"fas fa-angle-right\"></i></div>
                                    </div>
                                </div>
                            </div>
    ";
    
    echo $element;
}