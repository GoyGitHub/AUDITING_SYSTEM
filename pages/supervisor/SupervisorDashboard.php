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

// Fetch pending supervisor comments
$comments = [];
$res = $conn->query("SELECT * FROM supervisor_comments WHERE status='pending' ORDER BY created_at DESC");
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $comments[] = $row;
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
            <h3 class="sidebar__title">MANAGE</h3>
            <div class="sidebar__list">
               <a href="SupervisorDashboard.php" class="sidebar__link active-link">
                  <i class="ri-dashboard-horizontal-fill"></i>
                  <span>Dashboard</span>
               </a>
               <a href="SupervisorDatabank.php" class="sidebar__link">
                  <i class="ri-database-fill"></i>
                  <span>UCX Data Bank</span>
               </a>
               <a href="SupervisorConductCoach.php" class="sidebar__link">
                  <i class="ri-ubuntu-fill"></i>
                  <span>UCX Connect</span>
               </a>
            </div>
         </div>
                  <div>
            <h3 class="sidebar__title">TOOLS</h3>
            <div class="sidebar__list">
               <a href="#" class="sidebar__link">
                  <i class="ri-mail-unread-fill"></i>
                  <span>My Messages</span>
               </a>
               <a href="#" class="sidebar__link">
                  <i class="ri-notification-2-fill"></i>
                  <span>Notifications</span>
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
        <p>Review and approve/disapprove comments submitted by <b>data analysts</b>. Disapproved comments require coaching.</p>
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
