<?php
// Database configuration
$host = "localhost/XE";
$username = "DBMS_TEST"; // Replace with your database username
$password = "1234"; // Replace with your database password

// Establish a connection to Oracle
$conn = oci_connect($username, $password, $host);

// Check the connection
if (!$conn) {
    $e = oci_error();
    die("Connection failed: " . $e['message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $name = $_POST['firstname'];
    $houseno = $_POST['houseno'];
    $roadno = $_POST['roadno'];
    $area = $_POST['area'];
    $district = $_POST['district'];
    $relation = $_POST['relation'];

    // Prepare the SQL query
    $query = "INSERT INTO Guardians (Guardians_ID, Guardians_Name, House_NO, Rood_No, Area, District, Relation) VALUES (GenerateGuardianID(), :name, :houseno, :roadno, :area, :district,:relation)";

    // Create a statement
    $stmt = oci_parse($conn, $query);

    // Bind the parameters
    oci_bind_by_name($stmt, ":name", $name);
    oci_bind_by_name($stmt, ":houseno", $houseno);
    oci_bind_by_name($stmt, ":roadno", $roadno);
    oci_bind_by_name($stmt, ":area", $area);
    oci_bind_by_name($stmt, ":district", $district);
    oci_bind_by_name($stmt, ":relation", $relation);


    // Execute the statement
    $result = oci_execute($stmt);

    if ($result) {
        echo "Registration Successful";
        oci_free_statement($stmt);
        oci_close($conn);
        exit;
    } else {
        $e = oci_error($stmt);
        echo "Error: " . $e['message'];
    }

    // Free the statement and close the connection
    oci_free_statement($stmt);
    oci_close($conn);
}
?>