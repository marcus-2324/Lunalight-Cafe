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

$sql_categories = "SELECT * FROM CATEGORIES";
$result_categories = sqlsrv_query($conn, $sql_categories);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Menu Item - Lunalight Café</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center py-5">
    <div class="card shadow-lg border-0 rounded-4" style="max-width: 700px; width: 100%;">
        <div class="card-body p-5">
            <h2 class="text-center fw-bold mb-4">🍴 Add New Menu Item</h2>

            <form action="process_add_menu.php" method="post">
                <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                
                <div class="mb-3">
                    <label class="form-label fw-medium">Item Name</label>
                    <input type="text" name="ITEM_NAME" class="form-control rounded-3" placeholder="Enter item name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium">Category</label>
                    <select name="CATEGORY_ID" class="form-select rounded-3" required>
                        <option value="">Select Category</option>
                        <?php
                        while($cat = sqlsrv_fetch_array($result_categories)) {
                            echo "<option value='" . $cat['CATEGORY_ID'] . "'>" . $cat['CATEGORY_NAME'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium">Price (₱)</label>
                    <input type="number" step="0.01" name="PRICE" class="form-control rounded-3" placeholder="0.00" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium">Description</label>
                    <textarea name="DESCRIPTION" class="form-control rounded-3" rows="3" placeholder="Enter item description"></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-medium">Availability</label>
                    <select name="AVAILABILITY" class="form-select rounded-3" required>
                        <option value="AVAILABLE">Available</option>
                        <option value="UNAVAILABLE">Unavailable</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <form action="manage_menu.php" method="post" class="flex-fill">
                        <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                        <button type="submit" class="btn btn-secondary w-100 rounded-3">← Back</button>
                    </form>
                    <button type="submit" class="btn btn-primary w-100 rounded-3 fw-semibold flex-fill">Add Menu Item</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>