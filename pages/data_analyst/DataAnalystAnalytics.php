<?php 
include('../../database/dbconnection.php');

// ✅ Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Pull data from session
$username = $_SESSION['username'] ?? 'User';
$rawRole = $_SESSION['user_role'] ?? 'User';

// ✅ Map and format roles (safe and consistent)
$roleMap = [
    'data_analyst' => 'Data Analyst',
    // add more roles as needed
];

$role = $roleMap[strtolower($rawRole)] ?? ucwords(str_replace('_', ' ', strtolower($rawRole)));

// ✅ Display name
$displayName = ucfirst($username) . '.';

// ✅ Fetch agents for dropdown
$agents_result = $conn->query("SELECT id, agent_firstname, agent_lastname FROM agents");

// ✅ Handle cancel action
if (isset($_GET['cancel_id'])) {
    $cancel_id = intval($_GET['cancel_id']);
    $conn->query("DELETE FROM coaching_sessions WHERE id = $cancel_id");
}
// --- Analytics Data Preparation ---

// Weekly audit trends
$weeklyLabels = [];
$weeklyCounts = [];
$weeklySql = "SELECT week, COUNT(*) as cnt FROM data_reports GROUP BY week ORDER BY week ASC";
$weeklyResult = $conn->query($weeklySql);
if ($weeklyResult && $weeklyResult->num_rows > 0) {
    while($row = $weeklyResult->fetch_assoc()) {
        $weeklyLabels[] = $row['week'];
        $weeklyCounts[] = $row['cnt'];
    }
}

// Coaching session types distribution
$typeLabels = [];
$typeCounts = [];
$typeSql = "SELECT type, COUNT(*) as cnt FROM coaching_sessions GROUP BY type";
$typeResult = $conn->query($typeSql);
if ($typeResult && $typeResult->num_rows > 0) {
    while($row = $typeResult->fetch_assoc()) {
        $typeLabels[] = $row['type'];
        $typeCounts[] = $row['cnt'];
    }
}

// Top 5 agents by audit count
$topAgentLabels = [];
$topAgentCounts = [];
$topAgentSql = "SELECT agent_name, COUNT(*) as cnt FROM data_reports GROUP BY agent_name ORDER BY cnt DESC LIMIT 5";
$topAgentResult = $conn->query($topAgentSql);
if ($topAgentResult && $topAgentResult->num_rows > 0) {
    while($row = $topAgentResult->fetch_assoc()) {
        $topAgentLabels[] = $row['agent_name'];
        $topAgentCounts[] = $row['cnt'];
    }
}

// Summary cards
$totalAudits = $conn->query("SELECT COUNT(*) as cnt FROM data_reports")->fetch_assoc()['cnt'] ?? 0;
$totalCoaching = $conn->query("SELECT COUNT(*) as cnt FROM coaching_sessions")->fetch_assoc()['cnt'] ?? 0;
$agentCount = $conn->query("SELECT COUNT(*) as cnt FROM agents")->fetch_assoc()['cnt'] ?? 1;
$avgAuditsPerAgent = $agentCount ? round($totalAudits / $agentCount, 2) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!--=============== GOOGLE FONT ===============-->
<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans&display=swap" rel="stylesheet">

<!--=============== REMIXICONS ===============-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css">

<!--=============== CSS ===============-->
<link rel="stylesheet" href="../../assets/css/styles.css">

<!--=============== CHART.JS ===============-->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<title>UCX Data Bank - Audit Report</title>

<style>
/* Chart container styling for proper alignment */
.chart-wrapper {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 40px;
    margin-bottom: 30px;
}
.chart-container {
    flex: 1 1 400px;
    max-width: 500px;
    min-width: 300px;
    height: 350px; /* Fixed height for uniform bar alignment */
}
.analytics-summary {
    display: flex;
    gap: 2rem;
    margin-bottom: 2.5rem;
    flex-wrap: wrap;
}
.analytics-card {
    background: #f5f8fa;
    border-radius: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 1.5rem 2rem;
    text-align: center;
    min-width: 180px;
    font-family: 'Nunito Sans', sans-serif;
}
.analytics-card .count {
    font-size: 2rem;
    font-weight: bold;
    color: #0055aa;
    margin-bottom: 0.5rem;
}
.analytics-card .label {
    color: #003366;
    font-size: 1rem;
}
</style>
</head>
<body>

<!--=============== HEADER ===============-->
<header class="header" id="header">
    <div class="header__container">
        <button class="header__toggle" id="header-toggle">
            <i class="ri-menu-line"></i>
        </button>
        <a href="https://yourlink.com" class="header__logo">
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
                    <a href="DataAnalystDashboard.php" class="sidebar__link">
                        <i class="ri-dashboard-horizontal-fill"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="DataAnalystAuditDatabank.php" class="sidebar__link">
                        <i class="ri-database-fill"></i>
                        <span>UCX Data Bank</span>
                    </a>
                    <a href="DataAnalystAnalytics.php" class="sidebar__link active-link">
                        <i class="ri-settings-3-fill"></i>
                        <span>UCX Analytics</span>
                    </a>
                                   <a href="DataAnalystConductCoach.php" class="sidebar__link">
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

<!--=============== MAIN CONTENT ===============-->
<main class="main container" id="main">
    <h1 style="margin-bottom: 20px; font-family:'Nunito Sans', sans-serif; color:#0d1b3d;">Audit Analytics</h1>

    <!-- Analytics Summary Cards -->
    <div class="analytics-summary">
        <div class="analytics-card">
            <div class="count"><?php echo $totalAudits; ?></div>
            <div class="label">Total Audits</div>
        </div>
        <div class="analytics-card">
            <div class="count"><?php echo $totalCoaching; ?></div>
            <div class="label">Total Coaching Sessions</div>
        </div>
        <div class="analytics-card">
            <div class="count"><?php echo $avgAuditsPerAgent; ?></div>
            <div class="label">Avg Audits per Agent</div>
        </div>
    </div>

    <!-- Chart Containers -->
    <div class="chart-wrapper">
        <div class="chart-container">
            <canvas id="weeklyLineChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="coachingTypePieChart"></canvas>
        </div>
    </div>
    <div class="chart-wrapper">
        <div class="chart-container">
            <canvas id="topAgentBarChart"></canvas>
        </div>
    </div>
</main>

<!--=============== MAIN JS ===============-->
<script src="../../assets/js/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Expand/Collapse logic
document.querySelectorAll('.toggle-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const detailsRow = document.getElementById('details-' + id);
        if (detailsRow.style.display === 'table-row') {
            detailsRow.style.display = 'none';
            this.textContent = '+';
        } else {
            detailsRow.style.display = 'table-row';
            this.textContent = '–';
        }
    });
});

// Weekly Audit Trends (Line Chart)
const weeklyLineCtx = document.getElementById('weeklyLineChart').getContext('2d');
new Chart(weeklyLineCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($weeklyLabels); ?>,
        datasets: [{
            label: 'Audits per Week',
            data: <?php echo json_encode($weeklyCounts); ?>,
            borderColor: '#2196f3',
            backgroundColor: 'rgba(33,150,243,0.1)',
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } },
        maintainAspectRatio: false
    }
});

// Coaching Session Types (Pie Chart)
const coachingTypeCtx = document.getElementById('coachingTypePieChart').getContext('2d');
new Chart(coachingTypeCtx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($typeLabels); ?>,
        datasets: [{
            label: 'Coaching Types',
            data: <?php echo json_encode($typeCounts); ?>,
            backgroundColor: ['#4caf50', '#f44336', '#2196f3', '#ff9800', '#9c27b0']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } },
        maintainAspectRatio: false
    }
});

// Top 5 Agents by Audit Count (Horizontal Bar Chart)
const topAgentBarCtx = document.getElementById('topAgentBarChart').getContext('2d');
new Chart(topAgentBarCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($topAgentLabels); ?>,
        datasets: [{
            label: 'Audit Count',
            data: <?php echo json_encode($topAgentCounts); ?>,
            backgroundColor: '#003366'
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true, precision:0 } }
    }
});
</script>
</body>
</html>
