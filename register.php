<?php
header('Access-Control-Allow-Origin: http://localhost:5173'); // Sesuaikan dengan URL React Anda
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
$username = isset($data['username']) ? mysqli_real_escape_string($koneksi, $data['username']) : '';
$password = isset($data['password']) ? mysqli_real_escape_string($koneksi, $data['password']) : '';


$query = "INSERT INTO users (username, password) VALUES (?, ?)";
$stmt = mysqli_prepare($koneksi, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);

    if (mysqli_stmt_execute($stmt)) {
        $response = array('success' => true, 'message' => 'Data berhasil disimpan!');
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
?>
