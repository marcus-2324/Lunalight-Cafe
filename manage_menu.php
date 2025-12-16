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

$sql = "SELECT m.*, c.CATEGORY_NAME, i.FILEPATH 
        FROM MENU_ITEMS m 
        LEFT JOIN CATEGORIES c ON m.CATEGORY_ID = c.CATEGORY_ID
        LEFT JOIN IMAGES i ON m.ITEM_ID = i.ITEM_ID
        ORDER BY m.ITEM_ID DESC";
$result = sqlsrv_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Menu - Lunalight Café</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .menu-image {
            width: 100%;
            height: 250px;
            object-fit: contain;
            background: #f8f9fa;
        }
        .no-image {
            width: 100%;
            height: 250px;
            background: linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-3 border-primary">
                    <h1 class="mb-0 fw-bold">🍴 Manage Menu Items</h1>
                    <div>
                        <form action="add_menu_item.php" method="post" class="d-inline">
                            <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                            <button type="submit" class="btn btn-primary me-2">+ Add New Item</button>
                        </form>
                        <form action="dashboard_admin.php" method="post" class="d-inline">
                            <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                            <button type="submit" class="btn btn-secondary">← Back</button>
                        </form>
                    </div>
                </div>

                <div class="row g-4">
                    <?php
                    while($row = sqlsrv_fetch_array($result)) {
                        $item_id = $row['ITEM_ID'];
                        $item_name = $row['ITEM_NAME'];
                        $category_name = $row['CATEGORY_NAME'];
                        $price = $row['PRICE'];
                        $description = $row['DESCRIPTION'];
                        $availability = $row['AVAILABILITY'];
                        $filepath = $row['FILEPATH'];
                        
                        $badge_class = 'bg-success';
                        if($availability == 'UNAVAILABLE') {
                            $badge_class = 'bg-danger';
                        }
                        
                        echo "<div class='col-md-4'>
                            <div class='card h-100 shadow-sm border-2 hover-shadow'>
                                <div class='card-img-top p-0 overflow-hidden'>";
                        
                        if($filepath) {
                            echo "<img src='$filepath' class='menu-image' alt='$item_name'>";
                        } else {
                            echo "<div class='no-image'>🍽️</div>";
                        }
                        
                        echo "</div>
                                <div class='card-body'>
                                    <h5 class='card-title fw-bold mb-2'>$item_name</h5>
                                    <p class='text-muted small mb-2'>$category_name</p>
                                    <p class='card-text text-secondary small mb-3' style='min-height: 40px;'>$description</p>
                                    <div class='d-flex justify-content-between align-items-center mb-3'>
                                        <h4 class='text-primary mb-0 fw-bold'>₱" . number_format($price, 2) . "</h4>
                                        <span class='badge $badge_class'>$availability</span>
                                    </div>
                                    
                                    <div class='d-flex gap-2 flex-wrap'>
                                        <form method='post' action='edit_menu_item.php' class='flex-fill'>
                                            <input type='hidden' name='ITEM_ID' value='$item_id'>
                                            <input type='hidden' name='USER_ID' value='$USER_ID'>
                                            <button type='submit' class='btn btn-warning btn-sm w-100'>Edit</button>
                                        </form>
                                        <form method='post' action='upload_image.php' class='flex-fill'>
                                            <input type='hidden' name='ITEM_ID' value='$item_id'>
                                            <input type='hidden' name='USER_ID' value='$USER_ID'>
                                            <button type='submit' class='btn btn-success btn-sm w-100'>Upload</button>
                                        </form>
                                        <form method='post' action='process_delete_menu.php' class='flex-fill' onsubmit='return confirm(\"Delete this item?\")'>
                                            <input type='hidden' name='ITEM_ID' value='$item_id'>
                                            <input type='hidden' name='USER_ID' value='$USER_ID'>
                                            <button type='submit' class='btn btn-danger btn-sm w-100'>Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .hover-shadow {
            transition: all 0.3s ease;
        }
        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
        }
    </style>
</body>
</html>