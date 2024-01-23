<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$host = "localhost";
$username = "aditya vahreza";
$password = "AdityaVahreza1";
$database = "db_total";

$koneksi = mysqli_connect($host, $username, $password, $database);
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$data = json_decode(file_get_contents("php://input"), true);
$selectedProducts = isset($data['selectedProducts']) ? $data['selectedProducts'] : [];
$totalCost = isset($data['TotalCost']) ? floatval($data['TotalCost']) : 0;

// Generate random 12-digit virtual account number
$virtualAccountNumber = generateVirtualAccountNumber();

// Generate random 10-character invoice ID
$invoiceID = generateInvoiceID();

// Gunakan prepared statement untuk mencegah SQL injection
$stmtCart = mysqli_prepare($koneksi, "INSERT INTO cart_items (invoiceID, title, quantity) VALUES (?, ?, ?)");
$stmtTotal = mysqli_prepare($koneksi, "INSERT INTO total (invoiceID, TotalCost, VirtualAccountNumber) VALUES (?, ?, ?)");

if ($stmtCart && $stmtTotal) {
    mysqli_stmt_bind_param($stmtCart, "ssi", $invoiceID, $title, $quantity);
    mysqli_stmt_bind_param($stmtTotal, "sds", $invoiceID, $totalCost, $virtualAccountNumber);

    // Simpan data ke tabel cart_items
    foreach ($selectedProducts as $product) {
        $title = mysqli_real_escape_string($koneksi, $product['title']);
        $quantity = intval($product['quantity']);  // Konversi ke integer
    
        mysqli_stmt_execute($stmtCart);  // Eksekusi prepared statement
    }

    // Simpan data ke tabel total
    if (mysqli_stmt_execute($stmtTotal)) {
        $response = array('success' => true, 'message' => 'Data berhasil disimpan!', 'InvoiceID' => $invoiceID, 'VirtualAccountNumber' => $virtualAccountNumber);
    } else {
        $response = array('success' => false, 'message' => 'Gagal menyimpan data: ' . mysqli_error($koneksi));
    }

    mysqli_stmt_close($stmtCart);
    mysqli_stmt_close($stmtTotal);
} else {
    $response = array('success' => false, 'message' => 'Gagal membuat statement: ' . mysqli_error($koneksi));
}

mysqli_close($koneksi);

header('Content-Type: application/json');
echo json_encode($response);

function generateVirtualAccountNumber() {
    // Generate a random 12-digit number
    return strval(mt_rand(100000000000, 999999999999));
}

function generateInvoiceID() {
    // Generate a random 10-character alphanumeric string
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $invoiceID = '';
    for ($i = 0; $i < 10; $i++) {
        $invoiceID .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $invoiceID;
}
?>
