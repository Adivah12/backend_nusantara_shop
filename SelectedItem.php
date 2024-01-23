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
$selectedProducts = isset($data['selectedProducts']) ? $data['selectedProducts'] : [];

// Gunakan prepared statement untuk mencegah SQL injection
$stmt = mysqli_prepare($koneksi, "INSERT INTO cart_items (title, quantity) VALUES (?, ?)");

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "si", $title, $quantity);

    foreach ($selectedProducts as $product) {
        $title = mysqli_real_escape_string($koneksi, $product['title']);
        $quantity = intval($product['quantity']);  // Konversi ke integer
    
        mysqli_stmt_bind_param($stmt, "si", $title, $quantity);  // Binding parameter
        mysqli_stmt_execute($stmt);  // Eksekusi prepared statement
    }

    mysqli_stmt_close($stmt);

    $response = array('success' => true, 'message' => 'Data produk berhasil disimpan!');
} else {
    $response = array('success' => false, 'message' => 'Gagal menyimpan data produk: ' . mysqli_error($koneksi));
}

mysqli_close($koneksi);

header('Content-Type: application/json');
echo json_encode($response);