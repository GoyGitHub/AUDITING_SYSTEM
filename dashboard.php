<?php
include('dbconnection.php');
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: LoginFunction.php");
    exit();
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
   <link rel="stylesheet" href="assets/css/styles.css">

   <title>Dashboard | Cool Pals</title>
   <style>
      .dashboard-article {
         padding: 2rem;
         background-color: #fff;
         border-radius: 1rem;
         box-shadow: 0 4px 12px rgba(0,0,0,0.08);
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
         gap: 2rem;
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
   </style>
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
         <img src="assets/img/logo.png" alt="Logo" style="height: 40px;">
      </a>
   </div>
</header>


   <!--=============== SIDEBAR ===============-->
   <nav class="sidebar" id="sidebar">
      <div class="sidebar__container">
         <div class="sidebar__user">
            <div class="sidebar__img">
               <img src="assets/img/perfil.png" alt="image">
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
                  <a href="dashboard.php" class="sidebar__link active-link">
                     <i class="ri-pie-chart-2-fill"></i>
                     <span>Dashboard</span>
                  </a>
                  <a href="list.php" class="sidebar__link">
                     <i class="ri-wallet-3-fill"></i>
                     <span>UCX Data Bank</span>
                  </a>
                  <a href="#" class="sidebar__link">
                     <i class="ri-calendar-fill"></i>
                     <span>UCX Connect</span>
                  </a>
                  <a href="audit_form.php" class="sidebar__link">
                     <i class="ri-arrow-up-down-line"></i>
                     <span>Unify Audit System (UAS)</span>
                  </a>
                  <a href="HrRecords.php" class="sidebar__link">
                     <i class="ri-bar-chart-box-fill"></i>
                     <span>HR Records</span>
                  </a>
               </div>
            </div>

            <div>
               <h3 class="sidebar__title">SETTINGS</h3>
               <div class="sidebar__list">
                  <a href="#" class="sidebar__link">
                     <i class="ri-settings-3-fill"></i>
                     <span>Settings</span>
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
            <a href="login.php" class="sidebar__link">
               <i class="ri-logout-box-r-fill"></i>
               <span>Log Out</span>
            </a>
         </div>
      </div>
   </nav>

   <!--=============== MAIN ===============-->
   <main class="main container" id="main">
      <section class="dashboard-article">
         <h2>Welcome to UCX Overview</h2>
         <p>
            UCX (Unify CX) serves as the centralized platform for handling internal company audits, workforce records, internal communications, and secure data storage.
         </p>
         <p>
            The tools below allow you to manage, assess, and review internal processes and reports across various operational units of the company. Select a function to begin managing your unit or viewing system-wide updates.
         </p>

         <div class="dashboard-buttons">
            <a href="list.php">
               <i class="ri-wallet-3-fill"></i> UCX Data Bank
            </a>
            <a href="#">
               <i class="ri-calendar-fill"></i> UCX Connect
            </a>
            <a href="audit_form.php">
               <i class="ri-arrow-up-down-line"></i> Unify Audit System
            </a>
            <a href="#">
               <i class="ri-bar-chart-box-fill"></i> HR Records
            </a>
         </div>
      </section>
   </main>
   
   <!--=============== MAIN JS ===============-->
   <script src="assets/js/main.js"></script>
</body>
</html>
