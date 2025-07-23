<!DOCTYPE html>
<html>
<head>
  <title>Show Orphans Information</title>
  <style>
    /* Table styling */
    table {
      margin: 0 auto; /* Center the table */
      width: 100%;
      border-collapse: collapse;
      text-align: center; /* Center align the table content */
    }

    th, td {
      padding: 10px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #4CAF50;
      color: white;
    }

    /* Search form styling */
    .search-form {
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      justify-content: center; /* Center the search form */
    }

    .search-form span {
      margin-right: 10px;
    }

    .search-form input[type="text"] {
      padding: 8px;
      width: 300px;
      border: none;
      border-radius: 4px 0 0 4px;
    }

    .search-form input[type="submit"] {
      padding: 8px 16px;
      background-color: #4CAF50;
      color: white;
      border: none;
      border-radius: 0 4px 4px 0;
      cursor: pointer;
    }

    /* Container styling */
    .container {
      margin: 0 auto;
      padding: 20px;
    }

    /* Hover effect */
    tr:hover {
      background-color: #f5f5f5;
    }

    /* Heading alignment */
    .center-heading {
      text-align: center;
    }

    /* Button styling */
    .admin-button {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 20px;
    }

    .admin-button a {
      padding: 8px 16px;
      background-color: #4CAF50;
      color: white;
      text-decoration: none;
      border-radius: 4px;
    }
    .table-div{
      width:100%;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="admin-button">
      <a href="adminPage.html">Go to Admin Page</a>
    </div>

    <h2 class="center-heading">Orphans and Guardians Information</h2>

    <form class="search-form" method="GET">
      <span>Search:</span>
      <input type="text" name="search" placeholder="Enter a value to search">
      <input type="submit" value="Search">
    </form>

    <?php
    // Connect to the database and execute the query
    $connection = oci_connect('DBMS_TEST', '1234', 'localhost/XE');
    if (!$connection) {
        $e = oci_error();
        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    }

    // Check if a search value is provided
    $searchValue = isset($_GET['search']) ? $_GET['search'] : '';

    // Build the SQL query with the search condition using the view
    $sqlQuery = "SELECT OG.Orphan_ID, OG.Orphan_Name, OG.Age, OG.Date_of_Birth, OG.Blood_Group, OG.Gender, OG.Guardians_Name, OG.House_NO, OG.Road_No, OG.Area, OG.District, OG.Relation, H.Adoptive_Parent, H.Adaption_Date
                 FROM Orphans_Guardians_View OG
                 JOIN Has_Guardians H ON OG.Orphan_ID = H.Orphan_ID";
    if ($searchValue !== '') {
        $searchValue = strtoupper($searchValue); // Convert search value to uppercase
        $sqlQuery .= " WHERE UPPER(OG.Orphan_ID) LIKE '%$searchValue%' OR UPPER(OG.Orphan_Name) LIKE '%$searchValue%' OR UPPER(OG.Age) LIKE '%$searchValue%' OR UPPER(OG.Date_of_Birth) LIKE '%$searchValue%' OR UPPER(OG.Blood_Group) LIKE '%$searchValue%' OR UPPER(OG.Gender) LIKE '%$searchValue%'
                      OR UPPER(OG.Guardians_Name) LIKE '%$searchValue%' OR UPPER(OG.House_NO) LIKE '%$searchValue%' OR UPPER(OG.Road_No) LIKE '%$searchValue%' OR UPPER(OG.Area) LIKE '%$searchValue%' OR UPPER(OG.District) LIKE '%$searchValue%' OR UPPER(OG.Relation) LIKE '%$searchValue%'";
    }

    $query = oci_parse($connection, $sqlQuery);
    oci_execute($query);

    // Display the information in a table
    echo '<table border="4px">';
    echo '<tr>
            <th>ID</th>
            <th>Orphan Name</th>
            <th>Age</th>
            <th>Date of Birth</th>
            <th>Blood Group</th>
            <th>Gender</th>
            <th>Guardian Name</th>
            <th>House No</th>
            <th>Road No</th>
            <th>Area</th>
            <th>District</th>
            <th>Relation</th>
            <th>Adoptive Parent</th>
            <th>Adoption Date</th>
            <th>Edit</th>
          </tr>';
    while ($row = oci_fetch_assoc($query)) {
        echo '<tr>';
        echo '<td>' . $row['ORPHAN_ID'] . '</td>';
        echo '<td>' . $row['ORPHAN_NAME'] . '</td>';
        echo '<td>' . $row['AGE'] . '</td>';
        echo '<td>' . $row['DATE_OF_BIRTH'] . '</td>';
        echo '<td>' . $row['BLOOD_GROUP'] . '</td>';
        echo '<td>' . $row['GENDER'] . '</td>';
        echo '<td>' . $row['GUARDIANS_NAME'] . '</td>';
        echo '<td>' . $row['HOUSE_NO'] . '</td>';
        echo '<td>' . $row['ROAD_NO'] . '</td>';
        echo '<td>' . $row['AREA'] . '</td>';
        echo '<td>' . $row['DISTRICT'] . '</td>';
        echo '<td>' . $row['RELATION'] . '</td>';
        echo '<td>' . $row['ADOPTIVE_PARENT'] . '</td>';
        echo '<td>' . $row['ADAPTION_DATE'] . '</td>';
        echo '<td><a href="editOrphan.php?id=' . $row['ORPHAN_ID'] . '">Edit</a></td>';
        
        echo '</tr>';
    }
    echo '</table>';

    // Close the database connection
    oci_free_statement($query);
    oci_close($connection);
    ?>
  </div>
</body>
</html>