<?php
include('../../database/dbconnection.php');

// ✅ Load session user details safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pull data from session
$username = $_SESSION['username'] ?? '';
$role = ucfirst($_SESSION['user_role'] ?? 'User');
$displayName = ucfirst($username) . '.';

// --- Resolve current agent by session (try full name or email) ---
$agentId = null;
$agentFullName = $displayName;
$agentEmail = '';
$agentTeam = '';

if (!empty($username)) {
    $stmtA = $conn->prepare("
        SELECT id, agent_firstname, agent_lastname, email, team
        FROM agents2
        WHERE CONCAT(agent_firstname, ' ', agent_lastname) = ? OR email = ?
        LIMIT 1
    ");
    if ($stmtA) {
        $stmtA->bind_param("ss", $username, $username);
        $stmtA->execute();
        $resA = $stmtA->get_result();
        if ($resA && $rowA = $resA->fetch_assoc()) {
            $agentId = (int)$rowA['id'];
            $agentFullName = $rowA['agent_firstname'] . ' ' . $rowA['agent_lastname'];
            $agentEmail = $rowA['email'] ?? '';
            $agentTeam = $rowA['team'] ?? '';
        }
        $stmtA->close();
    }
}

// Fallback: if no agent match, try displayName (without trailing dot)
if (!$agentId) {
    $tryName = rtrim($displayName, '.');
    $stmtB = $conn->prepare("
        SELECT id, agent_firstname, agent_lastname, email, team
        FROM agents2
        WHERE CONCAT(agent_firstname, ' ', agent_lastname) = ? LIMIT 1
    ");
    if ($stmtB) {
        $stmtB->bind_param("s", $tryName);
        $stmtB->execute();
        $resB = $stmtB->get_result();
        if ($resB && $rowB = $resB->fetch_assoc()) {
            $agentId = (int)$rowB['id'];
            $agentFullName = $rowB['agent_firstname'] . ' ' . $rowB['agent_lastname'];
            $agentEmail = $rowB['email'] ?? '';
            $agentTeam = $rowB['team'] ?? '';
        }
        $stmtB->close();
    }
}

// --- Fetch agent audits (latest 10) ---
$agentAudits = [];
if ($agentFullName) {
    $stmt = $conn->prepare("SELECT * FROM data_reports WHERE agent_name = ? ORDER BY date DESC, time DESC LIMIT 10");
    if ($stmt) {
        $stmt->bind_param("s", $agentFullName);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            while ($r = $res->fetch_assoc()) $agentAudits[] = $r;
        }
        $stmt->close();
    }
}

// --- Compute stats: total audits, avg score, last score, score series ---
$totalAudits = count($agentAudits);
$scoreSum = 0;
$scoreCount = 0;
$scoreSeries = []; // ['label'=>date, 'score'=>int]
$lastScoreLabel = 'N/A';
$lastScoreVal = null;

foreach ($agentAudits as $ar) {
    $score = 0;
    for ($i = 1; $i <= 10; $i++) {
        $ans = strtolower(trim($ar["q{$i}"] ?? ''));
        if ($ans === 'yes') $score += 10;
    }
    $scoreSum += $score;
    $scoreCount++;
    $label = ($ar['date'] ? $ar['date'] : substr($ar['created_at'],0,10));
    $scoreSeries[] = ['label' => $label, 'score' => $score];
    if ($lastScoreVal === null) {
        $lastScoreVal = $score;
        $lastScoreLabel = $label;
    }
}
$avgScore = $scoreCount ? round($scoreSum / $scoreCount, 1) : 0;

// --- Upcoming coaching sessions for this agent ---
$upcomingCoaching = [];
$stmtC = $conn->prepare("SELECT id, coach, date, time, type, notes FROM coaching_sessions WHERE agent = ? AND date >= CURDATE() ORDER BY date ASC, time ASC LIMIT 10");
if ($stmtC) {
    $stmtC->bind_param("s", $agentFullName);
    $stmtC->execute();
    $rc = $stmtC->get_result();
    if ($rc) {
        while ($row = $rc->fetch_assoc()) $upcomingCoaching[] = $row;
    }
    $stmtC->close();
}

// --- Supervisor comments for this agent (latest 10) ---
$supervisorComments = [];
$stmtS = $conn->prepare("SELECT id, comment, status, created_at, reviewer_name FROM supervisor_comments WHERE agent_name = ? ORDER BY created_at DESC LIMIT 10");
if ($stmtS) {
    $stmtS->bind_param("s", $agentFullName);
    $stmtS->execute();
    $rs = $stmtS->get_result();
    if ($rs) {
        while ($row = $rs->fetch_assoc()) $supervisorComments[] = $row;
    }
    $stmtS->close();
}

// --- If no agent found, mark a flag for UI ---
$agentFound = !empty($agentId) || (!empty($agentFullName) && !empty($agentEmail) || $totalAudits > 0);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Agent Dashboard</title>
<link rel="stylesheet" href="../../assets/css/styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css">
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
/* Minimal professional styling aligned with admin pages */
.container { max-width:1200px; margin:90px auto 40px; padding: 1rem; margin-left:320px; }
.profile-card { background:#fff; border-radius:12px; padding:1.2rem; display:flex; gap:1rem; align-items:center; box-shadow:0 6px 18px rgba(0,0,0,0.06); }
.profile-card .meta { flex:1; }
.profile-card .meta h2 { margin:0; color:#003366; }
.stats-grid { display:grid; grid-template-columns: repeat(auto-fit,minmax(180px,1fr)); gap:1rem; margin-top:1rem; }
.stat { background:#f5f8fa; padding:1rem; border-radius:10px; text-align:center; }
.section { margin-top:1.25rem; background:#fff; padding:1rem; border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,0.04); }
.table { width:100%; border-collapse:collapse; }
.table th, .table td { padding:0.6rem; border-bottom:1px solid #eee; text-align:left; }
.actions { display:flex; gap:0.5rem; justify-content:flex-end; align-items:center; }
.small-btn { padding:8px 12px; border-radius:8px; background:#1976d2; color:#fff; border:none; cursor:pointer; text-decoration:none; }
.small-btn.ghost { background:transparent; color:#1976d2; border:1px solid rgba(25,118,210,0.12); }
.empty { color:#888; padding:1rem; text-align:center; }
@media(max-width:900px){ .profile-card{flex-direction:column;align-items:flex-start} .actions{justify-content:flex-start} }
</style>
</head>
<body>
<!-- =============== HEADER =============== -->
<header class="header" id="header">
   <div class="header__container">
      <button class="header__toggle" id="header-toggle"><i class="ri-menu-line"></i></button>
      <a href="https://yourlink.com" class="header__logo"><img src="../../assets/img/logo.png" alt="Logo" style="height:40px;"></a>
   </div>
</header>

<!-- =============== SIDEBAR =============== -->
<nav class="sidebar" id="sidebar">
   <div class="sidebar__container">
      <div class="sidebar__user">
         <div class="sidebar__img"><img src="../../assets/img/perfil.png" alt="image"></div>
         <div class="sidebar__info"><h3><?php echo htmlspecialchars($agentFullName); ?></h3><span><?php echo htmlspecialchars($role); ?></span></div>
      </div>
      <div class="sidebar__content">
         <div><h3 class="sidebar__title">MY WORKSPACE</h3>
            <div class="sidebar__list">
               <a href="AgentDashboard.php" class="sidebar__link active-link"><i class="ri-dashboard-line"></i><span>Dashboard</span></a>
               <a href="../auditor/AuditorAuditForm.php" class="sidebar__link"><i class="ri-survey-fill"></i><span>View Audits</span></a>
               <a href="AgentProfile.php" class="sidebar__link"><i class="ri-user-3-fill"></i><span>Profile</span></a>
               <a href="../supervisor/SupervisorConductCoach.php" class="sidebar__link"><i class="ri-ubuntu-fill"></i><span>Coaching</span></a>
            </div>
         </div>
      </div>
      <div class="sidebar__actions">
         <button><i class="ri-moon-clear-fill sidebar__link sidebar__theme" id="theme-button"><span>Theme</span></i></button>
         <a href="../../LoginFunction.php" class="sidebar__link"><i class="ri-logout-box-r-fill"></i><span>Log Out</span></a>
      </div>
   </div>
</nav>

<!-- =============== MAIN =============== -->
<main class="container">
   <!-- Profile Card -->
   <div class="profile-card">
      <div style="width:72px;height:72px;background:#eaf1fb;border-radius:12px;display:flex;align-items:center;justify-content:center;">
         <i class="ri-user-3-fill" style="font-size:28px;color:#003366;"></i>
      </div>
      <div class="meta">
         <h2><?php echo htmlspecialchars($agentFullName); ?></h2>
         <div style="color:#666;margin-top:6px;">
            Team: <?php echo htmlspecialchars($agentTeam ?: 'N/A'); ?> &nbsp; • &nbsp;
            Email: <?php echo htmlspecialchars($agentEmail ?: 'N/A'); ?>
         </div>
      </div>
      <div class="actions">
         <a class="small-btn ghost" href="AgentProfile.php">Edit Profile</a>
         <a class="small-btn" href="AgentMyAudits.php">My Audits</a>
      </div>
   </div>

   <!-- Stats Grid -->
   <div class="stats-grid">
      <div class="stat">
         <div style="font-size:1.1rem;color:#666;">Total Audits</div>
         <div style="font-size:1.6rem;font-weight:700;color:#003366;"><?php echo $totalAudits; ?></div>
      </div>
      <div class="stat">
         <div style="font-size:1.1rem;color:#666;">Average Score</div>
         <div style="font-size:1.6rem;font-weight:700;color:#003366;"><?php echo $avgScore; ?> / 100</div>
      </div>
      <div class="stat">
         <div style="font-size:1.1rem;color:#666;">Last Audit Score</div>
         <div style="font-size:1.6rem;font-weight:700;color:#003366;"><?php echo ($lastScoreVal !== null) ? ($lastScoreVal . ' / 100') : 'N/A'; ?></div>
      </div>
      <div class="stat">
         <div style="font-size:1.1rem;color:#666;">Upcoming Coaching</div>
         <div style="font-size:1.6rem;font-weight:700;color:#003366;"><?php echo count($upcomingCoaching); ?></div>
      </div>
   </div>

   <!-- Score Trend Section -->
   <div class="section">
      <h3>Score Trend</h3>
      <div style="height:300px;"><canvas id="scoreTrend"></canvas></div>
   </div>

   <!-- Recent Audits Section -->
   <div class="section" style="margin-top:1rem;">
      <h3>Recent Audits</h3>
      <?php if ($totalAudits > 0): ?>
      <table class="table">
         <thead><tr><th>Date</th><th>Reviewer</th><th>Status</th><th>Score</th><th>Comment</th></tr></thead>
         <tbody>
            <?php foreach ($agentAudits as $a):
               $score = 0;
               for ($i=1;$i<=10;$i++) if (strtolower(trim($a["q{$i}"] ?? '')) === 'yes') $score+=10;
               $status = $a['status'] ?? 'N/A';
            ?>
            <tr>
               <td><?php echo htmlspecialchars($a['date'] ?: substr($a['created_at'],0,10)); ?></td>
               <td><?php echo htmlspecialchars($a['reviewer_name']); ?></td>
               <td><?php echo htmlspecialchars($status); ?></td>
               <td><?php echo $score; ?> / 100</td>
               <td><?php echo htmlspecialchars($a['comment']); ?></td>
            </tr>
            <?php endforeach; ?>
         </tbody>
      </table>
      <?php else: ?>
         <div class="empty">No audits found for <?php echo htmlspecialchars($agentFullName); ?>.</div>
      <?php endif; ?>
   </div>

   <!-- Coaching and Comments Section -->
   <div class="section" style="margin-top:1rem; display:flex; gap:1rem; flex-wrap:wrap;">
      <!-- Upcoming Coaching Sessions -->
      <div style="flex:1; min-width:320px;">
         <h3>Upcoming Coaching Sessions</h3>
         <?php if (count($upcomingCoaching)): ?>
         <table class="table">
            <thead><tr><th>Date</th><th>Time</th><th>Coach</th><th>Type</th><th></th></tr></thead>
            <tbody>
               <?php foreach ($upcomingCoaching as $c): ?>
               <tr>
                  <td><?php echo htmlspecialchars($c['date']); ?></td>
                  <td><?php echo htmlspecialchars($c['time']); ?></td>
                  <td><?php echo htmlspecialchars($c['coach']); ?></td>
                  <td><?php echo htmlspecialchars($c['type']); ?></td>
                  <td style="text-align:right;"><a class="small-btn ghost" href="SupervisorConductCoach.php?agent=<?php echo urlencode($agentFullName); ?>">Details</a></td>
               </tr>
               <?php endforeach; ?>
            </tbody>
         </table>
         <?php else: ?>
            <div class="empty">No upcoming coaching sessions.</div>
         <?php endif; ?>
      </div>

      <!-- Supervisor Comments -->
      <div style="flex:1; min-width:320px;">
         <h3>Supervisor Comments</h3>
         <?php if (count($supervisorComments)): ?>
            <ul style="list-style:none;padding:0;margin:0;">
               <?php foreach ($supervisorComments as $sc): ?>
                  <li style="border-bottom:1px solid #eee;padding:0.6rem 0;">
                     <div style="font-weight:700;"><?php echo htmlspecialchars($sc['reviewer_name'] ?: 'Supervisor'); ?> <span style="color:#888;font-weight:600;font-size:0.9rem;">• <?php echo htmlspecialchars(substr($sc['created_at'],0,10)); ?></span></div>
                     <div style="margin-top:6px;"><?php echo nl2br(htmlspecialchars($sc['comment'])); ?></div>
                     <div style="margin-top:6px;font-size:0.9rem;color:#666;">Status: <?php echo htmlspecialchars($sc['status'] ?: 'pending'); ?></div>
                  </li>
               <?php endforeach; ?>
            </ul>
         <?php else: ?>
            <div class="empty">No supervisor comments.</div>
         <?php endif; ?>
      </div>
   </div>
</main>

<!-- Score Trend Chart Script -->
<script>
// Score trend chart
const labels = <?php echo json_encode(array_column($scoreSeries,'label')); ?>;
const dataPoints = <?php echo json_encode(array_column($scoreSeries,'score')); ?>;
const ctx = document.getElementById('scoreTrend').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Score',
            data: dataPoints,
            borderColor: '#1976d2',
            backgroundColor: 'rgba(25,118,210,0.08)',
            fill: true,
            tension: 0.3,
            pointRadius: 4
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero:true, max:100 } },
        maintainAspectRatio: false
    }
});
</script>

</body>
</html>
