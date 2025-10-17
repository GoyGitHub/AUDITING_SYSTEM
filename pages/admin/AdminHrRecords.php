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
         color: #0d1b3d;
      }
      th, td {
         padding: 12px 15px;
         border-bottom: 1px solid #eee;
         text-align: left;
         vertical-align: middle;
      }
      thead {
         background-color: #f9f9f9;
      }
      tr:hover {
         background: #f5faff;
      }
      .role-auditor {
         color: #007bff; /* blue */
         font-weight: bold;
      }
      .role-agent {
         color: #28a745; /* green */
         font-weight: bold;
      }
      .role-supervisor {
    color: #ff9800; /* orange */
    font-weight: bold;
}

.role-data-analyst {
    color: #9c27b0; /* purple */
    font-weight: bold;
}
      .search-bar {
         margin-bottom: 18px;
         display: flex;
         gap: 12px;
         align-items: center;
      }
      .search-bar input, .search-bar select {
         padding: 7px 12px;
         border: 1px solid #ccc;
         border-radius: 6px;
         font-size: 1rem;
      }
      .export-btn {
         background: #007bff;
         color: #fff;
         border: none;
         padding: 8px 18px;
         border-radius: 6px;
         cursor: pointer;
         font-weight: bold;
         margin-left: auto;
      }
      .export-btn:hover {
         background: #0056b3;
      }
      th.sortable {
         cursor: pointer;
         user-select: none;
      }
      .pagination {
         margin-top: 16px;
         display: flex;
         justify-content: flex-end;
         gap: 6px;
      }
      .pagination button {
         padding: 4px 10px;
         border: 1px solid #007bff;
         background: #fff;
         color: #007bff;
         border-radius: 4px;
         cursor: pointer;
      }
      .pagination button.active, .pagination button:hover {
         background: #007bff;
         color: #fff;
      }
   </style>

   <title>UCX Data Bank - HR Records</title>
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
               <a href="AdminHrRecords.php" class="sidebar__link active-link">
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

<!--=============== MAIN CONTENT ===============-->
<main class="main container" id="main">
   <h1 style="margin-bottom: 20px; font-family:'Nunito Sans', sans-serif; color:#0d1b3d;">HR Records</h1>

   <!-- Search/Filter Bar and Export Button -->
   <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Search ">
      <select id="roleFilter">
         <option value="">All Roles</option>
         <option value="Auditor">Auditor</option>
         <option value="Agent">Agent</option>
         <option value="Supervisor">Supervisor</option>
         <option value="Data Analyst">Data Analyst</option>
      </select>
      <button class="export-btn" id="exportBtn">Export to CSV</button>
   </div>

   <div class="table-container" style="overflow-x:auto; background:#fff; border-radius:12px; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 24px;">
      <table id="hrTable">
         <thead>
            <tr>
               <th class="sortable" data-sort="name">Name &#8597;</th>
               <th class="sortable" data-sort="role">Role &#8597;</th>
               <th class="sortable" data-sort="dept">Department / Team &#8597;</th>
               <th class="sortable" data-sort="email">Email &#8597;</th>
               <th class="sortable" data-sort="birthday">Birthday &#8597;</th>
            </tr>
         </thead>
         <tbody id="hrTableBody">
<?php
// Collect all records into a PHP array for easier JS handling
$records = [];

// === Fetch Auditors ===
$auditorQuery = $conn->query("SELECT auditor_firstname, auditor_lastname, birthday, email, department 
                              FROM auditors2 ORDER BY auditor_firstname ASC");
if ($auditorQuery && $auditorQuery->num_rows > 0) {
   while ($row = $auditorQuery->fetch_assoc()) {
      $records[] = [
         'name' => $row['auditor_firstname'] . ' ' . $row['auditor_lastname'],
         'role' => 'Auditor',
         'role_class' => 'role-auditor',
         'dept' => $row['department'],
         'email' => $row['email'],
         'birthday' => $row['birthday']
      ];
   }
}

// === Fetch Agents ===
$agentQuery = $conn->query("SELECT agent_firstname, agent_lastname, birthday, email, team 
                            FROM agents ORDER BY agent_firstname ASC");
if ($agentQuery && $agentQuery->num_rows > 0) {
   while ($row = $agentQuery->fetch_assoc()) {
      $records[] = [
         'name' => $row['agent_firstname'] . ' ' . $row['agent_lastname'],
         'role' => 'Agent',
         'role_class' => 'role-agent',
         'dept' => $row['team'],
         'email' => $row['email'],
         'birthday' => $row['birthday']
      ];
   }
}

// === Fetch Supervisors ===
$supervisorQuery = $conn->query("SELECT supervisor_firstname, supervisor_lastname, birthday, email, team 
                                 FROM supervisors ORDER BY supervisor_firstname ASC");
if ($supervisorQuery && $supervisorQuery->num_rows > 0) {
   while ($row = $supervisorQuery->fetch_assoc()) {
      $records[] = [
         'name' => $row['supervisor_firstname'] . ' ' . $row['supervisor_lastname'],
         'role' => 'Supervisor',
         'role_class' => 'role-supervisor',
         'dept' => $row['team'],
         'email' => $row['email'],
         'birthday' => $row['birthday']
      ];
   }
}

// === Fetch Data Analysts ===
$dataAnalystQuery = $conn->query("SELECT data_analyst_firstname, data_analyst_lastname, birthday, email, department 
                                  FROM data_analysts ORDER BY data_analyst_firstname ASC");
if ($dataAnalystQuery && $dataAnalystQuery->num_rows > 0) {
   while ($row = $dataAnalystQuery->fetch_assoc()) {
      $records[] = [
         'name' => $row['data_analyst_firstname'] . ' ' . $row['data_analyst_lastname'],
         'role' => 'Data Analyst',
         'role_class' => 'role-data-analyst',
         'dept' => $row['department'],
         'email' => $row['email'],
         'birthday' => $row['birthday']
      ];
   }
}

// Output as JSON for JS
echo "<script>const hrRecords = " . json_encode($records) . ";</script>";
?>
         </tbody>
      </table>
      <div class="pagination" id="pagination"></div>
   </div>
</main>

<!--=============== MAIN JS ===============-->
<script src="../../assets/js/main.js"></script>
<script>
// --- HR Records Table Features ---

const tableBody = document.getElementById('hrTableBody');
const searchInput = document.getElementById('searchInput');
const roleFilter = document.getElementById('roleFilter');
const exportBtn = document.getElementById('exportBtn');
const paginationDiv = document.getElementById('pagination');
const pageSize = 10;
let currentPage = 1;
let sortKey = '';
let sortAsc = true;

// Render table rows
function renderTable(records) {
   tableBody.innerHTML = '';
   records.forEach(rec => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
         <td>${rec.name}</td>
         <td class="${rec.role_class}">${rec.role}</td>
         <td>${rec.dept}</td>
         <td>${rec.email}</td>
         <td>${rec.birthday}</td>
      `;
      tableBody.appendChild(tr);
   });
}

// Filter, sort, and paginate
function getFilteredSortedRecords() {
   let filtered = hrRecords.filter(rec => {
      const search = searchInput.value.toLowerCase();
      const matchesSearch = !search || (
         rec.name.toLowerCase().includes(search) ||
         rec.email.toLowerCase().includes(search) ||
         rec.dept.toLowerCase().includes(search)
      );
      const matchesRole = !roleFilter.value || rec.role === roleFilter.value;
      return matchesSearch && matchesRole;
   });

   if (sortKey) {
      filtered.sort((a, b) => {
         let v1 = a[sortKey] || '';
         let v2 = b[sortKey] || '';
         if (sortKey === 'birthday') {
            v1 = new Date(v1);
            v2 = new Date(v2);
         } else {
            v1 = v1.toLowerCase();
            v2 = v2.toLowerCase();
         }
         if (v1 < v2) return sortAsc ? -1 : 1;
         if (v1 > v2) return sortAsc ? 1 : -1;
         return 0;
      });
   }
   return filtered;
}

// Pagination controls
function renderPagination(total, page) {
   paginationDiv.innerHTML = '';
   const pageCount = Math.ceil(total / pageSize);
   for (let i = 1; i <= pageCount; i++) {
      const btn = document.createElement('button');
      btn.textContent = i;
      if (i === page) btn.classList.add('active');
      btn.onclick = () => {
         currentPage = i;
         updateTable();
      };
      paginationDiv.appendChild(btn);
   }
}

// Update table with filters, sorting, and pagination
function updateTable() {
   const filtered = getFilteredSortedRecords();
   const start = (currentPage - 1) * pageSize;
   const paginated = filtered.slice(start, start + pageSize);
   renderTable(paginated);
   renderPagination(filtered.length, currentPage);
}
searchInput.addEventListener('input', () => { currentPage = 1; updateTable(); });
roleFilter.addEventListener('change', () => { currentPage = 1; updateTable(); });

// Sorting
document.querySelectorAll('th.sortable').forEach(th => {
   th.addEventListener('click', () => {
      const keyMap = { name: 'name', role: 'role', dept: 'dept', email: 'email', birthday: 'birthday' };
      const key = keyMap[th.dataset.sort];
      if (sortKey === key) sortAsc = !sortAsc;
      else { sortKey = key; sortAsc = true; }
      updateTable();
   });
});

// Export to CSV
exportBtn.addEventListener('click', () => {
   const filtered = getFilteredSortedRecords();
   let csv = 'Name,Role,Department/Team,Email,Birthday\n';
   filtered.forEach(rec => {
      csv += `"${rec.name}","${rec.role}","${rec.dept}","${rec.email}","${rec.birthday}"\n`;
   });
   const blob = new Blob([csv], { type: 'text/csv' });
   const url = URL.createObjectURL(blob);
   const a = document.createElement('a');
   a.href = url;
   a.download = 'hr_records.csv';
   document.body.appendChild(a);
   a.click();
   document.body.removeChild(a);
   URL.revokeObjectURL(url);
});

// Initial render
updateTable();
</script>
</body>
</html>
