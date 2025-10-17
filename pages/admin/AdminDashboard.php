<?php 
include('../../database/dbconnection.php'); 

// ✅ Start the session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Ensure user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_role'])) {
    header("Location: ../../LoginFunction.php");
    exit();
}

// ✅ Role-based access control (redirect if not admin)
$user_role = strtolower($_SESSION['user_role']);
if ($user_role !== 'admin') {
    switch ($user_role) {
        case 'auditor':
            header("Location: ../auditor/AuditorDashboard.php");
            break;
        case 'supervisor':
            header("Location: ../supervisor/SupervisorDashboard.php");
            break;
        case 'data_analyst':
            header("Location: ../data_analyst/DataAnalystDashboard.php");
            break;
        default:
            header("Location: ../../LoginFunction.php");
            break;
    }
    exit();
}

// ✅ Pull data from session for header display
$username = $_SESSION['username'] ?? 'User';
$role = ucfirst($_SESSION['user_role'] ?? 'User');
$displayName = ucfirst($username) . '.';

// ✅ Fetch agents for dropdown (if used anywhere on dashboard)
$agents_result = $conn->query("SELECT id, agent_firstname, agent_lastname FROM agents");

// ✅ Dashboard Data
$new_supervisors_count = 0;
$total_audits_count = 0;

if ($conn) {
    // Count new supervisors
    $supervisor_sql = "SELECT COUNT(*) as count FROM supervisors WHERE DATE(created_at) >= CURDATE() - INTERVAL 7 DAY";
    if ($supervisor_result = $conn->query($supervisor_sql)) {
        if ($row = $supervisor_result->fetch_assoc()) {
            $new_supervisors_count = (int)$row['count'];
        }
    }

    // Count total audits
    $total_sql = "SELECT COUNT(*) as count FROM data_reports";
    if ($total_result = $conn->query($total_sql)) {
        if ($row = $total_result->fetch_assoc()) {
            $total_audits_count = (int)$row['count'];
        }
    }
}

// ✅ Handle optional cancel action
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

   <!--=============== REMIXICONS ===============-->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css">

   <!--=============== CSS ===============-->
   <link rel="stylesheet" href="../../assets/css/styles.css">

   <title>Dashboard | Cool Pals</title>
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

      .dashboard-buttons {
         display: flex;
         flex-wrap: wrap;
         gap: 1.7rem;
         margin-top: 1rem;
      }

      .dashboard-buttons a {
         padding: 2.5rem 6.5rem;
         background-color: #003366;
         color: #fff;
         text-decoration: none;
         border-radius: 0.8rem;
         transition: 0.3s;
         display: inline-flex;
         align-items: center;
         gap: 0.5rem;
      }

      .dashboard-buttons a:hover {
         background-color: #0055aa;
      }

      .dashboard-buttons i {
         font-size: 1.2rem;
      }

      /* Slideshow Styles */
      .slideshow-container {
         position: relative;
         max-width: 100%;   /* smaller size */
         height: 550px;    /* fixed height */
         margin: 20px auto;
         border-radius: 1rem;
         overflow: hidden;
         box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      }

      .slide {
         display: none;
         height: 100%;
      }

      .slide img {
         width: 100%;
         height: 100%;
         object-fit: cover;
         border-radius: 1rem;
      }

      .fade {
         animation: fadeEffect 1.5s;
      }

      @keyframes fadeEffect {
         from {opacity: 0.4}
         to {opacity: 1}
      }

      /* Dots (Indicators) */
      .dots {
         text-align: center;
         margin-top: 10px;
      }

      .dot {
         height: 12px;
         width: 12px;
         margin: 0 4px;
         background-color: #bbb;
         border-radius: 50%;
         display: inline-block;
         transition: background-color 0.3s;
         cursor: pointer;
      }

      .active-dot {
         background-color: #003366;
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
                  <a href="AdminDashboard.php" class="sidebar__link active-link">
                     <i class="ri-dashboard-horizontal-fill"></i>
                     <span>Dashboard</span>
                  </a>
                  <a href="AdminAuditDatabank.php" class="sidebar__link">
                     <i class="ri-database-fill"></i>
                     <span>UCX Data Bank</span>
                  </a>
                  <a href="AdminConductCoach.php" class="sidebar__link">
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
      <section class="dashboard-article" style="background: none; box-shadow: none; padding: 0; margin-top: 2rem;">
         <!-- DASHBOARD GRID -->
         <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 2rem; margin-bottom: 2.5rem;">
            <!-- Quick Stats Cards -->
            <div style="background: #fff; border-radius: 1rem; padding: 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08); display: flex; flex-direction: column; align-items: flex-start;">
               <span style="font-size: 2.2rem; color: #3a8de0; margin-bottom: 0.5rem;"><i class="ri-group-fill"></i></span>
               <span style="font-size: 2.1rem; font-weight: 700;">128</span>
               <span style="color: #888; font-size: 1rem;">Active Users</span>
            </div>
            <div style="background: #fff; border-radius: 1rem; padding: 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08); display: flex; flex-direction: column; align-items: flex-start;">
               <span style="font-size: 2.2rem; color: #3a8de0; margin-bottom: 0.5rem;"><i class="ri-database-2-fill"></i></span>
               <span style="font-size: 2.1rem; font-weight: 700;"><?php echo $total_audits_count; ?></span>
               <span style="color: #888; font-size: 1rem;">Audits Completed</span>
            </div>
            <div style="background: #fff; border-radius: 1rem; padding: 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08); display: flex; flex-direction: column; align-items: flex-start;">
               <span style="font-size: 2.2rem; color: #3a8de0; margin-bottom: 0.5rem;"><i class="ri-bar-chart-2-fill"></i></span>
               <span style="font-size: 2.1rem; font-weight: 700;">7</span>
               <span style="color: #888; font-size: 1rem;">Pending Reports</span>
            </div>
            <div style="background: #fff; border-radius: 1rem; padding: 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08); display: flex; flex-direction: column; align-items: flex-start;">
               <span style="font-size: 2.2rem; color: #3a8de0; margin-bottom: 0.5rem;"><i class="ri-user-star-fill"></i></span>
               <span style="font-size: 2.1rem; font-weight: 700;"><?php echo $new_supervisors_count; ?></span>
               <span style="color: #888; font-size: 1rem;">New Supervisors</span>
            </div>
         </div>

         <!-- Recent Activity Table (Dynamic from data_reports and user accounts) -->
         <div style="background: #fff; border-radius: 1rem; padding: 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08); margin-bottom: 2.5rem;">
            <h3 style="margin-top: 0; margin-bottom: 1.2rem; color: #003366; font-size: 1.3rem;">Recent Activity</h3>
            <div style="overflow-x: auto;">
               <table style="width: 100%; border-collapse: collapse;">
                  <thead>
                     <tr style="background: #f5faff;">
                        <th style="padding: 0.7rem 1rem; text-align: left; color: #3a8de0;">Date</th>
                        <th style="padding: 0.7rem 1rem; text-align: left; color: #3a8de0;">User</th>
                        <th style="padding: 0.7rem 1rem; text-align: left; color: #3a8de0;">Action</th>
                        <th style="padding: 0.7rem 1rem; text-align: left; color: #3a8de0;">Status</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php
                     // Fetch recent data_reports (latest 7)
                     $activity_sql = "SELECT agent_name, reviewer_name, date, time, q1, q2, q3, q4, q5, q6, q7, q8, q9, q10 FROM data_reports ORDER BY date DESC, time DESC LIMIT 7";
                     $activity_result = $conn->query($activity_sql);
                     $activity_rows = [];
                     if ($activity_result && $activity_result->num_rows > 0) {
                        while ($row = $activity_result->fetch_assoc()) {
                           $date = htmlspecialchars($row['date']) . ' ' . htmlspecialchars($row['time']);
                           $user = htmlspecialchars($row['agent_name']);
                           $action = "Audit Submitted";
                           // Check if all 10 questions have a value (not empty/null)
                           $completed = true;
                           for ($i = 1; $i <= 10; $i++) {
                              $q = $row["q$i"];
                              if ($q === null || $q === '') {
                                 $completed = false;
                                 break;
                              }
                           }
                           $status = $completed ? "Completed" : "Incomplete";
                           $statusColor = $completed ? "#43a047" : "#ffa000";
                           $activity_rows[] = [
                              'date' => $date,
                              'user' => $user,
                              'action' => $action,
                              'status' => $status,
                              'statusColor' => $statusColor
                           ];
                        }
                     }

                     // Fetch recent user account creations (latest 3)
                     // Assumes 'users' table has 'created_at' or 'date_created' column
                     $user_sql = "SELECT username, ";
                     // Try to detect the correct date field
                     $date_field = '';
                     $fields_res = $conn->query("SHOW COLUMNS FROM users");
                     if ($fields_res) {
                        while ($f = $fields_res->fetch_assoc()) {
                           if (in_array(strtolower($f['Field']), ['created_at', 'date_created', 'created'])) {
                              $date_field = $f['Field'];
                              break;
                           }
                        }
                     }
                     if ($date_field) {
                        $user_sql .= "$date_field as created FROM users ORDER BY $date_field DESC LIMIT 3";
                        $user_result = $conn->query($user_sql);
                        if ($user_result && $user_result->num_rows > 0) {
                           while ($row = $user_result->fetch_assoc()) {
                              $date = htmlspecialchars($row['created']);
                              $user = "Admin";
                              $action = "Added User Account: " . htmlspecialchars($row['username']);
                              $status = "Success";
                              $statusColor = "#1976d2";
                              $activity_rows[] = [
                                 'date' => $date,
                                 'user' => $user,
                                 'action' => $action,
                                 'status' => $status,
                                 'statusColor' => $statusColor
                              ];
                           }
                        }
                     }
                     // Sort all activities by date descending (and time if available)
                     usort($activity_rows, function($a, $b) {
                        return strtotime($b['date']) <=> strtotime($a['date']);
                     });
                     // Show up to 10 activities
                     $activity_rows = array_slice($activity_rows, 0, 10);
                     if (count($activity_rows) > 0) {
                        foreach ($activity_rows as $row) {
                           echo "<tr>
                              <td style='padding: 0.6rem 1rem;'>{$row['date']}</td>
                              <td style='padding: 0.6rem 1rem;'>{$row['user']}</td>
                              <td style='padding: 0.6rem 1rem;'>{$row['action']}</td>
                              <td style='padding: 0.6rem 1rem;'><span style='color: {$row['statusColor']}; font-weight: 600;'>{$row['status']}</span></td>
                           </tr>";
                        }
                     } else {
                        echo "<tr><td colspan='4' style='padding: 1rem; text-align: center; color: #888;'>No recent activity found.</td></tr>";
                     }
                     ?>
                  </tbody>
               </table>
            </div>
         </div>

         <!-- Dashboard Shortcuts -->
         <div class="dashboard-buttons" style="margin-top: 0;">
            <a href="AdminAuditDatabank.php">
               <i class="ri-wallet-3-fill"></i> UCX Data Bank
            </a>
            <a href="#">
               <i class="ri-calendar-fill"></i> UCX Connect
            </a>
            <a href="AdminAuditForm.php">
               <i class="ri-arrow-up-down-line"></i> Unify Audit System
            </a>
            <a href="AdminHrRecords.php">
               <i class="ri-bar-chart-box-fill"></i> HR Records
            </a>
         </div>
      </section>
   </main>
   
   <!--=============== MAIN JS ===============-->
   <script src="../../assets/js/main.js"></script>

   <!-- Slideshow JS -->
   <script>
      let slideIndex = 0;
      let autoSlideTimeout;

      function showSlides() {
         let slides = document.getElementsByClassName("slide");
         let dots = document.getElementsByClassName("dot");

         for (let i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";  
         }
         slideIndex++;
         if (slideIndex > slides.length) {slideIndex = 1}

         for (let i = 0; i < dots.length; i++) {
            dots[i].classList.remove("active-dot");
         }

         slides[slideIndex-1].style.display = "block";  
         dots[slideIndex-1].classList.add("active-dot");

         autoSlideTimeout = setTimeout(showSlides, 5000); // Auto slide
      }

      function currentSlide(n) {
         clearTimeout(autoSlideTimeout);
         slideIndex = n - 1;
         showSlides();
      }

      // Start slideshow
      showSlides();
   </script>
</body>
</html>
