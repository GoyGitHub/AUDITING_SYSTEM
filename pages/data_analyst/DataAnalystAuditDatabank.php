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
      .failed-row td {
         color: red !important;
         font-weight: bold;
      }
      table {
         width: 100%;
         border-collapse: collapse;
         font-family: 'Nunito Sans', sans-serif;
         color:#0d1b3d;
      }
      th, td {
         padding: 12px;
         border-bottom: 1px solid #eee;
         text-align: left;
      }
      thead {
         background-color:#f9f9f9;
      }
      .toggle-btn {
         cursor: pointer;
         font-size: 18px;
         font-weight: bold;
         text-align: center;
      }
      .details-row {
         display: none;
         background: #fdfdfd;
      }
      .details-row td {
         border-top: none;
         font-size: 14px;
         padding: 10px 20px;
      }
      .warning-row td {
   background-color: #ffe5e5 !important;
   color: #b30000 !important;
   font-weight: bold;
}

   </style>

   <title>UCX Data Bank - Audit Report</title>
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
                    <a href="DataAnalystAuditDatabank.php" class="sidebar__link active-link">
                        <i class="ri-database-fill"></i>
                        <span>UCX Data Bank</span>
                    </a>
                    <a href="DataAnalystAnalytics.php" class="sidebar__link">
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

   <div class="table-container" style="overflow-x:auto; background:#fff; border-radius:12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 24px;">
      <table>
<thead>
   <tr>
      <th></th> <!-- plus/minus -->
      <th>#</th> <!-- numbering -->
      <th>Reviewer Name</th>
      <th>Agent Name</th>
      <th>Status</th>
      <th>Date</th>
      <th>Week</th>
      <th>Time</th>
      <th>Caller Name</th>
      <th>Duration</th>
      <th>Queue</th>
      <th>MDN</th>
      <th>Account Number</th>
      <th>Actions</th> <!-- NEW column -->
   </tr>
</thead>
<tbody>
<?php
$sql = "SELECT * FROM data_reports ORDER BY id ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
   $counter = 1; 
   while($row = $result->fetch_assoc()) {
      
      // ✅ Count how many "No" answers from q1–q10
      $noCount = 0;
      for ($i = 1; $i <= 10; $i++) {
         if (isset($row["q$i"]) && strtolower($row["q$i"]) === "no") {
            $noCount++;
         }
      }

      // ✅ Apply warning style if 7+ "No"
      $rowClass = "";
      if ($row['status'] === 'Failed') {
         $rowClass = 'failed-row';
      }
      if ($noCount >= 7) {
         $rowClass .= " warning-row"; 
      }

      echo "<tr class='$rowClass'>";
      echo "<td class='toggle-btn' data-id='" . $row['id'] . "'>+</td>";
      echo "<td>" . $counter . "</td>"; 
      echo "<td>" . htmlspecialchars($row['reviewer_name']) . "</td>";
      echo "<td>" . htmlspecialchars($row['agent_name']) . "</td>";
      echo "<td>" . htmlspecialchars($row['status']) . "</td>";
      echo "<td>" . htmlspecialchars($row['date']) . "</td>";
      echo "<td>" . htmlspecialchars($row['week']) . "</td>";
      echo "<td>" . htmlspecialchars($row['time']) . "</td>";
      echo "<td>" . htmlspecialchars($row['caller_name']) . "</td>";
      echo "<td>" . htmlspecialchars($row['duration']) . "</td>";
      echo "<td>" . $counter . "</td>";
      echo "<td>" . htmlspecialchars($row['mdn']) . "</td>";
      echo "<td>" . htmlspecialchars($row['account_number']) . "</td>";

      echo "<td>
            <a href='../../conditions/delete.php?id=" . $row['id'] . "' 
               onclick=\"return confirm('Are you sure you want to delete this record?');\" 
               style='color:red;'>Delete</a>
            </td>";
      echo "</tr>";

      // hidden expandable row
      echo "<tr class='details-row' id='details-" . $row['id'] . "'>";
      echo "<td colspan='14'>";
      for ($i = 1; $i <= 10; $i++) {
         echo "<strong>Q$i:</strong> " . htmlspecialchars($row["q$i"]) . "<br>";
      }
      echo "<strong>Comments:</strong> " . htmlspecialchars($row['comment']);
      echo "</td>";
      echo "</tr>";

      $counter++;
   }
}
?>

</tbody>

      </table>
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
</body>
</html>
