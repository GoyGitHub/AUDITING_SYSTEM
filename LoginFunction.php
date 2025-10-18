<?php
include('database/dbconnection.php');
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    // Normalize selected role early for consistent compare
    $selected_role = isset($_POST['role']) ? strtolower(trim($_POST['role'])) : '';

    if (empty($username) || empty($password) || empty($selected_role)) {
        $error = "All fields are required.";
    } else {
        // Use prepared statement for security
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows === 1) {
                $user = $result->fetch_assoc();

                // Verify password (plain or hashed)
                $validPassword = ($password === $user['password'] || password_verify($password, $user['password']));
                $dbRole = strtolower(trim($user['role'] ?? ''));

                // Accept exact role match
                $validRole = ($selected_role === $dbRole);

                if ($validPassword && $validRole) {
                    // ✅ Set session variables consistently
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_role'] = $dbRole;

                    // ✅ Redirect user to proper dashboard
                    switch ($_SESSION['user_role']) {
                        case 'admin':
                            header("Location: pages/admin/AdminDashboard.php");
                            exit;
                        case 'auditor':
                            header("Location: pages/auditor/AuditorDashboard.php");
                            exit;
                        case 'supervisor':
                            header("Location: pages/supervisor/SupervisorDashboard.php");
                            exit;
                        case 'data_analyst':
                            header("Location: pages/data_analyst/DataAnalystDashboard.php");
                            exit;
                        case 'agent':
                            header("Location: pages/agent/AgentDashboard.php");
                            exit;
                        default:
                            $error = "Unknown role detected.";
                            break;
                    }
                } else {
                    // clearer error to help debug role vs password mismatches
                    if (!$validPassword) {
                        $error = "Invalid password.";
                    } else {
                        $error = "Invalid role selection. (selected: {$selected_role}, account role: {$dbRole})";
                    }
                }
            } else {
                $error = "User not found.";
            }

            $stmt->close();
        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- External CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <main class="card" aria-label="Responsive Login Form">
        <!-- Left Section: Welcome Message -->
        <section class="hero">
            <!-- unifyCX Logo -->
            <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 10px;">
                <img src="assets/img/logo2.png" alt="unifyCX Logo" style="max-width: 160px; height: auto;">
            </div>
            <h1 style="color: #fff;">Welcome to <span style="color: #fff;">UnifyCX</span></h1>
            <p style="font-size: 1.08rem; color: #fff; font-weight: 500; margin-top: 10px; text-align: left;">
                At unifyCX, we empower your business with seamless auditing and analytics.<br>
                Please log in to access your personalized dashboard and drive operational excellence.<br>
                <span style="color: #fff; font-weight: 700;">Your experience, unified.</span>
            </p>
        </section>

        <!-- Right Section: Login Form -->
        <section class="panel">
            <div class="decor">
                <div class="circle-outline"></div>
                <span class="dot one"></span>
                <span class="dot two"></span>
                <div class="corner-blob"></div>
            </div>

            <h2>Login Using your Account</h2>
            <form method="POST" action="">
                <!-- Role Dropdown (moved above Username) -->
                <div class="field" style="margin-bottom: 18px;">
                    <select class="input casual-role-select" name="role" required style="
                        background: #f7f7fa;
                        border: 1.5px solid #bdbdbd;
                        border-radius: 12px;
                        padding: 12px 14px;
                        font-size: 1.05rem;
                        color: #444;
                        font-family: 'Nunito Sans', sans-serif;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
                        outline: none;
                        transition: border 0.2s;
                        margin-bottom: 0;
                    ">
                        <option value="" disabled selected style="color:#aaa;">👤 Choose your role...</option>
                        <option value="admin">🛡️ Administrator</option>
                        <option value="auditor">🔍 Auditor</option>
                        <option value="supervisor">👔 Supervisor</option>
                        <option value="data_analyst">📊 Data Analyst</option>
                        <option value="agent">👤 Agent</option>
                    </select>
                </div>

                <!-- Username -->
                <div class="field" style="margin-bottom: 18px;">
                    <input type="text" class="input" name="username" placeholder="Username" required>
                </div>

                <!-- Password -->
                <div class="field">
                    <input type="password" class="input" id="password" name="password" placeholder="Password" required>
                </div>

                <!-- Show Password Checkbox -->
                <div class="row-between">
                    <label class="show-password" style="font-size: 13px; color: #6b6b6b;">
                        <input type="checkbox" id="showPassword" onclick="togglePassword()">
                        <span>Show Password</span>
                    </label>
                </div>

                <!-- Error Message -->
                <?php if (!empty($error)): ?>
                    <p style="color: red; text-align: center; margin-bottom: 10px;">
                        <?php echo $error; ?>
                    </p>
                <?php endif; ?>

                <!-- Submit Button -->
                <button class="btn" type="submit">Login</button>
            </form>
        </section>
    </main>

    <script>
        // Toggle Password Visibility
        function togglePassword() {
            var pass = document.getElementById("password");
            pass.type = (pass.type === "password") ? "text" : "password";
        }
    </script>
</body>
</html>
