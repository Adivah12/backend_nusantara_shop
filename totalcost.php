<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$host = "localhost";
$username = "aditya vahreza";
$password = "AdityaVahreza1";
$database = "login_database";

$koneksi = mysqli_connect($host, $username, $password, $database);
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$data = json_decode(file_get_contents("php://input"), true);
$totalCost = isset($data['TotalCost']) ? floatval($data['TotalCost']) : 0;

// Generate random 12-digit virtual account number
$virtualAccountNumber = generateVirtualAccountNumber();

$query = "INSERT INTO total (TotalCost, VirtualAccountNumber) VALUES (?, ?)";
$stmt = mysqli_prepare($koneksi, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ds", $totalCost, $virtualAccountNumber);

    if (mysqli_stmt_execute($stmt)) {
        $response = array('success' => true, 'message' => 'Data berhasil disimpan!', 'VirtualAccountNumber' => $virtualAccountNumber);
    } else {
        $response = array('success' => false, 'message' => 'Gagal menyimpan data: ' . mysqli_error($koneksi));
    }

    mysqli_stmt_close($stmt);
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
?>
