<?php
include('../database/dbconnection.php'); // adjust path if needed

// Handle Add Account
if (isset($_POST['add_account'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']); // plain text
    $role = $_POST['role'];
    $created_at = date("Y-m-d H:i:s");

    // Check for empty fields
    if (empty($username) || empty($password) || empty($role)) {
        echo "<script>alert('❌ All fields are required!');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, created_at) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $password, $role, $created_at);

        if ($stmt->execute()) {
            // Redirect to AdminTools.php on success
            echo "<script>alert('✅ Account added successfully!'); window.location='../pages/admin/AdminTools.php';</script>";
        } else {
            echo "<script>alert('❌ Error adding account: " . addslashes($conn->error) . "');</script>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Admin Account</title>
<link rel="stylesheet" href="../../assets/css/styles.css">
<style>
    body { font-family: 'Nunito Sans', sans-serif; background: #f0f2f5; padding: 40px; }
    .container { max-width: 500px; margin: auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 6px 16px rgba(0,0,0,0.12);}
    h2 { text-align: center; margin-bottom: 20px; color: #003366; }
    input, select, button { width: 100%; padding: 12px; margin: 10px 0; border-radius: 8px; border: 1px solid #ccc; font-size: 1rem; }
    button { background: #003366; color: #fff; border: none; cursor: pointer; transition: 0.3s; }
    button:hover { background: #0055aa; }
</style>
</head>
<body>

<div class="container">
    <h2>Add Admin Account</h2>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="" disabled selected>Select Role</option>
            <option value="admin">Admin</option>
            <option value="auditor">Auditor</option>
            <option value="supervisor">Supervisor</option>
            <option value="data_analyst">Data Analyst</option>
            <option value="user">User</option>
        </select>
        <button type="submit" name="add_account">Add Account</button>
    </form>
</div>

</body>
</html>
