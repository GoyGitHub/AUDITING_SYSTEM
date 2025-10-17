<?php
include('../../database/dbconnection.php');
if (session_status() === PHP_SESSION_NONE) session_start();
$username = $_SESSION['username'] ?? 'Admin';
$role = ucfirst($_SESSION['user_role'] ?? 'Admin');
$displayName = ucfirst($username) . '.';
$notice = '';

// Delete account (POST)
if (isset($_POST['delete_account'])) {
    $userId = intval($_POST['user_id'] ?? 0);
    if ($userId > 0) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->close();
            $notice = "Account deleted.";
        } else {
            $notice = "Delete error: " . $conn->error;
        }
    }
}

// Update account (POST)
if (isset($_POST['update_account'])) {
    $userId = intval($_POST['id'] ?? 0);
    $uname = trim($_POST['username'] ?? '');
    $upass = trim($_POST['password'] ?? '');
    $urole = trim($_POST['role'] ?? '');
    if ($userId && $uname !== '' && $urole !== '') {
        if ($upass === '') {
            $uStmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
            $uStmt->bind_param("ssi", $uname, $urole, $userId);
        } else {
            $uStmt = $conn->prepare("UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?");
            $uStmt->bind_param("sssi", $uname, $upass, $urole, $userId);
        }
        if ($uStmt->execute()) {
            $notice = "Account updated.";
        } else {
            $notice = "Update error: " . $uStmt->error;
        }
        $uStmt->close();
    } else {
        $notice = "Please fill required fields.";
    }
}

// Fetch users and left-join agents2 on username = agent_firstname
$sql = "
SELECT u.id as uid, u.username, u.role, u.created_at,
       a.id AS agent_id, a.agent_firstname, a.agent_lastname, a.email AS agent_email, a.team AS agent_team, a.birthday AS agent_birthday
FROM users u
LEFT JOIN agents2 a ON u.username = a.agent_firstname
ORDER BY u.created_at DESC
";
$res = $conn->query($sql);
$users = [];
if ($res && $res->num_rows) {
    while ($r = $res->fetch_assoc()) $users[] = $r;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Admin — List of Accounts</title>
<link rel="stylesheet" href="../../assets/css/styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css">
<style>
.container { max-width:1200px; margin:90px auto 40px; padding: 1rem; margin-left:320px; }
.card { background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 6px 18px rgba(0,0,0,0.06); }
.table { max-width: 920px; margin: 0 auto; width:100%; border-collapse:collapse; }
.table th, .table td { padding:0.9rem; border-bottom:1px solid #eee; vertical-align:middle; }
.table thead th { background:#f5f7fb; color:#003366; text-align:left; }
.actions { text-align:right; }
.btn { padding:8px 12px; border-radius:8px; background:#1976d2; color:#fff; text-decoration:none; display:inline-block; }
.btn.danger { background:#d32f2f; }
.info { color:#666; font-size:0.95rem; }
.badge { display:inline-block; padding:6px 10px; border-radius:999px; font-weight:700; }
.incomplete { background:#fff3cd; color:#856404; }
.complete { background:#e6f4ea; color:#2e7d32; }
.edit-form { background:#fbfbff; padding:12px; border-radius:8px; border:1px solid #eef; }

/* center the main card/table slightly and limit width for better reading */
.center-wrap {
    max-width: 980px;
    margin: 40px auto;
    padding: 0 16px;
}

/* ensure sidebar actions (logout) are visible and pinned to bottom */
.sidebar__container { position: relative; min-height: 100vh; padding-bottom: 90px; }
.sidebar__actions {
    position: absolute;
    bottom: 16px;
    left: 16px;
    right: 16px;
    display: flex;
    gap: 8px;
    align-items: center;
    justify-content: flex-start;
    z-index: 2000;
}

/* slightly narrow table and center card content */
.card { padding: 1.25rem; border-radius:12px; background:#fff; box-shadow:0 6px 18px rgba(0,0,0,0.06); }
.table { max-width: 920px; margin: 0 auto; width:100%; }

/* Column alignment: Username (col1) left, Role+Created center, Agent Info left, Actions right */
.table th, .table td { padding:0.9rem; border-bottom:1px solid #eee; vertical-align:middle; }
.table thead th { text-align:left; }
.table th:nth-child(1), .table td:nth-child(1) { text-align:left; }
.table th:nth-child(2), .table td:nth-child(2),
.table th:nth-child(3), .table td:nth-child(3) { text-align:center; }
.table th:nth-child(4), .table td:nth-child(4) { text-align:left; }
.table th:nth-child(5), .table td:nth-child(5) { text-align:right; width:220px; }

/* Truncate long agent info for neat display */
.table td:nth-child(4) { max-width: 360px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* Responsive tweaks */
@media (max-width:900px){
    .center-wrap { padding: 12px; margin-left:16px; margin-right:16px; }
    .sidebar__actions { position: static; padding-top: 12px; }
    .table thead th, .table td { text-align:left; }
    .table th:nth-child(5), .table td:nth-child(5) { text-align:left; width:auto; }
    .table td:nth-child(4) { white-space:normal; }
}
</style>
</head>
<body>
<header class="header" id="header">
   <div class="header__container">
      <button class="header__toggle" id="header-toggle"><i class="ri-menu-line"></i></button>
      <a href="#" class="header__logo"><img src="../../assets/img/logo.png" alt="Logo" style="height:40px;"></a>
   </div>
</header>
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
            </div>
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

<main class="container">
   <div class="center-wrap">
      <div class="card">
         <h2>List of Accounts</h2>
         <?php if ($notice): ?><div style="padding:8px;background:#eaf4ff;border-radius:8px;margin-bottom:12px;"><?php echo htmlspecialchars($notice); ?></div><?php endif; ?>
         <div style="overflow-x:auto;">
            <table class="table">
               <thead>
                  <tr>
                     <th>Username</th>
                     <th>Role</th>
                     <th>Created</th>
                     <th>Agent Info</th>
                     <th class="actions">Actions</th>
                  </tr>
               </thead>
               <tbody>
                  <?php if (count($users)): foreach ($users as $u): 
                     // determine agent completeness
                     $agentComplete = (!empty($u['agent_email']) && !empty($u['agent_team']) && !empty($u['agent_birthday']));
                     $agentLabel = $u['agent_firstname'] ? ($u['agent_firstname'] . ' ' . ($u['agent_lastname'] ?? '')) : '';
                  ?>
                  <tr>
                     <td><?php echo htmlspecialchars($u['username']); ?></td>
                     <td><?php echo htmlspecialchars($u['role']); ?></td>
                     <td><?php echo htmlspecialchars($u['created_at']); ?></td>
                     <td>
                        <?php if ($agentLabel): ?>
                           <div style="font-weight:700;"><?php echo htmlspecialchars($agentLabel); ?></div>
                           <div class="info">Email: <?php echo $u['agent_email'] ? htmlspecialchars($u['agent_email']) : '<span style="color:#c00;">(missing)</span>'; ?></div>
                           <div class="info">Team: <?php echo $u['agent_team'] ? htmlspecialchars($u['agent_team']) : '<span style="color:#c00;">(missing)</span>'; ?></div>
                           <div class="info">Birthday: <?php echo $u['agent_birthday'] ? htmlspecialchars($u['agent_birthday']) : '<span style="color:#c00;">(missing)</span>'; ?></div>
                           <div style="margin-top:6px;">
                              <span class="badge <?php echo $agentComplete ? 'complete' : 'incomplete'; ?>"><?php echo $agentComplete ? 'Complete' : 'Incomplete'; ?></span>
                           </div>
                        <?php else: ?>
                           <div class="info" style="color:#c00;font-weight:700;">No agent match for this username</div>
                        <?php endif; ?>
                     </td>
                     <td class="actions">
                        <!-- Edit toggle -->
                        <a href="javascript:void(0)" onclick="toggleEdit(<?php echo $u['uid']; ?>)" style="margin-right:10px;color:#1976d2;font-weight:600;text-decoration:none;">Edit</a>

                        <!-- Delete form -->
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this account?');">
                           <input type="hidden" name="user_id" value="<?php echo $u['uid']; ?>">
                           <button type="submit" name="delete_account" class="btn danger">Delete</button>
                        </form>
                     </td>
                  </tr>

                  <!-- Inline edit form row (hidden by default) -->
                  <tr id="edit-row-<?php echo $u['uid']; ?>" style="display:none;">
                     <td colspan="5">
                        <form method="POST" class="edit-form">
                           <input type="hidden" name="id" value="<?php echo $u['uid']; ?>">
                           <div style="display:flex; gap:12px; flex-wrap:wrap;">
                              <div style="flex:1;min-width:200px;">
                                 <label>Username</label>
                                 <input type="text" name="username" value="<?php echo htmlspecialchars($u['username']); ?>" required>
                              </div>
                              <div style="flex:1;min-width:160px;">
                                 <label>Password (leave blank to keep)</label>
                                 <input type="text" name="password" placeholder="New password">
                              </div>
                              <div style="flex:1;min-width:160px;">
                                 <label>Role</label>
                                 <select name="role" required>
                                    <option value="admin" <?php if ($u['role']=='admin') echo 'selected'; ?>>admin</option>
                                    <option value="auditor" <?php if ($u['role']=='auditor') echo 'selected'; ?>>auditor</option>
                                    <option value="supervisor" <?php if ($u['role']=='supervisor') echo 'selected'; ?>>supervisor</option>
                                    <option value="data_analyst" <?php if ($u['role']=='data_analyst') echo 'selected'; ?>>data_analyst</option>
                                    <option value="agent" <?php if ($u['role']=='agent') echo 'selected'; ?>>agent</option>
                                 </select>
                              </div>
                           </div>
                           <div style="margin-top:12px; display:flex; gap:8px;">
                              <button type="submit" name="update_account" class="btn">Save</button>
                              <button type="button" onclick="toggleEdit(<?php echo $u['uid']; ?>)" style="padding:8px 12px;border-radius:8px;border:1px solid #ccc;background:#fff;cursor:pointer;">Cancel</button>
                           </div>
                        </form>
                     </td>
                  </tr>

                  <?php endforeach; else: ?>
                  <tr><td colspan="5" class="info">No accounts found.</td></tr>
                  <?php endif; ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</main>

<script>
function toggleEdit(id){
   var row = document.getElementById('edit-row-' + id);
   if (!row) return;
   row.style.display = (row.style.display === 'none' || row.style.display === '') ? 'table-row' : 'none';
}
</script>
</body>
</html>
