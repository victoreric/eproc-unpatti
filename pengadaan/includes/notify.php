<?php
// function createNotification($conn, $user_id, $type, $message)
// {
//     $stmt = $conn->prepare("
//         INSERT INTO notifications (user_id, type, message)
//         VALUES (?, ?, ?)
//     ");
//     $stmt->bind_param("iss", $user_id, $type, $message);
//     $stmt->execute();
//     $stmt->close();
// }



// includes/notify.php

// function createNotification(PDO $pdo, $user_id, $type, $message)
// {
//     $sql = "INSERT INTO notifications (user_id, type, message, is_read, created_at)
//             VALUES (?, ?, ?, 0, NOW())";

//     $stmt = $pdo->prepare($sql);
//     $stmt->execute([$user_id, $type, $message]);

//     return true;
// }


function createNotification($pdo, $user_id, $type, $message, $company_id = null, $section = null)
{
    $sql = "INSERT INTO notifications (user_id, type, message, company_id, section, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $user_id,
        $type,        // update_identitas, update_dokumen, dll
        $message,
        $company_id,  // ID perusahaan
        $section      // identitas, dokumen, akta, dll
    ]);
}
