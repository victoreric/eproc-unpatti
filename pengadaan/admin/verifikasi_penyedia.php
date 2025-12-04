<?php
// Error reporting untuk debugging (matikan di production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once "../config/db.php";

// Pastikan parameter company_id tersedia
if (!isset($_GET['company_id'])) {
    echo "<script>alert('Parameter perusahaan tidak ditemukan!'); window.location='list_perusahaan.php';</script>";
    exit;
}

$company_id = $_GET['company_id'];

// Ambil data perusahaan + user pemilik
$stmt = $pdo->prepare("
    SELECT c.*, u.id AS user_id, u.email, u.full_name 
    FROM companies c
    JOIN users u ON c.user_id = u.id
    WHERE c.id = ?
    LIMIT 1
");
$stmt->execute([$company_id]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika perusahaan tidak ditemukan
if (!$company) {
    echo "<script>alert('Data perusahaan tidak ditemukan!'); window.location='list_perusahaan.php';</script>";
    exit;
}

/* =========================================================
   HANDLE POST (Update Verifikasi & Keputusan DPT)
   ========================================================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $section = $_POST['section'] ?? 'identitas';

    /* -----------------------------
       VERIFIKASI IDENTITAS
       ----------------------------- */
    if ($section === 'identitas') {
        $new_status = $_POST['status'] ?? '';
        $notes      = $_POST['notes'] ?? '';

        $allowed = ['draft', 'submitted', 'under_review', 'verified', 'rejected'];

        if (!in_array($new_status, $allowed, true)) {
            header("Location: verifikasi_penyedia.php?company_id={$company_id}&tab=identitas&msg=" . urlencode("Status tidak valid") . "&type=danger");
            exit;
        }

        $stmt = $pdo->prepare("
            UPDATE companies
            SET status = :status,
                notes = :notes,
                verified_by = :verified_by,
                verified_at = NOW(),
                updated_at = NOW()
            WHERE id = :id
        ");

        $stmt->execute([
            ':status'      => $new_status,
            ':notes'       => $notes,
            ':verified_by' => $_SESSION['user_id'] ?? null,
            ':id'          => $company_id
        ]);

        // Notifikasi untuk identitas
        $msg_for_company  = "Status verifikasi identitas perusahaan Anda telah diperbarui menjadi: ";
        $msg_for_company .= strtoupper($new_status) . ". ";
        if (!empty($notes)) {
            $msg_for_company .= "Catatan admin: " . $notes;
        }

        $insertNotif = $pdo->prepare("
            INSERT INTO notifications
            (user_id, company_id, section, type, message, is_read, created_at)
            VALUES
            (:uid, :company_id, 'identitas', :type, :msg, 0, NOW())
        ");
        $insertNotif->execute([
            ':uid'        => $company['user_id'],
            ':company_id' => $company_id,
            ':type'       => 'info',
            ':msg'        => $msg_for_company
        ]);

        header("Location: verifikasi_penyedia.php?company_id={$company_id}&tab=identitas&msg=" . urlencode("Status identitas berhasil diperbarui") . "&type=success");
        exit;
    }

    /* -----------------------------
       VERIFIKASI ALAMAT
       ----------------------------- */
    if ($section === 'alamat') {
        $stmtAddrCheck = $pdo->prepare("SELECT * FROM address WHERE user_id = ? LIMIT 1");
        $stmtAddrCheck->execute([$company['user_id']]);
        $addrRow = $stmtAddrCheck->fetch(PDO::FETCH_ASSOC);

        if (!$addrRow) {
            header("Location: verifikasi_penyedia.php?company_id={$company_id}&tab=alamat&msg=" . urlencode("Alamat belum diisi oleh perusahaan") . "&type=danger");
            exit;
        }

        $verif_status = $_POST['verif_status'] ?? '0';
        $notes_alamat = $_POST['notes'] ?? '';

        if ($verif_status === '1') {
            $stmtUpd = $pdo->prepare("
                UPDATE address
                SET notes = :notes,
                    verified_by = :verified_by,
                    verified_at = NOW(),
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmtUpd->execute([
                ':notes'       => $notes_alamat,
                ':verified_by' => $_SESSION['user_id'] ?? null,
                ':id'          => $addrRow['id']
            ]);
        } else {
            $stmtUpd = $pdo->prepare("
                UPDATE address
                SET notes = :notes,
                    verified_by = NULL,
                    verified_at = NULL,
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmtUpd->execute([
                ':notes' => $notes_alamat,
                ':id'    => $addrRow['id']
            ]);
        }

        $msg_for_company  = "Status verifikasi alamat perusahaan Anda telah diperbarui menjadi: ";
        $msg_for_company .= ($verif_status === '1') ? "TERVERIFIKASI. " : "BELUM DIVERIFIKASI. ";
        if (!empty($notes_alamat)) {
            $msg_for_company .= "Catatan admin: " . $notes_alamat;
        }

        $insertNotif = $pdo->prepare("
            INSERT INTO notifications
            (user_id, company_id, section, type, message, is_read, created_at)
            VALUES
            (:uid, :company_id, 'alamat', :type, :msg, 0, NOW())
        ");
        $insertNotif->execute([
            ':uid'        => $company['user_id'],
            ':company_id' => $company_id,
            ':type'       => 'info',
            ':msg'        => $msg_for_company
        ]);

        header("Location: verifikasi_penyedia.php?company_id={$company_id}&tab=alamat&msg=" . urlencode("Verifikasi alamat berhasil diperbarui") . "&type=success");
        exit;
    }

    /* -----------------------------
       VERIFIKASI IJIN USAHA
       ----------------------------- */
    if ($section === 'ijin_usaha') {

        $stmtIU = $pdo->prepare("SELECT * FROM ijin_usaha WHERE user_id = ? LIMIT 1");
        $stmtIU->execute([$company['user_id']]);
        $ijinRow = $stmtIU->fetch(PDO::FETCH_ASSOC);

        if (!$ijinRow) {
            header("Location: verifikasi_penyedia.php?company_id={$company_id}&tab=ijinusaha&msg=" . urlencode("Ijin usaha belum diisi oleh perusahaan") . "&type=danger");
            exit;
        }

        $verif_status = $_POST['verif_status'] ?? '0';
        $notes_ijin   = $_POST['notes'] ?? '';

        if ($verif_status === '1') {
            $stmtUpd = $pdo->prepare("
                UPDATE ijin_usaha
                SET notes = :notes,
                    verified_by = :verified_by,
                    verified_at = NOW(),
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmtUpd->execute([
                ':notes'       => $notes_ijin,
                ':verified_by' => $_SESSION['user_id'] ?? null,
                ':id'          => $ijinRow['id']
            ]);
        } else {
            $stmtUpd = $pdo->prepare("
                UPDATE ijin_usaha
                SET notes = :notes,
                    verified_by = NULL,
                    verified_at = NULL,
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmtUpd->execute([
                ':notes' => $notes_ijin,
                ':id'    => $ijinRow['id']
            ]);
        }

        $msg_for_company  = "Status verifikasi ijin usaha perusahaan Anda telah diperbarui menjadi: ";
        $msg_for_company .= ($verif_status === '1') ? "TERVERIFIKASI. " : "BELUM DIVERIFIKASI. ";
        if (!empty($notes_ijin)) {
            $msg_for_company .= "Catatan admin: " . $notes_ijin;
        }

        $insertNotif = $pdo->prepare("
            INSERT INTO notifications
            (user_id, company_id, section, type, message, is_read, created_at)
            VALUES
            (:uid, :company_id, 'ijin_usaha', :type, :msg, 0, NOW())
        ");
        $insertNotif->execute([
            ':uid'        => $company['user_id'],
            ':company_id' => $company_id,
            ':type'       => 'info',
            ':msg'        => $msg_for_company
        ]);

        header("Location: verifikasi_penyedia.php?company_id={$company_id}&tab=ijinusaha&msg=" . urlencode("Verifikasi ijin usaha berhasil diperbarui") . "&type=success");
        exit;
    }

    /* -----------------------------
       VERIFIKASI AKTA PERUSAHAAN
       ----------------------------- */
    if ($section === 'akta') {

        $stmtAkta = $pdo->prepare("SELECT * FROM akta_perusahaan WHERE user_id = ? LIMIT 1");
        $stmtAkta->execute([$company['user_id']]);
        $aktaRow = $stmtAkta->fetch(PDO::FETCH_ASSOC);

        if (!$aktaRow) {
            header("Location: verifikasi_penyedia.php?company_id={$company_id}&tab=akta&msg=" . urlencode("Data akta perusahaan belum diisi oleh perusahaan") . "&type=danger");
            exit;
        }

        $verif_status = $_POST['verif_status'] ?? '0';
        $notes_akta   = $_POST['notes'] ?? '';

        if ($verif_status === '1') {
            $stmtUpd = $pdo->prepare("
                UPDATE akta_perusahaan
                SET notes = :notes,
                    verified_by = :verified_by,
                    verified_at = NOW(),
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmtUpd->execute([
                ':notes'       => $notes_akta,
                ':verified_by' => $_SESSION['user_id'] ?? null,
                ':id'          => $aktaRow['id']
            ]);
        } else {
            $stmtUpd = $pdo->prepare("
                UPDATE akta_perusahaan
                SET notes = :notes,
                    verified_by = NULL,
                    verified_at = NULL,
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmtUpd->execute([
                ':notes' => $notes_akta,
                ':id'    => $aktaRow['id']
            ]);
        }

        $msg_for_company  = "Status verifikasi akta perusahaan Anda telah diperbarui menjadi: ";
        $msg_for_company .= ($verif_status === '1') ? "TERVERIFIKASI. " : "BELUM DIVERIFIKASI. ";
        if (!empty($notes_akta)) {
            $msg_for_company .= "Catatan admin: " . $notes_akta;
        }

        $insertNotif = $pdo->prepare("
            INSERT INTO notifications
            (user_id, company_id, section, type, message, is_read, created_at)
            VALUES
            (:uid, :company_id, 'akta', :type, :msg, 0, NOW())
        ");
        $insertNotif->execute([
            ':uid'        => $company['user_id'],
            ':company_id' => $company_id,
            ':type'       => 'info',
            ':msg'        => $msg_for_company
        ]);

        header("Location: verifikasi_penyedia.php?company_id={$company_id}&tab=akta&msg=" . urlencode("Verifikasi akta perusahaan berhasil diperbarui") . "&type=success");
        exit;
    }

    /* -----------------------------
       VERIFIKASI KEUANGAN
       ----------------------------- */
    if ($section === 'keuangan') {

        $stmtKeu = $pdo->prepare("SELECT * FROM keuangan WHERE user_id = ? LIMIT 1");
        $stmtKeu->execute([$company['user_id']]);
        $keuRow = $stmtKeu->fetch(PDO::FETCH_ASSOC);

        if (!$keuRow) {
            header("Location: verifikasi_penyedia.php?company_id={$company_id}&tab=keuangan&msg=" . urlencode("Data keuangan belum diisi oleh perusahaan") . "&type=danger");
            exit;
        }

        $verif_status = $_POST['verif_status'] ?? '0';
        $notes_keu    = $_POST['notes'] ?? '';

        if ($verif_status === '1') {
            $stmtUpd = $pdo->prepare("
                UPDATE keuangan
                SET notes = :notes,
                    verified_by = :verified_by,
                    verified_at = NOW(),
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmtUpd->execute([
                ':notes'       => $notes_keu,
                ':verified_by' => $_SESSION['user_id'] ?? null,
                ':id'          => $keuRow['id']
            ]);
        } else {
            $stmtUpd = $pdo->prepare("
                UPDATE keuangan
                SET notes = :notes,
                    verified_by = NULL,
                    verified_at = NULL,
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmtUpd->execute([
                ':notes' => $notes_keu,
                ':id'    => $keuRow['id']
            ]);
        }

        $msg_for_company  = "Status verifikasi keuangan perusahaan Anda telah diperbarui menjadi: ";
        $msg_for_company .= ($verif_status === '1') ? "TERVERIFIKASI. " : "BELUM DIVERIFIKASI. ";
        if (!empty($notes_keu)) {
            $msg_for_company .= "Catatan admin: " . $notes_keu;
        }

        $insertNotif = $pdo->prepare("
            INSERT INTO notifications
            (user_id, company_id, section, type, message, is_read, created_at)
            VALUES
            (:uid, :company_id, 'keuangan', :type, :msg, 0, NOW())
        ");
        $insertNotif->execute([
            ':uid'        => $company['user_id'],
            ':company_id' => $company_id,
            ':type'       => 'info',
            ':msg'        => $msg_for_company
        ]);

        header("Location: verifikasi_penyedia.php?company_id={$company_id}&tab=keuangan&msg=" . urlencode("Verifikasi keuangan berhasil diperbarui") . "&type=success");
        exit;
    }

    /* -----------------------------
       VERIFIKASI PAJAK
       ----------------------------- */
    if ($section === 'pajak') {

        $stmtPajak = $pdo->prepare("SELECT * FROM pajak WHERE user_id = ? LIMIT 1");
        $stmtPajak->execute([$company['user_id']]);
        $pajakRow = $stmtPajak->fetch(PDO::FETCH_ASSOC);

        if (!$pajakRow) {
            header("Location: verifikasi_penyedia.php?company_id={$company_id}&tab=pajak&msg=" . urlencode("Data pajak belum diisi oleh perusahaan") . "&type=danger");
            exit;
        }

        $verif_status = $_POST['verif_status'] ?? '0';
        $notes_pajak  = $_POST['notes'] ?? '';

        if ($verif_status === '1') {
            $stmtUpd = $pdo->prepare("
                UPDATE pajak
                SET notes = :notes,
                    verified_by = :verified_by,
                    verified_at = NOW(),
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmtUpd->execute([
                ':notes'       => $notes_pajak,
                ':verified_by' => $_SESSION['user_id'] ?? null,
                ':id'          => $pajakRow['id']
            ]);
        } else {
            $stmtUpd = $pdo->prepare("
                UPDATE pajak
                SET notes = :notes,
                    verified_by = NULL,
                    verified_at = NULL,
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmtUpd->execute([
                ':notes' => $notes_pajak,
                ':id'    => $pajakRow['id']
            ]);
        }

        $msg_for_company  = "Status verifikasi pajak perusahaan Anda telah diperbarui menjadi: ";
        $msg_for_company .= ($verif_status === '1') ? "TERVERIFIKASI. " : "BELUM DIVERIFIKASI. ";
        if (!empty($notes_pajak)) {
            $msg_for_company .= "Catatan admin: " . $notes_pajak;
        }

        $insertNotif = $pdo->prepare("
            INSERT INTO notifications
            (user_id, company_id, section, type, message, is_read, created_at)
            VALUES
            (:uid, :company_id, 'pajak', :type, :msg, 0, NOW())
        ");
        $insertNotif->execute([
            ':uid'        => $company['user_id'],
            ':company_id' => $company_id,
            ':type'       => 'info',
            ':msg'        => $msg_for_company
        ]);

        header("Location: verifikasi_penyedia.php?company_id={$company_id}&tab=pajak&msg=" . urlencode("Verifikasi pajak berhasil diperbarui") . "&type=success");
        exit;
    }

    /* -----------------------------
       VERIFIKASI PEMILIK & PENGURUS
       ----------------------------- */
    if ($section === 'pengurus') {

        $stmtPeng = $pdo->prepare("SELECT * FROM pengurus WHERE user_id = ? LIMIT 1");
        $stmtPeng->execute([$company['user_id']]);
        $pengRow = $stmtPeng->fetch(PDO::FETCH_ASSOC);

        if (!$pengRow) {
            header("Location: verifikasi_penyedia.php?company_id={$company_id}&tab=pemilik&msg=" . urlencode("Data pemilik & pengurus belum diisi oleh perusahaan") . "&type=danger");
            exit;
        }

        $verif_status = $_POST['verif_status'] ?? '0';
        $notes_peng   = $_POST['notes'] ?? '';

        if ($verif_status === '1') {
            $stmtUpd = $pdo->prepare("
                UPDATE pengurus
                SET notes = :notes,
                    verified_by = :verified_by,
                    verified_at = NOW(),
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmtUpd->execute([
                ':notes'       => $notes_peng,
                ':verified_by' => $_SESSION['user_id'] ?? null,
                ':id'          => $pengRow['id']
            ]);
        } else {
            $stmtUpd = $pdo->prepare("
                UPDATE pengurus
                SET notes = :notes,
                    verified_by = NULL,
                    verified_at = NULL,
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmtUpd->execute([
                ':notes' => $notes_peng,
                ':id'    => $pengRow['id']
            ]);
        }

        $msg_for_company  = "Status verifikasi pemilik & pengurus perusahaan Anda telah diperbarui menjadi: ";
        $msg_for_company .= ($verif_status === '1') ? "TERVERIFIKASI. " : "BELUM DIVERIFIKASI. ";
        if (!empty($notes_peng)) {
            $msg_for_company .= "Catatan admin: " . $notes_peng;
        }

        $insertNotif = $pdo->prepare("
            INSERT INTO notifications
            (user_id, company_id, section, type, message, is_read, created_at)
            VALUES
            (:uid, :company_id, 'pengurus', :type, :msg, 0, NOW())
        ");
        $insertNotif->execute([
            ':uid'        => $company['user_id'],
            ':company_id' => $company_id,
            ':type'       => 'info',
            ':msg'        => $msg_for_company
        ]);

        header("Location: verifikasi_penyedia.php?company_id={$company_id}&tab=pemilik&msg=" . urlencode("Verifikasi pemilik & pengurus berhasil diperbarui") . "&type=success");
        exit;
    }

    /* -----------------------------
       VERIFIKASI TENAGA AHLI (AGREGAT)
       ----------------------------- */
    // if ($section === 'tenaga') {

    //     $verif_status = $_POST['verif_status'] ?? '0';
    //     $notes_tenaga = $_POST['notes'] ?? '';

    //     // Update semua baris tenaga ahli milik user ini
    //     if ($verif_status === '1') {
    //         $stmtUpd = $pdo->prepare("
    //             UPDATE tenaga_ahli
    //             SET notes = :notes,
    //                 verified_by = :verified_by,
    //                 verified_at = NOW(),
    //                 updated_at = NOW()
    //             WHERE user_id = :uid
    //         ");
    //         $stmtUpd->execute([
    //             ':notes'       => $notes_tenaga,
    //             ':verified_by' => $_SESSION['user_id'] ?? null,
    //             ':uid'         => $company['user_id']
    //         ]);
    //     } else {
    //         $stmtUpd = $pdo->prepare("
    //             UPDATE tenaga_ahli
    //             SET notes = :notes,
    //                 verified_by = NULL,
    //                 verified_at = NULL,
    //                 updated_at = NOW()
    //             WHERE user_id = :uid
    //         ");
    //         $stmtUpd->execute([
    //             ':notes' => $notes_tenaga,
    //             ':uid'   => $company['user_id']
    //         ]);
    //     }

    //     $msg_for_company  = "Status verifikasi tenaga ahli perusahaan Anda telah diperbarui menjadi: ";
    //     $msg_for_company .= ($verif_status === '1') ? "TERVERIFIKASI. " : "BELUM DIVERIFIKASI. ";
    //     if (!empty($notes_tenaga)) {
    //         $msg_for_company .= "Catatan admin: " . $notes_tenaga;
    //     }

    //     $insertNotif = $pdo->prepare("
    //         INSERT INTO notifications
    //         (user_id, company_id, section, type, message, is_read, created_at)
    //         VALUES
    //         (:uid, :company_id, 'tenaga', :type, :msg, 0, NOW())
    //     ");
    //     $insertNotif->execute([
    //         ':uid'        => $company['user_id'],
    //         ':company_id' => $company_id,
    //         ':type'       => 'info',
    //         ':msg'        => $msg_for_company
    //     ]);

    //     header("Location: verifikasi_penyedia.php?company_id={$company_id}&tab=tenaga&msg=" . urlencode("Verifikasi tenaga ahli berhasil diperbarui") . "&type=success");
    //     exit;
    // }

    /* -----------------------------
       VERIFIKASI TENAGA AHLI (PER-ROW)
       ----------------------------- */
     if ($section === 'tenaga') {
        $tenaga_id = $_POST['tenaga_id'] ?? null;
        if (empty($tenaga_id) || !ctype_digit((string)$tenaga_id)) {
            header("Location: verifikasi_penyedia.php?company_id={$company_id}&tab=tenaga&msg=" . urlencode("Tenaga ahli tidak valid") . "&type=danger");
            exit;
        }

        // Pastikan tenaga ahli ini milik user/perusahaan ini
        $stmtTenagaCheck = $pdo->prepare("
            SELECT * FROM tenaga_ahli 
            WHERE id = :id AND user_id = :uid
            LIMIT 1
        ");
        $stmtTenagaCheck->execute([
            ':id'  => $tenaga_id,
            ':uid' => $company['user_id']
        ]);
        $tenagaRow = $stmtTenagaCheck->fetch(PDO::FETCH_ASSOC);

        if (!$tenagaRow) {
            header("Location: verifikasi_penyedia.php?company_id={$company_id}&tab=tenaga&msg=" . urlencode("Data tenaga ahli tidak ditemukan") . "&type=danger");
            exit;
        }

        $verif_status = $_POST['verif_status'] ?? '0';
        $notes_tenaga = $_POST['notes'] ?? '';

        if ($verif_status === '1') {
            $stmtUpd = $pdo->prepare("
                UPDATE tenaga_ahli
                SET notes = :notes,
                    verified_by = :verified_by,
                    verified_at = NOW(),
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmtUpd->execute([
                ':notes'       => $notes_tenaga,
                ':verified_by' => $_SESSION['user_id'] ?? null,
                ':id'          => $tenagaRow['id']
            ]);
        } else {
            $stmtUpd = $pdo->prepare("
                UPDATE tenaga_ahli
                SET notes = :notes,
                    verified_by = NULL,
                    verified_at = NULL,
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmtUpd->execute([
                ':notes' => $notes_tenaga,
                ':id'    => $tenagaRow['id']
            ]);
        }

        $msg_for_company  = "Status verifikasi data tenaga ahli ";
        $msg_for_company .= "'" . ($tenagaRow['nama'] ?? 'N/A') . "' ";
        $msg_for_company .= "telah diperbarui menjadi: ";
        $msg_for_company .= ($verif_status === '1') ? "TERVERIFIKASI. " : "BELUM DIVERIFIKASI. ";
        if (!empty($notes_tenaga)) {
            $msg_for_company .= "Catatan admin: " . $notes_tenaga;
        }

        $insertNotif = $pdo->prepare("
            INSERT INTO notifications
            (user_id, company_id, section, type, message, is_read, created_at)
            VALUES
            (:uid, :company_id, 'tenaga', :type, :msg, 0, NOW())
        ");
        $insertNotif->execute([
            ':uid'        => $company['user_id'],
            ':company_id' => $company_id,
            ':type'       => 'info',
            ':msg'        => $msg_for_company
        ]);

        header("Location: verifikasi_penyedia.php?company_id={$company_id}&tab=tenaga&msg=" . urlencode("Verifikasi tenaga ahli berhasil diperbarui") . "&type=success");
        exit;
    }

    /* -----------------------------
       KEPUTUSAN DPT (KESIMPULAN)
       ----------------------------- */
    if ($section === 'kesimpulan' && isset($_POST['approve_dpt'])) {

        $final_score     = floatval($_POST['final_score'] ?? 0);
        $recommendation  = $_POST['recommendation'] ?? '';
        $notes_kesimpulan = $_POST['notes_kesimpulan'] ?? '';

        // Simpan keputusan ke notifikasi (log aman, tidak tergantung struktur companies)
        $msg_for_company  = "Perusahaan Anda dinyatakan LAYAK MASUK DPT dengan skor akhir: ";
        $msg_for_company .= number_format($final_score, 2) . ". ";
        if (!empty($notes_kesimpulan)) {
            $msg_for_company .= "Catatan admin: " . $notes_kesimpulan;
        }

        $insertNotif = $pdo->prepare("
            INSERT INTO notifications
            (user_id, company_id, section, type, message, is_read, created_at)
            VALUES
            (:uid, :company_id, 'kesimpulan', :type, :msg, 0, NOW())
        ");
        $insertNotif->execute([
            ':uid'        => $company['user_id'],
            ':company_id' => $company_id,
            ':type'       => 'dpt_approved',
            ':msg'        => $msg_for_company
        ]);

        // Optional: jika nanti ada tabel khusus DPT, bisa ditambah di sini (pakai try/catch agar tidak error)

        header("Location: verifikasi_penyedia.php?company_id={$company_id}&tab=kesimpulan&msg=" . urlencode("Perusahaan disetujui masuk DPT") . "&type=success");
        exit;
    }
}
/* =========================================================
   END HANDLE POST
   ========================================================= */

// ******* Ambil data per-seksi untuk display & kesimpulan *******

// Alamat
$stmtAddr = $pdo->prepare("SELECT * FROM address WHERE user_id = ? LIMIT 1");
$stmtAddr->execute([$company['user_id']]);
$address = $stmtAddr->fetch(PDO::FETCH_ASSOC);

// Ijin Usaha
$stmtIU = $pdo->prepare("SELECT * FROM ijin_usaha WHERE user_id = ? LIMIT 1");
$stmtIU->execute([$company['user_id']]);
$ijin_usaha = $stmtIU->fetch(PDO::FETCH_ASSOC);

// Akta Perusahaan
$stmtAkta = $pdo->prepare("SELECT * FROM akta_perusahaan WHERE user_id = ? LIMIT 1");
$stmtAkta->execute([$company['user_id']]);
$akta_perusahaan = $stmtAkta->fetch(PDO::FETCH_ASSOC);

// Keuangan
$stmtKeu = $pdo->prepare("SELECT * FROM keuangan WHERE user_id = ? LIMIT 1");
$stmtKeu->execute([$company['user_id']]);
$keuangan = $stmtKeu->fetch(PDO::FETCH_ASSOC);

// Pajak
$stmtPajak = $pdo->prepare("SELECT * FROM pajak WHERE user_id = ? LIMIT 1");
$stmtPajak->execute([$company['user_id']]);
$pajak = $stmtPajak->fetch(PDO::FETCH_ASSOC);

// Pemilik & Pengurus
$stmtPeng = $pdo->prepare("SELECT * FROM pengurus WHERE user_id = ? LIMIT 1");
$stmtPeng->execute([$company['user_id']]);
$pengurus = $stmtPeng->fetch(PDO::FETCH_ASSOC);

// Tenaga Ahli (multi-row)
// $stmtTenaga = $pdo->prepare("SELECT * FROM tenaga_ahli WHERE user_id = ?");
// $stmtTenaga->execute([$company['user_id']]);
// $tenaga_list = $stmtTenaga->fetchAll(PDO::FETCH_ASSOC);

// TENAGA AHLI: BISA LEBIH DARI SATU ROW
$stmtTenaga = $pdo->prepare("SELECT * FROM tenaga_ahli WHERE user_id = ? ORDER BY nama ASC");
$stmtTenaga->execute([$company['user_id']]);
$tenaga_list = $stmtTenaga->fetchAll(PDO::FETCH_ASSOC);

// ----------------------------------------------------------
// Perhitungan status & skor untuk TAB KESIMPULAN
// ----------------------------------------------------------
$identitas_ok = (isset($company['status']) && $company['status'] === 'verified');

// alamat
$alamat_ok = false;
if ($address && !empty($address['verified_by']) && !empty($address['verified_at'])) {
    $alamat_ok = true;
}

// ijin usaha
$ijin_ok = false;
if ($ijin_usaha && !empty($ijin_usaha['verified_by']) && !empty($ijin_usaha['verified_at'])) {
    $ijin_ok = true;
}

// akta
$akta_ok = false;
if ($akta_perusahaan && !empty($akta_perusahaan['verified_by']) && !empty($akta_perusahaan['verified_at'])) {
    $akta_ok = true;
}

// keuangan
$keu_ok = false;
if ($keuangan && !empty($keuangan['verified_by']) && !empty($keuangan['verified_at'])) {
    $keu_ok = true;
}

// pajak
$pajak_ok = false;
if ($pajak && !empty($pajak['verified_by']) && !empty($pajak['verified_at'])) {
    $pajak_ok = true;
}

// pengurus
$pengurus_ok = false;
if ($pengurus && !empty($pengurus['verified_by']) && !empty($pengurus['verified_at'])) {
    $pengurus_ok = true;
}

// tenaga ahli: hitung berapa yang terverifikasi
$tenaga_count = count($tenaga_list);
$tenaga_verified_count = 0;
foreach ($tenaga_list as $t) {
    if (!empty($t['verified_by']) && !empty($t['verified_at'])) {
        $tenaga_verified_count++;
    }
}
$tenaga_ok = ($tenaga_count > 0 && $tenaga_verified_count === $tenaga_count);

// ----------------------------------------------------------
// Sistem Bobot (LPSE-style):
// - Legal & Administrasi (70%):
//   Identitas, Alamat, Ijin, Akta, Pajak, Pengurus
// - Keuangan (20%)
// - Tenaga Ahli (10%)
// Skor tiap komponen: 0 atau 100 (bisa dikembangkan nanti)
// ----------------------------------------------------------
$legal_items = [
    $identitas_ok,
    $alamat_ok,
    $ijin_ok,
    $akta_ok,
    $pajak_ok,
    $pengurus_ok
];

$legal_score = 0.0;
$legal_total_components = count($legal_items);
if ($legal_total_components > 0) {
    $sum = 0;
    foreach ($legal_items as $ok) {
        $sum += $ok ? 100 : 0;
    }
    $legal_score = $sum / $legal_total_components; // rata-rata %
}

// keuangan: 0 atau 100
$keu_score = $keu_ok ? 100.0 : 0.0;

// tenaga ahli: proporsi baris terverifikasi
$tenaga_score = 0.0;
if ($tenaga_count > 0) {
    $tenaga_score = ($tenaga_verified_count / $tenaga_count) * 100.0;
}

// Bobot
$w_legal   = 0.70;
$w_keu     = 0.20;
$w_tenaga  = 0.10;

// Skor akhir
$final_score = $legal_score * $w_legal + $keu_score * $w_keu + $tenaga_score * $w_tenaga;

// Mandatory pass (LPSE-like): dokumen legal utama harus verified
$mandatory_ok = $identitas_ok && $ijin_ok && $akta_ok && $pajak_ok;

// Rekomendasi otomatis
if (!$mandatory_ok) {
    $recommendation = "BELUM LAYAK (Dokumen legal utama belum lengkap/terverifikasi)";
} else {
    if ($final_score >= 85) {
        $recommendation = "LAYAK MASUK DPT";
    } elseif ($final_score >= 70) {
        $recommendation = "LAYAK DENGAN CATATAN";
    } else {
        $recommendation = "BELUM LAYAK (Skor belum memenuhi)";
    }
}

// Eligibility tombol "Setujui Masuk DPT"
$eligible_for_dpt = ($mandatory_ok && $final_score >= 85);


//  // ******* DISPLAY PAGE *************** 
$page_title = "Verifikasi Dokumen Penyedia - Admin Panel";

include 'header&menu_admin.php';
?>

<div class="container mt-4">

    <div class="mb-3">
        <a href="list_perusahaan.php" class="btn btn-secondary btn-sm">← Kembali ke Daftar Perusahaan</a>
    </div>

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Verifikasi Dokumen Penyedia</h4>
            <small>Perusahaan: <b><?= htmlspecialchars($company['name']) ?></b></small><br>
            <small>Email: <?= htmlspecialchars($company['email']) ?></small>
        </div>

        <div class="card-body">

            <!-- NAV TABS -->
            <ul class="nav nav-tabs" id="verifTabs" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="identitas-tab" data-bs-toggle="tab"
                        data-bs-target="#identitas" type="button" role="tab">
                        Identitas
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="alamat-tab" data-bs-toggle="tab"
                        data-bs-target="#alamat" type="button" role="tab">
                        Alamat
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="ijinusaha-tab" data-bs-toggle="tab"
                        data-bs-target="#ijinusaha" type="button" role="tab">
                        Ijin Usaha
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="akta-tab" data-bs-toggle="tab"
                        data-bs-target="#akta" type="button" role="tab">
                        Akta Perusahaan
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="keuangan-tab" data-bs-toggle="tab"
                        data-bs-target="#keuangan" type="button" role="tab">
                        Keuangan
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pajak-tab" data-bs-toggle="tab"
                        data-bs-target="#pajak" type="button" role="tab">
                        Pajak
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pemilik-tab" data-bs-toggle="tab"
                        data-bs-target="#pemilik" type="button" role="tab">
                        Pemilik & Pengurus
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tenaga-tab" data-bs-toggle="tab"
                        data-bs-target="#tenaga" type="button" role="tab">
                        Tenaga Ahli
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="kesimpulan-tab" data-bs-toggle="tab"
                        data-bs-target="#kesimpulan" type="button" role="tab">
                        Kesimpulan
                    </button>
                </li>

            </ul>

            <!-- TAB CONTENT -->
            <div class="tab-content p-3 border border-top-0" id="verifTabsContent">

                <!-- TAB 1: IDENTITAS -->
                <div class="tab-pane fade show active" id="identitas" role="tabpanel">

                    <h5 class="mb-3">Identitas Perusahaan</h5>
                    <table class="table table-bordered">
                        <tr><th>Kode Member</th><td><?= htmlspecialchars($company['code_member']) ?></td></tr>
                        <tr><th>Nama Perusahaan</th><td><?= htmlspecialchars($company['name']) ?></td></tr>
                        <tr><th>Email</th><td><?= htmlspecialchars($company['email']) ?></td></tr>
                        <tr><th>Telepon</th><td><?= htmlspecialchars($company['phone']) ?></td></tr>
                        <tr><th>Kepemilikan</th><td><?= htmlspecialchars($company['ownership']) ?></td></tr>
                        <tr><th>Tgl Berdiri</th><td><?= htmlspecialchars($company['established']) ?></td></tr>
                        <tr><th>Website</th><td><?= htmlspecialchars($company['website']) ?></td></tr>
                    </table>

                    <hr>

                    <?php
                    $status = $company['status'] ?? 'draft';
                    $badge  = 'secondary';
                    switch ($status) {
                        case 'submitted':    $badge = 'warning'; break;
                        case 'under_review': $badge = 'info';    break;
                        case 'verified':     $badge = 'success'; break;
                        case 'rejected':     $badge = 'danger';  break;
                    }
                    ?>

                    <div class="mb-3">
                        <label class="form-label"><strong>Status Saat Ini</strong></label><br>
                        <span class="badge bg-<?= $badge ?>">
                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $status))) ?>
                        </span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Catatan Admin</strong></label>
                        <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($company['notes'] ?? '') ?></textarea>
                    </div>

                    <form method="POST">
                        <input type="hidden" name="section" value="identitas">

                        <div class="mb-3">
                            <label class="form-label"><strong>Ubah Status Verifikasi</strong></label>
                            <select name="status" class="form-select" required>
                                <option value="draft"        <?= $status == 'draft'        ? 'selected' : '' ?>>Draft</option>
                                <option value="submitted"    <?= $status == 'submitted'    ? 'selected' : '' ?>>Submitted</option>
                                <option value="under_review" <?= $status == 'under_review' ? 'selected' : '' ?>>Under Review</option>
                                <option value="verified"     <?= $status == 'verified'     ? 'selected' : '' ?>>Verified</option>
                                <option value="rejected"     <?= $status == 'rejected'     ? 'selected' : '' ?>>Rejected</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><strong>Catatan (opsional)</strong></label>
                            <textarea class="form-control" name="notes" rows="4"><?= htmlspecialchars($company['notes'] ?? '') ?></textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success px-4">Simpan Verifikasi</button>
                        </div>
                    </form>
                </div>

                <!-- TAB 2: ALAMAT -->
                <div class="tab-pane fade" id="alamat" role="tabpanel">
                    <h5 class="mb-3">Alamat Perusahaan</h5>

                    <?php if (!$address): ?>
                        <div class="alert alert-warning">
                            Perusahaan <strong>belum mengisi</strong> data alamat.
                        </div>
                    <?php else: ?>
                        <table class="table table-bordered">
                            <tr><th>Provinsi</th><td><?= htmlspecialchars($address['provinsi']) ?></td></tr>
                            <tr><th>Kabupaten / Kota</th><td><?= htmlspecialchars($address['kabupaten']) ?></td></tr>
                            <tr><th>Kecamatan</th><td><?= htmlspecialchars($address['kecamatan']) ?></td></tr>
                            <tr><th>Kelurahan / Desa</th><td><?= htmlspecialchars($address['kelurahan']) ?></td></tr>
                            <tr><th>Jalan</th><td><?= htmlspecialchars($address['jalan']) ?></td></tr>
                            <tr><th>Kode Pos</th><td><?= htmlspecialchars($address['kodepos']) ?></td></tr>
                            <tr>
                                <th>File Domisili</th>
                                <td>
                                    <?php if (!empty($address['file_domisili'])): ?>
                                        <a href="/<?= htmlspecialchars(ltrim($address['file_domisili'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            Lihat File Domisili
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum ada file domisili diunggah.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>

                        <?php
                        $addr_verified_by = $address['verified_by'] ?? null;
                        $addr_verified_at = $address['verified_at'] ?? null;
                        $addr_notes       = $address['notes'] ?? '';

                        if ($addr_verified_by && $addr_verified_at) {
                            $alamat_status_text = 'TERVERIFIKASI';
                            $alamat_badge_class = 'success';
                            $is_verified_addr   = true;
                        } else {
                            $alamat_status_text = 'BELUM DIVERIFIKASI';
                            $alamat_badge_class = 'secondary';
                            $is_verified_addr   = false;
                        }
                        ?>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label"><strong>Status Verifikasi Alamat</strong></label><br>
                            <span class="badge bg-<?= $alamat_badge_class ?>">
                                <?= htmlspecialchars($alamat_status_text) ?>
                            </span>
                            <?php if ($addr_verified_at): ?>
                                <br>
                                <small class="text-muted">
                                    Terakhir diverifikasi: <?= htmlspecialchars($addr_verified_at) ?>
                                </small>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><strong>Catatan Admin (Saat Ini)</strong></label>
                            <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($addr_notes) ?></textarea>
                        </div>

                        <form method="POST">
                            <input type="hidden" name="section" value="alamat">

                            <div class="mb-3">
                                <label class="form-label"><strong>Ubah Status Verifikasi Alamat</strong></label>
                                <select name="verif_status" class="form-select" required>
                                    <option value="0" <?= !$is_verified_addr ? 'selected' : '' ?>>Belum Diverifikasi</option>
                                    <option value="1" <?= $is_verified_addr  ? 'selected' : '' ?>>Terverifikasi</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><strong>Catatan untuk Perusahaan (opsional)</strong></label>
                                <textarea class="form-control" name="notes" rows="4"><?= htmlspecialchars($addr_notes) ?></textarea>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success px-4">Simpan Verifikasi Alamat</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>

                <!-- TAB 3: IJIN USAHA -->
                <div class="tab-pane fade" id="ijinusaha" role="tabpanel">
                    <h5 class="mb-3">Ijin Usaha</h5>

                    <?php if (!$ijin_usaha): ?>
                        <div class="alert alert-warning">
                            Perusahaan <strong>belum mengisi</strong> data ijin usaha.
                        </div>
                    <?php else: ?>
                        <table class="table table-bordered">
                            <tr><th>Kualifikasi Usaha</th><td><?= htmlspecialchars($ijin_usaha['kualifikasi_usaha']) ?></td></tr>
                            <tr><th>Kualifikasi Pengadaan</th><td><?= htmlspecialchars($ijin_usaha['kualifikasi_pengadaan']) ?></td></tr>
                            <tr><th>PKP</th><td><?= htmlspecialchars($ijin_usaha['pkp']) ?></td></tr>
                            <tr><th>NIB</th><td><?= htmlspecialchars($ijin_usaha['nib']) ?></td></tr>
                            <tr>
                                <th>File Ijin Usaha</th>
                                <td>
                                    <?php if (!empty($ijin_usaha['file_ijin_usaha'])): ?>
                                        <a href="/<?= htmlspecialchars(ltrim($ijin_usaha['file_ijin_usaha'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            Lihat Ijin Usaha
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum diunggah.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>File NIB</th>
                                <td>
                                    <?php if (!empty($ijin_usaha['file_nib'])): ?>
                                        <a href="/<?= htmlspecialchars(ltrim($ijin_usaha['file_nib'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            Lihat NIB
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum diunggah.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>File Sertifikat Badan Usaha</th>
                                <td>
                                    <?php if (!empty($ijin_usaha['file_sert_badan_usaha'])): ?>
                                        <a href="/<?= htmlspecialchars(ltrim($ijin_usaha['file_sert_badan_usaha'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            Lihat Sertifikat Badan Usaha
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum diunggah.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>File SKA Konstruksi</th>
                                <td>
                                    <?php if (!empty($ijin_usaha['file_ska_konstruksi'])): ?>
                                        <a href="/<?= htmlspecialchars(ltrim($ijin_usaha['file_ska_konstruksi'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            Lihat SKA Konstruksi
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum diunggah.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>File SKT Konstruksi</th>
                                <td>
                                    <?php if (!empty($ijin_usaha['file_skt_konstruksi'])): ?>
                                        <a href="/<?= htmlspecialchars(ltrim($ijin_usaha['file_skt_konstruksi'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            Lihat SKT Konstruksi
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum diunggah.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>

                        <?php
                        $iu_verified_by = $ijin_usaha['verified_by'] ?? null;
                        $iu_verified_at = $ijin_usaha['verified_at'] ?? null;
                        $iu_notes       = $ijin_usaha['notes'] ?? '';
                        if ($iu_verified_by && $iu_verified_at) {
                            $iu_status_text = 'TERVERIFIKASI';
                            $iu_badge_class = 'success';
                            $iu_is_verified = true;
                        } else {
                            $iu_status_text = 'BELUM DIVERIFIKASI';
                            $iu_badge_class = 'secondary';
                            $iu_is_verified = false;
                        }
                        ?>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label"><strong>Status Verifikasi Ijin Usaha</strong></label><br>
                            <span class="badge bg-<?= $iu_badge_class ?>">
                                <?= htmlspecialchars($iu_status_text) ?>
                            </span>
                            <?php if ($iu_verified_at): ?>
                                <br>
                                <small class="text-muted">Terakhir diverifikasi: <?= htmlspecialchars($iu_verified_at) ?></small>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><strong>Catatan Admin (Saat Ini)</strong></label>
                            <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($iu_notes) ?></textarea>
                        </div>

                        <form method="POST">
                            <input type="hidden" name="section" value="ijin_usaha">

                            <div class="mb-3">
                                <label class="form-label"><strong>Ubah Status Verifikasi Ijin Usaha</strong></label>
                                <select name="verif_status" class="form-select" required>
                                    <option value="0" <?= !$iu_is_verified ? 'selected' : '' ?>>Belum Diverifikasi</option>
                                    <option value="1" <?= $iu_is_verified  ? 'selected' : '' ?>>Terverifikasi</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><strong>Catatan untuk Perusahaan (opsional)</strong></label>
                                <textarea class="form-control" name="notes" rows="4"><?= htmlspecialchars($iu_notes) ?></textarea>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success px-4">Simpan Verifikasi Ijin Usaha</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>

                <!-- TAB 4: AKTA -->
                <div class="tab-pane fade" id="akta" role="tabpanel">
                    <h5 class="mb-3">Akta Perusahaan</h5>

                    <?php if (!$akta_perusahaan): ?>
                        <div class="alert alert-warning">
                            Perusahaan <strong>belum mengisi</strong> data akta perusahaan.
                        </div>
                    <?php else: ?>
                        <table class="table table-bordered">
                            <tr><th>No. Akta Pendirian</th><td><?= htmlspecialchars($akta_perusahaan['no_akta_pendirian']) ?></td></tr>
                            <tr><th>Tgl Akta Pendirian</th><td><?= htmlspecialchars($akta_perusahaan['tgl_akta_pendirian']) ?></td></tr>
                            <tr><th>Notaris Pendirian</th><td><?= htmlspecialchars($akta_perusahaan['notaris_pendirian']) ?></td></tr>
                            <tr><th>No. Akta Perubahan</th><td><?= htmlspecialchars($akta_perusahaan['no_akta_perubahan']) ?></td></tr>
                            <tr><th>Tgl Akta Perubahan</th><td><?= htmlspecialchars($akta_perusahaan['tgl_akta_perubahan']) ?></td></tr>
                            <tr><th>Notaris Perubahan</th><td><?= htmlspecialchars($akta_perusahaan['notaris_perubahan']) ?></td></tr>
                            <tr>
                                <th>File Akta Pendirian</th>
                                <td>
                                    <?php if (!empty($akta_perusahaan['file_akta_pendirian'])): ?>
                                        <a href="/<?= htmlspecialchars(ltrim($akta_perusahaan['file_akta_pendirian'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Lihat Akta Pendirian</a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum diunggah.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>File Akta Perubahan</th>
                                <td>
                                    <?php if (!empty($akta_perusahaan['file_akta_perubahan'])): ?>
                                        <a href="/<?= htmlspecialchars(ltrim($akta_perusahaan['file_akta_perubahan'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Lihat Akta Perubahan</a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum diunggah.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>File Kop Surat</th>
                                <td>
                                    <?php if (!empty($akta_perusahaan['file_kop_surat'])): ?>
                                        <a href="/<?= htmlspecialchars(ltrim($akta_perusahaan['file_kop_surat'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Lihat Kop Surat</a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum diunggah.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>File Pengalaman</th>
                                <td>
                                    <?php if (!empty($akta_perusahaan['file_pengalaman'])): ?>
                                        <a href="/<?= htmlspecialchars(ltrim($akta_perusahaan['file_pengalaman'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Lihat Dokumen Pengalaman</a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum diunggah.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>

                        <?php
                        $akta_verified_by = $akta_perusahaan['verified_by'] ?? null;
                        $akta_verified_at = $akta_perusahaan['verified_at'] ?? null;
                        $akta_notes       = $akta_perusahaan['notes'] ?? '';

                        if ($akta_verified_by && $akta_verified_at) {
                            $akta_status_text = 'TERVERIFIKASI';
                            $akta_badge_class = 'success';
                            $akta_is_verified = true;
                        } else {
                            $akta_status_text = 'BELUM DIVERIFIKASI';
                            $akta_badge_class = 'secondary';
                            $akta_is_verified = false;
                        }
                        ?>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label"><strong>Status Verifikasi Akta</strong></label><br>
                            <span class="badge bg-<?= $akta_badge_class ?>">
                                <?= htmlspecialchars($akta_status_text) ?>
                            </span>
                            <?php if ($akta_verified_at): ?>
                                <br><small class="text-muted">Terakhir diverifikasi: <?= htmlspecialchars($akta_verified_at) ?></small>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><strong>Catatan Admin (Saat Ini)</strong></label>
                            <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($akta_notes) ?></textarea>
                        </div>

                        <form method="POST">
                            <input type="hidden" name="section" value="akta">

                            <div class="mb-3">
                                <label class="form-label"><strong>Ubah Status Verifikasi Akta</strong></label>
                                <select name="verif_status" class="form-select" required>
                                    <option value="0" <?= !$akta_is_verified ? 'selected' : '' ?>>Belum Diverifikasi</option>
                                    <option value="1" <?= $akta_is_verified  ? 'selected' : '' ?>>Terverifikasi</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><strong>Catatan untuk Perusahaan (opsional)</strong></label>
                                <textarea class="form-control" name="notes" rows="4"><?= htmlspecialchars($akta_notes) ?></textarea>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success px-4">Simpan Verifikasi Akta</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>

                <!-- TAB 5: KEUANGAN -->
                <div class="tab-pane fade" id="keuangan" role="tabpanel">
                    <h5 class="mb-3">Data Keuangan</h5>

                    <?php if (!$keuangan): ?>
                        <div class="alert alert-warning">
                            Perusahaan <strong>belum mengisi</strong> data keuangan.
                        </div>
                    <?php else: ?>
                        <table class="table table-bordered">
                            <tr><th>No. Rekening</th><td><?= htmlspecialchars($keuangan['no_rekening']) ?></td></tr>
                            <tr><th>Nama Pemilik Rekening</th><td><?= htmlspecialchars($keuangan['pemilik_rekening']) ?></td></tr>
                            <tr><th>Nama Bank</th><td><?= htmlspecialchars($keuangan['nama_bank']) ?></td></tr>
                            <tr><th>Cabang Bank</th><td><?= htmlspecialchars($keuangan['cabang_bank']) ?></td></tr>
                            <tr>
                                <th>Rekening Koran</th>
                                <td>
                                    <?php if (!empty($keuangan['file_rek_koran'])): ?>
                                        <a href="/<?= htmlspecialchars(ltrim($keuangan['file_rek_koran'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            Lihat Rekening Koran
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum diunggah.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Buku Rekening</th>
                                <td>
                                    <?php if (!empty($keuangan['file_buku_rekening'])): ?>
                                        <a href="/<?= htmlspecialchars(ltrim($keuangan['file_buku_rekening'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            Lihat Buku Rekening
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum diunggah.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Lampiran Neraca</th>
                                <td>
                                    <?php if (!empty($keuangan['file_neraca'])): ?>
                                        <a href="/<?= htmlspecialchars(ltrim($keuangan['file_neraca'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            Lihat Neraca
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum ada file neraca.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Laporan Laba Rugi</th>
                                <td>
                                    <?php if (!empty($keuangan['file_labarugi'])): ?>
                                        <a href="/<?= htmlspecialchars(ltrim($keuangan['file_labarugi'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            Lihat Laporan Laba Rugi
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum ada file laba-rugi.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>

                        <?php
                        $keu_verified_by = $keuangan['verified_by'] ?? null;
                        $keu_verified_at = $keuangan['verified_at'] ?? null;
                        $keu_notes       = $keuangan['notes'] ?? '';

                        if ($keu_verified_by && $keu_verified_at) {
                            $keu_status_text = 'TERVERIFIKASI';
                            $keu_badge_class = 'success';
                            $keu_is_verified = true;
                        } else {
                            $keu_status_text = 'BELUM DIVERIFIKASI';
                            $keu_badge_class = 'secondary';
                            $keu_is_verified = false;
                        }
                        ?>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label"><strong>Status Verifikasi Keuangan</strong></label><br>
                            <span class="badge bg-<?= $keu_badge_class ?>">
                                <?= htmlspecialchars($keu_status_text) ?>
                            </span>
                            <?php if ($keu_verified_at): ?>
                                <br><small class="text-muted">Terakhir diverifikasi: <?= htmlspecialchars($keu_verified_at) ?></small>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><strong>Catatan Admin (Saat Ini)</strong></label>
                            <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($keu_notes) ?></textarea>
                        </div>

                        <form method="POST">
                            <input type="hidden" name="section" value="keuangan">

                            <div class="mb-3">
                                <label class="form-label"><strong>Ubah Status Verifikasi Keuangan</strong></label>
                                <select name="verif_status" class="form-select" required>
                                    <option value="0" <?= !$keu_is_verified ? 'selected' : '' ?>>Belum Diverifikasi</option>
                                    <option value="1" <?= $keu_is_verified  ? 'selected' : '' ?>>Terverifikasi</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><strong>Catatan untuk Perusahaan (opsional)</strong></label>
                                <textarea class="form-control" name="notes" rows="4"><?= htmlspecialchars($keu_notes) ?></textarea>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success px-4">Simpan Verifikasi Keuangan</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>

                <!-- TAB 6: PAJAK -->
                <div class="tab-pane fade" id="pajak" role="tabpanel">
                    <h5 class="mb-3">Dokumen Pajak</h5>

                    <?php if (!$pajak): ?>
                        <div class="alert alert-warning">
                            Perusahaan <strong>belum mengisi</strong> data pajak.
                        </div>
                    <?php else: ?>
                        <table class="table table-bordered">
                            <tr><th>NPWP Perusahaan</th><td><?= htmlspecialchars($pajak['npwp_perusahaan']) ?></td></tr>
                            <tr><th>NPWP Direktur</th><td><?= htmlspecialchars($pajak['npwp_direktur']) ?></td></tr>
                            <tr>
                                <th>File Tanda Daftar</th>
                                <td>
                                    <?php if (!empty($pajak['file_tanda_daftar'])): ?>
                                        <a href="/<?= htmlspecialchars(ltrim($pajak['file_tanda_daftar'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            Lihat Tanda Daftar
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum diunggah.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>File NPWP Perusahaan</th>
                                <td>
                                    <?php if (!empty($pajak['file_npwp_perusahaan'])): ?>
                                        <a href="/<?= htmlspecialchars(ltrim($pajak['file_npwp_perusahaan'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            Lihat NPWP Perusahaan
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum diunggah.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>File NPWP Direktur</th>
                                <td>
                                    <?php if (!empty($pajak['file_npwp_direktur'])): ?>
                                        <a href="/<?= htmlspecialchars(ltrim($pajak['file_npwp_direktur'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            Lihat NPWP Direktur
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum diunggah.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>File Laporan Pajak</th>
                                <td>
                                    <?php if (!empty($pajak['file_lapor_pajak'])): ?>
                                        <a href="/<?= htmlspecialchars(ltrim($pajak['file_lapor_pajak'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            Lihat Laporan Pajak
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum diunggah.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>

                        <?php
                        $pajak_verified_by = $pajak['verified_by'] ?? null;
                        $pajak_verified_at = $pajak['verified_at'] ?? null;
                        $pajak_notes       = $pajak['notes'] ?? '';

                        if ($pajak_verified_by && $pajak_verified_at) {
                            $pajak_status_text = 'TERVERIFIKASI';
                            $pajak_badge_class = 'success';
                            $pajak_is_verified = true;
                        } else {
                            $pajak_status_text = 'BELUM DIVERIFIKASI';
                            $pajak_badge_class = 'secondary';
                            $pajak_is_verified = false;
                        }
                        ?>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label"><strong>Status Verifikasi Pajak</strong></label><br>
                            <span class="badge bg-<?= $pajak_badge_class ?>">
                                <?= htmlspecialchars($pajak_status_text) ?>
                            </span>
                            <?php if ($pajak_verified_at): ?>
                                <br><small class="text-muted">Terakhir diverifikasi: <?= htmlspecialchars($pajak_verified_at) ?></small>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><strong>Catatan Admin (Saat Ini)</strong></label>
                            <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($pajak_notes) ?></textarea>
                        </div>

                        <form method="POST">
                            <input type="hidden" name="section" value="pajak">

                            <div class="mb-3">
                                <label class="form-label"><strong>Ubah Status Verifikasi Pajak</strong></label>
                                <select name="verif_status" class="form-select" required>
                                    <option value="0" <?= !$pajak_is_verified ? 'selected' : '' ?>>Belum Diverifikasi</option>
                                    <option value="1" <?= $pajak_is_verified  ? 'selected' : '' ?>>Terverifikasi</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><strong>Catatan untuk Perusahaan (opsional)</strong></label>
                                <textarea class="form-control" name="notes" rows="4"><?= htmlspecialchars($pajak_notes) ?></textarea>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success px-4">Simpan Verifikasi Pajak</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>

                <!-- TAB 7: PEMILIK & PENGURUS -->
                <div class="tab-pane fade" id="pemilik" role="tabpanel">
                    <h5 class="mb-3">Pemilik & Pengurus</h5>

                    <?php if (!$pengurus): ?>
                        <div class="alert alert-warning">
                            Perusahaan <strong>belum mengisi</strong> data pemilik & pengurus.
                        </div>
                    <?php else: ?>
                        <table class="table table-bordered">
                            <tr><th>Pemilik</th><td><?= htmlspecialchars($pengurus['pemilik']) ?></td></tr>
                            <tr><th>Jenis Identitas Pemilik</th><td><?= htmlspecialchars($pengurus['jenis_identitas_pemilik']) ?></td></tr>
                            <tr><th>No. Identitas Pemilik</th><td><?= htmlspecialchars($pengurus['nomor_identitas_pemilik']) ?></td></tr>
                            <tr><th>Nama Direktur</th><td><?= htmlspecialchars($pengurus['direktur']) ?></td></tr>
                            <tr><th>Jenis Identitas Direktur</th><td><?= htmlspecialchars($pengurus['jenis_identitas_direktur']) ?></td></tr>
                            <tr><th>No. Identitas Direktur</th><td><?= htmlspecialchars($pengurus['nomor_identitas_direktur']) ?></td></tr>
                            <tr>
                                <th>File Kartu Identitas Pemilik</th>
                                <td>
                                    <?php if (!empty($pengurus['file_kartu_pemilik'])): ?>
                                        <a href="/<?= htmlspecialchars(ltrim($pengurus['file_kartu_pemilik'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            Lihat Kartu Pemilik
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum diunggah.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>File Kartu Identitas Direktur</th>
                                <td>
                                    <?php if (!empty($pengurus['file_kartu_direktur'])): ?>
                                        <a href="/<?= htmlspecialchars(ltrim($pengurus['file_kartu_direktur'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            Lihat Kartu Direktur
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum diunggah.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>

                        <?php
                        $peng_verified_by = $pengurus['verified_by'] ?? null;
                        $peng_verified_at = $pengurus['verified_at'] ?? null;
                        $peng_notes       = $pengurus['notes'] ?? '';

                        if ($peng_verified_by && $peng_verified_at) {
                            $peng_status_text = 'TERVERIFIKASI';
                            $peng_badge_class = 'success';
                            $peng_is_verified = true;
                        } else {
                            $peng_status_text = 'BELUM DIVERIFIKASI';
                            $peng_badge_class = 'secondary';
                            $peng_is_verified = false;
                        }
                        ?>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label"><strong>Status Verifikasi Pemilik & Pengurus</strong></label><br>
                            <span class="badge bg-<?= $peng_badge_class ?>">
                                <?= htmlspecialchars($peng_status_text) ?>
                            </span>
                            <?php if ($peng_verified_at): ?>
                                <br><small class="text-muted">Terakhir diverifikasi: <?= htmlspecialchars($peng_verified_at) ?></small>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><strong>Catatan Admin (Saat Ini)</strong></label>
                            <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($peng_notes) ?></textarea>
                        </div>

                        <form method="POST">
                            <input type="hidden" name="section" value="pengurus">

                            <div class="mb-3">
                                <label class="form-label"><strong>Ubah Status Verifikasi Pemilik & Pengurus</strong></label>
                                <select name="verif_status" class="form-select" required>
                                    <option value="0" <?= !$peng_is_verified ? 'selected' : '' ?>>Belum Diverifikasi</option>
                                    <option value="1" <?= $peng_is_verified  ? 'selected' : '' ?>>Terverifikasi</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><strong>Catatan untuk Perusahaan (opsional)</strong></label>
                                <textarea class="form-control" name="notes" rows="4"><?= htmlspecialchars($peng_notes) ?></textarea>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success px-4">Simpan Verifikasi Pemilik & Pengurus</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>

               

                <!-- TAB 8: TENAGA AHLI (MULTI-ROW + VERIFIKASI PER-ROW) -->
                <div class="tab-pane fade" id="tenaga" role="tabpanel">
                    <h5 class="mb-3">Tenaga Ahli</h5>

                    <?php if (!$tenaga_list || count($tenaga_list) === 0): ?>
                        <div class="alert alert-warning">
                            Perusahaan <strong>belum mengisi</strong> data tenaga ahli.
                        </div>
                    <?php else: ?>

                        <?php
                        $total_tenaga    = count($tenaga_list);
                        $verified_tenaga = 0;
                        foreach ($tenaga_list as $t) {
                            if (!empty($t['verified_by']) && !empty($t['verified_at'])) {
                                $verified_tenaga++;
                            }
                        }

                        $summary_badge_class = ($verified_tenaga === $total_tenaga) ? 'success' : 'secondary';
                        $summary_text        = ($verified_tenaga === $total_tenaga)
                            ? 'SEMUA TENAGA AHLI TERVERIFIKASI'
                            : 'BELUM SEMUA TENAGA AHLI TERVERIFIKASI';
                        ?>

                        <!-- Ringkasan agregat -->
                        <div class="mb-3">
                            <label class="form-label"><strong>Ringkasan Verifikasi Tenaga Ahli</strong></label><br>
                            <span class="badge bg-<?= $summary_badge_class ?>">
                                <?= $summary_text ?> (<?= $verified_tenaga ?>/<?= $total_tenaga ?>)
                            </span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Pendidikan</th>
                                        <th>Keahlian</th>
                                        <th>Pengalaman (th)</th>
                                        <th>CV</th>
                                        <th>Sertifikat</th>
                                        <th>Status</th>
                                        <th>Catatan Admin</th>
                                        <th>Aksi Verifikasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($tenaga_list as $t): ?>
                                        <?php
                                        $row_verified_by = $t['verified_by'] ?? null;
                                        $row_verified_at = $t['verified_at'] ?? null;
                                        $row_notes       = $t['notes'] ?? '';

                                        if ($row_verified_by && $row_verified_at) {
                                            $row_status_text = 'TERVERIFIKASI';
                                            $row_badge_class = 'success';
                                            $row_is_verified = true;
                                        } else {
                                            $row_status_text = 'BELUM DIVERIFIKASI';
                                            $row_badge_class = 'secondary';
                                            $row_is_verified = false;
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= htmlspecialchars($t['nama']) ?></td>
                                            <td><?= htmlspecialchars($t['jabatan']) ?></td>
                                            <td><?= htmlspecialchars($t['pendidikan']) ?></td>
                                            <td><?= nl2br(htmlspecialchars($t['keahlian'])) ?></td>
                                            <td><?= htmlspecialchars($t['pengalaman_tahun']) ?></td>
                                            <td>
                                                <?php if (!empty($t['file_cv'])): ?>
                                                    <a href="/<?= htmlspecialchars(ltrim($t['file_cv'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        Lihat CV
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Tidak ada CV</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($t['file_sertifikat'])): ?>
                                                    <a href="/<?= htmlspecialchars(ltrim($t['file_sertifikat'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        Lihat Sertifikat
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Tidak ada sertifikat</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $row_badge_class ?>">
                                                    <?= htmlspecialchars($row_status_text) ?>
                                                </span>
                                                <?php if ($row_verified_at): ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?= htmlspecialchars($row_verified_at) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <textarea class="form-control form-control-sm" rows="3" readonly><?= htmlspecialchars($row_notes) ?></textarea>
                                            </td>
                                            <td style="min-width: 220px;">
                                                <!-- Form verifikasi per-row -->
                                                <form method="POST" class="mb-2">
                                                    <input type="hidden" name="section" value="tenaga">
                                                    <input type="hidden" name="tenaga_id" value="<?= (int)$t['id'] ?>">

                                                    <div class="mb-2">
                                                        <select name="verif_status" class="form-select form-select-sm" required>
                                                            <option value="0" <?= !$row_is_verified ? 'selected' : '' ?>>Belum Diverifikasi</option>
                                                            <option value="1" <?= $row_is_verified ? 'selected' : '' ?>>Terverifikasi</option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-2">
                                                        <textarea name="notes" class="form-control form-control-sm" rows="3" placeholder="Catatan untuk perusahaan (opsional)"><?= htmlspecialchars($row_notes) ?></textarea>
                                                    </div>

                                                    <button type="submit" class="btn btn-success btn-sm w-100">
                                                        Simpan
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                    <?php endif; ?>
                </div>


                <!-- TAB 9: KESIMPULAN -->
                <div class="tab-pane fade" id="kesimpulan" role="tabpanel">
                    <div id="kesimpulan-content">
                        <h5 class="mb-3">Kesimpulan Penilaian Penyedia</h5>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="card border-success mb-3">
                                    <div class="card-header bg-success text-white">
                                        Status Dokumen Utama (Legal & Administrasi)
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li>Identitas: 
                                                <span class="badge bg-<?= $identitas_ok ? 'success' : 'secondary' ?>">
                                                    <?= $identitas_ok ? 'Verified' : 'Belum' ?>
                                                </span>
                                            </li>
                                            <li>Alamat: 
                                                <span class="badge bg-<?= $alamat_ok ? 'success' : 'secondary' ?>">
                                                    <?= $alamat_ok ? 'Verified' : 'Belum' ?>
                                                </span>
                                            </li>
                                            <li>Ijin Usaha: 
                                                <span class="badge bg-<?= $ijin_ok ? 'success' : 'secondary' ?>">
                                                    <?= $ijin_ok ? 'Verified' : 'Belum' ?>
                                                </span>
                                            </li>
                                            <li>Akta: 
                                                <span class="badge bg-<?= $akta_ok ? 'success' : 'secondary' ?>">
                                                    <?= $akta_ok ? 'Verified' : 'Belum' ?>
                                                </span>
                                            </li>
                                            <li>Pajak: 
                                                <span class="badge bg-<?= $pajak_ok ? 'success' : 'secondary' ?>">
                                                    <?= $pajak_ok ? 'Verified' : 'Belum' ?>
                                                </span>
                                            </li>
                                            <li>Pemilik & Pengurus: 
                                                <span class="badge bg-<?= $pengurus_ok ? 'success' : 'secondary' ?>">
                                                    <?= $pengurus_ok ? 'Verified' : 'Belum' ?>
                                                </span>
                                            </li>
                                        </ul>
                                        <hr>
                                        <p class="mb-0">
                                            <strong>Skor Legal & Administrasi:</strong>
                                            <?= number_format($legal_score, 2) ?> / 100
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-info mb-3">
                                    <div class="card-header bg-info text-white">
                                        Status Keuangan & Tenaga Ahli
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-1">
                                            Keuangan:
                                            <span class="badge bg-<?= $keu_ok ? 'success' : 'secondary' ?>">
                                                <?= $keu_ok ? 'Verified' : 'Belum' ?>
                                            </span>
                                            <br>
                                            <strong>Skor Keuangan:</strong>
                                            <?= number_format($keu_score, 2) ?> / 100
                                        </p>
                                        <hr>
                                        <p class="mb-1">
                                            Tenaga Ahli:
                                            <?php if ($tenaga_count === 0): ?>
                                                <span class="badge bg-secondary">Belum Ada Data</span>
                                            <?php else: ?>
                                                <span class="badge bg-<?= $tenaga_ok ? 'success' : 'warning' ?>">
                                                    <?= $tenaga_ok ? 'Semua Terverifikasi' : "Verified {$tenaga_verified_count}/{$tenaga_count}" ?>
                                                </span>
                                            <?php endif; ?>
                                            <br>
                                            <strong>Skor Tenaga Ahli:</strong>
                                            <?= number_format($tenaga_score, 2) ?> / 100
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h6>Perhitungan Skor Berbobot </h6>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kategori</th>
                                        <th>Bobot</th>
                                        <th>Skor Rata-rata</th>
                                        <th>Kontribusi ke Skor Akhir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Legal & Administrasi<br><small>(Identitas, Alamat, Ijin, Akta, Pajak, Pengurus)</small></td>
                                        <td>70%</td>
                                        <td><?= number_format($legal_score, 2) ?></td>
                                        <td><?= number_format($legal_score * $w_legal, 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td>Keuangan</td>
                                        <td>20%</td>
                                        <td><?= number_format($keu_score, 2) ?></td>
                                        <td><?= number_format($keu_score * $w_keu, 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td>Tenaga Ahli</td>
                                        <td>10%</td>
                                        <td><?= number_format($tenaga_score, 2) ?></td>
                                        <td><?= number_format($tenaga_score * $w_tenaga, 2) ?></td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3">Skor Akhir</th>
                                        <th><?= number_format($final_score, 2) ?> / 100</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <?php
                        // Badge rekomendasi
                        $rec_badge = 'secondary';
                        if (strpos($recommendation, 'LAYAK MASUK DPT') !== false) {
                            $rec_badge = 'success';
                        } elseif (strpos($recommendation, 'LAYAK DENGAN') !== false) {
                            $rec_badge = 'warning';
                        } elseif (strpos($recommendation, 'BELUM') !== false) {
                            $rec_badge = 'danger';
                        }
                        ?>

                        <div class="alert alert-light border">
                            <h6 class="mb-2">Rekomendasi Sistem</h6>
                            <p class="mb-1">
                                <span class="badge bg-<?= $rec_badge ?> me-2">
                                    <?= htmlspecialchars($recommendation) ?>
                                </span>
                            </p>
                            <ul class="mb-2">
                                <li>Dokumen legal utama wajib <strong>Verified</strong> (Identitas, Ijin Usaha, Akta, Pajak).</li>
                                <li>Skor akhir ≥ <strong>85</strong> &amp; mandatory pass → <strong>Layak Masuk DPT</strong>.</li>
                                <li>70 ≤ Skor &lt; 85 → <strong>Layak dengan Catatan</strong>.</li>
                                <li>Skor &lt; 70 atau mandatory gagal → <strong>Belum Layak</strong>.</li>
                            </ul>
                        </div>
                    </div> <!-- /#kesimpulan-content -->

                    <hr>

                    <!-- Form Keputusan DPT -->
                    <form method="POST" class="mt-2">
                        <input type="hidden" name="section" value="kesimpulan">
                        <input type="hidden" name="final_score" value="<?= htmlspecialchars($final_score) ?>">
                        <input type="hidden" name="recommendation" value="<?= htmlspecialchars($recommendation) ?>">

                        <div class="mb-3">
                            <label class="form-label"><strong>Catatan Tambahan Admin (opsional)</strong></label>
                            <textarea class="form-control" name="notes_kesimpulan" rows="3" placeholder="Contoh: Disarankan untuk pengadaan di atas 500 juta, pengalaman cukup dalam pekerjaan sejenis."></textarea>
                        </div>

                        <div class="d-flex flex-wrap justify-content-between align-items-center">
                            <div class="mb-2">
                                <button type="button" class="btn btn-outline-secondary" onclick="printKesimpulan()">
                                    Cetak / PDF Laporan Kesimpulan
                                </button>
                            </div>

                            <div class="mb-2">
                                <button type="submit" name="approve_dpt" value="1"
                                        class="btn btn-primary px-4"
                                        <?= $eligible_for_dpt ? '' : 'disabled' ?>>
                                    Setujui Masuk DPT
                                </button>
                                <?php if (!$eligible_for_dpt): ?>
                                    <small class="text-muted ms-2 d-block d-md-inline">
                                        * Tombol aktif jika semua dokumen legal utama verified &amp; skor ≥ 85
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>

        </div>
    </div>

            </div> <!-- /.tab-content -->
        </div> <!-- /.card-body -->
    </div> <!-- /.card -->

</div> <!-- /.container -->

<?php
$activeTab = $_GET['tab'] ?? 'identitas';
$msg       = $_GET['msg'] ?? null;
$type      = $_GET['type'] ?? null;
?>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Aktifkan tab sesuai parameter ?tab=
        const tabID = "<?= $activeTab ?>-tab";
        const triggerEl = document.getElementById(tabID);

        if (triggerEl) {
            const tab = new bootstrap.Tab(triggerEl);
            tab.show();
        }

        // Popup SweetAlert2 jika ada pesan
        <?php if ($msg): ?>
            const notifMsg  = <?= json_encode($msg) ?>;
            const notifType = <?= json_encode($type) ?>; // success, danger, warning, info

            let icon = 'info';
            if (notifType === 'success') icon = 'success';
            else if (notifType === 'danger') icon = 'error';
            else if (notifType === 'warning') icon = 'warning';

            Swal.fire({
                icon: icon,
                title: 'Pemberitahuan',
                text: notifMsg,
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
    });

    // Cetak / PDF hanya untuk konten kesimpulan
    function printKesimpulan() {
        const content = document.getElementById('kesimpulan-content').innerHTML;
        const win = window.open('', '_blank', 'width=900,height=600');

        win.document.write(`
            <html>
                <head>
                    <title>Laporan Kesimpulan Penyedia</title>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
                    <style>
                        body { padding: 20px; font-size: 12px; }
                        h5, h6 { margin-top: 0; }
                    </style>
                </head>
                <body>
                    <h4>Laporan Kesimpulan Penyedia</h4>
                    <p><strong>Perusahaan:</strong> <?= htmlspecialchars($company['name']) ?><br>
                       <strong>Email:</strong> <?= htmlspecialchars($company['email']) ?><br>
                       <strong>Kode Member:</strong> <?= htmlspecialchars($company['code_member']) ?></p>
                    <hr>
                    ${content}
                </body>
            </html>
        `);

        win.document.close();
        win.focus();
        win.print();   // user bisa pilih "Save as PDF"
        // win.close(); // bisa diaktifkan jika ingin otomatis menutup
    }
</script>

<?php include 'footer_admin.php'; ?>
