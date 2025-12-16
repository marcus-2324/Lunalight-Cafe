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
$ITEM_ID = $_POST['ITEM_ID'];

$sql_check = "SELECT * FROM USERS WHERE USER_ID = $USER_ID AND ROLE = 'ADMIN'";
$result_check = sqlsrv_query($conn, $sql_check);
$user_data = sqlsrv_fetch_array($result_check);

if($user_data == false) {
    header("Location: admin_login.html");
    exit();
}

$sql = "SELECT * FROM MENU_ITEMS WHERE ITEM_ID = $ITEM_ID";
$result = sqlsrv_query($conn, $sql);
$item = sqlsrv_fetch_array($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Image - Lunalight Café</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center py-5">
    <div class="card shadow-lg border-0 rounded-4" style="max-width: 600px; width: 100%;">
        <div class="card-body p-5">
            <h2 class="text-center fw-bold mb-4">📷 Upload Product Image</h2>

            <div class="card bg-light border-0 rounded-4 p-4 mb-4 text-center">
                <h4 class="fw-bold mb-2"><?php echo $item['ITEM_NAME']; ?></h4>
                <p class="text-muted mb-0 fs-5">₱<?php echo number_format($item['PRICE'], 2); ?></p>
            </div>

            <form action="process_upload_image.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                <input type="hidden" name="ITEM_ID" value="<?php echo $ITEM_ID; ?>">

                <div class="mb-4">
                    <label class="form-label fw-medium">Select Image File</label>
                    <input type="file" name="file" class="form-control form-control-lg rounded-3" accept="image/jpeg,image/png,image/jpg" required>
                    <small class="text-muted">Accepted formats: JPG, PNG (Max 5MB)</small>
                </div>

                <div class="d-flex gap-2">
                    <form action="manage_menu.php" method="post" class="flex-fill">
                        <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                        <button type="submit" class="btn btn-secondary w-100 rounded-3">← Back</button>
                    </form>
                    <button type="submit" class="btn btn-success w-100 rounded-3 fw-semibold flex-fill">Upload Image</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>