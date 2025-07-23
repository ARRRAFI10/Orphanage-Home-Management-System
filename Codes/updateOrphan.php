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
    $orphanId = $_POST['orphan_id'];
    $orphanName = $_POST['orphan_name'];
    $age = $_POST['age'];
    $dateOfBirth = $_POST['date_of_birth'];
    $bloodGroup = $_POST['blood_group'];
    $gender = $_POST['gender'];
    $houseNo = $_POST['house_no'];
    $roadNo = $_POST['road_no'];
    $area = $_POST['area'];
    $district = $_POST['district'];
    $guardianName = $_POST['guardian_name'];
    $relation = $_POST['relation'];
    $adoptiveParent = $_POST['adoptive_parent'];
    $adoptionDate = $_POST['adaption_date'];

    // Update the orphan information
    $updateOrphanQuery = "UPDATE Orphans
                          SET Orphan_Name = :orphan_name,
                              Age = :age,
                              Date_of_Birth = :date_of_birth,
                              Blood_Group = :blood_group,
                              Gender = :gender
                          WHERE Orphan_ID = :orphan_id";

    $updateOrphanStmt = oci_parse($connection, $updateOrphanQuery);
    oci_bind_by_name($updateOrphanStmt, ':orphan_name', $orphanName);
    oci_bind_by_name($updateOrphanStmt, ':age', $age);
    oci_bind_by_name($updateOrphanStmt, ':date_of_birth', $dateOfBirth);
    oci_bind_by_name($updateOrphanStmt, ':blood_group', $bloodGroup);
    oci_bind_by_name($updateOrphanStmt, ':gender', $gender);
    oci_bind_by_name($updateOrphanStmt, ':orphan_id', $orphanId);

    oci_execute($updateOrphanStmt);

    // Update the guardian information
    $updateGuardianQuery = "UPDATE Guardians
                            SET Guardians_Name = :guardian_name,
                                Relation = :relation,
                                House_No = :house_no,
                                Road_No = :road_no,
                                Area = :area,
                                District = :district
                            WHERE Guardians_ID IN (
                                SELECT Guardians_ID
                                FROM has_guardians
                                WHERE Orphan_ID = :orphan_id
                            )";

    $updateGuardianStmt = oci_parse($connection, $updateGuardianQuery);
    oci_bind_by_name($updateGuardianStmt, ':guardian_name', $guardianName);
    oci_bind_by_name($updateGuardianStmt, ':relation', $relation);
    oci_bind_by_name($updateGuardianStmt, ':house_no', $houseNo);
    oci_bind_by_name($updateGuardianStmt, ':road_no', $roadNo);
    oci_bind_by_name($updateGuardianStmt, ':area', $area);
    oci_bind_by_name($updateGuardianStmt, ':district', $district);
    oci_bind_by_name($updateGuardianStmt, ':orphan_id', $orphanId);

    oci_execute($updateGuardianStmt);

    // Update the additional guardian information
    $updateHasGuardianQuery = "UPDATE has_guardians
                               SET Adoptive_Parent = :adoptive_parent,
                                   Adaption_Date = :adoption_date
                               WHERE Orphan_ID = :orphan_id";

    $updateHasGuardianStmt = oci_parse($connection, $updateHasGuardianQuery);
    oci_bind_by_name($updateHasGuardianStmt, ':adoptive_parent', $adoptiveParent);
    oci_bind_by_name($updateHasGuardianStmt, ':adoption_date', $adoptionDate);
    oci_bind_by_name($updateHasGuardianStmt, ':orphan_id', $orphanId);

    oci_execute($updateHasGuardianStmt);

    // Close the database connection
    oci_free_statement($updateOrphanStmt);
    oci_free_statement($updateGuardianStmt);
    oci_free_statement($updateHasGuardianStmt);
    oci_close($connection);

    // Display message in a pop-up window and redirect
    echo '<script>alert("Update Successful."); window.location.href = "showorphan.php";</script>';
    exit();
} else {
    echo '<p>Invalid request.</p>';
}
?>
