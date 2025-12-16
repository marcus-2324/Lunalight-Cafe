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

$sql = "SELECT * FROM CATEGORIES ORDER BY CATEGORY_ID DESC";
$result = sqlsrv_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Categories - Lunalight Café</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-3 border-primary">
                    <h1 class="mb-0 fw-bold">📂 Manage Categories</h1>
                    <form action="dashboard_admin.php" method="post">
                        <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                        <button type="submit" class="btn btn-secondary rounded-3">← Back to Dashboard</button>
                    </form>
                </div>

                <div class="card bg-light border-0 rounded-4 p-4 mb-4">
                    <h4 class="fw-bold mb-3">Add New Category</h4>
                    <form action="process_add_category.php" method="post">
                        <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label fw-medium">Category Name</label>
                                <input type="text" name="CATEGORY_NAME" class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-medium">Description</label>
                                <input type="text" name="DESCRIPTION" class="form-control rounded-3">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100 rounded-3 fw-semibold">Add</button>
                            </div>
                        </div>
                    </form>
                </div>

                <h4 class="fw-bold mb-3">All Categories</h4>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Category Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Date Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while($row = sqlsrv_fetch_array($result)) {
                                $cat_id = $row['CATEGORY_ID'];
                                $cat_name = $row['CATEGORY_NAME'];
                                $description = $row['DESCRIPTION'];
                                $status = $row['STATUS'];
                                if($status == '') {
                                    $status = 'ACTIVE';
                                }
                                $date_created = $row['DATE_CREATED'];
                                
                                if($date_created) {
                                    $date_display = $date_created->format('Y-m-d');
                                } else {
                                    $date_display = 'N/A';
                                }
                                
                                $status_badge = 'bg-success';
                                if($status == 'INACTIVE') {
                                    $status_badge = 'bg-danger';
                                }
                                
                                echo "<tr>
                                    <td>$cat_id</td>
                                    <td class='fw-semibold'>$cat_name</td>
                                    <td>$description</td>
                                    <td><span class='badge $status_badge'>$status</span></td>
                                    <td>$date_display</td>
                                    <td>";
                                
                                if($status == 'ACTIVE') {
                                    echo "<form method='post' action='process_deactivate_category.php' class='d-inline' onsubmit='return confirm(\"Deactivate this category and all its menu items?\")'>
                                            <input type='hidden' name='CATEGORY_ID' value='$cat_id'>
                                            <input type='hidden' name='USER_ID' value='$USER_ID'>
                                            <button type='submit' class='btn btn-warning btn-sm me-1 rounded-2'>Deactivate</button>
                                          </form>";
                                } else {
                                    echo "<form method='post' action='process_activate_category.php' class='d-inline'>
                                            <input type='hidden' name='CATEGORY_ID' value='$cat_id'>
                                            <input type='hidden' name='USER_ID' value='$USER_ID'>
                                            <button type='submit' class='btn btn-success btn-sm me-1 rounded-2'>Activate</button>
                                          </form>";
                                }
                                
                                echo "<form method='post' action='process_delete_category.php' class='d-inline' onsubmit='return confirm(\"Delete this category?\")'>
                                        <input type='hidden' name='CATEGORY_ID' value='$cat_id'>
                                        <input type='hidden' name='USER_ID' value='$USER_ID'>
                                        <button type='submit' class='btn btn-danger btn-sm rounded-2'>Delete</button>
                                      </form>";
                                
                                echo "</td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>