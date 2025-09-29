<?php
$conn = new mysqli("localhost", "root", "", "test_db");
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

// Pagination
$limit = 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1)
    $page = 1;
$start = ($page - 1) * $limit;

// Fetch records
$res = $conn->query("SELECT * FROM users ORDER BY id ASC LIMIT $start, $limit");

// Total records
$totalRes = $conn->query("SELECT COUNT(*) AS total FROM users");
$totalRow = $totalRes->fetch_assoc();
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);
?>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>City</th>
        <th>Profile</th>
        <th>Action</th>
    </tr>
    <?php
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['phone']}</td>
                <td>{$row['city']}</td>
                <td><img src='uploads/{$row['profile']}'></td>
                <td>
                    <a href='index.php?id={$row['id']}'>Update</a> | 
                    <button class='deleteBtn' data-id='{$row['id']}' data-page='{$page}'>Delete</button>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No Records Found</td></tr>";
    }
    ?>
</table>

<div class="pagination">
    <?php
    // Prev
    if ($page > 1) {
        $prev = $page - 1;
        echo "<a href='#' data-page='{$prev}'>⬅ Prev</a>";
    } else {
        echo "<span>⬅ Prev</span>";
    }

    // Next
    if ($page < $totalPages) {
        $next = $page + 1;
        echo "<a href='#' data-page='{$next}'>Next ➡</a>";
    } else {
        echo "<span>Next ➡</span>";
    }
    ?>
</div>