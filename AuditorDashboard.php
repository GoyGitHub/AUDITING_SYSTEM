<?php
include('dbconnection.php');
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'auditor') {
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
   <link rel="stylesheet" href="../../assets/css/styles.css">

   <title>Cool Pals</title>
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
               <h3><?php echo $_SESSION['username']; ?></h3>
               <span>Auditor</span>
            </div>
         </div>

         <div class="sidebar__content">
            <div>
               <h3 class="sidebar__title">MANAGE</h3>

               <div class="sidebar__list">
                  <a href="#" class="sidebar__link active-link">
                     <i class="ri-pie-chart-2-fill"></i>
                     <span>Dashboard</span>
                  </a>

                  <a href="audit_form.php" class="sidebar__link">
                     <i class="ri-arrow-up-down-line"></i>
                     <span>Unify Audit System (UAS)</span>
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
      <h1>
         
      </h1>
   </main>
   
   <!--=============== MAIN JS ===============-->
   <script src="assets/js/main.js"></script>
</body>
</html>
