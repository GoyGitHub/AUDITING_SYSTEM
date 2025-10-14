<?php
include(__DIR__ . '/../database/dbconnection.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM data_reports WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // ✅ redirect correctly
        header("Location: ../pages/admin/AdminAuditDatabank.php?msg=deleted");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}
$conn->close();
?>
