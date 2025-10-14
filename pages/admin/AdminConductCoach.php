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

// Fetch agents for dropdown
$agents_result = $conn->query("SELECT id, agent_firstname, agent_lastname FROM agents");

// Handle cancel action
if (isset($_GET['cancel_id'])) {
   $cancel_id = intval($_GET['cancel_id']);
   $conn->query("DELETE FROM coaching_sessions WHERE id = $cancel_id");
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
      .conduct-card {
         background: #fff;
         border-radius: 1.2rem;
         box-shadow: 0 4px 20px rgba(0,0,0,0.07);
         margin-bottom: 2.5rem;
         padding: 2rem 2.5rem 1.5rem 2.5rem;
         font-family: 'Nunito Sans', sans-serif;
         position: relative;
         border-left: 8px solid #3a8de0;
         max-width: none;
         width: 100%;
         margin-left: 0;
         margin-right: 0;
      }
      .conduct-header {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
         gap: 0.7rem 2.5rem;
         margin-bottom: 1.2rem;
         border-bottom: 1px solid #e0e0e0;
         padding-bottom: 1rem;
         position: relative;
      }
      .conduct-header-info {
         min-width: 0;
         display: flex;
         align-items: center;
         gap: 0.3rem;
         white-space: nowrap;
         overflow: hidden;
         text-overflow: ellipsis;
      }
      .conduct-header-info label {
         font-weight: 900 !important;
         color: #1a237e;
         margin-right: 6px;
         font-size: 1.04rem;
         letter-spacing: 0.01em;
      }
      .conduct-actions {
         position: absolute;
         top: 0.2rem;
         right: 0.5rem;
         display: flex;
         align-items: center;
         gap: 8px;
         z-index: 2;
      }
      .conduct-comments {
         margin-top: 1rem;
         font-size: 1.05rem;
         color: #333;
         background: #f9f9f9;
         border-radius: 0.5rem;
         padding: 0.7rem 1.2rem;
      }
      .conduct-form label {
         font-weight: 700;
         color: #1a237e;
         margin-bottom: 0.3rem;
         display: block;
      }
      .conduct-form input, .conduct-form textarea, .conduct-form select {
         width: 100%;
         padding: 0.6rem;
         border: 1.5px solid #ccc;
         border-radius: 0.8rem;
         font-size: 1rem;
         margin-bottom: 1rem;
         font-family: 'Nunito Sans', sans-serif;
      }
      .conduct-form textarea {
         min-height: 80px;
         resize: vertical;
      }
      .conduct-form .submit-btn {
         background-color: #1a237e;
         color: white;
         padding: 0.75rem 2rem;
         border: none;
         border-radius: 0.5rem;
         font-size: 1rem;
         cursor: pointer;
         transition: background 0.3s ease;
         margin-top: 1rem;
      }
      .conduct-form .submit-btn:hover {
         background-color: #0d1b5e;
      }
      .success, .error {
         margin-top: 1rem;
         font-weight: 600;
      }
      .success { color: green; }
      .error { color: red; }
      .schedule-table {
         width: 100%;
         border-collapse: collapse;
         margin-top: 2.5rem;
         background: #fff;
         border-radius: 1rem;
         box-shadow: 0 2px 8px rgba(0,0,0,0.06);
         overflow: hidden;
      }
      .schedule-table th, .schedule-table td {
         padding: 1rem;
         text-align: center;
         border-bottom: 1px solid #e0e0e0;
      }
      .schedule-table th {
         background: #1a237e;
         color: #fff;
         font-weight: 600;
      }
      .schedule-table td {
         font-size: 1rem;
      }
      .schedule-table tr:last-child td {
         border-bottom: none;
      }
      .top-notification {
         position: fixed;
         top: 0;
         left: 0;
         width: 100%;
         background: #4CAF50;
         color: #fff;
         text-align: center;
         padding: 12px;
         font-weight: bold;
         font-size: 16px;
         z-index: 9999;
         display: none;
         animation: slideDown 0.4s ease;
      }
      @keyframes slideDown {
         from { opacity: 0; transform: translateY(-20px); }
         to { opacity: 1; transform: translateY(0); }
      }
   </style>
   <title>Conduct Coaching - UCX</title>
</head>
<body>
<div id="notification" class="top-notification"></div>
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
               <a href="AdminDashboard.php" class="sidebar__link">
                  <i class="ri-dashboard-horizontal-fill"></i>
                  <span>Dashboard</span>
               </a>
               <a href="AdminAuditDatabank.php" class="sidebar__link">
                  <i class="ri-database-fill"></i>
                  <span>UCX Data Bank</span>
               </a>
               <a href="AdminConductCoach.php" class="sidebar__link active-link">
                  <i class="ri-ubuntu-fill"></i>
                  <span>UCX Connect</span>
               </a>
               <a href="AdminAuditForm.php" class="sidebar__link">
                  <i class="ri-survey-fill"></i>
                  <span>Unify Audit System (UAS)</span>
               </a>
               <a href="AdminHrRecords.php" class="sidebar__link">
                  <i class="ri-folder-history-fill"></i>
                  <span>HR Records</span>
               </a>
            </div>
         </div>
         <div>
            <h3 class="sidebar__title">TOOLS</h3>
            <div class="sidebar__list">
               <a href="AdminTools.php" class="sidebar__link">
                  <i class="ri-settings-3-fill"></i>
                  <span>Admin Tools</span>
               </a>
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

<main class="main container" id="main" style="max-width: 100vw; width: 100vw;">
   <h1 style="margin-bottom: 20px; font-family:'Nunito Sans', sans-serif; color:#0d1b3d;">Conduct Coaching Schedule</h1>
   <div class="conduct-card">
      <form method="POST" class="conduct-form">
         <div class="conduct-header">
            <div class="conduct-header-info">
               <label>Coach:</label>
               <input type="text" name="coach" required>
            </div>
            <div class="conduct-header-info">
               <label>Agent:</label>
               <select name="agent" required>
                  <option value="">Select Agent</option>
                  <?php
                  if ($agents_result && $agents_result->num_rows > 0) {
                     while ($agent = $agents_result->fetch_assoc()) {
                        $fullname = $agent['agent_firstname'] . ' ' . $agent['agent_lastname'];
                        echo '<option value="' . htmlspecialchars($fullname) . '">' . htmlspecialchars($fullname) . '</option>';
                     }
                  }
                  ?>
               </select>
            </div>
            <div class="conduct-header-info">
               <label>Date:</label>
               <input type="date" name="date" required>
            </div>
            <div class="conduct-header-info">
               <label>Time:</label>
               <input type="time" name="time" required>
            </div>
            <div class="conduct-header-info">
               <label>Type:</label>
               <select name="type" required>
                  <option value="">Select Type</option>
                  <option value="Performance">Performance</option>
                  <option value="Behavioral">Behavioral</option>
                  <option value="Attendance">Attendance</option>
                  <option value="Others">Others</option>
               </select>
            </div>
         </div>
         <!-- Coaching Notes below the main fields -->
         <div class="conduct-header" style="border:none; margin-bottom:0;">
            <div class="conduct-header-info" style="grid-column: 1 / -1;">
               <label for="notes" style="font-weight:900;color:#1a237e;font-size:1.04rem;letter-spacing:0.01em;display:block;margin-bottom:0.3rem;">Coaching Notes:</label>
               <textarea id="notes" name="notes" rows="4" required style="width:100%;padding:0.6rem;border:1.5px solid #ccc;border-radius:0.8rem;font-size:1rem;margin-bottom:1rem;font-family:'Nunito Sans',sans-serif;"></textarea>
            </div>
         </div>
         <button type="submit" name="submit" class="submit-btn">Schedule Coaching</button>
      </form>
      <?php
      if (isset($_POST['submit'])) {
         $coach = $_POST['coach'];
         $agent = $_POST['agent'];
         $date = $_POST['date'];
         $time = $_POST['time'];
         $type = $_POST['type'];
         $notes = $_POST['notes'];
         $stmt = $conn->prepare("INSERT INTO coaching_sessions (coach, agent, date, time, type, notes) VALUES (?, ?, ?, ?, ?, ?)");
         $stmt->bind_param("ssssss", $coach, $agent, $date, $time, $type, $notes);
         if ($stmt->execute()) {
            echo "<script>
               document.addEventListener('DOMContentLoaded', function() {
                  const n = document.getElementById('notification');
                  n.textContent = 'Coaching session scheduled successfully!';
                  n.style.background = '#4CAF50';
                  n.style.display = 'block';
                  setTimeout(() => { n.style.display = 'none'; }, 4000);
               });
            </script>";
         } else {
            $error = addslashes($stmt->error);
            echo "<script>
               document.addEventListener('DOMContentLoaded', function() {
                  const n = document.getElementById('notification');
                  n.textContent = 'Error: {$error}';
                  n.style.background = '#f44336';
                  n.style.display = 'block';
                  setTimeout(() => { n.style.display = 'none'; }, 5000);
               });
            </script>";
         }
         $stmt->close();
      }
      ?>
      <!-- Coaching Schedule Table -->
      <h2 style="margin-top:2.5rem; color:#1a237e; font-size:1.3rem;">Upcoming Coaching Sessions</h2>
      <table class="schedule-table">
         <thead>
            <tr>
               <th>Coach</th>
               <th>Agent</th>
               <th>Date</th>
               <th>Time</th>
               <th>Type</th>
               <th>Notes</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
            <?php
            $sched_result = $conn->query("SELECT id, coach, agent, date, time, type, notes FROM coaching_sessions ORDER BY date DESC, time DESC");
            if ($sched_result && $sched_result->num_rows > 0) {
               while ($sched = $sched_result->fetch_assoc()) {
                  echo "<tr>
                     <td>" . htmlspecialchars($sched['coach']) . "</td>
                     <td>" . htmlspecialchars($sched['agent']) . "</td>
                     <td>" . htmlspecialchars($sched['date']) . "</td>
                     <td>" . htmlspecialchars($sched['time']) . "</td>
                     <td>" . htmlspecialchars($sched['type']) . "</td>
                     <td>" . htmlspecialchars($sched['notes']) . "</td>
                     <td>
                        <a href='?cancel_id=" . $sched['id'] . "' style='color:#d32f2f;font-weight:600;text-decoration:none;' onclick=\"return confirm('Cancel this coaching session?');\">Cancel</a>
                     </td>
                  </tr>";
               }
            } else {
               echo "<tr><td colspan='7' style='color:#888;'>No scheduled coaching sessions.</td></tr>";
            }
            ?>
         </tbody>
      </table>
   </div>
</main>
<script src="../../assets/js/main.js"></script>
</body>
</html>
