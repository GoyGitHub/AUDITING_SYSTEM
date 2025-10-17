<?php
// ✅ Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}                  
include('../../database/dbconnection.php');


// ✅ Pull data from session
$username = $_SESSION['username'] ?? 'User';
$rawRole = $_SESSION['user_role'] ?? 'User';

// ✅ Map and format roles
$roleMap = [
    'data_analyst'        => 'Data Analyst',

];


// ✅ Use mapped role if it exists; otherwise, format automatically
if (isset($roleMap[strtolower($rawRole)])) {
    $role = $roleMap[strtolower($rawRole)];
} else {
    // Auto-format: convert underscores to spaces and capitalize words
    $role = ucwords(str_replace('_', ' ', strtolower($rawRole)));
}

// ✅ Display name
$displayName = ucfirst($username) . '.';

// Dashboard summary counts
function getCount($conn, $table) {
    $result = $conn->query("SELECT COUNT(*) as cnt FROM $table");
    return $result ? $result->fetch_assoc()['cnt'] : 0;
}
$dataReportCount = getCount($conn, 'data_reports');
// changed to use agents2 (agents are stored in agents2)
$agentCount = getCount($conn, 'agents2');
$supervisorCount = getCount($conn, 'supervisors');
$coachingCount = getCount($conn, 'coaching_sessions');

// Fetch recent data reports
$recentReports = [];
$reportQuery = "SELECT * FROM data_reports ORDER BY created_at DESC LIMIT 5";
$reportResult = $conn->query($reportQuery);
if ($reportResult && $reportResult->num_rows > 0) {
    while ($row = $reportResult->fetch_assoc()) {
        $recentReports[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <!--=============== REMIXICONS ===============-->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css">

   <!--=============== CSS ===============-->
   <link rel="stylesheet" href="../../assets/css/styles.css">

   <title>Data Analyst Dashboard | Cool Pals</title>
   <style>
      .dashboard-article {
         padding: 2rem;
         background-color: #fff;
         border-radius: 1rem;
         box-shadow: 0 4px 12px rgba(0,0,0,0.10);
         margin-top: 2rem;
         font-family: 'Nunito Sans', sans-serif;
      }

      .dashboard-article h2 {
         font-size: 1.8rem;
         color: #003366;
         margin-bottom: 1rem;
      }

      .dashboard-article p {
         font-size: 1rem;
         line-height: 1.6;
         color: #333;
         margin-bottom: 1.5rem;
      }

      .dashboard-summary {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
         gap: 2rem;
         margin-bottom: 2.5rem;
      }

      .summary-card {
         background: #f5f8fa;
         border-radius: 1rem;
         box-shadow: 0 2px 8px rgba(0,0,0,0.08);
         padding: 2rem 1.5rem;
         text-align: center;
      }

      .summary-card h3 {
         font-size: 1.1rem;
         color: #003366;
         margin-bottom: 0.7rem;
      }

      .summary-card .count {
         font-size: 2.2rem;
         font-weight: bold;
         color: #0055aa;
         margin-bottom: 0.5rem;
      }

      .summary-card .icon {
         font-size: 2rem;
         color: #003366;
         margin-bottom: 0.5rem;
      }

      .dashboard-buttons {
         display: flex;
         flex-wrap: wrap;
         gap: 1.7rem;
         margin-top: 1.5rem;
      }

      .dashboard-buttons a {
         padding: 1.2rem 2.5rem;
         background-color: #003366;
         color: #fff;
         text-decoration: none;
         border-radius: 0.8rem;
         transition: 0.3s;
         display: inline-flex;
         align-items: center;
         gap: 0.5rem;
         font-size: 1rem;
      }

      .dashboard-buttons a:hover {
         background-color: #0055aa;
      }

      .dashboard-buttons i {
         font-size: 1.2rem;
      }

      .recent-reports-table {
         width: 100%;
         border-collapse: collapse;
         margin-top: 2.5rem;
         background: #fff;
         border-radius: 1rem;
         box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      }

      .recent-reports-table th, .recent-reports-table td {
         padding: 0.8rem 1rem;
         border-bottom: 1px solid #eee;
         text-align: left;
         font-size: 1rem;
      }

      .recent-reports-table th {
         background: #f5f8fa;
         color: #003366;
         font-weight: 600;
      }

      .recent-reports-table tr:last-child td {
         border-bottom: none;
      }

      .quick-actions {
         margin-top: 2.5rem;
         display: flex;
         gap: 1.5rem;
         flex-wrap: wrap;
      }

      .quick-actions a {
         background: #eaf1fb;
         color: #003366;
         padding: 1rem 2rem;
         border-radius: 0.7rem;
         text-decoration: none;
         font-weight: 500;
         box-shadow: 0 1px 4px rgba(0,0,0,0.07);
         transition: background 0.2s;
      }

      .quick-actions a:hover {
         background: #d0e2ff;
      }
   </style>
</head>
<!--=============== HEADER ===============-->
<header class="header" id="header">
   <div class="header__container">
      <button class="header__toggle" id="header-toggle">
         <i class="ri-menu-line"></i>
      </button>

      <!-- Right-side Logo Link -->
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
                    <a href="DataAnalystDashboard.php" class="sidebar__link active-link">
                        <i class="ri-dashboard-horizontal-fill"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="DataAnalystAuditDatabank.php" class="sidebar__link">
                        <i class="ri-database-fill"></i>
                        <span>UCX Data Bank</span>
                    </a>
                    <a href="DataAnalystAnalytics.php" class="sidebar__link">
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
            <h3 class="sidebar__title"></h3>
            <div class="sidebar__list">
               <a href="AdminTools.php" class="sidebar__link">
                  <i class=""></i>
                  <span></span>
               </a>
               <a href="#" class="sidebar__link">
                  <i class=""></i>
                  <span></span>
               </a>
               <a href="#" class="sidebar__link">
                  <i class=""></i>
                  <span></span>
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

   <!--=============== MAIN ===============-->
   <main class="main container" id="main">
      <section class="dashboard-article">
         <h2>Data Analyst Dashboard</h2>
         <p>
            Welcome! Here you can quickly review key metrics, access recent reports, and perform common analyst actions.
         </p>
         <!-- Dashboard Summary Section -->
         <div class="dashboard-summary">
            <div class="summary-card">
                <div class="icon"><i class="ri-bar-chart-box-fill"></i></div>
                <div class="count"><?php echo $dataReportCount; ?></div>
                <h3>Data Reports</h3>
            </div>
            <div class="summary-card">
                <div class="icon"><i class="ri-user-2-fill"></i></div>
                <div class="count"><?php echo $agentCount; ?></div>
                <h3>Agents</h3>
            </div>
            <div class="summary-card">
                <div class="icon"><i class="ri-user-star-fill"></i></div>
                <div class="count"><?php echo $supervisorCount; ?></div>
                <h3>Supervisors</h3>
            </div>
            <div class="summary-card">
                <div class="icon"><i class="ri-ubuntu-fill"></i></div>
                <div class="count"><?php echo $coachingCount; ?></div>
                <h3>Coaching Sessions</h3>
            </div>
         </div>

         <!-- Quick Actions -->
         <div class="quick-actions">
            <a href="DataAnalystAuditDatabank.php"><i class="ri-database-2-fill"></i> View Data Bank</a>
            <a href="DataAnalystAnalytics.php"><i class="ri-bar-chart-grouped-fill"></i> Analytics Tools</a>
            <a href="DataAnalystNewReport.php"><i class="ri-add-circle-fill"></i> Add New Report</a>
         </div>

         <!-- Recent Data Reports Table -->
         <h2 style="margin-top:2.5rem;">Recent Data Reports</h2>
         <table class="recent-reports-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Reviewer</th>
                    <th>Agent</th>
                    <th>Status</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($recentReports)): ?>
                    <?php foreach ($recentReports as $report): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($report['date']); ?></td>
                            <td><?php echo htmlspecialchars($report['reviewer_name']); ?></td>
                            <td><?php echo htmlspecialchars($report['agent_name']); ?></td>
                            <td><?php echo htmlspecialchars($report['status']); ?></td>
                            <td><?php echo htmlspecialchars($report['comment']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">No recent reports found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
         </table>
      </section>
   </main>
   
   <!--=============== MAIN JS ===============-->
   <script src="../../assets/js/main.js"></script>
</body>
</html>
