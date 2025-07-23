<?php
// Connect to the database
$connection = oci_connect('DBMS_TEST', '1234', 'localhost/XE');

if (!$connection) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

// Check if the orphan ID is provided
if (isset($_GET['id'])) {
    // Get the orphan ID from the URL parameter
    $orphanId = $_GET['id'];

    // Delete the orphan's relationship with guardians from the Has_Guardians table
    $deleteHasGuardiansQuery = "DELETE FROM Has_Guardians WHERE Orphan_ID = :orphan_id";
    $deleteHasGuardiansStmt = oci_parse($connection, $deleteHasGuardiansQuery);
    oci_bind_by_name($deleteHasGuardiansStmt, ':orphan_id', $orphanId);
    oci_execute($deleteHasGuardiansStmt);

    // Delete the corresponding guardian information from the Guardians table
    $deleteGuardianQuery = "DELETE FROM Guardians WHERE Guardians_ID IN (
        SELECT Guardians_ID FROM Has_Guardians WHERE Orphan_ID = :orphan_id
    )";
    $deleteGuardianStmt = oci_parse($connection, $deleteGuardianQuery);
    oci_bind_by_name($deleteGuardianStmt, ':orphan_id', $orphanId);
    oci_execute($deleteGuardianStmt);

    // Delete the orphan information from the Orphans table
    $deleteOrphanQuery = "DELETE FROM Orphans WHERE Orphan_ID = :orphan_id";
    $deleteOrphanStmt = oci_parse($connection, $deleteOrphanQuery);
    oci_bind_by_name($deleteOrphanStmt, ':orphan_id', $orphanId);
    oci_execute($deleteOrphanStmt);

    // Close the database connection
    oci_free_statement($deleteHasGuardiansStmt);
    oci_free_statement($deleteGuardianStmt);
    oci_free_statement($deleteOrphanStmt);
    oci_close($connection);

    // Redirect back to the showOrphan.php page or any other desired page
    header('Location: showOrphan.php');
    exit();
} else {
    echo '<p>Invalid request.</p>';
}
?>
