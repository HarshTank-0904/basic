<?php 
$conn = new mysqli("localhost", "root", "", "test_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - AJAX Pagination</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { text-align: center; margin-bottom: 20px; color: #333; }
        table { border-collapse: collapse; width: 90%; margin: 0 auto; font-size: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ddd; padding: 10px 12px; text-align: center; }
        th { background-color: #f4f4f4; color: #333; }
        tr:nth-child(even) { background-color: #fafafa; }
        tr:hover { background-color: #f1f1f1; }
        a { text-decoration: none; color: #007bff; margin: 0 5px; cursor: pointer; }
        a:hover { text-decoration: underline; }
        img { height: 50px; width: 50px; object-fit: cover; border-radius: 4px; }
        .pagination { text-align: center; margin: 15px; }
        .pagination a, .pagination span { padding: 6px 12px; border: 1px solid #ddd; margin: 0 3px; border-radius: 4px; background: #f9f9f9; color: #007bff; cursor:pointer; }
        .pagination span { color: grey; cursor: default; }
        .pagination a:hover { background: #007bff; color: #fff; }
        #msgBox { width:90%;margin:10px auto;padding:10px;text-align:center;border-radius:4px;display:none; }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<h2>User Management (AJAX)</h2>
<div id="msgBox"></div>

<!-- Table + Pagination will be loaded here -->
<div id="tableContainer"></div>

<script>
function loadTable(page = 1) {
    $.get("fetch.php", { page: page }, function(data) {
        $("#tableContainer").html(data);
    });
}

$(document).ready(function() {
    // initial load
    loadTable();

    // Handle pagination click
    $(document).on("click", ".pagination a", function(e) {
        e.preventDefault();
        let page = $(this).data("page");
        loadTable(page);
    });

    // Handle delete
    $(document).on("click", ".deleteBtn", function() {
        if (!confirm("Are you sure you want to delete this user?")) return;
        let id = $(this).data("id");
        let page = $(this).data("page");

        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: { action: "delete", id: id, page: page },
            dataType: "json",
            success: function (response) {
                $("#msgBox")
                    .text(response.message)
                    .css({
                        "background": response.status === "success" ? "#d4edda" : "#f8d7da",
                        "color": response.status === "success" ? "#155724" : "#721c24",
                        "border": response.status === "success" ? "1px solid #c3e6cb" : "1px solid #f5c6cb"
                    })
                    .fadeIn();

                setTimeout(() => { $("#msgBox").fadeOut(); }, 3000);

                if (response.status === "success") {
                    loadTable(response.redirectPage); // reload table
                }
            }
        });
    });
});
</script>

</body>
</html>
