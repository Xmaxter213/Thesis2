<?php
session_start();

// Database configurations
$firstDb = array(
    'host' => 'db4free.net',
    'user' => 'userthesis2',
    'pass' => 'dbThesis123',
    'name' => 'dbthesis2'
);

$secondDb = array(
    'host' => 'db4free.net',
    'user' => 'dbhospital1',
    'pass' => 'dbhospital1',
    'name' => 'dbhospital1'
);

$thirdDb = array(
    'host' => 'db4free.net',
    'user' => 'dbhospital2',
    'pass' => 'dbhospital2',
    'name' => 'dbhospital2'
);

// Function to switch database
function switchDatabase($con, $dbConfig) {
    mysqli_close($con); // Close the current connection

    // Connect to the new database
    if (!$newCon = mysqli_connect($dbConfig['host'], $dbConfig['user'], $dbConfig['pass'], $dbConfig['name'])) {
        die("Failed to connect to the database!");
    }

    return $newCon;
}

$con = mysqli_connect($firstDb['host'], $firstDb['user'], $firstDb['pass'], $firstDb['name']);

if (!$con) {
    die("Failed to connect to the first database!");
}