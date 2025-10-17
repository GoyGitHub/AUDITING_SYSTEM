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

// Auditors list
$auditors_query = "SELECT id, auditor_firstname, auditor_lastname FROM auditors2";
$auditors_result = mysqli_query($conn, $auditors_query);

// Agents list
$agents_query = "SELECT id, agent_firstname, agent_lastname FROM agents2";
$agents_result = mysqli_query($conn, $agents_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Agent Audit Sheet</title>
    <!-- Remix Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css" />
    <!-- Custom Styles -->
    <link rel="stylesheet" href="../../assets/css/styles.css" />

    <style>
        /* --- Notification --- */
        .top-notification {
            position: fixed;
            top: 0; /* stick at very top of the page */
            left: 0;
            width: 100%;
            background: #4CAF50; /* success green */
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

        /* --- Main Redesign --- */
        body {
            font-family: 'Nunito Sans', sans-serif;
            background: #fff; /* changed to white */
            margin: 0;
            padding: 0;
        }

        .main.container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 2.5rem 2rem 2rem 2rem;
            max-width: 1500px;
            margin: 90px auto 0 auto;
            /* Add left margin to offset sidebar width */
            margin-left: 320px;
        }

        h1 {
            text-align: left;
            font-size: 2.2rem;
            margin-bottom: 1.5rem;
            font-weight: 800;
            color: #1a237e;
            letter-spacing: 1px;
            align-self: flex-start;
            margin-left: 60px; /* move title more to the right */
        }

        .audit-card {
            background: #fff;
            border-radius: 1.2rem;
            box-shadow: 0 8px 32px rgba(26,35,126,0.10), 0 1.5px 4px rgba(0,0,0,0.04);
            padding: 2.5rem 2rem 2rem 2rem;
            margin-bottom: 2.5rem;
            width: 100%;
            max-width: 1250px; /* even wider */
        }

        .info-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            margin-bottom: 1.5rem;
            background: #f5f7fb;
            padding: 1.5rem 1rem 1rem 1rem;
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(26,35,126,0.04);
            width: 100%;
        }

        .info-field {
            flex: 1 1 240px;
            min-width: 220px;
            margin-bottom: 0.5rem;
            position: relative;
        }

        .info-field label {
            font-weight: 700;
            margin-bottom: 0.4rem;
            display: block;
            color: #1a237e;
            font-size: 1.02rem;
        }

        .info-field input, .info-field textarea, .info-field select {
            width: 100%;
            padding: 0.7rem 1rem 0.7rem 2.5rem;
            border: 1.5px solid #c5cae9;
            border-radius: 0.7rem;
            font-size: 1rem;
            background-color: #fff;
            transition: border 0.2s;
            outline: none;
        }

        .info-field input:focus, .info-field select:focus, .info-field textarea:focus {
            border: 1.5px solid #3949ab;
            background: #f0f4ff;
        }

        .info-field textarea {
            min-height: 60px;
            resize: vertical;
        }

        /* --- Icon for Reviewer and Agent Dropdowns --- */
        .info-field.select-icon {
            position: relative;
        }

        .info-field.select-icon select {
            padding-left: 2.5rem !important;
            background-repeat: no-repeat;
            background-position: 0.7rem center;
            background-size: 1.3rem;
        }

        .info-field.select-icon.reviewer select {
            background-image: url('https://cdn.jsdelivr.net/npm/remixicon@4.2.0/icons/User/user-3-fill.svg');
        }

        .info-field.select-icon.agent select {
            background-image: url('https://cdn.jsdelivr.net/npm/remixicon@4.2.0/icons/User/user-2-fill.svg');
        }

        /* --- Custom select arrow --- */
        .info-field select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image:
                url("data:image/svg+xml;charset=US-ASCII,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='8'><polygon points='0,0 12,0 6,8' fill='%233949ab'/></svg>"),
                none;
            background-repeat: no-repeat, no-repeat;
            background-position: right 1rem center, left 0.7rem center;
            background-size: 1rem, 1.3rem;
        }

        /* --- Duration fields --- */
        .duration-fields input {
            padding-left: 1rem;
            padding-right: 0.5rem;
            width: 33%;
        }

        /* --- Table --- */
        .audit-table-container {
            overflow-x: auto;
            margin-left: 0;
            width: 100%;
            margin-bottom: 2rem;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            box-shadow: 0 2px 8px rgba(26,35,126,0.06);
            border-radius: 1rem;
            overflow: hidden;
        }

        th, td {
            padding: 1.1rem 0.7rem;
            text-align: center;
            border-bottom: 1px solid #e3e7f7;
        }

        th {
            background: linear-gradient(90deg, #283593 60%, #5c6bc0 100%);
            color: #fff;
            font-weight: 700;
            font-size: 1.07rem;
            letter-spacing: 0.5px;
        }

        td:first-child {
            text-align: left;
            font-weight: 600;
            color: #283593;
            font-size: 1.01rem;
        }

        .question-number {
            display: inline-block;
            background: #3949ab;
            color: #fff;
            border-radius: 50%;
            width: 1.7em;
            height: 1.7em;
            text-align: center;
            line-height: 1.7em;
            font-size: 1em;
            margin-right: 0.7em;
            font-weight: 700;
        }

        input[type="radio"] {
            transform: scale(1.2);
            cursor: pointer;
            accent-color: #3949ab;
        }

        .submit-btn {
            margin-top: 1.5rem;
            background: linear-gradient(90deg, #283593 60%, #5c6bc0 100%);
            color: white;
            padding: 0.85rem 2.5rem;
            border: none;
            border-radius: 0.7rem;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(26,35,126,0.08);
            transition: background 0.3s;
        }

        .submit-btn:hover {
            background: linear-gradient(90deg, #1a237e 60%, #3949ab 100%);
        }

        /* --- Responsive --- */
        @media (max-width: 900px) {
            .main.container { padding: 1rem; }
            .audit-card { padding: 1.2rem 0.5rem; max-width: 100%; }
            .info-bar { gap: 1rem; }
        }

        @media (max-width: 600px) {
            .info-bar { flex-direction: column; }
            .info-field { min-width: 100%; }
        }
    </style>
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
                    <img src="../../assets/img/perfil.png" alt="image" />
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
                        <a href="AdminAuditForm.php" class="sidebar__link active-link">
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

    <!-- MAIN -->
    <main class="main container" id="main">
        <h1>Agent Audit Sheet</h1>
        <div class="audit-card">
            <form method="POST">
                <div class="info-bar">
                    <!-- Reviewer Dropdown with icon -->
                    <div class="info-field select-icon reviewer">
                        <label>Reviewer:</label>
                        <select name="reviewer_name" required>
                            <option value=""> Select Reviewer</option>
                            <?php while ($row = mysqli_fetch_assoc($auditors_result)) : 
                                $fullname = $row['auditor_firstname'] . " " . $row['auditor_lastname']; ?>
                                <option value="<?php echo htmlspecialchars($fullname); ?>"><?php echo htmlspecialchars($fullname); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <!-- Agent Dropdown with icon -->
                    <div class="info-field select-icon agent">
                        <label>Agent's Name:</label>
                        <select name="agent_name" required>
                            <option value=""> Select Agent</option>
                            <?php while ($row = mysqli_fetch_assoc($agents_result)) : 
                                $fullname = $row['agent_firstname'] . " " . $row['agent_lastname']; ?>
                                <option value="<?php echo htmlspecialchars($fullname); ?>"><?php echo htmlspecialchars($fullname); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="info-field">
                        <label>Status:</label>
                        <input type="text" name="status" list="status-options" required />
                        <datalist id="status-options">
                            <option value="Regular" />
                            <option value="Probationary" />
                            <option value="Trainee" />
                            <option value="Others" />
                        </datalist>
                    </div>
                    <div class="info-field">
                        <label>Date:</label>
                        <input type="date" name="date" id="date" required />
                    </div>
                    <div class="info-field">
                        <label>Week:</label>
                        <input type="text" name="week" id="week" required />
                    </div>
                    <div class="info-field">
                        <label>Time:</label>
                        <input type="time" name="time" required />
                    </div>
                    <div class="info-field">
                        <label>Caller's Name:</label>
                        <input type="text" name="caller_name" required />
                    </div>
                    <div class="info-field duration-fields">
                        <label>Duration:</label>
                        <div style="display: flex; gap: 0.5rem;">
                            <input type="number" name="duration_hours" min="0" max="23" placeholder="Hour/s" required />
                            <input type="number" name="duration_minutes" min="0" max="59" placeholder="Minute/s" required />
                            <input type="number" name="duration_seconds" min="0" max="59" placeholder="Second/s" required />
                        </div>
                    </div>
                    <div class="info-field">
                        <label>MDN:</label>
                        <input type="text" name="mdn" required />
                    </div>
                    <div class="info-field">
                        <label>Account Number:</label>
                        <input type="text" name="account_number" required />
                    </div>
                </div>

                <div class="audit-table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Audit Criteria</th>
                                <th>Yes</th>
                                <th>No</th>
                                <th>N/A</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $questions = [
                                "Adheres to schedule and login time",
                                "Follows proper call handling procedures",
                                "Demonstrates product knowledge",
                                "Maintains professional tone",
                                "Uses appropriate language",
                                "Accurate documentation",
                                "Customer empathy and support",
                                "Problem resolution effectiveness",
                                "Compliance with company policy",
                                "Follows QA guidelines"
                            ];
                            foreach ($questions as $index => $q) :
                                $num = $index + 1; ?>
                                <tr>
                                    <td>
                                        <span class="question-number"><?php echo $num; ?></span>
                                        <?php echo htmlspecialchars($q); ?>
                                    </td>
                                    <td><input type="radio" name="q<?php echo $num; ?>" value="Yes" required /></td>
                                    <td><input type="radio" name="q<?php echo $num; ?>" value="No" /></td>
                                    <td><input type="radio" name="q<?php echo $num; ?>" value="N/A" /></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="info-bar" style="margin-top: 2rem;">
                    <div class="info-field" style="flex: 1 1 100%;">
                        <label>Comments:</label>
                        <textarea name="comment" rows="3"></textarea>
                    </div>
                </div>

                <button type="submit" name="submit" class="submit-btn">Submit Audit</button>
            </form>
        </div>
        <?php
        if (isset($_POST['submit'])) {
            $hours   = (int)$_POST['duration_hours'];
            $minutes = min((int)$_POST['duration_minutes'], 59);
            $seconds = min((int)$_POST['duration_seconds'], 59);
            $duration = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);

            $reviewer = $_POST['reviewer_name'];
            $agent = $_POST['agent_name'];
            $status = $_POST['status'];
            $date = $_POST['date'];
            $week = $_POST['week'];
            $time = $_POST['time'];
            $caller = $_POST['caller_name'];
            $queue = $_POST['queue'];
            $mdn = $_POST['mdn'];
            $account = $_POST['account_number'];
            $comment = $_POST['comment'];

            // Collect responses
            $responses = [];
            for ($i = 1; $i <= 10; $i++) {
                $responses[] = $_POST["q$i"] ?? 'N/A';
            }

            $stmt = $conn->prepare("
                INSERT INTO data_reports 
                (reviewer_name, agent_name, status, date, week, time, caller_name, duration, queue, mdn, account_number, q1, q2, q3, q4, q5, q6, q7, q8, q9, q10, comment)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
            ");

            $stmt->bind_param(
                "ssssssssssssssssssssss",
                $reviewer, $agent, $status, $date, $week, $time, $caller, $duration, $queue, $mdn, $account,
                $responses[0], $responses[1], $responses[2], $responses[3], $responses[4], $responses[5],
                $responses[6], $responses[7], $responses[8], $responses[9], $comment
            );

            if ($stmt->execute()) {
                // After audit submission, handle supervisor comment
                if (!empty($_POST['supervisor_comment'])) {
                    // Get last inserted audit id
                    $audit_id = $conn->insert_id;
                    $supervisor_comment = $_POST['supervisor_comment'];
                    // Append filed-by info (auditor who is the reviewer on this audit)
                    $filedByRole = "Auditor";
                    $filedByName = $reviewer; // reviewer selected on the form
                    $comment_with_filer = $supervisor_comment . " (Filed by: {$filedByRole} - {$filedByName})";

                    $stmt2 = $conn->prepare("INSERT INTO supervisor_comments (audit_id, agent_name, reviewer_name, comment) VALUES (?, ?, ?, ?)");
                    $stmt2->bind_param("isss", $audit_id, $agent, $reviewer, $comment_with_filer);
                    $stmt2->execute();
                    $stmt2->close();
                }
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const n = document.getElementById('notification');
                        n.textContent = 'Audit submitted successfully!';
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
    </main>

    <script>
        document.getElementById("date").addEventListener("change", function() {
            const inputDate = new Date(this.value);

            if (!isNaN(inputDate)) {
                // Get the ISO week number
                const target = new Date(inputDate.valueOf());
                const dayNr = (inputDate.getDay() + 6) % 7; // Make Monday = 0
                target.setDate(target.getDate() - dayNr + 3);
                const firstThursday = target.valueOf();
                target.setMonth(0, 1);
                if (target.getDay() !== 4) {
                    target.setMonth(0, 1 + ((4 - target.getDay()) + 7) % 7);
                }
                const weekNumber = 1 + Math.ceil((firstThursday - target) / 604800000);

                document.getElementById("week").value = "Week " + weekNumber;
            }
        });

        
    </script>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
