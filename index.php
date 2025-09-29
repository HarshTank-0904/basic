<?php
$conn = new mysqli("localhost", "root", "", "test_db");
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

// Default values
$id = $name = $email = $phone = $city = $profilePath = '';
$updateMode = false;

// If redirected for update
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $res = $conn->query("SELECT * FROM users WHERE id=$id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $name = $row['name'];
        $email = $row['email'];
        $phone = $row['phone'];
        $city = $row['city'];
        $profilePath = $row['profile'] ? "uploads/" . $row['profile'] : '';
        $updateMode = true;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= $updateMode ? "Update User" : "Add User" ?></title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        form {
            width: 400px;
            margin: 20px auto;
        }

        input {
            width: 90%;
            margin: 5px 0;
            padding: 8px;
        }

        img {
            max-width: 80px;
            margin-top: 5px;
        }

        button {
            padding: 5px 10px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <h2 style="text-align:center;"><?= $updateMode ? "Update User" : "Add User" ?></h2>



    <form id="regForm" enctype="multipart/form-data">
        <input type="hidden" name="id" id="user_id" value="<?= $id ?>">

        <label>Name:</label><br>
        <input type="text" name="name" id="name" value="<?= $name ?>" required><br>

        <label>Email:</label><br>
        <input type="email" name="email" id="email" value="<?= $email ?>" required><br>

        <label>Phone:</label><br>
        <input type="text" name="phone" id="phone" value="<?= $phone ?>" required><br>

        <label>City:</label><br>
        <input type="text" name="city" id="city" value="<?= $city ?>" required><br>

        <label>Profile Picture:</label><br>
        <input type="file" name="profile" id="profile"><br>
        <?php if ($profilePath): ?>
            <img id="preview" src="<?= $profilePath ?>" alt="Profile">
        <?php else: ?>
            <img id="preview" style="display:none;">
        <?php endif; ?>

        <br>
        <button type="submit"><?= $updateMode ? "Update" : "Submit" ?></button>
        <button type="reset" id="resetBtn">Reset</button>
    </form>

    <script>
        $(document).ready(function () {

            // Preview image
            $('#profile').on('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) { $('#preview').attr('src', e.target.result).show(); }
                    reader.readAsDataURL(file);
                } else { $('#preview').hide(); }
            });

            // Submit form
            $('#regForm').on('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                $.ajax({
                    url: 'ajax.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        // show message for insert
                        if (response.status === "success" && !$("#user_id").val()) {
                            $("#msgBox")
                                .text(response.message)
                                .css({
                                    "background": "#d4edda",
                                    "color": "#155724",
                                    "border": "1px solid #c3e6cb"
                                })
                                .fadeIn();

                            setTimeout(() => { $("#msgBox").fadeOut(); }, 3000);

                            $('#regForm')[0].reset();
                            $('#preview').hide();
                        }
                        // redirect for update
                        else if (response.status === "success" && $("#user_id").val()) {
                            // pass message via query string
                            window.location.href = "table.php?msg=" + encodeURIComponent(response.message);
                        }
                        else {
                            // error case
                            $("#msgBox")
                                .text(response.message)
                                .css({
                                    "background": "#f8d7da",
                                    "color": "#721c24",
                                    "border": "1px solid #f5c6cb"
                                })
                                .fadeIn();
                            setTimeout(() => { $("#msgBox").fadeOut(); }, 3000);
                        }
                    },


                    error: function (err) {
                        alert('AJAX error');
                        console.error(err);
                    }
                });
            });

            // Reset form
            $('#resetBtn').on('click', function () {
                $('#user_id').val('');
                $('#preview').hide();
            });

        });
    </script>
</body>

</html>