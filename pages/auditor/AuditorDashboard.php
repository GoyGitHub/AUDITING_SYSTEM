<?php
include('../../repositories/AuditorAuthentications.php');  // ✅ Enforces login
requireAuditor();                      // ✅ Only admins
include('../../database/dbconnection.php');

// ✅ Load session user details safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pull data from session
$username = $_SESSION['username'] ?? 'User';
$role = ucfirst($_SESSION['user_role'] ?? 'User');
$displayName = ucfirst($username) . '.';

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
                  <a href="AuditorDashboard.php" class="sidebar__link   active-link">
                     <i class="ri-dashboard-horizontal-fill"></i>
                     <span>Dashboard</span>
                  </a>
                  <a href="AuditorAuditForm.php" class="sidebar__link">
                     <i class="ri-survey-fill"></i>
                     <span>Unify Audit System (UAS)</span>
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

   <!--=============== MAIN ===============-->
   <main class="main container" id="main">
      <section class="dashboard-article">
         <h2>Welcome to UCX Overview</h2>

         <!-- Slideshow Section -->
         <div class="slideshow-container">
            <div class="slide fade">
               <img src="../../assets/img/1.jfif" alt="Slideshow Image 1">
            </div>
            <div class="slide fade">
               <img src="../../assets/img/2.jfif" alt="Slideshow Image 2">
            </div>
            <div class="slide fade">
               <img src="../../assets/img/3.jfif" alt="Slideshow Image 3">
            </div>
         </div>

         <!-- Dots (Indicators) -->
         <div class="dots">
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
         </div>

         <!-- Buttons under slideshow -->
         <div class="dashboard-buttons">
            <a href="AuditDatabank.php">
               <i class="ri-wallet-3-fill"></i> UCX Data Bank
            </a>
            <a href="#">
               <i class="ri-calendar-fill"></i> UCX Connect
            </a>
            <a href="AuditForm.php">
               <i class="ri-arrow-up-down-line"></i> Unify Audit System
            </a>
            <a href="HrRecords.php">
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
