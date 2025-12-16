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

$sql = "SELECT * FROM USERS ORDER BY USER_ID DESC";
$result = sqlsrv_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users - Lunalight Café</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
        }
        .badge-admin {
            background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
        }
        .badge-cashier {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
        }
    </style>
</head>
<body>
    <div class="container-fluid p-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-3 border-primary">
                    <h1 class="mb-0 fw-bold">👥 Manage Users</h1>
                    <div>
                        <form action="add_user.php" method="post" class="d-inline">
                            <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                            <button type="submit" class="btn btn-primary me-2 rounded-3 fw-semibold">+ Add New User</button>
                        </form>
                        <form action="dashboard_admin.php" method="post" class="d-inline">
                            <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                            <button type="submit" class="btn btn-secondary rounded-3">← Back</button>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Full Name</th>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Status</th>
                                <th>Date Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while($row = sqlsrv_fetch_array($result)) {
                                $user_id_row = $row['USER_ID'];
                                $username = $row['USERNAME'];
                                $full_name = $row['FULL_NAME'];
                                $role = $row['ROLE'];
                                $email = $row['EMAIL'];
                                $mobile = $row['MOBILE_NUMBER'];
                                $status = $row['STATUS'];
                                $date_created = $row['DATE_CREATED'];
                                
                                if($date_created) {
                                    $date_display = $date_created->format('Y-m-d');
                                } else {
                                    $date_display = 'N/A';
                                }
                                
                                $role_badge = 'badge-admin';
                                if($role == 'CASHIER') {
                                    $role_badge = 'badge-cashier';
                                }
                                
                                $status_badge = 'bg-success';
                                if($status == 'INACTIVE') {
                                    $status_badge = 'bg-danger';
                                }
                                
                                $sql_check_delete = "SELECT * FROM USERS WHERE USER_ID = $user_id_row AND USER_ID != $USER_ID";
                                $result_delete = sqlsrv_query($conn, $sql_check_delete);
                                $can_delete = sqlsrv_fetch_array($result_delete);
                                
                                $delete_btn = '';
                                if($can_delete) {
                                    $delete_btn = "<form method='post' action='process_delete_user.php' class='d-inline' onsubmit='return confirm(\"Delete this user?\")'>
                                                    <input type='hidden' name='DELETE_ID' value='$user_id_row'>
                                                    <input type='hidden' name='USER_ID' value='$USER_ID'>
                                                    <button type='submit' class='btn btn-danger btn-sm rounded-2'>Delete</button>
                                                  </form>";
                                }
                                
                                echo "<tr>
                                    <td>$user_id_row</td>
                                    <td class='fw-semibold'>$username</td>
                                    <td>$full_name</td>
                                    <td><span class='badge $role_badge text-white'>$role</span></td>
                                    <td>$email</td>
                                    <td>$mobile</td>
                                    <td><span class='badge $status_badge'>$status</span></td>
                                    <td>$date_display</td>
                                    <td>$delete_btn</td>
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