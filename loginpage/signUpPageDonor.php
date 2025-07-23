<?php
// Connect to the database
$connection = oci_connect('DBMS_TEST', '1234', 'localhost/XE');

if (!$connection) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

// Retrieve the form data
$donorName = $_POST['donorname'];
$houseNo = $_POST['houseno'];
$roadNo = $_POST['roadno'];
$area = $_POST['area'];
$district = $_POST['district'];
$donorContact = $_POST['donorcontact'];

// Check if all input fields are filled
if (empty($donorName)   || empty($area) || empty($district) || empty($donorContact)) {
    // Show error message and redirect back to the form
    echo "<script>alert('Please fill all the fields.');</script>";
    echo "<script>window.location.href = 'signUpDonor.html';</script>";
    exit(); // Stop further execution
}

// Generate donor ID using the generatedonorid function in the database
$donorIdQuery = "BEGIN :result := GenerateDonorID(); END;";
$donorIdStmt = oci_parse($connection, $donorIdQuery);
oci_bind_by_name($donorIdStmt, ":result", $donorId, 100);
oci_execute($donorIdStmt);

// Insert donor information into the Donor table
$insertDonorQuery = "INSERT INTO Donor (Donor_ID, Donor_Name, Address, Contact, Orphanage_ID)
                     VALUES (:donor_id, :donor_name, AddressType(:house_no, :road_no, :area, :district),
                             :donor_contact, 'A1')";

$insertDonorStmt = oci_parse($connection, $insertDonorQuery);
oci_bind_by_name($insertDonorStmt, ":donor_id", $donorId);
oci_bind_by_name($insertDonorStmt, ":donor_name", $donorName);
oci_bind_by_name($insertDonorStmt, ":house_no", $houseNo);
oci_bind_by_name($insertDonorStmt, ":road_no", $roadNo);
oci_bind_by_name($insertDonorStmt, ":area", $area);
oci_bind_by_name($insertDonorStmt, ":district", $district);
oci_bind_by_name($insertDonorStmt, ":donor_contact", $donorContact);

oci_execute($insertDonorStmt);

// Insert login information into the Login table
$insertLoginQuery = "INSERT INTO Login (Login_ID, Login_Password)
                     VALUES (:login_id, :login_password)";

$insertLoginStmt = oci_parse($connection, $insertLoginQuery);
oci_bind_by_name($insertLoginStmt, ":login_id", $donorId);
oci_bind_by_name($insertLoginStmt, ":login_password", $donorId); // Use donor ID as the password

oci_execute($insertLoginStmt);

// Close the database connection
oci_free_statement($donorIdStmt);
oci_free_statement($insertDonorStmt);
oci_free_statement($insertLoginStmt);
oci_close($connection);

// Show the completion message with the donor ID
echo "<script>alert('Donor registration completed. Donor ID: $donorId, Login Password: $donorId');</script>";
echo "<script>window.location.href = 'loginPageDonor.html';</script>";
?>
