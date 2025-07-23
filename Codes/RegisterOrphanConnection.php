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
    $gender = $_POST['gender'];
    $dob = $_POST['event-time'];
    $blood_group = $_POST['group'];
    $description = $_POST['description'];

    // Prepare the SQL query
    $query = "INSERT INTO Orphans (Orphan_ID, Orphan_Name, Date_of_Birth, Blood_Group, Gender, Age, Description,Orphanage_ID) VALUES (GenerateOrphanID(), :name, TO_DATE(:dob, 'YYYY-MM-DD\"T\"HH24:MI'), :blood_group, :gender, :age,:description,'A1')";

    // Create a statement
    $stmt = oci_parse($conn, $query);

    // Bind the parameters
    oci_bind_by_name($stmt, ":name", $name);
    oci_bind_by_name($stmt, ":blood_group", $blood_group);
    oci_bind_by_name($stmt, ":gender", $gender);
    oci_bind_by_name($stmt, ":dob", $dob);
    oci_bind_by_name($stmt, ":description", $description);

    // Calculate age
    $dobDateTime = new DateTime($dob); // Create DateTime object from date of birth
    $currentDateTime = new DateTime(); // Create DateTime object for current date/time
    $ageInterval = $currentDateTime->diff($dobDateTime); // Calculate the difference between current date and date of birth
    $age = $ageInterval->y; // Retrieve the number of years from the difference
    oci_bind_by_name($stmt, ":age", $age);

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
