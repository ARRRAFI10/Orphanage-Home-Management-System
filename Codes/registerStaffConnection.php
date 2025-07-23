<?php
// Connect to the database
$connection = oci_connect('DBMS_TEST', '1234', 'localhost/XE');

if (!$connection) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

// Retrieve the form data
$fullName = $_POST['firstname'];
$designation = $_POST['designation'];
$joiningDate = date('Y-m-d H:i:s', strtotime($_POST['event-time'])); // Format the date correctly
$salary = $_POST['salary'];
$houseNo = $_POST['house_no'];
$roadNo = $_POST['road_no'];
$area = $_POST['area'];
$district = $_POST['district'];
$contact = $_POST['contact'];

// Generate staff ID using the generatestaffid function in the database
$staffIdQuery = "BEGIN :result := GenerateStuffID(); END;";
$staffIdStmt = oci_parse($connection, $staffIdQuery);
oci_bind_by_name($staffIdStmt, ":result", $staffId, 100);
oci_execute($staffIdStmt);

// Insert staff information into the Staff table
$insertStaffQuery = "INSERT INTO Staff (Staff_ID, Staff_Name, Staff_Salary, Joining_Date, Designation, Address, contact, Orphanage_ID)
                     VALUES (:staff_id, :staff_name, :staff_salary, TO_DATE(:joining_date, 'YYYY-MM-DD HH24:MI:SS'), :designation,
                             AddressType(:house_no, :road_no, :area, :district), :contact, 'A1')";

$insertStaffStmt = oci_parse($connection, $insertStaffQuery);
oci_bind_by_name($insertStaffStmt, ":staff_id", $staffId);
oci_bind_by_name($insertStaffStmt, ":staff_name", $fullName);
oci_bind_by_name($insertStaffStmt, ":staff_salary", $salary);
oci_bind_by_name($insertStaffStmt, ":joining_date", $joiningDate);
oci_bind_by_name($insertStaffStmt, ":designation", $designation);
oci_bind_by_name($insertStaffStmt, ":house_no", $houseNo);
oci_bind_by_name($insertStaffStmt, ":road_no", $roadNo);
oci_bind_by_name($insertStaffStmt, ":area", $area);
oci_bind_by_name($insertStaffStmt, ":district", $district);
oci_bind_by_name($insertStaffStmt, ":contact", $contact);

oci_execute($insertStaffStmt);

// Insert login information into the Login table
$insertLoginQuery = "INSERT INTO Login (Login_ID, Login_Password)
                     VALUES (:login_id, :Login_Password)";

$insertLoginStmt = oci_parse($connection, $insertLoginQuery);
oci_bind_by_name($insertLoginStmt, ":login_id", $staffId);
oci_bind_by_name($insertLoginStmt, ":Login_Password", $staffId); // Use staff ID as the password

oci_execute($insertLoginStmt);

// Close the database connection
oci_free_statement($staffIdStmt);
oci_free_statement($insertStaffStmt);
oci_free_statement($insertLoginStmt);
oci_close($connection);

// Show the completion message with the staff ID and password in a pop-up window
echo "<script>alert('Insertion completed. ID: $staffId, Login Password: $staffId')</script>";

// Redirect to the admin page
echo "<script>window.location.href = 'adminPage.html';</script>";
?>
