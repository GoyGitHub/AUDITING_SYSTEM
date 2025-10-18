<?php 
include('../../database/dbconnection.php'); 

// ✅ Load session user details safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pull data from session
$username = $_SESSION['username'] ?? 'User';
$role = ucfirst($_SESSION['user_role'] ?? 'User');
$displayName = ucfirst($username) . '.';

// --- Search handling: agent/reviewer/date/status ---
$search_agent = trim($_GET['agent'] ?? '');
$search_reviewer = trim($_GET['reviewer'] ?? '');
$search_from = trim($_GET['from'] ?? '');
$search_to = trim($_GET['to'] ?? '');
$search_status = trim($_GET['status'] ?? '');

// Build dynamic WHERE with prepared statement support
$where = [];
$params = [];
$types = '';

if ($search_agent !== '') {
    $where[] = "agent_name LIKE ?";
    $types .= 's';
    $params[] = '%' . $search_agent . '%';
}
if ($search_reviewer !== '') {
    $where[] = "reviewer_name LIKE ?";
    $types .= 's';
    $params[] = '%' . $search_reviewer . '%';
}
if ($search_from !== '') {
    $where[] = "date >= ?";
    $types .= 's';
    $params[] = $search_from;
}
if ($search_to !== '') {
    $where[] = "date <= ?";
    $types .= 's';
    $params[] = $search_to;
}
if ($search_status !== '') {
    $where[] = "status = ?";
    $types .= 's';
    $params[] = $search_status;
}

$query = "SELECT *, ((q1='Yes') + (q2='Yes') + (q3='Yes') + (q4='Yes') + (q5='Yes') + (q6='Yes') + (q7='Yes') + (q8='Yes') + (q9='Yes') + (q10='Yes')) * 10 AS score
          FROM data_reports";
if (count($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}
$query .= " ORDER BY id DESC LIMIT 500"; // safety limit

$stmt = $conn->prepare($query);
if ($stmt && count($params)) {
    // bind params dynamically
    $bind_names = [];
    $bind_names[] = &$types;
    for ($i = 0; $i < count($params); $i++) {
        $bind_names[] = &$params[$i];
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_names);
}
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // fallback
    $result = $conn->query("SELECT *, ((q1='Yes')+(q2='Yes')+(q3='Yes')+(q4='Yes')+(q5='Yes')+(q6='Yes')+(q7='Yes')+(q8='Yes')+(q9='Yes')+(q10='Yes'))*10 AS score FROM data_reports ORDER BY id DESC LIMIT 500");
}
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

.audit-card {
   background: #fff;
   border-radius: 1.2rem;
   box-shadow: 0 4px 20px rgba(0,0,0,0.07);
   margin-bottom: 2.5rem;
   padding: 2rem 2.5rem 1.5rem 2.5rem;
   font-family: 'Nunito Sans', sans-serif;
   position: relative;
   border-left: 8px solid #3a8de0;
   transition: box-shadow 0.2s, min-height 0.3s, padding-bottom 0.3s, height 0.3s, max-height 0.3s;
   min-height: 180px;
   overflow: hidden;
}
.audit-card.collapsed {
   box-shadow: 0 2px 8px rgba(0,0,0,0.04);
   min-height: 0;
   height: 140px; /* Increased from 90px */
   max-height: 180px; /* Increased from 120px */
   padding-bottom: 0.5rem;
}
.audit-toggle-btn {
   background: #3a8de0;
   color: #fff;
   border: none;
   border-radius: 1rem;
   padding: 0.3rem 1.1rem;
   font-size: 1rem;
   font-weight: 600;
   cursor: pointer;
   margin-bottom: 0.7rem;
   margin-top: 0.2rem;
   transition: background 0.2s;
}
.audit-toggle-btn:hover {
   background: #1a237e;
}
.audit-questions-table {
   transition: max-height 0.3s, opacity 0.3s;
   overflow: hidden;
}
.audit-questions-table.collapsed {
   max-height: 0;
   opacity: 0;
   pointer-events: none;
   padding: 0;
   margin: 0;
   border: none;
}
.audit-questions-table.expanded {
   max-height: 1000px;
   opacity: 1;
}
.audit-header {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
         gap: 0.7rem 2.5rem;
         margin-bottom: 1.2rem;
         border-bottom: 1px solid #e0e0e0;
         padding-bottom: 1rem;
         transition: border-bottom 0.3s, margin-bottom 0.3s, padding-bottom 0.3s;
         position: relative;
      }
.audit-header-info {
         min-width: 0;
         display: flex;
         align-items: center;
         gap: 0.3rem;
         white-space: nowrap;
         overflow: hidden;
         text-overflow: ellipsis;
      }
.audit-header-info label {
         font-weight: 900 !important;
         color: #1a237e;
         margin-right: 6px;
         font-size: 1.04rem;
         letter-spacing: 0.01em;
      }
.audit-card.collapsed .audit-header {
         border-bottom: none;
         margin-bottom: 0.2rem;
         padding-bottom: 0.2rem;
      }
.audit-actions {
         position: absolute;
         top: 0.2rem;
         right: 0.5rem;
         display: flex;
         align-items: center;
         gap: 8px;
         z-index: 2;
      }
.audit-plus-btn {
         background: #3a8de0;
         color: #fff;
         border: none;
         border-radius: 50%;
         width: 32px;
         height: 32px;
         font-size: 1.4rem;
         display: inline-flex;
         align-items: center;
         justify-content: center;
         cursor: pointer;
         margin-right: 10px;
         margin-left: 0;
         order: -1;
         position: absolute;
         left: -44px;
         top: 0.2rem;
         z-index: 3;
         box-shadow: 0 2px 8px rgba(58,141,224,0.08);
         transition: background 0.2s;
     }
     .audit-plus-btn:hover {
         background: #1a237e;
     }
      @media (max-width: 900px) {
         .audit-header {
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem 1.2rem;
         }
         .audit-plus-btn {
            left: -36px;
         }
      }
      @media (max-width: 600px) {
         .audit-header {
            grid-template-columns: 1fr;
            gap: 0.3rem 0;
         }
         .audit-plus-btn {
            left: -32px;
         }
      }
.audit-status {
   font-weight: 700;
   font-size: 1.1rem;
   padding: 0.25rem 1.2rem;
   border-radius: 1rem;
   display: inline-block;
   margin-left: 0.5rem;
}
.audit-status.completed { background: #e8f5e9; color: #388e3c; }
.audit-status.incomplete { background: #fff8e1; color: #ff9800; }
.audit-status.failed { background: #ffebee; color: #d32f2f; }
.audit-questions-table {
   width: 100%;
   border-collapse: collapse;
   margin-bottom: 1.2rem;
   margin-top: 0.5rem;
}
.audit-questions-table th, .audit-questions-table td {
   padding: 0.7rem 1rem;
   border-bottom: 1px solid #f0f0f0;
   text-align: left;
   font-size: 1rem;
}
.audit-questions-table th {
   background: #f5faff;
   color: #3a8de0;
   font-weight: 700;
}
.audit-comments {
   margin-top: 1rem;
   font-size: 1.05rem;
   color: #333;
   background: #f9f9f9;
   border-radius: 0.5rem;
   padding: 0.7rem 1.2rem;
}
.audit-meta {
   font-size: 0.97rem;
   color: #888;
   margin-top: 0.5rem;
}
.audit-actions {
   position: absolute;
   top: 1.5rem;
   right: 2.5rem;
}
.audit-actions a {
   color: #d32f2f;
   font-weight: 600;
   text-decoration: none;
   margin-left: 1rem;
}
.audit-actions a:hover {
   text-decoration: underline;
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
                    <a href="DataAnalystAuditDatabank.php" class="sidebar__link active-link">
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
<!--=============== MAIN CONTENT ===============-->
<main class="main container" id="main">
   <h1 style="margin-bottom: 20px;">Audit Reports</h1>

   <!-- Search form -->
   <form method="get" style="max-width:1100px;margin-bottom:12px;display:flex;gap:8px;flex-wrap:wrap;">
       <input type="text" name="agent" placeholder="Agent name" value="<?php echo htmlspecialchars($search_agent); ?>" style="flex:1;min-width:200px;padding:8px;border-radius:6px;border:1px solid #ddd;">
       <input type="text" name="reviewer" placeholder="Reviewer name" value="<?php echo htmlspecialchars($search_reviewer); ?>" style="flex:1;min-width:200px;padding:8px;border-radius:6px;border:1px solid #ddd;">
       <input type="date" name="from" value="<?php echo htmlspecialchars($search_from); ?>" style="padding:8px;border-radius:6px;border:1px solid #ddd;">
       <input type="date" name="to" value="<?php echo htmlspecialchars($search_to); ?>" style="padding:8px;border-radius:6px;border:1px solid #ddd;">
      
       <button type="submit" style="background:#1976d2;color:#fff;border:none;padding:8px 12px;border-radius:6px;">Search</button>
       <a href="DataAnalystAuditDatabank.php" style="display:inline-flex;align-items:center;padding:8px 12px;border-radius:6px;background:#6c757d;color:#fff;text-decoration:none;">Clear</a>
   </form>

   <!-- Reports table (existing rendering) -->
   <div>
      <!-- ...existing table rendering but use $result from above instead of fresh query... -->
      <?php
      // ...existing table markup ...
      // Replace data source loop to use $result:
      if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              // Determine status
              $completed = true;
              $noCount = 0;
              for ($i = 1; $i <= 10; $i++) {
                 $ans = $row["q$i"];
                 if ($ans === null || $ans === '') $completed = false;
                 if (strtolower($ans) === "no") $noCount++;
              }
              $status = $completed ? "Completed" : "Incomplete";
              $statusClass = $completed ? "completed" : "incomplete";
              if ($row['status'] === 'Failed' || $noCount >= 7) {
                 $status = "Failed";
                 $statusClass = "failed";
              }
              // --- NEW: compute score (each "Yes" = 10 points) ---
              $score = 0;
              for ($i = 1; $i <= 10; $i++) {
                 $ans = strtolower(trim($row["q{$i}"] ?? ''));
                 if ($ans === 'yes') $score += 10;
              }
              $scoreLabel = $score . " / 100";

              // choose a badge class for score display (reuse existing classes)
              if ($score >= 90) {
                 $scoreClass = "audit-status completed";
              } elseif ($score >= 75) {
                 $scoreClass = "audit-status incomplete";
              } elseif ($score >= 50) {
                 $scoreClass = "audit-status incomplete";
              } else {
                 $scoreClass = "audit-status failed";
              }
              $cardId = "auditq-" . $row['id'];
?>
      <div class="audit-card collapsed" id="card-<?php echo $row['id']; ?>">
         <div class="audit-header" style="position:relative;">
            <!-- Plus button at the left beside Reviewer -->
            <button class="audit-plus-btn" id="plus-btn-<?php echo $row['id']; ?>" type="button"
               onclick="toggleAuditQuestions('<?php echo $cardId; ?>', '<?php echo $row['id']; ?>')">
               <span id="plus-icon-<?php echo $row['id']; ?>">+</span>
            </button>
            <div class="audit-header-info">
               <label>Reviewer:</label> <?php echo htmlspecialchars($row['reviewer_name']); ?>
            </div>
            <div class="audit-header-info">
               <label>Agent:</label> <?php echo htmlspecialchars($row['agent_name']); ?>
            </div>
            <div class="audit-header-info">
               <label>Status:</label>
               <span class="audit-status <?php echo $statusClass; ?>"><?php echo $status; ?></span>
            </div>
            <div class="audit-header-info">
               <label>Date:</label> <?php echo htmlspecialchars($row['date']); ?>
            </div>
            <div class="audit-header-info">
               <label>Time:</label> <?php echo htmlspecialchars($row['time']); ?>
            </div>
            <div class="audit-header-info">
               <label>Week:</label> <?php echo htmlspecialchars($row['week']); ?>
            </div>
            <div class="audit-header-info">
               <label>Caller:</label> <?php echo htmlspecialchars($row['caller_name']); ?>
            </div>
            <div class="audit-header-info">
               <label>Duration:</label> <?php echo htmlspecialchars($row['duration']); ?>
            </div>
            <div class="audit-header-info">
               <label>MDN:</label> <?php echo htmlspecialchars($row['mdn']); ?>
            </div>
            <div class="audit-header-info">
               <label>Account #:</label> <?php echo htmlspecialchars($row['account_number']); ?>
            </div>
            <div class="audit-header-info">
               <label>Score:</label>
               <span class="<?php echo $scoreClass; ?>"><?php echo $scoreLabel; ?></span>
            </div>
            <!-- Delete button stays at the top right -->
            <div class="audit-actions">
               <a href='../../conditions/delete.php?id=<?php echo $row['id']; ?>'
                  onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
            </div>
         </div>
         <!-- Hide Queue when collapsed, show only when expanded -->
         <div class="audit-meta" style="display:none;"></div>
         <div class="audit-meta">
            Queue: <?php echo htmlspecialchars($row['queue']); ?>
         </div>
         <table class="audit-questions-table collapsed" id="<?php echo $cardId; ?>">
            <thead>
               <tr>
                  <th>Audit Criteria</th>
                  <th>Answer</th>
                  <th>Points</th>
               </tr>
            </thead>
            <tbody>
               <?php
               foreach ($questions as $i => $q) {
                  $num = $i + 1;
                  $ans = htmlspecialchars($row["q$num"]);
                  $point = (strtolower(trim($row["q$num"] ?? '')) === 'yes') ? 10 : 0;
                  echo "<tr>
                     <td>$q</td>
                     <td>$ans</td>
                     <td style='font-weight:700; text-align:center;'>$point</td>
                  </tr>";
               }
               // show total row
               echo "<tr style='background:#f5f7fb;'>
                       <td style='font-weight:800;'>Total</td>
                       <td></td>
                       <td style='font-weight:800; text-align:center;'>{$scoreLabel}</td>
                     </tr>";
               ?>
            </tbody>
         </table>
         <div class="audit-comments">
            <strong>Comments:</strong> <?php echo htmlspecialchars($row['comment']); ?>
         </div>
      </div>
<?php
          }
      } else {
          echo "<div style='color:#888;'>No audit reports found.</div>";
      }
      ?>
   </div>
</main>

<!--=============== MAIN JS ===============-->
<script src="../../assets/js/main.js"></script>
<script>
function toggleAuditQuestions(tableId, cardId) {
   var table = document.getElementById(tableId);
   var card = document.getElementById('card-' + cardId);
   var plusIcon = document.getElementById('plus-icon-' + cardId);
   // Find the audit-meta divs inside the card
   var metaDivs = card.querySelectorAll('.audit-meta');
   if (table.classList.contains('collapsed')) {
      table.classList.remove('collapsed');
      table.classList.add('expanded');
      card.classList.remove('collapsed');
      plusIcon.textContent = "–";
      // Show the queue meta
      if (metaDivs.length > 1) metaDivs[1].style.display = '';
   } else {
      table.classList.add('collapsed');
      table.classList.remove('expanded');
      card.classList.add('collapsed');
      plusIcon.textContent = "+";
      // Hide the queue meta
      if (metaDivs.length > 1) metaDivs[1].style.display = 'none';
   }
}
// Hide all queue meta on page load (for collapsed cards)
document.addEventListener('DOMContentLoaded', function() {
   document.querySelectorAll('.audit-card.collapsed .audit-meta:nth-of-type(2)').forEach(function(meta) {
      meta.style.display = 'none';
   });
});
</script>
</body>
</html>
