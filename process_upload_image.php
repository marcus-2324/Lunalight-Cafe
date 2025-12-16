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

$ITEM_ID = $_POST['ITEM_ID'];

$destination = "uploads/";
$filename = basename($_FILES['file']['name']);
$finalfilepath = $destination . $filename;

$allowtypes = array('jpg', 'png', 'jpeg');
$filetype = pathinfo($finalfilepath, PATHINFO_EXTENSION);

if(in_array(strtolower($filetype), $allowtypes)) {
    $finalfolder = move_uploaded_file($_FILES['file']['tmp_name'], $finalfilepath);
    
    $sql_delete_old = "DELETE FROM IMAGES WHERE ITEM_ID = $ITEM_ID";
    sqlsrv_query($conn, $sql_delete_old);
    
    $sql_images = "INSERT INTO IMAGES(FILENAME, FILEPATH, DATE_UPLOADED, ITEM_ID)
                    VALUES('$filename', '$finalfilepath', GETDATE(), '$ITEM_ID')";
    $result_images = sqlsrv_query($conn, $sql_images);
    
    if($result_images) {
        echo "<script>alert('Image uploaded successfully!');</script>";
        echo '<form id="redirectForm" action="manage_menu.php" method="post">
                <input type="hidden" name="USER_ID" value="'.$USER_ID.'">
              </form>
              <script>document.getElementById("redirectForm").submit();</script>';
    }
    else {
        echo 'Error uploading image';
        die(print_r(sqlsrv_errors(), true));
    }
}
else {
    echo "<script>alert('Invalid file type! Only JPG, PNG allowed.');</script>";
    echo '<form id="redirectForm" action="upload_image.php" method="post">
            <input type="hidden" name="ITEM_ID" value="'.$ITEM_ID.'">
            <input type="hidden" name="USER_ID" value="'.$USER_ID.'">
          </form>
          <script>document.getElementById("redirectForm").submit();</script>';
    exit();
}
?>