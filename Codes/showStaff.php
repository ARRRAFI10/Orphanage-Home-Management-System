<!DOCTYPE html>
<html>
<head>
  <title>Show Staffs Information</title>
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
      max-width: 800px;
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
  </style>
</head>
<body>
  <div class="container">
    <div class="admin-button">
      <a href="adminPage.html">Go to Admin Page</a>
    </div>

    <h2 class="center-heading">Staff Information</h2>

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

    // Build the SQL query with the search condition
    $sqlQuery = "SELECT s.Staff_ID, s.Staff_Name, s.Staff_Salary, s.Joining_Date, s.Designation,s.Address.House_No AS HouseNo, s.Address.Road_No AS RoadNo,
    s.Address.Area AS Area, s.Address.District AS District,s.contact
    FROM Staff s";
    if ($searchValue !== '') {
        $searchValue = strtoupper($searchValue); // Convert search value to uppercase
        $sqlQuery .= " WHERE UPPER(S.Staff_ID) LIKE '%$searchValue%' OR UPPER(S.Staff_Name) LIKE '%$searchValue%' OR UPPER(S.Staff_Salary) LIKE '%$searchValue%' OR UPPER(S.Joining_Date) LIKE '%$searchValue%' OR UPPER(S.Designation) LIKE '%$searchValue%' OR 
                       UPPER(S.Address.House_No) LIKE '%$searchValue%' OR UPPER(S.Address.Road_No) LIKE '%$searchValue%' OR UPPER(S.Address.Area) LIKE '%$searchValue%' OR UPPER(S.Address.District) LIKE '%$searchValue%'";
    }

    $query = oci_parse($connection, $sqlQuery);
    oci_execute($query);

    // Display the information in a table
    echo '<table border="4px">';
    echo '<tr>
            <th>ID</th>
            <th>Staff Name</th>
            <th>Salary</th>
            <th>Joining Date</th>
            <th>Designation</th>
            <th>House No</th>
            <th>Road No</th>
            <th>Area</th>
            <th>District</th>
            <th>contact</th>
            <th>Edit</th>
          </tr>';
    while ($row = oci_fetch_assoc($query)) {
        echo '<tr>';
        echo '<td>' . $row['STAFF_ID'] . '</td>';
        echo '<td>' . $row['STAFF_NAME'] . '</td>';
        echo '<td>' . $row['STAFF_SALARY'] . '</td>';
        echo '<td>' . $row['JOINING_DATE'] . '</td>';
        echo '<td>' . $row['DESIGNATION'] . '</td>';
        echo '<td>' . $row['HOUSENO'] . '</td>';
        echo '<td>' . $row['ROADNO'] . '</td>';
        echo '<td>' . $row['AREA'] . '</td>';
        echo '<td>' . $row['DISTRICT'] . '</td>';
        echo '<td>' . $row['CONTACT'] . '</td>';
        echo '<td><a href="editStaff.php?id=' . $row['STAFF_ID'] . '">Edit</a></td>';
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
