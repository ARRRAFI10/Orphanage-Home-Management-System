<?php
// Database configuration
$host = "localhost/XE";
$username = "ADIB VAI"; // Replace with your database username
$password = "123"; // Replace with your database password

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
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $emergencyContact = $_POST['emergencycontact'];
    $weight = $_POST['currentweight'];
    $height = $_POST['yourheight'];
    $area = $_POST['country'];
    $address = $_POST['address'];
    $reason = $_POST['subject'];

    // Calculate age
    $dobDateTime = new DateTime($dob); // Create DateTime object from date of birth
    $currentDateTime = new DateTime(); // Create DateTime object for current date/time
    $ageInterval = $currentDateTime->diff($dobDateTime); // Calculate the difference between current date and date of birth
    $age = $ageInterval->y; // Retrieve the number of years from the difference

    // Prepare the SQL query
    $query = "INSERT INTO member1 (id, NAME, GENDER, DATE_OF_BIRTH, CONTACT_NO, EMAIL, EMERGENCY_CONTACT_NO, WEIGHT, HEIGHT, AGE, AREA, ADDRESS, REASON) 
              VALUES (MEMBER1_SEQ.NEXTVAL, :name, :gender, TO_DATE(:dob, 'YYYY-MM-DD\"T\"HH24:MI'), :contact, :email, :emergency_contact, :weight, :height, :age, :area, :address, :reason) RETURNING id INTO :new_id";

    // Create a statement
    $stmt = oci_parse($conn, $query);

    // Bind the parameters
    oci_bind_by_name($stmt, ":name", $name);
    oci_bind_by_name($stmt, ":gender", $gender);
    oci_bind_by_name($stmt, ":dob", $dob);
    oci_bind_by_name($stmt, ":contact", $contact);
    oci_bind_by_name($stmt, ":email", $email);
    oci_bind_by_name($stmt, ":emergency_contact", $emergencyContact);
    oci_bind_by_name($stmt, ":weight", $weight);
    oci_bind_by_name($stmt, ":height", $height);
    oci_bind_by_name($stmt, ":age", $age);
    oci_bind_by_name($stmt, ":area", $area);
    oci_bind_by_name($stmt, ":address", $address);
    oci_bind_by_name($stmt, ":reason", $reason);
    oci_bind_by_name($stmt, ":new_id", $newId, 32); // Bind the parameter for the returning ID

    // Execute the statement
    $result = oci_execute($stmt);

    if ($result) {
        oci_commit($conn); // Commit the transaction to make the inserted data permanent

        // Free the statement and close the connection
        oci_free_statement($stmt);
        oci_close($conn);

        // Redirect to registration_success.html with the ID as a parameter
        header("Location: registration_success.php?id=$newId");
        exit;
    } else {
        $e = oci_error($stmt);
        if ($e['code'] === 20001) {
            header("Location: fault_register.html");
        }
        echo "Error: " . $e['message'];
    }

    // Free the statement and close the connection
    oci_free_statement($stmt);
    oci_close($conn);
}