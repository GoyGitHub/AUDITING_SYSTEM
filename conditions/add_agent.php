<?php
include('../../database/dbconnection.php');

if (isset($_POST['add_agent'])) {
    $agent_lastname   = trim($_POST['agent_lastname']);
    $agent_firstname  = trim($_POST['agent_firstname']); // ✅ double T
    $birthday         = trim($_POST['agent_birthday']);
    $email            = trim($_POST['agent_email']);
    $team             = trim($_POST['team']);

    if (empty($agent_lastname) || empty($agent_firstname) || empty($birthday) || empty($email) || empty($team)) {
        echo "<script>alert('⚠️ Please fill in all agent fields.');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO agents 
            (agent_lastname, agent_firsttname, birthday, email, team) 
            VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $agent_lastname, $agent_firstname, $birthday, $email, $team);

        if ($stmt->execute()) {
            echo "<script>alert('✅ Agent added successfully!');</script>";
        } else {
            echo "<script>alert('❌ Error: " . addslashes($conn->error) . "');</script>";
        }
        $stmt->close();
    }
}
$conn->close();
?>
