<?php
// Connect to the database
$connection = oci_connect('DBMS_TEST', '1234', 'localhost/XE');

if (!$connection) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

// Retrieve the form data
$fullName = $_POST['doctor_name'];
$specialist = $_POST['specialist'];
$joiningDate = date('Y-m-d H:i:s', strtotime($_POST['event-time'])); // Format the date correctly
$salary = $_POST['doctor_salary'];
$houseNo = $_POST['house_no'];
$roadNo = $_POST['road_no'];
$area = $_POST['area'];
$district = $_POST['district'];
$education_qualification = $_POST['education_qualification'];


// Check for empty fields
if (empty($fullName) || empty($specialist) || empty($joiningDate) || empty($salary) || empty($education_qualification) || empty($area) || empty($district)) {
    // Display error message and redirect back to the registration page
    echo "<script>
            alert('Please fill in all the required fields.');
            window.location.href = 'RegisterDoctor.html';
          </script>";
    exit; // Stop further execution
}

// Generate doctor ID using the generatedoctorid function in the database
$doctorIdQuery = "BEGIN :result := GenerateDoctorID(); END;";
$doctorIdStmt = oci_parse($connection, $doctorIdQuery);
oci_bind_by_name($doctorIdStmt, ":result", $doctorId, 100);
oci_execute($doctorIdStmt);

// Insert doctor information into the Doctor table
$insertDoctorQuery = "INSERT INTO Doctor (Doctor_ID, Doctor_Name, Doctor_Salary, Joining_Date, Specialist, Education_Qualification, Address, Orphanage_ID)
                      VALUES (:doctor_id, :doctor_name, :doctor_salary, TO_DATE(:joining_date, 'YYYY-MM-DD HH24:MI:SS'), :specialist,
                      :education_qualification, AddressType(:house_no, :road_no, :area, :district), 'A1')";

$insertDoctorStmt = oci_parse($connection, $insertDoctorQuery);
oci_bind_by_name($insertDoctorStmt, ":doctor_id", $doctorId);
oci_bind_by_name($insertDoctorStmt, ":doctor_name", $fullName);
oci_bind_by_name($insertDoctorStmt, ":doctor_salary", $salary);
oci_bind_by_name($insertDoctorStmt, ":joining_date", $joiningDate);
oci_bind_by_name($insertDoctorStmt, ":specialist", $specialist);
oci_bind_by_name($insertDoctorStmt, ":education_qualification", $education_qualification);
oci_bind_by_name($insertDoctorStmt, ":house_no", $houseNo);
oci_bind_by_name($insertDoctorStmt, ":road_no", $roadNo);
oci_bind_by_name($insertDoctorStmt, ":area", $area);
oci_bind_by_name($insertDoctorStmt, ":district", $district);
// Replace 'Orphanage_ID_Value' with the actual value of the Orphanage_ID column

oci_execute($insertDoctorStmt);

// Insert login information into the Login table
$insertLoginQuery = "INSERT INTO Login (Login_ID, Login_Password)
                     VALUES (:login_id, :Login_Password)";

$insertLoginStmt = oci_parse($connection, $insertLoginQuery);
oci_bind_by_name($insertLoginStmt, ":login_id", $doctorId);
oci_bind_by_name($insertLoginStmt, ":Login_Password", $doctorId); // Use doctor ID as the password

oci_execute($insertLoginStmt);

// Close the database connection
oci_free_statement($doctorIdStmt);
oci_free_statement($insertDoctorStmt);
oci_free_statement($insertLoginStmt);
oci_close($connection);

// Display the completion message in a pop-up window
echo "<script>
    alert('Insertion completed. ID: $doctorId, Login Password: $doctorId');
    window.location.href = 'adminPage.html';
</script>";
?>
