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

$USERNAME = $_POST['USERNAME'];
$PASSWORD = $_POST['PASSWORD'];
$ROLE_TYPE = $_POST['ROLE_TYPE'];

$sql = "SELECT * FROM USERS WHERE USERNAME = '$USERNAME' AND PASSWORD = '$PASSWORD' AND ROLE = '$ROLE_TYPE' AND STATUS = 'ACTIVE'";
$result = sqlsrv_query($conn, $sql);
$row = sqlsrv_fetch_array($result);

if($row) {
    $USER_ID = $row['USER_ID'];
    $FULL_NAME = $row['FULL_NAME'];
    $ROLE = $row['ROLE'];
    
    if($ROLE == 'ADMIN') {
        echo '<form id="redirectForm" action="dashboard_admin.php" method="post">
                <input type="hidden" name="USER_ID" value="'.$USER_ID.'">
              </form>
              <script>document.getElementById("redirectForm").submit();</script>';
        exit();
    }
    else {
        echo '<form id="redirectForm" action="dashboard_cashier.php" method="post">
                <input type="hidden" name="USER_ID" value="'.$USER_ID.'">
              </form>
              <script>document.getElementById("redirectForm").submit();</script>';
        exit();
    }
}
else {
    if($ROLE_TYPE == 'ADMIN') {
        echo "<script>alert('Invalid admin credentials!'); window.location.href='admin_login.html';</script>";
    }
    else {
        echo "<script>alert('Invalid cashier credentials!'); window.location.href='cashier_login.html';</script>";
    }
}
?>