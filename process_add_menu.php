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

$ITEM_NAME = $_POST['ITEM_NAME'];
$CATEGORY_ID = $_POST['CATEGORY_ID'];
$PRICE = $_POST['PRICE'];
$DESCRIPTION = $_POST['DESCRIPTION'];
$AVAILABILITY = $_POST['AVAILABILITY'];
$DATE_ADDED = date('Y-m-d');

$sql = "INSERT INTO MENU_ITEMS (ITEM_NAME, CATEGORY_ID, PRICE, DESCRIPTION, AVAILABILITY, DATE_ADDED) 
        VALUES ('$ITEM_NAME', '$CATEGORY_ID', '$PRICE', '$DESCRIPTION', '$AVAILABILITY', '$DATE_ADDED')";
$result = sqlsrv_query($conn, $sql);

if($result) {
    echo "<script>alert('Menu item added successfully!');</script>";
    echo '<form id="redirectForm" action="manage_menu.php" method="post">
            <input type="hidden" name="USER_ID" value="'.$USER_ID.'">
          </form>
          <script>document.getElementById("redirectForm").submit();</script>';
}
else {
    echo 'Error adding menu item';
    die(print_r(sqlsrv_errors(), true));
}
?>