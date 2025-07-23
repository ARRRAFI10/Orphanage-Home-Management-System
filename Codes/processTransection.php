<?php
session_start();

// Check if the staff is logged in, otherwise redirect to the login page
if (!isset($_SESSION['loggedIn'])) {
    header("Location: loginPageStaff.php");
    exit;
}

// Database connection details
$host = "localhost/XE";
$username = "DBMS_TEST";
$password = "1234";

// Establish database connection
$conn = oci_connect($username, $password, $host);
if (!$conn) {
    $error = oci_error();
    die("Database connection failed: " . $error['message']);
}

// Retrieve the logged-in staff's ID
$staffId = $_SESSION['loggedIn'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve transaction information from the form
    $transactionAmount = $_POST['amount'];
    $transactionDate = $_POST['transactionDate'];
    $transactionType = $_POST['transactionType'];

    // Generate transaction ID using a database function
    $generateTransectionIdQuery = "BEGIN :result := generateTransectionId; END;";
    $stmtGenerateId = oci_parse($conn, $generateTransectionIdQuery);
    oci_bind_by_name($stmtGenerateId, ":result", $transactionId, 100);
    oci_execute($stmtGenerateId);

    // Check if any transaction information is empty
    if (empty($transactionAmount) || empty($transactionDate) || empty($transactionType)) {
        // Display an error message and redirect back to the previous page
        echo "<script>alert('Please fill in all the transaction information.');</script>";
        echo "<script>window.location.href = 'staffPage.php';</script>";
    } else {
        // Retrieve the orphanage ID associated with the staff
        $getOrphanageIdQuery = "SELECT o.Orphanage_ID
                                FROM Orphanage o, Staff s
                                WHERE s.Staff_ID = :staffId AND s.Orphanage_ID = o.Orphanage_ID";
        $stmtGetOrphanageId = oci_parse($conn, $getOrphanageIdQuery);
        oci_bind_by_name($stmtGetOrphanageId, ":staffId", $staffId);
        oci_execute($stmtGetOrphanageId);

        if ($row = oci_fetch_assoc($stmtGetOrphanageId)) {
            $orphanageId = $row['ORPHANAGE_ID'];

            // Insert transaction information into the Transection table
            $insertTransectionQuery = "INSERT INTO Transection (Transection_ID, Transection_Amount, Transection_Date, Transection_Type, Orphanage_ID)
                                       VALUES (:transactionId, :transactionAmount, TO_DATE(:transactionDate, 'YYYY-MM-DD'), :transactionType, :orphanageId)";
            $stmtInsertTransection = oci_parse($conn, $insertTransectionQuery);
            oci_bind_by_name($stmtInsertTransection, ":transactionId", $transactionId);
            oci_bind_by_name($stmtInsertTransection, ":transactionAmount", $transactionAmount);
            oci_bind_by_name($stmtInsertTransection, ":transactionDate", $transactionDate);
            oci_bind_by_name($stmtInsertTransection, ":transactionType", $transactionType);
            oci_bind_by_name($stmtInsertTransection, ":orphanageId", $orphanageId);

            $resultInsertTransection = oci_execute($stmtInsertTransection);

            if ($resultInsertTransection) {
                // Insert entry into the Done_By table
                $insertDoneByQuery = "INSERT INTO Done_By (Transection_ID, Staff_ID)
                                      VALUES (:transactionId, :staffId)";
                $stmtInsertDoneBy = oci_parse($conn, $insertDoneByQuery);
                oci_bind_by_name($stmtInsertDoneBy, ":transactionId", $transactionId);
                oci_bind_by_name($stmtInsertDoneBy, ":staffId", $staffId);

                $resultInsertDoneBy = oci_execute($stmtInsertDoneBy);

                if ($resultInsertDoneBy) {
                    // Transaction processed successfully
                    echo "<script>alert('Transaction processed successfully.');</script>";
                } else {
                    // Display an error message
                    echo "<script>alert('Failed to insert entry into Done_By table.');</script>";
                }
            } else {
                $error = oci_error($stmtInsertTransection);
                if ($error['code'] == 20001) {
                    // Not enough funds available
                    echo "<script>alert('Not enough funds available.');</script>";
                } else {
                    // Display a general error message
                    echo "<script>alert('Failed to insert transaction information.');</script>";
                }
            }
            echo "<script>window.location.href = 'staffPage.php';</script>";
        } else {
            // Display an error message
            echo "<script>alert('Failed to retrieve orphanage ID for the staff.');</script>";
            echo "<script>window.location.href = 'staffPage.php';</script>";
        }
    }
}

// Close the database connection
oci_close($conn);
?>
