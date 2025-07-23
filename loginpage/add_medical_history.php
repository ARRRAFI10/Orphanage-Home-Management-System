<?php
session_start();

// Assuming you have a database connection established

// Check if the doctor is logged in, otherwise redirect to the login page
if (!isset($_SESSION['doctorId'])) {
    header("Location: login.php");
    exit();
}

// Retrieve the doctor ID from the session
$doctorId = $_SESSION['doctorId'];

// Retrieve the orphan ID, disease, treatment, and last checkup date from the form
$orphanId = $_POST['orphanId'];
$disease = $_POST['disease'];
$treatment = $_POST['treatment'];
$lastCheckupDate = $_POST['lastCheckupDate'];

// TODO: Validate the input and perform necessary sanitization

// Establish database connection
$host = "localhost/XE"; // Replace with your Oracle service name or SID
$username = "DBMS_TEST";
$password = "1234";

$conn = oci_connect($username, $password, $host);
if (!$conn) {
    $error = oci_error();
    die("Database connection failed: " . $error['message']);
}

// Insert the new entry into the Medical_History table
$query = "INSERT INTO Medical_History (History_No, Disease, Treatment, Last_Checkup_Date)
          VALUES (:historyNo, :disease, :treatment, TO_DATE(:lastCheckupDate, 'YYYY-MM-DD'))";
$stmt = oci_parse($conn, $query);
$historyNo = generateHistoryID($conn); // Call the database function to generate a unique history number
oci_bind_by_name($stmt, ":historyNo", $historyNo);
oci_bind_by_name($stmt, ":disease", $disease);
oci_bind_by_name($stmt, ":treatment", $treatment);
oci_bind_by_name($stmt, ":lastCheckupDate", $lastCheckupDate);

if (oci_execute($stmt)) {
    // Insert the entry into the Medical table
    $insertQuery = "INSERT INTO Medical (Orphan_ID, Doctor_ID, History_No)
                    VALUES (:orphanId, :doctorId, :historyNo)";
    $insertStmt = oci_parse($conn, $insertQuery);
    oci_bind_by_name($insertStmt, ":orphanId", $orphanId);
    oci_bind_by_name($insertStmt, ":doctorId", $doctorId);
    oci_bind_by_name($insertStmt, ":historyNo", $historyNo);

    if (oci_execute($insertStmt)) {
        $errorMsg = "Medical history entry added successfully.";
        echo "<script>alert('$errorMsg'); window.location.href = 'DoctorPage.php';</script>";
    } else {
        $errorMsg ="Failed to add medical history entry.";
        echo "<script>alert('$errorMsg'); window.location.href = 'DoctorPage.php';</script>";
    }

    oci_free_statement($insertStmt);
} else {
    $errorMsg = "Failed to add medical history entry.";
    echo "<script>alert('$errorMsg'); window.location.href = 'DoctorPage.php';</script>";
}

// Close the statement and the database connection
oci_free_statement($stmt);
oci_close($conn);

// Database function to generate unique history ID
function generateHistoryID($conn) {
    $query = "SELECT GenerateHistoryID() AS History_ID FROM DUAL";
    $stmt = oci_parse($conn, $query);
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);
    return $row['HISTORY_ID'];
}
?>
