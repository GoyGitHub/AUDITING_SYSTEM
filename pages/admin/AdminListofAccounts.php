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

/* ----------------------------
   DELETE ACCOUNT
---------------------------- */
if (isset($_POST['delete_account'])) {
    $userId = intval($_POST['user_id']);
    if ($userId > 0) {
        $deleteQuery = $conn->prepare("DELETE FROM users WHERE id = ?");
        $deleteQuery->bind_param("i", $userId);
        $deleteQuery->execute();
        $deleteQuery->close();
        echo "<script>alert('🗑️ Account deleted successfully!'); window.location.href=window.location.href;</script>";
    }
}

/* ----------------------------
   EDIT ACCOUNT (UPDATE)
---------------------------- */
if (isset($_POST['update_account'])) {
    $userId   = intval($_POST['id']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role     = trim($_POST['role']);

    if ($userId > 0 && $username !== "" && $password !== "" && $role !== "") {
        $updateQuery = $conn->prepare("UPDATE users SET username=?, password=?, role=? WHERE id=?");
        $updateQuery->bind_param("sssi", $username, $password, $role, $userId);
        $updateQuery->execute();
        $updateQuery->close();
        echo "<script>alert('✅ Account updated successfully!'); window.location.href=window.location.href;</script>";
    }
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
      .action-links form {
         display: inline;
      }
      .edit-link { color: blue; cursor:pointer; }
      .delete-link { color: red; cursor:pointer; border:none; background:none; }
      .edit-form input, .edit-form select {
         margin: 4px 0;
         padding: 6px;
         border: 1px solid #ccc;
         border-radius: 6px;
      }
      .edit-form button {
         padding: 6px 12px;
         border:none;
         border-radius:6px;
         cursor:pointer;
         margin-top: 4px;
      }
      .edit-form .save { background: #007bff; color:#fff; }
      .edit-form .cancel { background: #ccc; }
      
      .header__buttons button {
   background: none;
   border: none;
   cursor: pointer;
   font-size: 1.5rem; /* makes icon bigger */
   padding: 0.1px;
   margin-right: 1px;
   color: #0d1b3d; /* same as your theme */
   display: flex;
   align-items: center;
   justify-content: center;
}

.header__buttons {
   display: flex;
   align-items: center;
   gap: 8px; /* space between buttons */
}

.edit-form form {
   background: #fdfdfd;
   border: 1px solid #eee;
   border-radius: 8px;
   padding: 16px;
   display: flex;
   flex-direction: column;
   gap: 10px;
   font-size: 0.95rem;
}

.edit-form label {
   font-weight: 600;
   margin-top: 6px;
   color: #0d1b3d;
}

.edit-form input,
.edit-form select {
   padding: 8px;
   border-radius: 6px;
   border: 1px solid #ccc;
   width: 100%;
}

.form-actions {
   display: flex;
   gap: 10px;
   margin-top: 8px;
}

.edit-form .save {
   background: #007bff;
   color: #fff;
   padding: 8px 14px;
   border: none;
   border-radius: 6px;
   cursor: pointer;
   transition: 0.2s;
}

.edit-form .save:hover {
   background: #0056b3;
}

.edit-form .cancel {
   background: #ccc;
   color: #333;
   padding: 8px 14px;
   border: none;
   border-radius: 6px;
   cursor: pointer;
   transition: 0.2s;
}

.edit-form .cancel:hover {
   background: #999;
   color: #fff;
}


   </style>

   <title>Admin - List of Accounts</title>
</head>
<body>

<!--=============== HEADER ===============-->
<header class="header" id="header">
   <div class="header__container">
      <div class="header__buttons">
         <!-- Sidebar Toggle -->
         <button class="header__toggle" id="header-toggle">
            <i class="ri-menu-line"></i>
         </button>

         <!-- Back Button -->
         <button class="header__back" onclick="history.back()">
            <i class="ri-arrow-left-line"></i>
         </button>
      </div>

      <!-- Logo -->
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
                  <a href="AdminTools.php" class="sidebar__link active-link">
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


<!--=============== MAIN CONTENT ===============-->
<main class="main container" id="main">
   <h1 style="margin-bottom: 20px; font-family:'Nunito Sans', sans-serif; color:#0d1b3d;">List of Accounts</h1>

   <div class="table-container" style="overflow-x:auto; background:#fff; border-radius:12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 24px;">
      <table>
<thead>
   <tr>
      <th>#</th>
      <th>Username</th>
      <th>Password</th>
      <th>Role</th>
      <th>Created At</th>
      <th>Actions</th>
   </tr>
</thead>
<tbody>
<?php
   $sql = "SELECT * FROM users ORDER BY created_at ASC";
   $result = $conn->query($sql);

   if ($result->num_rows > 0) {
      $counter = 1;
      while($row = $result->fetch_assoc()) {
         echo "<tr>";
         echo "<td>" . $counter++ . "</td>";
         echo "<td>" . htmlspecialchars($row['username']) . "</td>";
         echo "<td>" . htmlspecialchars($row['password']) . "</td>";
         echo "<td>" . htmlspecialchars($row['role']) . "</td>";
         echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
         echo "<td class='action-links'>
                  <a class='edit-link' onclick=\"toggleEdit(".$row['id'].")\">Edit</a>
                  <a href='?delete=" . $row['id'] . "' onclick=\"return confirm('Are you sure you want to delete this account?');\" class='delete-link'>Delete</a>
               </td>";
         echo "</tr>";

// Inline edit form (hidden by default)
echo "<tr id='edit-form-".$row['id']."' class='edit-form' style='display:none;'>
         <td colspan='6'>
            <form method='POST'>
               <input type='hidden' name='id' value='".$row['id']."'>
               <label>Username:</label>
               <input type='text' name='username' value='".htmlspecialchars($row['username'])."' required>
               <label>Password:</label>
               <input type='text' name='password' value='".htmlspecialchars($row['password'])."' required>
               <label>Role:</label>
               <select name='role' required>
                  <option value='admin' ".($row['role']=="admin"?"selected":"").">Admin</option>
                  <option value='auditor' ".($row['role']=="auditor"?"selected":"").">Auditor</option>
                  <option value='supervisor' ".($row['role']=="supervisor"?"selected":"").">Supervisor</option>
                  <option value='data_analyst' ".($row['role']=="data_analyst"?"selected":"").">Data Analyst</option>
               </select>
               <div class='form-actions'>
                  <button type='submit' name='update_account' class='save'>Save</button>
                  <button type='button' class='cancel' onclick=\"toggleEdit(".$row['id'].")\">Cancel</button>
               </div>
            </form>
         </td>
      </tr>";

      }
   } else {
      echo "<tr><td colspan='6'>No accounts found</td></tr>";
   }
?>
</tbody>
      </table>
   </div>

   <!-- Hidden Edit Form (popup style) -->
   <div id="editFormContainer" style="display:none; margin-top:20px;">
      <form method="POST" class="edit-form">
         <input type="hidden" name="user_id" id="edit_user_id">
         <label>Username:</label><br>
         <input type="text" name="username" id="edit_username" required><br>
         <label>Password:</label><br>
         <input type="text" name="password" id="edit_password" required><br>
         <label>Role:</label><br>
         <select name="role" id="edit_role" required>
            <option value="admin">Admin</option>
            <option value="auditor">Auditor</option>
            <option value="supervisor">Supervisor</option>
            <option value="agent">Agent</option>
            <option value="data_analyst">Data Analyst</option>
         </select><br>
         <button type="submit" name="update_account" class="save">Save</button>
         <button type="button" class="cancel" onclick="hideEditForm()">Cancel</button>
      </form>
   </div>
</main>

<script>
function showEditForm(id, username, password, role) {
   document.getElementById("editFormContainer").style.display = "block";
   document.getElementById("edit_user_id").value = id;
   document.getElementById("edit_username").value = username;
   document.getElementById("edit_password").value = password;
   document.getElementById("edit_role").value = role;
   window.scrollTo(0, document.body.scrollHeight);
}
function hideEditForm() {
   document.getElementById("editFormContainer").style.display = "none";
}

</script>

<!--=============== MAIN JS ===============-->
<script src="../../assets/js/main.js"></script>

<script>
   function toggleEdit(id) {
      let row = document.getElementById("edit-form-" + id);
      row.style.display = (row.style.display === "none" || row.style.display === "") ? "table-row" : "none";
   }
</script>
</body>
</html>
