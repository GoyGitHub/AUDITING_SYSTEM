<?php 
include('../../database/dbconnection.php'); 
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
   <style>
      table {
         width: 100%;
         border-collapse: collapse;
         font-family: 'Nunito Sans', sans-serif;
         color: #0d1b3d;
      }
      th, td {
         padding: 12px 15px;
         border-bottom: 1px solid #eee;
         text-align: left;
         vertical-align: middle;
      }
      thead {
         background-color: #f9f9f9;
      }
      tr:hover {
         background: #f5faff;
      }
      .role-auditor {
         color: #007bff; /* blue */
         font-weight: bold;
      }
      .role-agent {
         color: #28a745; /* green */
         font-weight: bold;

         .role-supervisor {
    color: #ff9800; /* orange */
    font-weight: bold;
}

.role-data-analyst {
    color: #9c27b0; /* purple */
    font-weight: bold;
}
      }
   </style>

   <title>UCX Data Bank - HR Records</title>
</head>
<body>

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
                    <a href="DataAnalystAnalytics.php" class="sidebar__link">
                        <i class="ri-settings-3-fill"></i>
                        <span>UCX Analytics</span>
                    </a>
                    <a href="DataAnalystHrRecords.php" class="sidebar__link active-link">
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
   <h1 style="margin-bottom: 20px; font-family:'Nunito Sans', sans-serif; color:#0d1b3d;">HR Records</h1>

   <div class="table-container" style="overflow-x:auto; background:#fff; border-radius:12px; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 24px;">
      <table>
         <thead>
            <tr>
               <th>Name</th>
               <th>Role</th>
               <th>Department / Team</th>
               <th>Email</th>
               <th>Birthday</th>
            </tr>
         </thead>
<tbody>
<?php
// === Fetch Auditors ===
$auditorQuery = $conn->query("SELECT auditor_firstname, auditor_lasttname, birthday, email, department 
                              FROM auditors2 ORDER BY auditor_firstname ASC");
if ($auditorQuery && $auditorQuery->num_rows > 0) {
   while ($row = $auditorQuery->fetch_assoc()) {
      echo "<tr>
               <td>{$row['auditor_firstname']} {$row['auditor_lasttname']}</td>
               <td class='role-auditor'>Auditor</td>
               <td>{$row['department']}</td>
               <td>{$row['email']}</td>
               <td>{$row['birthday']}</td>
            </tr>";
   }
}

// === Fetch Agents ===
$agentQuery = $conn->query("SELECT agent_firsttname, agent_lastname, birthday, email, team 
                            FROM agents ORDER BY agent_firsttname ASC");
if ($agentQuery && $agentQuery->num_rows > 0) {
   while ($row = $agentQuery->fetch_assoc()) {
      echo "<tr>
               <td>{$row['agent_firsttname']} {$row['agent_lastname']}</td>
               <td class='role-agent'>Agent</td>
               <td>{$row['team']}</td>
               <td>{$row['email']}</td>
               <td>{$row['birthday']}</td>
            </tr>";
   }
}

// === Fetch Supervisors ===
$supervisorQuery = $conn->query("SELECT supervisor_firstname, supervisor_lastname, birthday, email, team 
                                 FROM supervisors ORDER BY supervisor_firstname ASC");
if ($supervisorQuery && $supervisorQuery->num_rows > 0) {
   while ($row = $supervisorQuery->fetch_assoc()) {
      echo "<tr>
               <td>{$row['supervisor_firstname']} {$row['supervisor_lastname']}</td>
               <td class='role-agent'>Supervisor</td>
               <td>{$row['team']}</td>
               <td>{$row['email']}</td>
               <td>{$row['birthday']}</td>
            </tr>";
   }
}

// === Fetch Data Analysts ===
$dataAnalystQuery = $conn->query("SELECT data_analyst_firstname, data_analyst_lastname, birthday, email, department 
                                  FROM data_analysts ORDER BY data_analyst_firstname ASC");
if ($dataAnalystQuery && $dataAnalystQuery->num_rows > 0) {
   while ($row = $dataAnalystQuery->fetch_assoc()) {
      echo "<tr>
               <td>{$row['data_analyst_firstname']} {$row['data_analyst_lastname']}</td>
               <td class='role-auditor'>Data Analyst</td>
               <td>{$row['department']}</td>
               <td>{$row['email']}</td>
               <td>{$row['birthday']}</td>
            </tr>";
   }
}
?>
</tbody>

      </table>
   </div>
</main>

<!--=============== MAIN JS ===============-->
<script src="../../assets/js/main.js"></script>
</body>
</html>
