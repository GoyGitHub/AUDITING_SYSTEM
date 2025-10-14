<?php include('../../database/dbconnection.php'); ?>
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
                <h3>Jhayvot G.</h3>
                <span>Administrator</span>
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
                    <a href="DataAnalystHrRecords.php" class="sidebar__link">
                        <i class="ri-folder-history-fill"></i>
                        <span>HR Records</span>
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
    <h1 style="margin-bottom: 20px; font-family:'Nunito Sans', sans-serif; color:#0d1b3d;">Audit Report</h1>

    <!--=============== CHART CONTAINERS ===============-->
    <div class="chart-wrapper">
        <div class="chart-container">
            <canvas id="statusPieChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="agentBarChart"></canvas>
        </div>
    </div>
</main>

<!--=============== MAIN JS ===============-->
<script src="../../assets/js/main.js"></script>
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
</script>

<!--=============== CHART JS ===============-->
<script>
<?php
$statusCounts = ['Passed'=>0, 'Failed'=>0];
$agentCounts = [];
$sql = "SELECT * FROM data_reports";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if (isset($statusCounts[$row['status']])) $statusCounts[$row['status']]++;
        $agent = $row['agent_name'];
        if (!isset($agentCounts[$agent])) $agentCounts[$agent] = 0;
        $agentCounts[$agent]++;
    }
}
$statusData = json_encode(array_values($statusCounts));
$statusLabels = json_encode(array_keys($statusCounts));
$agentData = json_encode(array_values($agentCounts));
$agentLabels = json_encode(array_keys($agentCounts));
?>

// Pie Chart - Status
const ctxPie = document.getElementById('statusPieChart').getContext('2d');
new Chart(ctxPie, {
    type: 'pie',
    data: {
        labels: <?php echo $statusLabels; ?>,
        datasets: [{
            label: 'Status Distribution',
            data: <?php echo $statusData; ?>,
            backgroundColor: ['#4caf50', '#f44336']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } },
        maintainAspectRatio: false
    }
});

// Bar Chart - Agents
const ctxBar = document.getElementById('agentBarChart').getContext('2d');
new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: <?php echo $agentLabels; ?>,
        datasets: [{
            label: 'Number of Audits',
            data: <?php echo $agentData; ?>,
            backgroundColor: '#2196f3'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false, // Important for proper alignment
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, precision:0 } }
    }
});
</script>

</body>
</html>
