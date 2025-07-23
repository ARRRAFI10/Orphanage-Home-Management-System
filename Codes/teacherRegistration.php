<?php
// Connect to the database
$connection = oci_connect('DBMS_TEST', '1234', 'localhost/XE');

if (!$connection) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

// Retrieve the form data
$fullName = $_POST['teachername'];
$educationQualification = $_POST['eduQualification'];
$joiningDate = date('Y-m-d H:i:s', strtotime($_POST['joiningdate'])); // Format the date correctly
$salary = $_POST['salary'];
$houseNo = $_POST['houseno'];
$roadNo = $_POST['roadno'];
$area = $_POST['area'];
$district = $_POST['district'];
$contact = $_POST['contact'];
$subject = $_POST['subject']; // Retrieve the subject from the form

// Generate teacher ID using the GenerateTeacherID function in the database
$teacherIdQuery = "BEGIN :result := GenerateTeacherID(); END;";
$teacherIdStmt = oci_parse($connection, $teacherIdQuery);
oci_bind_by_name($teacherIdStmt, ":result", $teacherId, 100);
oci_execute($teacherIdStmt);

// Insert teacher information into the Teacher table
$insertTeacherQuery = "INSERT INTO Teacher (Teacher_ID, Teacher_Name, Teacher_Salary, Joining_Date, Education_Qualification, Subject, Address, Orphanage_ID)
                     VALUES (:teacher_id, :teacher_name, :teacher_salary, TO_DATE(:joining_date, 'YYYY-MM-DD HH24:MI:SS'), :education_qualification, :subject,
                             AddressType(:house_no, :road_no, :area, :district), 'A1')";

$insertTeacherStmt = oci_parse($connection, $insertTeacherQuery);
oci_bind_by_name($insertTeacherStmt, ":teacher_id", $teacherId);
oci_bind_by_name($insertTeacherStmt, ":teacher_name", $fullName);
oci_bind_by_name($insertTeacherStmt, ":teacher_salary", $salary);
oci_bind_by_name($insertTeacherStmt, ":joining_date", $joiningDate);
oci_bind_by_name($insertTeacherStmt, ":education_qualification", $educationQualification);
oci_bind_by_name($insertTeacherStmt, ":subject", $subject);
oci_bind_by_name($insertTeacherStmt, ":house_no", $houseNo);
oci_bind_by_name($insertTeacherStmt, ":road_no", $roadNo);
oci_bind_by_name($insertTeacherStmt, ":area", $area);
oci_bind_by_name($insertTeacherStmt, ":district", $district);


oci_execute($insertTeacherStmt);

// Insert login information into the Login table
$insertLoginQuery = "INSERT INTO Login (Login_ID, Login_Password)
                     VALUES (:login_id, :login_password)";

$insertLoginStmt = oci_parse($connection, $insertLoginQuery);
oci_bind_by_name($insertLoginStmt, ":login_id", $teacherId);
oci_bind_by_name($insertLoginStmt, ":login_password", $teacherId); // Use teacher ID as the password

oci_execute($insertLoginStmt);

// Close the database connection
oci_free_statement($teacherIdStmt);
oci_free_statement($insertTeacherStmt);
oci_free_statement($insertLoginStmt);
oci_close($connection);

// Show the completion message with the teacher ID and password in a pop-up window
echo "<script>alert('Teacher registration completed. ID: $teacherId, Login Password: $teacherId')</script>";

// Redirect
