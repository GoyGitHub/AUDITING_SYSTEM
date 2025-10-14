<?php
session_start();

// Redirect if already completed checklist
if (isset($_SESSION['checklist_done']) && $_SESSION['checklist_done'] === true) {
    header("Location: PreAuditChecklist.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['checklist_done'] = true;
    header("Location: AuditorDashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pre-Audit Checklist</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; }
        .container { width: 50%; margin: 80px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
        h2 { text-align: center; }
        .checklist-item { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="checkbox"] { margin-right: 10px; }
        .btn { background: #007bff; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; }
        .btn:disabled { background: #aaa; }
    </style>
</head>
<body>
<div class="container">
    <h2>📝 Pre-Audit Checklist</h2>
    <form method="POST">
        <div class="checklist-item">
            <label><input type="checkbox" id="recording" required> I have the agent’s call or chat recording ready.</label>
        </div>
        <div class="checklist-item">
            <label><input type="checkbox" id="case" required> I have verified the case or ticket ID.</label>
        </div>
        <div class="checklist-item">
            <label><input type="checkbox" id="rubric" required> I have reviewed the latest QA rubric and scoring guide.</label>
        </div>
        <div class="checklist-item">
            <label><input type="checkbox" id="tools" required> All required QA tools are accessible and working.</label>
        </div>

        <center><button type="submit" class="btn">✅ Proceed to Dashboard</button></center>
    </form>
</div>
</body>
</html>
