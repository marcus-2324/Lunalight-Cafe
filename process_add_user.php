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
$user_data = sqlsrv_fetch_array($result_check);

if($user_data == false) {
    header("Location: admin_login.html");
    exit();
}

$USERNAME = $_POST['USERNAME'];
$PASSWORD = $_POST['PASSWORD'];
$EMAIL = $_POST['EMAIL'];
$ROLE = $_POST['ROLE'];
$FULL_NAME = $_POST['FULL_NAME'];
$MOBILE_NUMBER = $_POST['MOBILE_NUMBER'];
$DATE_CREATED = date('Y-m-d');
$STATUS = 'ACTIVE';

$sql = "INSERT INTO USERS (USERNAME, PASSWORD, EMAIL, ROLE, FULL_NAME, MOBILE_NUMBER, DATE_CREATED, STATUS) 
        VALUES ('$USERNAME', '$PASSWORD', '$EMAIL', '$ROLE', '$FULL_NAME', '$MOBILE_NUMBER', '$DATE_CREATED', '$STATUS')";
$result = sqlsrv_query($conn, $sql);

if($result) {
    echo "<script>alert('User added successfully!');</script>";
    echo '<form id="redirectForm" action="manage_users.php" method="post">
            <input type="hidden" name="USER_ID" value="'.$USER_ID.'">
          </form>
          <script>document.getElementById("redirectForm").submit();</script>';
}
else {
    echo 'Error adding user';
    die(print_r(sqlsrv_errors(), true));
}
?>