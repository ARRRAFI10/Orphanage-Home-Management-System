<?php
// Connect to the database
$connection = oci_connect('DBMS_TEST', '1234', 'localhost/XE');

if (!$connection) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data
    $staffId = $_POST['staff_id'];
    $staffName = $_POST['staff_name'];
    $salary = $_POST['staff_salary'];
    $designation = $_POST['designation'];
    $houseNo = $_POST['house_no'];
    $roadNo = $_POST['road_no'];
    $area = $_POST['area'];
    $district = $_POST['district'];

    // Update the staff information
    $updateStaffQuery = "UPDATE Staff
                         SET Staff_Name = :staff_name,
                             Staff_Salary = :salary,
                             Designation = :designation,
                             Address = AddressType(:house_no, :road_no, :area, :district)
                         WHERE Staff_ID = :staff_id";

    $updateStaffStmt = oci_parse($connection, $updateStaffQuery);
    oci_bind_by_name($updateStaffStmt, ':staff_name', $staffName);
    oci_bind_by_name($updateStaffStmt, ':salary', $salary);
    oci_bind_by_name($updateStaffStmt, ':designation', $designation);
    oci_bind_by_name($updateStaffStmt, ':house_no', $houseNo);
    oci_bind_by_name($updateStaffStmt, ':road_no', $roadNo);
    oci_bind_by_name($updateStaffStmt, ':area', $area);
    oci_bind_by_name($updateStaffStmt, ':district', $district);
    oci_bind_by_name($updateStaffStmt, ':staff_id', $staffId);

    $result = oci_execute($updateStaffStmt);
    oci_commit($connection);

    // Close the database connection
    oci_free_statement($updateStaffStmt);
    oci_close($connection);

    if ($result) {
        // Update successful, display message in a pop-up window and redirect
        echo '<script>alert("Update Successful."); window.location.href = "showStaff.php";</script>';
        exit();
    } else {
        // Update failed, display error message in a pop-up window and redirect
        echo '<script>alert("Update Failed."); window.location.href = "showStaff.php";</script>';
        exit();
    }
} else {
    echo '<p>Invalid request.</p>';
}
?>
