<?php
include('../../database/dbconnection.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$username = $_SESSION['username'] ?? 'Supervisor';
$role = ucfirst($_SESSION['user_role'] ?? 'Supervisor');
$displayName = ucfirst($username) . '.';

// Handle approve/disapprove actions
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->query("UPDATE supervisor_comments SET status='approved' WHERE id=$id");
    header("Location: SupervisorDashboard.php.php");
    exit();
}
if (isset($_GET['disapprove'])) {
    $id = intval($_GET['disapprove']);
    $conn->query("UPDATE supervisor_comments SET status='disapproved' WHERE id=$id");
    header("Location: SupervisorDashboard.php.php");
    exit();
}

// Handle supervisor comment submission
if (isset($_POST['add_comment']) && isset($_POST['audit_id'])) {
    $audit_id = intval($_POST['audit_id']);
    $supervisor_comment = trim($_POST['supervisor_comment']);
    if ($supervisor_comment !== '') {
        $stmt = $conn->prepare("INSERT INTO supervisor_comments (audit_id, agent_name, reviewer_name, comment) VALUES (?, ?, ?, ?)");
        // Fetch agent and reviewer from audit
        $auditRes = $conn->query("SELECT agent_name, reviewer_name FROM data_reports WHERE id=$audit_id LIMIT 1");
        $agent = $reviewer = '';
        if ($auditRes && $auditRow = $auditRes->fetch_assoc()) {
            $agent = $auditRow['agent_name'];
            $reviewer = $auditRow['reviewer_name'];
        }
        $stmt->bind_param("isss", $audit_id, $agent, $reviewer, $supervisor_comment);
        $stmt->execute();
        $stmt->close();
        header("Location: SupervisorDashboard.php.php");
        exit();
    }
}

// Fetch pending supervisor comments
$comments = [];
$res = $conn->query("SELECT * FROM supervisor_comments WHERE status='pending' ORDER BY created_at DESC");
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $comments[] = $row;
    }
}

// Fetch audits for comment form (latest 10)
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
    <title>Supervisor Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css">
    <style>
        body { background: #fff; font-family: 'Nunito Sans', sans-serif; }
        .dashboard-article { padding: 2.5rem; background: #fff; border-radius: 1rem; box-shadow: 0 6px 16px rgba(0,0,0,0.12); margin-top: 2.5rem; }
        .comments-table { width: 100%; border-collapse: collapse; margin-top: 2rem; }
        .comments-table th, .comments-table td { padding: 1rem; border-bottom: 1px solid #eee; text-align: left; }
        .comments-table th { background: #f5f8fa; color: #003366; }
        .comments-table td .btn { padding: 0.5rem 1.2rem; border-radius: 0.5rem; border: none; cursor: pointer; font-weight: 600; margin-right: 0.5rem; }
        .btn-approve { background: #43a047; color: #fff; }
        .btn-disapprove { background: #e53935; color: #fff; }
        .btn-coach { background: #1976d2; color: #fff; }
    </style>
</head>
<body>
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
            <h3 class="sidebar__title">TOOLS</h3>
            <div class="sidebar__list">
               <a href="SupervisorDashboard.php.php" class="sidebar__link active-link">
                  <i class="ri-settings-3-fill"></i>
                  <span>Admin Tools</span>
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
        <h2>Supervisor Approval Panel</h2>
        <p>Review and approve/disapprove comments submitted by data analysts. Disapproved comments require coaching.</p>
        <!-- Supervisor Add Comment Form -->
        <div style="margin-bottom:2.5rem; background:#f5f8fa; border-radius:1rem; padding:1.5rem;">
            <h3 style="margin-top:0;">Add Supervisor Comment to Audit</h3>
            <form method="POST" style="display:flex; gap:1.2rem; flex-wrap:wrap; align-items:center;">
                <select name="audit_id" required style="padding:0.7rem 1rem; border-radius:0.5rem; border:1px solid #ccc;">
                    <option value="">Select Audit</option>
                    <?php foreach ($auditRows as $a): ?>
                        <option value="<?php echo $a['id']; ?>">
                            <?php echo htmlspecialchars($a['agent_name']) . " | " . htmlspecialchars($a['reviewer_name']) . " | " . htmlspecialchars($a['date']) . " | " . htmlspecialchars($a['week']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <textarea name="supervisor_comment" rows="2" placeholder="Supervisor comment..." required style="flex:1; border-radius:0.5rem; border:1px solid #ccc; padding:0.7rem 1rem;"></textarea>
                <button type="submit" name="add_comment" style="background:#1976d2; color:#fff; border:none; border-radius:0.5rem; padding:0.7rem 2rem; font-weight:600;">Add Comment</button>
            </form>
        </div>
        <table class="comments-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Agent</th>
                    <th>Reviewer</th>
                    <th>Comment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($comments)): ?>
                    <?php foreach ($comments as $c): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($c['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($c['agent_name']); ?></td>
                            <td><?php echo htmlspecialchars($c['reviewer_name']); ?></td>
                            <td><?php echo htmlspecialchars($c['comment']); ?></td>
                            <td>
                                <form method="get" style="display:inline;">
                                    <button type="submit" name="approve" value="<?php echo $c['id']; ?>" class="btn btn-approve">Approve</button>
                                    <button type="submit" name="disapprove" value="<?php echo $c['id']; ?>" class="btn btn-disapprove">Disapprove</button>
                                </form>
                                <?php if ($c['status'] === 'disapproved'): ?>
                                    <a href="ConductCoach.php?agent=<?php echo urlencode($c['agent_name']); ?>" class="btn btn-coach">Conduct Coaching</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">No pending supervisor comments.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>
<script src="../../assets/js/main.js"></script>
</body>
</html>
