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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add User - Lunalight Café</title>
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
            <div class="text-center mb-4">
                <div class="fs-1 mb-3">🌙</div>
                <h1 class="fw-bold mb-2">Add New User</h1>
                <p class="text-muted">Create Admin or Cashier Account</p>
            </div>

            <form action="process_add_user.php" method="post">
                <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                
                <div class="card bg-light border-0 rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3">Account Information</h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Username</label>
                        <input type="text" name="USERNAME" class="form-control rounded-3" placeholder="Choose a username" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Password</label>
                        <input type="password" name="PASSWORD" class="form-control rounded-3" placeholder="Create a password" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Email</label>
                        <input type="email" name="EMAIL" class="form-control rounded-3" placeholder="you@example.com" required>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-medium">Role</label>
                        <select name="ROLE" class="form-select rounded-3" required>
                            <option value="">Select Role</option>
                            <option value="ADMIN">Admin</option>
                            <option value="CASHIER">Cashier</option>
                        </select>
                    </div>
                </div>

                <div class="card bg-light border-0 rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3">Personal Details</h5>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Full Name</label>
                        <input type="text" name="FULL_NAME" class="form-control rounded-3" placeholder="Enter full name" required>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-medium">Mobile Number</label>
                        <input type="text" name="MOBILE_NUMBER" class="form-control rounded-3" placeholder="09123456789">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 fw-semibold mb-3">Create User Account</button>

                <div class="text-center">
                    <form action="manage_users.php" method="post" class="d-inline">
                        <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                        <button type="submit" class="btn btn-link text-decoration-none fw-semibold">← Back to User Management</button>
                    </form>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>