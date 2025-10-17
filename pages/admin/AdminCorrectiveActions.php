<?php
include('../../database/dbconnection.php');
if (session_status() === PHP_SESSION_NONE) session_start();
$username = $_SESSION['username'] ?? 'Admin';
$role = $_SESSION['user_role'] ?? 'admin';
$displayName = ucfirst($username) . '.';

// --- Create corrective_actions table if not exists ---
$conn->query("
CREATE TABLE IF NOT EXISTS corrective_actions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    target_role ENUM('agent','auditor','supervisor','data_analyst') NOT NULL,
    target_name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    severity ENUM('Low','Medium','High') DEFAULT 'Medium',
    due_date DATE DEFAULT NULL,
    status ENUM('Open','In Progress','Resolved','Completed') DEFAULT 'Open',
    created_by VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// --- Fetch people lists for selects ---
$agents = $conn->query("SELECT CONCAT(agent_firstname, ' ', agent_lastname) AS name FROM agents2 ORDER BY agent_firstname");
$auditors = $conn->query("SELECT CONCAT(auditor_firstname, ' ', auditor_lastname) AS name FROM auditors2 ORDER BY auditor_firstname");
$supervisors = $conn->query("SELECT CONCAT(supervisor_firstname, ' ', supervisor_lastname) AS name FROM supervisors ORDER BY supervisor_firstname");
$data_analysts = $conn->query("SELECT CONCAT(data_analyst_firstname, ' ', data_analyst_lastname) AS name FROM data_analysts ORDER BY data_analyst_firstname");

// --- Handle add action ---
$notice = '';
if (isset($_POST['add_action'])) {
    $role_t = $_POST['target_role'] ?? '';
    $name_t = trim($_POST['target_name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $severity = $_POST['severity'] ?? 'Medium';
    $due = $_POST['due_date'] ?: null;
    if ($role_t && $name_t && $desc) {
        $stmt = $conn->prepare("INSERT INTO corrective_actions (target_role, target_name, description, severity, due_date, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $role_t, $name_t, $desc, $severity, $due, $displayName);
        if ($stmt->execute()) $notice = "Corrective action added.";
        else $notice = "Error: " . $stmt->error;
        $stmt->close();
    } else {
        $notice = "Please fill required fields.";
    }
}

// ✅ Fetch all corrective actions for display (safe)
$actions = $conn->query("SELECT * FROM corrective_actions ORDER BY created_at DESC");
if ($actions === false) {
    // Query failed or table not present — use empty array fallback so view rendering won't error
    $actions = [];
}

// --- Resolve handler logic ---
if (isset($_GET['resolve'])) {
    $id = intval($_GET['resolve']);
    $ok = $conn->query("UPDATE corrective_actions SET status='Completed' WHERE id=$id");
    if ($ok === false || $conn->affected_rows === 0) {
        $conn->query("UPDATE corrective_actions SET status='Resolved' WHERE id=$id");
    }
    header("Location: AdminCorrectiveActions.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Admin — Corrective Actions</title>
<link rel="stylesheet" href="../../assets/css/styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css">
<style>
.container { max-width:1200px; margin:90px auto 40px; padding: 1rem; margin-left:320px; }
.card { background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 6px 18px rgba(0,0,0,0.06); }

/* Form layout improvements */
.form-row { display:flex; gap:0.8rem; flex-wrap:wrap; align-items:center; }
.info-field { flex:1 1 220px; min-width:200px; position:relative; }
.info-field label { display:block; font-weight:700; color:#1a237e; margin-bottom:6px; }
.info-field input,
.info-field select,
.info-field textarea {
    width:100%;
    padding:0.7rem 1rem;
    border:1.5px solid #cfcfe6;
    border-radius:8px;
    background:#fff;
    font-size:1rem;
    outline:none;
    transition: border-color .15s, box-shadow .15s;
}
.info-field input:focus,
.info-field select:focus,
.info-field textarea:focus {
    border-color:#3949ab;
    box-shadow:0 4px 16px rgba(57,73,171,0.06);
    background:#f6f8ff;
}

/* select with icon support */
.select-icon { position:relative; }
.select-icon select { padding-left:2.5rem !important; background-repeat:no-repeat; background-position:0.8rem center; background-size:1.2rem; }

/* small decorative icons for role and person selects */
.select-icon.role select { background-image: url('../../assets/img/icons/user-role.svg'); }
.select-icon.person select { background-image: url('../../assets/img/icons/user-fill.svg'); }

/* Align form actions: notice left, add button right */
.form-actions {
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:1rem;
    margin-top:0.8rem;
}

/* Primary action button */
.add-action-btn {
    padding:10px 16px;
    border-radius:8px;
    background: linear-gradient(90deg,#1976d2,#0d47a1);
    color:#fff;
    border:none;
    font-weight:700;
    cursor:pointer;
    box-shadow:0 4px 10px rgba(13,71,161,0.12);
    transition: transform .06s ease, box-shadow .12s ease;
}
.add-action-btn:hover { transform: translateY(-1px); box-shadow:0 6px 16px rgba(13,71,161,0.14); }

/* Notice styling */
.notice { margin:0; font-weight:600; color:#1976d2; }

/* Table & actions alignment */
.actions-table { width:100%; border-collapse:collapse; margin-top:1rem; }
.actions-table th, .actions-table td { padding:0.8rem 0.9rem; border-bottom:1px solid #eee; text-align:left; vertical-align: middle; }
.actions-table th:last-child, .actions-table td:last-child { text-align:right; width:240px; }

/* Group actions in cell and style small buttons */
.actions-cell { display:flex; justify-content:flex-end; gap:8px; align-items:center; }
.small-btn {
    padding:8px 12px;
    border-radius:8px;
    background:#1976d2;
    color:#fff;
    text-decoration:none;
    font-weight:600;
    border:none;
    cursor:pointer;
    display:inline-flex;
    align-items:center;
    gap:8px;
}
.small-btn.secondary { background:#43a047; } /* green for completed/resolve */
.small-btn.ghost { background:transparent; color:#1976d2; border:1px solid rgba(25,118,210,0.12); }

/* Contact link */
.contact-link { display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:8px; text-decoration:none; color:#1976d2; border:1px solid rgba(25,118,210,0.08); background:#fff; }
.contact-link:hover { background:rgba(25,119,210,0.06); color:#0d47a1; }

/* Responsive tweaks */
@media (max-width:900px) {
    .form-row { flex-direction:column; }
    .actions-table th:last-child, .actions-table td:last-child { text-align:center; width:auto; }
    .actions-cell { justify-content:center; flex-wrap:wrap; }
    .form-actions { flex-direction:column-reverse; align-items:stretch; }
}
</style>
</head>
<body>

<!-- ADMIN HEADER & SIDEBAR -->
<header class="header" id="header">
   <div class="header__container">
      <button class="header__toggle" id="header-toggle">
         <i class="ri-menu-line"></i>
      </button>
      <a href="#" class="header__logo">
         <img src="../../assets/img/logo.png" alt="Logo" style="height: 40px;">
      </a>
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
            <span><?php echo htmlspecialchars(ucfirst($role)); ?></span>
         </div>
      </div>

      <div class="sidebar__content">
         <div>
            <h3 class="sidebar__title">MANAGE</h3>
            <div class="sidebar__list">
               <a href="AdminDashboard.php" class="sidebar__link"><i class="ri-dashboard-horizontal-fill"></i><span>Dashboard</span></a>
               <a href="AdminAuditDatabank.php" class="sidebar__link"><i class="ri-database-fill"></i><span>UCX Data Bank</span></a>
               <a href="AdminConductCoach.php" class="sidebar__link"><i class="ri-ubuntu-fill"></i><span>UCX Connect</span></a>
               <a href="AdminAuditForm.php" class="sidebar__link"><i class="ri-survey-fill"></i><span>Unify Audit System (UAS)</span></a>
               <a href="AdminHrRecords.php" class="sidebar__link"><i class="ri-folder-history-fill"></i><span>HR Records</span></a>
            </div>
         </div>

         <div>
            <h3 class="sidebar__title">TOOLS</h3>
            <div class="sidebar__list">
               <a href="AdminTools.php" class="sidebar__link active-link"><i class="ri-settings-3-fill"></i><span>Admin Tools</span></a>
            </div>
         </div>
      </div>

      <div class="sidebar__actions">
         <button>
            <i class="ri-moon-clear-fill sidebar__link sidebar__theme" id="theme-button"><span>Theme</span></i>
         </button>
         <a href="../../LoginFunction.php" class="sidebar__link"><i class="ri-logout-box-r-fill"></i><span>Log Out</span></a>
      </div>
   </div>
</nav>

<main class="container">
   <div class="card">
      <h2>Corrective Actions</h2>
      <p>Create corrective actions for Agents, Auditors, Supervisors and Data Analysts.</p>

      <!-- Updated form: controls wrapped with .info-field and .select-icon -->
      <form method="POST" class="card" style="margin-top:1rem;">
         <div class="form-row">
            <!-- Role select -->
            <div class="info-field select-icon role">
                <label for="target_role">Role</label>
                <select name="target_role" id="target_role" required onchange="onRoleChange(this.value)">
                   <option value="">Select Role</option>
                   <option value="agent">Agent</option>
                   <option value="auditor">Auditor</option>
                   <option value="supervisor">Supervisor</option>
                   <option value="data_analyst">Data Analyst</option>
                </select>
            </div>

            <!-- Person select -->
            <div class="info-field select-icon person">
                <label for="target_name">Person</label>
                <select name="target_name" id="target_name" required>
                   <option value="">Select Person</option>
                </select>
            </div>

            <!-- Severity -->
            <div class="info-field">
                <label for="severity">Severity</label>
                <select name="severity" id="severity" required>
                   <option value="Low">Low</option>
                   <option value="Medium" selected>Medium</option>
                   <option value="High">High</option>
                </select>
            </div>

            <!-- Due date -->
            <div class="info-field">
                <label for="due_date">Due Date</label>
                <input type="date" name="due_date" id="due_date" />
            </div>
         </div>

         <div style="margin-top:0.8rem;">
            <div class="info-field">
                <label for="description">Description</label>
                <textarea name="description" id="description" rows="3" style="min-height:110px; padding:0.9rem; border-radius:8px;" placeholder="Describe corrective action" required></textarea>
            </div>
         </div>

        <!-- Changed form actions: notice left, button right -->
        <div class="form-actions">
            <div class="notice"><?php if ($notice) echo htmlspecialchars($notice); ?></div>
            <button type="submit" name="add_action" class="add-action-btn">Add Action</button>
         </div>
      </form>

      <h3 style="margin-top:1.25rem;">Existing Actions</h3>
      <table class="actions-table">
         <thead>
            <tr><th>Date</th><th>Role</th><th>Person</th><th>Description</th><th>Severity</th><th>Due</th><th>Status</th><th>Actions</th></tr>
         </thead>
         <tbody>
            <?php
            // Support both mysqli_result and array fallback
            $has_actions = false;
            if ($actions instanceof mysqli_result) {
                $has_actions = ($actions->num_rows > 0);
            } elseif (is_array($actions)) {
                $has_actions = (count($actions) > 0);
            }

            if ($has_actions):
                if ($actions instanceof mysqli_result):
                    while ($a = $actions->fetch_assoc()):
                        // ...existing per-row preparation code ...
                        // find email for contact if exists
                        $email = '';
                        $roleTable = ($a['target_role'] === 'agent') ? 'agents2' : (($a['target_role'] === 'auditor') ? 'auditors2' : (($a['target_role'] === 'supervisor') ? 'supervisors' : 'data_analysts'));
                        $concatField = ($a['target_role']=='agent') ? "CONCAT(agent_firstname, ' ', agent_lastname)" :
                                       (($a['target_role']=='auditor') ? "CONCAT(auditor_firstname, ' ', auditor_lastname)" :
                                       (($a['target_role']=='supervisor') ? "CONCAT(supervisor_firstname, ' ', supervisor_lastname)" :
                                       "CONCAT(data_analyst_firstname, ' ', data_analyst_lastname)"));
                        $emailStmt = $conn->prepare("SELECT email FROM $roleTable WHERE $concatField = ? LIMIT 1");
                        if ($emailStmt) {
                            $emailStmt->bind_param("s", $a['target_name']);
                            $emailStmt->execute();
                            $er = $emailStmt->get_result();
                            if ($er && $re = $er->fetch_assoc()) $email = $re['email'] ?? '';
                            $emailStmt->close();
                        }
                        $mailto = $email ? "mailto:" . rawurlencode($email) . "?subject=" . rawurlencode("Corrective action: " . $a['target_name']) : '';
                        $coachLink = "AdminConductCoach.php?agent=" . urlencode($a['target_name']);
            ?>
            <tr>
               <td><?php echo htmlspecialchars($a['created_at']); ?></td>
               <td><?php echo htmlspecialchars(ucfirst(str_replace('_',' ',$a['target_role']))); ?></td>
               <td><?php echo htmlspecialchars($a['target_name']); ?></td>
               <td><?php echo nl2br(htmlspecialchars($a['description'])); ?></td>
               <td><?php echo htmlspecialchars($a['severity']); ?></td>
               <td><?php echo htmlspecialchars($a['due_date']); ?></td>
               <td><?php echo htmlspecialchars($a['status']); ?></td>
               <td>
                  <div class="actions-cell">
                     <?php if ($email): ?>
                        <a href="<?php echo htmlspecialchars($mailto); ?>" class="contact-link" title="Email <?php echo htmlspecialchars($a['target_name']); ?>"><i class="ri-mail-line"></i></a>
                     <?php else: ?>
                        <span class="contact-disabled" title="No email on file"><i class="ri-mail-line"></i></span>
                     <?php endif; ?>
                     <a href="<?php echo htmlspecialchars($coachLink); ?>" class="small-btn" style="margin-left:8px;">Schedule Coaching</a>
                     <?php if (strtolower($a['status']) !== 'completed'): ?>
                        <a href="?resolve=<?php echo $a['id']; ?>" class="small-btn secondary" style="margin-left:8px;">Mark Completed</a>
                     <?php else: ?>
                        <span style="margin-left:8px; font-weight:700; color:#43a047;">Completed</span>
                     <?php endif; ?>
                  </div>
               </td>
            </tr>
            <?php
                    endwhile;
                else:
                    foreach ($actions as $a):
                        // same per-row rendering for array fallback...
            ?>
            <tr>
               <td><?php echo htmlspecialchars($a['created_at']); ?></td>
               <td><?php echo htmlspecialchars(ucfirst(str_replace('_',' ',$a['target_role']))); ?></td>
               <td><?php echo htmlspecialchars($a['target_name']); ?></td>
               <td><?php echo nl2br(htmlspecialchars($a['description'])); ?></td>
               <td><?php echo htmlspecialchars($a['severity']); ?></td>
               <td><?php echo htmlspecialchars($a['due_date']); ?></td>
               <td><?php echo htmlspecialchars($a['status']); ?></td>
               <td>
                  <div class="actions-cell">
                     <?php
                     // find email for contact if exists (same as above)
                     $email = '';
                     $roleTable = ($a['target_role'] === 'agent') ? 'agents2' : (($a['target_role'] === 'auditor') ? 'auditors2' : (($a['target_role'] === 'supervisor') ? 'supervisors' : 'data_analysts'));
                     $concatField = ($a['target_role']=='agent') ? "CONCAT(agent_firstname, ' ', agent_lastname)" :
                                    (($a['target_role']=='auditor') ? "CONCAT(auditor_firstname, ' ', auditor_lastname)" :
                                    (($a['target_role']=='supervisor') ? "CONCAT(supervisor_firstname, ' ', supervisor_lastname)" :
                                    "CONCAT(data_analyst_firstname, ' ', data_analyst_lastname)"));
                     $emailStmt = $conn->prepare("SELECT email FROM $roleTable WHERE $concatField = ? LIMIT 1");
                     if ($emailStmt) {
                         $emailStmt->bind_param("s", $a['target_name']);
                         $emailStmt->execute();
                         $er = $emailStmt->get_result();
                         if ($er && $re = $er->fetch_assoc()) $email = $re['email'] ?? '';
                         $emailStmt->close();
                     }
                     $mailto = $email ? "mailto:" . rawurlencode($email) . "?subject=" . rawurlencode("Corrective action: " . $a['target_name']) : '';
                     $coachLink = "AdminConductCoach.php?agent=" . urlencode($a['target_name']);
                     ?>
                     <?php if ($email): ?>
                        <a href="<?php echo htmlspecialchars($mailto); ?>" class="contact-link" title="Email <?php echo htmlspecialchars($a['target_name']); ?>"><i class="ri-mail-line"></i></a>
                     <?php else: ?>
                        <span class="contact-disabled" title="No email on file"><i class="ri-mail-line"></i></span>
                     <?php endif; ?>
                     <a href="<?php echo htmlspecialchars($coachLink); ?>" class="small-btn" style="margin-left:8px;">Schedule Coaching</a>
                     <?php if (strtolower($a['status']) !== 'completed'): ?>
                        <a href="?resolve=<?php echo $a['id']; ?>" class="small-btn secondary" style="margin-left:8px;">Mark Completed</a>
                     <?php else: ?>
                        <span style="margin-left:8px; font-weight:700; color:#43a047;">Completed</span>
                     <?php endif; ?>
                  </div>
               </td>
            </tr>
            <?php
                    endforeach;
                endif;
            else:
            ?>
            <tr><td colspan="8" style="color:#888;">No corrective actions found.</td></tr>
            <?php endif; ?>
         </tbody>
      </table>
   </div>
</main>

<script>
const agents = <?php $alist=[]; if($agents){while($r=$agents->fetch_assoc()) $alist[]=$r['name'];} echo json_encode($alist); ?>;
const auditors = <?php $alist=[]; if($auditors){while($r=$auditors->fetch_assoc()) $alist[]=$r['name'];} echo json_encode($alist); ?>;
const supervisors = <?php $alist=[]; if($supervisors){while($r=$supervisors->fetch_assoc()) $alist[]=$r['name'];} echo json_encode($alist); ?>;
const dataAnalysts = <?php $alist=[]; if($data_analysts){while($r=$data_analysts->fetch_assoc()) $alist[]=$r['name'];} echo json_encode($alist); ?>;
function onRoleChange(role) {
    const sel = document.getElementById('target_name');
    sel.innerHTML = '<option value="">Select Person</option>';
    let list = [];
    if (role === 'agent') list = agents;
    if (role === 'auditor') list = auditors;
    if (role === 'supervisor') list = supervisors;
    if (role === 'data_analyst') list = dataAnalysts;
    list.forEach(name => {
        const o = document.createElement('option');
        o.value = name;
        o.textContent = name;
        sel.appendChild(o);
    });
}
</script>

</body>
</html>
