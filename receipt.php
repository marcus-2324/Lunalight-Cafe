<?php
$serverName = "LAPTOP-QNA7LUF8\SQLEXPRESS";
$connectionOptions = [
    "Database" => "LUNALIGHT",
    "Uid" => "",
    "PWD" => ""
];
$conn = sqlsrv_connect($serverName, $connectionOptions);
if($conn == false) {
    die(print_r(sqlsrv_errors(), true));
}

$ORDER_NUMBER = $_POST['ORDER_NUMBER'];
$USER_ID = $_POST['USER_ID'];

$sql = "SELECT o.ORDER_ID, o.ORDER_NUMBER, o.ORDER_DATE, o.ORDER_TIME, o.TOTAL_AMOUNT, 
               o.PAYMENT_METHOD, o.AMOUNT_PAID, o.CHANGE_AMOUNT, o.STATUS,
               u.FULL_NAME, u.ROLE
        FROM ORDERS AS o 
        INNER JOIN USERS AS u ON o.USER_ID = u.USER_ID 
        WHERE o.ORDER_NUMBER = '$ORDER_NUMBER'";
$result = sqlsrv_query($conn, $sql);
$order = sqlsrv_fetch_array($result);

if($order == false) {
    echo "Order not found!";
    exit();
}

$sql_items = "SELECT oi.QUANTITY, oi.UNIT_PRICE, oi.SUBTOTAL, m.ITEM_NAME 
              FROM ORDER_ITEMS AS oi 
              INNER JOIN MENU_ITEMS AS m ON oi.ITEM_ID = m.ITEM_ID 
              WHERE oi.ORDER_ID = " . $order['ORDER_ID'];
$result_items = sqlsrv_query($conn, $sql_items);

$receipt_html = '<!DOCTYPE html>
<html>
<head>
    <title>Receipt - ' . $ORDER_NUMBER . '</title>
    <style>
        body {
            font-family: "Courier New", monospace;
            padding: 20px;
            background: white;
            margin: 0;
        }
        .receipt {
            max-width: 400px;
            margin: 0 auto;
            border: 2px solid black;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px dashed black;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 24px;
            margin: 10px 0;
        }
        .header p {
            font-size: 12px;
            margin: 3px 0;
        }
        .info {
            margin-bottom: 15px;
            font-size: 13px;
        }
        .info p {
            margin: 5px 0;
        }
        .items {
            margin-bottom: 15px;
            border-top: 1px dashed black;
            padding-top: 10px;
        }
        .item-row {
            margin: 8px 0;
            font-size: 13px;
        }
        .totals {
            border-top: 2px solid black;
            padding-top: 10px;
            margin-top: 10px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            font-size: 14px;
        }
        .grand-total {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid black;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px dashed black;
            font-size: 12px;
        }
        @media print {
            body {
                background: white;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div style="font-size: 40px;">🌙</div>
            <h1>LUNALIGHT CAFÉ</h1>
            <p>Where Night Meets Delight</p>
            <p>Manila, Metro Manila, Philippines</p>
            <p>Contact: 09123456789</p>
        </div>

        <div class="info">
            <p><strong>Receipt No:</strong> ' . $order['ORDER_NUMBER'] . '</p>
            <p><strong>Date:</strong> ' . $order['ORDER_DATE']->format('F d, Y') . '</p>
            <p><strong>Time:</strong> ' . $order['ORDER_TIME']->format('h:i A') . '</p>
            <p><strong>Cashier:</strong> ' . $order['FULL_NAME'] . '</p>
        </div>

        <div class="items">';

while($item = sqlsrv_fetch_array($result_items)) {
    $receipt_html .= '<div class="item-row">
                <div style="font-weight: bold;">' . $item['ITEM_NAME'] . '</div>
                <div style="display: flex; justify-content: space-between;">
                    <span>' . $item['QUANTITY'] . ' x ₱' . number_format($item['UNIT_PRICE'], 2) . '</span>
                    <span>₱' . number_format($item['SUBTOTAL'], 2) . '</span>
                </div>
            </div>';
}

$receipt_html .= '</div>

        <div class="totals">
            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>₱' . number_format($order['TOTAL_AMOUNT'], 2) . '</span>
            </div>
            <div class="total-row">
                <span>Payment:</span>
                <span>' . $order['PAYMENT_METHOD'] . '</span>
            </div>
            <div class="total-row">
                <span>Amount Paid:</span>
                <span>₱' . number_format($order['AMOUNT_PAID'], 2) . '</span>
            </div>
            <div class="total-row">
                <span>Change:</span>
                <span>₱' . number_format($order['CHANGE_AMOUNT'], 2) . '</span>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for visiting Lunalight Café!</p>
            <p>Come back soon! 🌙</p>
            <p>*** OFFICIAL RECEIPT ***</p>
        </div>
    </div>

    <div style="text-align: center; margin-top: 30px;" class="no-print">
        <button onclick="window.print()" style="background: #27ae60; color: white; border: none; padding: 12px 30px; border-radius: 10px; font-weight: 600; cursor: pointer;">🖨️ Print Receipt</button>
        <form action="';

if($order['ROLE'] == 'ADMIN') {
    $receipt_html .= 'dashboard_admin.php';
}
else {
    $receipt_html .= 'dashboard_cashier.php';
}

$receipt_html .= '" method="post" style="display: inline;">
            <input type="hidden" name="USER_ID" value="' . $USER_ID . '">
            <button type="submit" style="background: #6c757d; color: white; border: none; padding: 12px 30px; border-radius: 10px; cursor: pointer; margin-left: 10px;">← Back to Dashboard</button>
        </form>
    </div>
</body>
</html>';

$receipts_folder = 'receipts';
if(file_exists($receipts_folder) == false) {
    mkdir($receipts_folder, 0777, true);
}

$filename = 'RECEIPT_' . $ORDER_NUMBER . '.html';
$filepath = $receipts_folder . '/' . $filename;
file_put_contents($filepath, $receipt_html);

$file_size_kb = filesize($filepath) / 1024;
$sql_update = "UPDATE RECEIPT SET FILE_SIZE_KB = $file_size_kb WHERE ORDER_NUMBER = '$ORDER_NUMBER'";
sqlsrv_query($conn, $sql_update);

echo $receipt_html;
?>