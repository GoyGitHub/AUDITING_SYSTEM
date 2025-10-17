<?php
include('../../database/dbconnection.php');

if (isset($_POST['add_auditor'])) {
    $auditor_firstname = trim($_POST['auditor_firstname']);
    $auditor_lastname  = trim($_POST['auditor_lastname']); // ✅ double T
    $birthday          = trim($_POST['auditor_birthday']);
    $email             = trim($_POST['auditor_email']);
    $department        = trim($_POST['department']);

    if (empty($auditor_firstname) || empty($auditor_lastname) || empty($birthday) || empty($email) || empty($department)) {
        echo "<script>alert('⚠️ Please fill in all auditor fields.');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO auditors2 
            (auditor_firstname, auditor_lasttname, birthday, email, department) 
            VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $auditor_firstname, $auditor_lastname, $birthday, $email, $department);

        if ($stmt->execute()) {
            echo "<script>alert('✅ Auditor added successfully!');</script>";
        } else {
            echo "<script>alert('❌ Error: " . addslashes($conn->error) . "');</script>";
        }
        $stmt->close();
    }
}
$conn->close();
?>
