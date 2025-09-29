<?php
error_reporting(0);
header('Content-Type: application/json; charset=UTF-8');

$conn = new mysqli("localhost", "root", "", "test_db");
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB Connection failed"]);
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int) $_POST['id'];
    $page = (int) $_POST['page'];

    // Delete query
    if ($conn->query("DELETE FROM users WHERE id=$id")) {

        // Count remaining records
        $countRes = $conn->query("SELECT COUNT(*) AS cnt FROM users");
        $totalRecords = (int) $countRes->fetch_assoc()['cnt'];

        $limit = 5; // same as table.php
        $totalPages = ceil($totalRecords / $limit);

        // ✅ Adjust page
        if ($totalRecords == 0) {
            $page = 1; // if no records left
        } elseif ($page > $totalPages) {
            $page = $totalPages; // go to last available page
        }

        echo json_encode([
            "status" => "success",
            "message" => "User deleted successfully",
            "redirectPage" => $page
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to delete user"
        ]);
    }
    exit; // ✅ stop further code execution
}



// ---------------- EXISTING INSERT / UPDATE CODE ----------------
$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$name = $conn->real_escape_string($_POST['name'] ?? '');
$email = $conn->real_escape_string($_POST['email'] ?? '');
$phone = $conn->real_escape_string($_POST['phone'] ?? '');
$city = $conn->real_escape_string($_POST['city'] ?? '');
$profile = '';

if (isset($_FILES['profile']) && $_FILES['profile']['error'] == 0) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir))
        mkdir($uploadDir, 0755, true);

    $ext = strtolower(pathinfo($_FILES['profile']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
        echo json_encode(["status" => "error", "message" => "Invalid image type"]);
        exit;
    }

    $profile = time() . '_' . basename($_FILES['profile']['name']);
    move_uploaded_file($_FILES['profile']['tmp_name'], $uploadDir . $profile);
}

if ($id != 0) {
    $sql = "UPDATE users SET name='$name', email='$email', phone='$phone', city='$city'";
    if ($profile)
        $sql .= ", profile='$profile'";
    $sql .= " WHERE id=$id";
    $msg = "User updated successfully";
} else {
    $sql = "INSERT INTO users (name,email,phone,city,profile) 
            VALUES ('$name','$email','$phone','$city','$profile')";
    $msg = "User inserted successfully";
}

if ($conn->query($sql))
    echo json_encode(["status" => "success", "message" => $msg]);
else
    echo json_encode(["status" => "error", "message" => $conn->error]);

exit;
?>