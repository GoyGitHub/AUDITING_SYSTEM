<?php
include('../../database/dbconnection.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$username = $_SESSION['username'] ?? 'DataAnalyst';
$role = ucfirst($_SESSION['user_role'] ?? 'Data Analyst');
$displayName = ucfirst($username) . '.';

// Handle supervisor comment report submission
$successMsg = '';
$errorMsg = '';
if (isset($_POST['file_report'])) {
    $audit_id = intval($_POST['audit_id'] ?? 0);
    $agent_name = trim($_POST['agent_name'] ?? '');
    $reviewer_name = trim($_POST['reviewer_name'] ?? '');
    $comment = trim($_POST['comment'] ?? '');
    if ($audit_id && $agent_name && $reviewer_name && $comment) {
        // Prefer the audit's reviewer as the "filed by" name if available, 
        // but the actual filer is the Data Analyst (session username)
        $filedByRole = "Data Analyst";
        $filedByName = $username; // session username
        $comment_with_filer = $comment . " (Filed by: {$filedByRole} - {$filedByName})";

        $stmt = $conn->prepare("INSERT INTO supervisor_comments (audit_id, agent_name, reviewer_name, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $audit_id, $agent_name, $reviewer_name, $comment_with_filer);
        if ($stmt->execute()) {
            $successMsg = "Report filed successfully!";
        } else {
            $errorMsg = "Error filing report: " . $conn->error;
        }
        $stmt->close();
    } else {
        $errorMsg = "All fields are required.";
    }
}

// Fetch audits for dropdown (latest 10)
$auditRows = [];
$auditRes = $conn->query("SELECT id, agent_name, reviewer_name, date, week FROM data_reports ORDER BY id DESC LIMIT 10");
if ($auditRes && $auditRes->num_rows > 0) {
    while ($row = $auditRes->fetch_assoc()) {
        $auditRows[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Supervisor Report</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css">
    <style>
        body { background: #fff; font-family: 'Nunito Sans', sans-serif; }
        .dashboard-article { padding: 2.5rem; background: #fff; border-radius: 1rem; box-shadow: 0 6px 16px rgba(0,0,0,0.12); margin-top: 2.5rem; }
        .report-form { background:#f5f8fa; border-radius:1rem; padding:2rem; max-width:600px; margin:2rem auto; }
        .report-form h2 { margin-top:0; color:#003366; }
        .report-form label { font-weight:600; color:#003366; margin-bottom:0.3rem; display:block; }
        .report-form select, .report-form textarea, .report-form input { width:100%; padding:0.7rem 1rem; border-radius:0.5rem; border:1px solid #ccc; margin-bottom:1.2rem; }
        .report-form button { background:#1976d2; color:#fff; border:none; border-radius:0.5rem; padding:0.7rem 2rem; font-weight:600; cursor:pointer; }
        .msg-success { color:#43a047; font-weight:600; margin-bottom:1rem; }
        .msg-error { color:#e53935; font-weight:600; margin-bottom:1rem; }
        /* lightweight notification style */
        .top-notification {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            color: #fff;
            text-align: center;
            padding: 12px;
            font-weight: bold;
            font-size: 16px;
            z-index: 9999;
            display: none;
            animation: slideDown 0.25s ease;
        }
        @keyframes slideDown { from {opacity:0; transform:translateY(-12px);} to {opacity:1; transform:translateY(0);} }
    </style>
</head>
<body>
<!-- top notification (used by script above) -->
<div id="notification" class="top-notification"></div>

<!--=============== HEADER ===============-->
<header class="header" id="header">
   <div class="header__container">
      <button class="header__toggle" id="header-toggle">
         <i class="ri-menu-line"></i>
      </button>
      <a href="#" class="header__logo">
         <img src="../../assets/img/logo.png" alt="Logo" style="height: 40px;">
      </a>
   </div>
</header>
<!--=============== SIDEBAR ===============-->
<nav class="sidebar" id="sidebar">
   <div class="sidebar__container">
      <div class="sidebar__user">
         <div class="sidebar__img">
            <img src="../../assets/img/perfil.png" alt="image">
         </div>
         <div class="sidebar__info">
            <h3><?php echo htmlspecialchars($displayName); ?></h3>
            <span><?php echo htmlspecialchars($role); ?></span>
         </div>
      </div>
      <div class="sidebar__content">
         <div>
            <h3 class="sidebar__title">MANAGE</h3>
            <div class="sidebar__list">
               <a href="DataAnalystDashboard.php" class="sidebar__link active-link">
                  <i class="ri-dashboard-horizontal-fill"></i>
                  <span>Dashboard</span>
               </a>
               <a href="DataAnalystAuditDatabank.php" class="sidebar__link">
                  <i class="ri-database-fill"></i>
                  <span>UCX Data Bank</span>
               </a>
               <a href="DataAnalystConductCoach.php" class="sidebar__link">
                  <i class="ri-ubuntu-fill"></i>
                  <span>UCX Connect</span>
               </a>
            </div>
         </div>
      </div>
      <div class="sidebar__actions">
         <button>
            <i class="ri-moon-clear-fill sidebar__link sidebar__theme" id="theme-button">
               <span>Theme</span>
            </i>
         </button>
         <a href="../../LoginFunction.php" class="sidebar__link">
            <i class="ri-logout-box-r-fill"></i>
            <span>Log Out</span>
         </a>
      </div>
   </div>
</nav>
<main class="main container" id="main">
    <section class="dashboard-article">
        <div class="report-form">
            <h2>File Supervisor Report</h2>
            <?php if ($successMsg): ?>
                <div class="msg-success"><?php echo $successMsg; ?></div>
            <?php endif; ?>
            <?php if ($errorMsg): ?>
                <div class="msg-error"><?php echo $errorMsg; ?></div>
            <?php endif; ?>
            <form method="POST">
                <label for="audit_id">Select Audit</label>
                <select name="audit_id" id="audit_id" required>
                    <option value="">Select Audit</option>
                    <?php foreach ($auditRows as $a): ?>
                        <option value="<?php echo $a['id']; ?>">
                            <?php echo htmlspecialchars($a['agent_name']) . " | " . htmlspecialchars($a['reviewer_name']) . " | " . htmlspecialchars($a['date']) . " | " . htmlspecialchars($a['week']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="agent_name">Agent Name</label>
                <input type="text" name="agent_name" id="agent_name" required>
                <label for="reviewer_name">Reviewer Name</label>
                <input type="text" name="reviewer_name" id="reviewer_name" required>
                <label for="comment">Supervisor Comment</label>
                <textarea name="comment" id="comment" rows="3" required></textarea>
                <button type="submit" name="file_report">File Report</button>
            </form>
        </div>
    </section>
</main>
<script src="../../assets/js/main.js"></script>
<!-- JS to auto-fill agent/reviewer based on audit select -->
<script>
document.getElementById('audit_id').addEventListener('change', function() {
    const val = this.value;
    if (!val) return;
    // Option text format: "AgentName | ReviewerName | date | week"
    const opt = this.options[this.selectedIndex].text;
    const parts = opt.split(' | ').map(s => s.trim());
    const agent = parts[0] || '';
    const reviewer = parts[1] || '';
    const agentInput = document.getElementById('agent_name');
    const reviewerInput = document.getElementById('reviewer_name');
    if (agentInput) agentInput.value = agent;
    if (reviewerInput) reviewerInput.value = reviewer;
});
</script>
</body>
</html>
