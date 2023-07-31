<?php

// $dbhost = "localhost";
// $dbuser = "root";
// $dbpass = "";
// $dbname = "advweb";

$dbhost = "db4free.net";
$dbuser = "userthesis2";
$dbpass = "dbThesis123";
$dbname = "dbthesis2";

if (!$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname))
{
    die("failed to connect!");
}

//This is used on add book.php to add to db using this variable
//$db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);