<?php
$serverName = "LAPTOP-QNA7LUF8\SQLEXPRESS";
$connectionOptions = [
    "Database" => "LUNALIGHT",
    "Uid" => "",
    "PWD" => ""
];
$conn = sqlsrv_connect($serverName, $connectionOptions);
if($conn == false)
    die(print_r(sqlsrv_errors(), true));

$USER_ID = $_POST['USER_ID'];

$sql_check = "SELECT * FROM USERS WHERE USER_ID = $USER_ID AND ROLE = 'ADMIN'";
$result_check = sqlsrv_query($conn, $sql_check);

if($result_check == false) {
    die(print_r(sqlsrv_errors(), true));
}

$user_data = sqlsrv_fetch_array($result_check);

if($user_data == false) {
    header("Location: admin_login.html");
    exit();
}

$CATEGORY_NAME = $_POST['CATEGORY_NAME'];
$DESCRIPTION = $_POST['DESCRIPTION'];
$DATE_CREATED = date('Y-m-d');
$STATUS = 'ACTIVE';

$sql = "INSERT INTO CATEGORIES (CATEGORY_NAME, DESCRIPTION, DATE_CREATED, STATUS) 
        VALUES ('$CATEGORY_NAME', '$DESCRIPTION', '$DATE_CREATED', '$STATUS')";
$result = sqlsrv_query($conn, $sql);

if($result) {
    echo "<script>alert('Category added successfully!');</script>";
    echo '<form id="redirectForm" action="manage_categories.php" method="post">
            <input type="hidden" name="USER_ID" value="'.$USER_ID.'">
          </form>
          <script>document.getElementById("redirectForm").submit();</script>';
}
else {
    echo 'Error adding category';
    die(print_r(sqlsrv_errors(), true));
}
?>