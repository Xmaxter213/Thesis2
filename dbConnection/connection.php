<?php
// if(session_status() == PHP_SESSION_NONE){
//     session_start();
// }

@session_start();
// $dbhost = "localhost";
// $dbuser = "root";
// $dbpass = "";
// $dbname = "advweb";

$dbhost = "db4free.net";
$dbuser = "userthesis2";
$dbpass = "dbThesis123";
$dbname = "dbthesis2";

// $dbhost = "sql207.infinityfree.com";
// $dbuser = "if0_35733375";
// $dbpass = "gXGcjb07QLy3";
// $dbname = "if0_35733375_Thesis2";

if (!$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname))
{
    die("failed to connect!" . mysqli_connect_error());
}

//This is used on add book.php to add to db using this variable
//$db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);