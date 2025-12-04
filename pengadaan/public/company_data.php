<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// company_data.php
// Dashboard data & dokumen perusahaan (Vendor)
// Perbaikan: POST handling untuk tab "identitas" diproses sebelum include header/menu
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

// Pastikan yang login adalah perusahaan/vendor (role 3)
// if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
//     header("Location: ../index.php");
//     exit;
// }

$user_id = $_SESSION['user_id'];

/*
 ---------------------------------------------------------------------
  HANDLE POST untuk TAB IDENTITAS (HARUS DIATAS include header supaya
  header() bisa dipanggil tanpa "headers already sent")
 ---------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['tab'] ?? '') === 'identitas')) {
    // sanitasi input
    $ownership   = isset($_POST['ownership']) ? trim($_POST['ownership']) : 'Swasta';
    $established = !empty($_POST['established']) ? $_POST['established'] : null;
    $website     = !empty($_POST['website']) ? trim($_POST['website']) : null;
    $phone = !empty($_POST['phone']) ? trim($_POST['phone']) : null;


    // ambil apakah perusahaan sudah ada
    $stmt = $pdo->prepare("SELECT * FROM companies WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);
    $is_new = !$company;

    if ($is_new) {
        // generate code_member
        $generated_code = "UNPBJ-" . date("Ym") . $user_id;

        // ambil data user untuk nama/email/phone
        $u = $pdo->prepare("SELECT full_name, email, phone FROM users WHERE id = ? LIMIT 1");
        $u->execute([$user_id]);
        $ud = $u->fetch(PDO::FETCH_ASSOC);

        $sql = "INSERT INTO companies 
                (user_id, code_member, name, email, phone, ownership, established, website, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $ins = $pdo->prepare($sql);
        $ins->execute([
            $user_id,
            $generated_code,
            $ud['full_name'] ?? '',
            $ud['email'] ?? '',
            $ud['phone'] ?? '',
            $ownership,
            $established,
            $website
        ]);

        // setelah insert, buat notifikasi untuk admin
        if (file_exists(__DIR__ . '/../includes/notify.php')) {
            require_once __DIR__ . '/../includes/notify.php';
            // company_id terakhir
            $company_id = $pdo->lastInsertId();
            createNotification(
                $pdo,
                $user_id,
                "create_identitas",
                "Perusahaan " . ($ud['full_name'] ?? 'Perusahaan') . " telah menambahkan identitas perusahaan.",
                $company_id,
                "identitas"
            );
        }

        // redirect kembali ke halaman (ke tab identitas)
        header("Location: company_data.php?success=1#identitas");
        exit;
    } else {
        // UPDATE existing company
        $sql = "UPDATE companies 
                SET ownership = ?, established = ?, website = ?, phone=? ,updated_at = NOW()
                WHERE user_id = ?";
        $upd = $pdo->prepare($sql);
        $upd->execute([$ownership, $established, $website, $phone, $user_id]);

        // create notification (jika file exist)
        if (file_exists(__DIR__ . '/../includes/notify.php')) {
            require_once __DIR__ . '/../includes/notify.php';
            $company_id = (int)$company['id'];
            createNotification(
                $pdo,
                $user_id,
                "update_identitas",
                "Perusahaan " . ($company['name'] ?? 'Perusahaan') . " melakukan perubahan data identitas.",
                $company_id,
                "identitas"
            );
        }

        header("Location: company_data.php?update=1#identitas");
        exit;
    }
}


 /*
 ---------------------------------------------------------------------
  HANDLE POST untuk TAB ALAMAT PERUSAHAAN
 ---------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['tab'] ?? '') === 'alamat')) {

    // Sanitasi input
    $provinsi  = isset($_POST['provinsi'])  ? trim($_POST['provinsi'])  : '';
    $kabupaten = isset($_POST['kabupaten']) ? trim($_POST['kabupaten']) : '';
    $kecamatan = isset($_POST['kecamatan']) ? trim($_POST['kecamatan']) : '';
    $kelurahan = isset($_POST['kelurahan']) ? trim($_POST['kelurahan']) : '';
    $jalan     = isset($_POST['jalan'])     ? trim($_POST['jalan'])     : '';
    $kodepos   = isset($_POST['kodepos'])   ? trim($_POST['kodepos'])   : '';

    // validasi sederhana
    if ($provinsi === '' || $kabupaten === '' || $kecamatan === '' || $kelurahan === '' || $jalan === '' || $kodepos === '') {
        header("Location: company_data.php?alamat_error=1#alamat");
        exit;
    }

    // ambil data perusahaan untuk code_member & instansi_id
    $stmtCompany = $pdo->prepare("SELECT id, code_member, instansi_id, name FROM companies WHERE user_id = ? LIMIT 1");
    $stmtCompany->execute([$user_id]);
    $companyForAddress = $stmtCompany->fetch(PDO::FETCH_ASSOC);

    $code_member = $companyForAddress['code_member'] ?? null;
    $instansi_id = $companyForAddress['instansi_id'] ?? null;

    // cek apakah address sudah ada
    $stmtAddr = $pdo->prepare("SELECT * FROM address WHERE user_id = ? LIMIT 1");
    $stmtAddr->execute([$user_id]);
    $addressRow = $stmtAddr->fetch(PDO::FETCH_ASSOC);
    $is_new_address = !$addressRow;

    // default: pakai file lama kalau ada
    $file_domisili_path = $addressRow['file_domisili'] ?? '';

   /*
 ---------------------------------------------------------------
  HANDLE FILE UPLOAD DOMISILI (FILE PDF SAJA, MAKS 2MB)
 ---------------------------------------------------------------
*/
    if (isset($_FILES['file_domisili']) && $_FILES['file_domisili']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['file_domisili']['error'] === UPLOAD_ERR_OK) {
            $allowedExt = ['pdf'];
            $maxSize    = 2 * 1024 * 1024; // 2 MB

            $originalName = $_FILES['file_domisili']['name'];
            $tmpName      = $_FILES['file_domisili']['tmp_name'];
            $size         = $_FILES['file_domisili']['size'];

            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            // Validasi EXT
            if ($ext !== 'pdf') {
                header("Location: company_data.php?alamat_filetype=1#alamat");
                exit;
            }

            // Validasi MIME untuk keamanan tambahan
            $mime = mime_content_type($tmpName);
            if ($mime !== 'application/pdf') {
                header("Location: company_data.php?alamat_filetype=1#alamat");
                exit;
            }

            if ($size > $maxSize) {
                header("Location: company_data.php?alamat_filesize=1#alamat");
                exit;
            }

            // Abspath menuju /storage_secure/domisili/
            $htdocsRoot = dirname(__DIR__, 3); // -> .../htdocs 
            $uploadDir  = $htdocsRoot . '/storage_secure/domisili/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newFileName = 'domisili_' . $user_id . '_' . time() . '.' . $ext;
            $destPathAbs = $uploadDir . $newFileName;                 // path ABSOLUT
            $destPathRel = 'storage_secure/domisili/' . $newFileName;  // path untuk disimpan di DB

            if (move_uploaded_file($tmpName, $destPathAbs)) {
                // opsional: hapus file lama
                if (!empty($addressRow['file_domisili'])) {
                    $oldAbs = $htdocsRoot . '/' . $addressRow['file_domisili'];
                    if (file_exists($oldAbs)) {
                        @unlink($oldAbs);
                    }
                }
                $file_domisili_path = $destPathRel;
            } else {
                header("Location: company_data.php?alamat_uploadfail=1#alamat");
                exit;
            }

        } else {
            header("Location: company_data.php?alamat_uploaderror=1#alamat");
            exit;
        }
    }
    /*
     ---------------------------------------------------------------
      INSERT atau UPDATE ke tabel address
     ---------------------------------------------------------------
    */
        if ($is_new_address) {
        $sqlIns = "INSERT INTO address
            (user_id, code_member, instansi_id, provinsi, kabupaten, kecamatan, kelurahan, jalan, kodepos, file_domisili, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmtIns = $pdo->prepare($sqlIns);
        $stmtIns->execute([
            $user_id,
            $code_member,
            $instansi_id,
            $provinsi,
            $kabupaten,
            $kecamatan,
            $kelurahan,
            $jalan,
            $kodepos,
            $file_domisili_path ?: ''   // pastikan bukan NULL
        ]);

        // Notifikasi jika ada
        if (file_exists(__DIR__ . '/../includes/notify.php')) {
            require_once __DIR__ . '/../includes/notify.php';
            $addr_id = $pdo->lastInsertId();
            createNotification(
                $pdo,
                $user_id,
                "create_address",
                "Perusahaan " . ($companyForAddress['name'] ?? 'Perusahaan') . " telah menambahkan alamat perusahaan.",
                $addr_id,
                "alamat"
            );
        }

        header("Location: company_data.php?alamat_success=1#alamat");
        exit;

        } else {
            $sqlUpd = "UPDATE address
                    SET provinsi = ?, kabupaten = ?, kecamatan = ?, kelurahan = ?, jalan = ?, kodepos = ?, file_domisili = ?, updated_at = NOW()
                    WHERE user_id = ?";
            $stmtUpd = $pdo->prepare($sqlUpd);
            $stmtUpd->execute([
                $provinsi,
                $kabupaten,
                $kecamatan,
                $kelurahan,
                $jalan,
                $kodepos,
                $file_domisili_path ?: '',
                $user_id
            ]);

            // Notifikasi jika ada
            if (file_exists(__DIR__ . '/../includes/notify.php')) {
                require_once __DIR__ . '/../includes/notify.php';
                $addr_id = (int)$addressRow['id'];
                createNotification(
                    $pdo,
                    $user_id,
                    "update_address",
                    "Perusahaan " . ($companyForAddress['name'] ?? 'Perusahaan') . " melakukan perubahan alamat perusahaan.",
                    $addr_id,
                    "alamat"
                );
            }

            header("Location: company_data.php?alamat_update=1#alamat");
            exit;
        }
    }

/*
 ---------------------------------------------------------------------
  HANDLE POST untuk TAB IZIN USAHA
 ---------------------------------------------------------------------
*/
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['tab'] ?? '') === 'izin')) {
         // PAKSA user_id ambil dari session, bukan dari input lain
        $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
        if ($user_id <= 0) {
            die('User tidak valid. Silakan login ulang.');
        }

        // Sanitasi input teks
        $kualifikasi_usaha      = isset($_POST['kualifikasi_usaha']) ? trim($_POST['kualifikasi_usaha']) : '';
        $kualifikasi_pengadaan  = isset($_POST['kualifikasi_pengadaan']) ? trim($_POST['kualifikasi_pengadaan']) : '';
        $pkp                    = isset($_POST['pkp']) ? trim($_POST['pkp']) : '';
        $nib                    = isset($_POST['nib']) ? trim($_POST['nib']) : '';

        // Validasi minimal
        if ($kualifikasi_usaha === '' || $kualifikasi_pengadaan === '' || $pkp === '' || $nib === '') {
            header("Location: company_data.php?izin_error=1#izin");
            exit;
        }

        // Ambil data perusahaan untuk code_member & instansi_id
        $stmtCompany = $pdo->prepare("SELECT id, code_member, instansi_id, name FROM companies WHERE user_id = ? LIMIT 1");
        $stmtCompany->execute([$user_id]);
        $companyForIzin = $stmtCompany->fetch(PDO::FETCH_ASSOC);

        $code_member = $companyForIzin['code_member'] ?? null;
        $instansi_id = $companyForIzin['instansi_id'] ?? null;

        // Cek apakah ijin_usaha sudah ada
        $stmtIzin = $pdo->prepare("SELECT * FROM ijin_usaha WHERE user_id = ? LIMIT 1");
        $stmtIzin->execute([$user_id]);
        $izinRow = $stmtIzin->fetch(PDO::FETCH_ASSOC);
        $is_new_izin = !$izinRow;

        // Default: pakai file lama jika tidak di-upload ulang
        $file_ijin_usaha_path    = $izinRow['file_ijin_usaha'] ?? '';
        $file_nib_path           = $izinRow['file_nib'] ?? '';
        $file_sert_bu_path       = $izinRow['file_sert_badan_usaha'] ?? '';
        $file_ska_konstruksi_path= $izinRow['file_ska_konstruksi'] ?? '';
        $file_skt_konstruksi_path= $izinRow['file_skt_konstruksi'] ?? '';

        // Konfigurasi direktori upload: /htdocs/storage_secure/ijin_usaha/
        $htdocsRoot = dirname(__DIR__, 3); // __DIR__ = .../htdocs/eproc-unpatti/pengadaan/public
        $uploadDir  = $htdocsRoot . '/storage_secure/ijin_usaha/';

        if (!is_dir($uploadDir)) {
            // jika gagal, akan kena warning, tapi biar tetap coba
            @mkdir($uploadDir, 0755, true);
        }

        // Helper kecil untuk upload satu file PDF
        $uploadPdf = function($fieldName, $currentPath, $prefix) use ($uploadDir, $htdocsRoot, $user_id) {
            if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
                return $currentPath; // tidak ada file baru
            }

            if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
                header("Location: company_data.php?izin_uploaderror=1#izin");
                exit;
            }

            $allowedExt = ['pdf'];
            $maxSize    = 2 * 1024 * 1024; // 2 MB

            $originalName = $_FILES[$fieldName]['name'];
            $tmpName      = $_FILES[$fieldName]['tmp_name'];
            $size         = $_FILES[$fieldName]['size'];

            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExt)) {
                header("Location: company_data.php?izin_filetype=1#izin");
                exit;
            }

            if ($size > $maxSize) {
                header("Location: company_data.php?izin_filesize=1#izin");
                exit;
            }

            // Validasi MIME (opsional tapi disarankan)
            if (function_exists('mime_content_type')) {
                $mime = mime_content_type($tmpName);
                if ($mime !== 'application/pdf') {
                    header("Location: company_data.php?izin_filetype=1#izin");
                    exit;
                }
            }

            // Pastikan direktori writeable
            if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
                header("Location: company_data.php?izin_uploadfail=1#izin");
                exit;
            }

            $newFileName = $prefix . '_' . $user_id . '_' . time() . '.pdf';
            $destPathAbs = $uploadDir . $newFileName;
            $destPathRel = 'storage_secure/ijin_usaha/' . $newFileName;

            if (move_uploaded_file($tmpName, $destPathAbs)) {
                // Hapus file lama jika ada
                if (!empty($currentPath)) {
                    $oldAbs = $htdocsRoot . '/' . ltrim($currentPath, '/');
                    if (file_exists($oldAbs)) {
                        @unlink($oldAbs);
                    }
                }
                return $destPathRel;
            } else {
                header("Location: company_data.php?izin_uploadfail=1#izin");
                exit;
            }
        };

        // Proses masing-masing dokumen (semua PDF)
        $file_ijin_usaha_path     = $uploadPdf('file_ijin_usaha',    $file_ijin_usaha_path,     'ijin_usaha');
        $file_nib_path            = $uploadPdf('file_nib',           $file_nib_path,            'nib');
        $file_sert_bu_path        = $uploadPdf('file_sert_badan_usaha', $file_sert_bu_path,    'sert_bu');
        $file_ska_konstruksi_path = $uploadPdf('file_ska_konstruksi',$file_ska_konstruksi_path,'ska');
        $file_skt_konstruksi_path = $uploadPdf('file_skt_konstruksi',$file_skt_konstruksi_path,'skt');

        /*
        ---------------------------------------------------------------
        INSERT atau UPDATE ke tabel ijin_usaha
        ---------------------------------------------------------------
        */
        if ($is_new_izin) {
            $sqlIns = "INSERT INTO ijin_usaha
                (user_id, code_member, instansi_id, kualifikasi_usaha, kualifikasi_pengadaan, pkp, nib,
                file_ijin_usaha, file_nib, file_sert_badan_usaha, file_ska_konstruksi, file_skt_konstruksi,
                created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            $stmtIns = $pdo->prepare($sqlIns);
            $stmtIns->execute([
                (int)$user_id,
                $code_member,
                $instansi_id,
                $kualifikasi_usaha,
                $kualifikasi_pengadaan,
                $pkp,
                $nib,
                $file_ijin_usaha_path ?: '',
                $file_nib_path ?: '',
                $file_sert_bu_path ?: '',
                $file_ska_konstruksi_path ?: '',
                $file_skt_konstruksi_path ?: ''
            ]);

            // Notifikasi jika ada
            if (file_exists(__DIR__ . '/../includes/notify.php')) {
                require_once __DIR__ . '/../includes/notify.php';
                $izin_id = $pdo->lastInsertId();
                createNotification(
                    $pdo,
                    $user_id,
                    "create_ijin_usaha",
                    "Perusahaan " . ($companyForIzin['name'] ?? 'Perusahaan') . " telah menambahkan data izin usaha.",
                    $izin_id,
                    "izin_usaha"
                );
            }

            header("Location: company_data.php?izin_success=1#izin");
            exit;

        } else {
            $sqlUpd = "UPDATE ijin_usaha
                    SET kualifikasi_usaha = ?, kualifikasi_pengadaan = ?, pkp = ?, nib = ?,
                        file_ijin_usaha = ?, file_nib = ?, file_sert_badan_usaha = ?,
                        file_ska_konstruksi = ?, file_skt_konstruksi = ?, updated_at = NOW()
                    WHERE user_id = ?";
            $stmtUpd = $pdo->prepare($sqlUpd);
            $stmtUpd->execute([
                $kualifikasi_usaha,
                $kualifikasi_pengadaan,
                $pkp,
                $nib,
                $file_ijin_usaha_path ?: '',
                $file_nib_path ?: '',
                $file_sert_bu_path ?: '',
                $file_ska_konstruksi_path ?: '',
                $file_skt_konstruksi_path ?: '',
                (int)$user_id
            ]);

            if (file_exists(__DIR__ . '/../includes/notify.php')) {
                require_once __DIR__ . '/../includes/notify.php';
                $izin_id = (int)$izinRow['id'];
                createNotification(
                    $pdo,
                    $user_id,
                    "update_ijin_usaha",
                    "Perusahaan " . ($companyForIzin['name'] ?? 'Perusahaan') . " melakukan perubahan data izin usaha.",
                    $izin_id,
                    "izin_usaha"
                );
            }

            header("Location: company_data.php?izin_update=1#izin");
            exit;
        }
    }

  /*
 ---------------------------------------------------------------------
  HANDLE POST untuk TAB AKTA PERUSAHAAN
 ---------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['tab'] ?? '') === 'akta')) {

    // Pastikan user_id valid dari SESSION
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
    if ($user_id <= 0) {
        die('User tidak valid. Silakan login ulang.');
    }

    // Sanitasi input teks
    $no_akta_pendirian   = isset($_POST['no_akta_pendirian']) ? trim($_POST['no_akta_pendirian']) : '';
    $tgl_akta_pendirian  = isset($_POST['tgl_akta_pendirian']) ? trim($_POST['tgl_akta_pendirian']) : '';
    $notaris_pendirian   = isset($_POST['notaris_pendirian']) ? trim($_POST['notaris_pendirian']) : '';

    $no_akta_perubahan   = isset($_POST['no_akta_perubahan']) ? trim($_POST['no_akta_perubahan']) : '';
    $tgl_akta_perubahan  = isset($_POST['tgl_akta_perubahan']) ? trim($_POST['tgl_akta_perubahan']) : '';
    $notaris_perubahan   = isset($_POST['notaris_perubahan']) ? trim($_POST['notaris_perubahan']) : '';

    // Validasi minimal (akta pendirian wajib)
    if ($no_akta_pendirian === '' || $tgl_akta_pendirian === '' || $notaris_pendirian === '') {
        header("Location: company_data.php?akta_error=1#akta");
        exit;
    }

    // Ambil data perusahaan untuk code_member & instansi_id
    $stmtCompany = $pdo->prepare("SELECT id, code_member, instansi_id, name FROM companies WHERE user_id = ? LIMIT 1");
    $stmtCompany->execute([$user_id]);
    $companyForAkta = $stmtCompany->fetch(PDO::FETCH_ASSOC);

    $code_member = $companyForAkta['code_member'] ?? null;
    $instansi_id = $companyForAkta['instansi_id'] ?? null;

    // Cek apakah akta_perusahaan sudah ada
    $stmtAkta = $pdo->prepare("SELECT * FROM akta_perusahaan WHERE user_id = ? LIMIT 1");
    $stmtAkta->execute([$user_id]);
    $aktaRow = $stmtAkta->fetch(PDO::FETCH_ASSOC);
    $is_new_akta = !$aktaRow;

    // Default: pakai file lama jika tidak di-upload ulang
    $file_akta_pendirian_path  = $aktaRow['file_akta_pendirian'] ?? '';
    $file_kop_surat_path  = $aktaRow['file_kop_surat'] ?? '';
    $file_pengalaman_path  = $aktaRow['file_pengalaman'] ?? '';
    $file_akta_perubahan_path  = $aktaRow['file_akta_perubahan'] ?? '';


    // Konfigurasi direktori upload: /htdocs/storage_secure/akta/
    $htdocsRoot = dirname(__DIR__, 3); // __DIR__ = .../htdocs/eproc-unpatti/pengadaan/public
    $uploadDir  = $htdocsRoot . '/storage_secure/akta/';

    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }

    // Helper untuk upload satu file PDF
    $uploadPdfAkta = function($fieldName, $currentPath, $prefix) use ($uploadDir, $htdocsRoot, $user_id) {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
            return $currentPath; // tidak ada file baru
        }

        if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            header("Location: company_data.php?akta_uploaderror=1#akta");
            exit;
        }

        $allowedExt = ['pdf'];
        $maxSize    = 2 * 1024 * 1024; // 2 MB

        $originalName = $_FILES[$fieldName]['name'];
        $tmpName      = $_FILES[$fieldName]['tmp_name'];
        $size         = $_FILES[$fieldName]['size'];

        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt)) {
            header("Location: company_data.php?akta_filetype=1#akta");
            exit;
        }

        if ($size > $maxSize) {
            header("Location: company_data.php?akta_filesize=1#akta");
            exit;
        }

        if (function_exists('mime_content_type')) {
            $mime = mime_content_type($tmpName);
            if ($mime !== 'application/pdf') {
                header("Location: company_data.php?akta_filetype=1#akta");
                exit;
            }
        }

        if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
            header("Location: company_data.php?akta_uploadfail=1#akta");
            exit;
        }

        $newFileName = $prefix . '_' . $user_id . '_' . time() . '.pdf';
        $destPathAbs = $uploadDir . $newFileName;
        $destPathRel = 'storage_secure/akta/' . $newFileName;

        if (move_uploaded_file($tmpName, $destPathAbs)) {
            // Hapus file lama jika ada
            if (!empty($currentPath)) {
                $oldAbs = $htdocsRoot . '/' . ltrim($currentPath, '/');
                if (file_exists($oldAbs)) {
                    @unlink($oldAbs);
                }
            }
            return $destPathRel;
        } else {
            header("Location: company_data.php?akta_uploadfail=1#akta");
            exit;
        }
    };

    // Proses file akta (semua PDF)
    $file_akta_pendirian_path = $uploadPdfAkta('file_akta_pendirian', $file_akta_pendirian_path, 'akta_pendirian');
    $file_kop_surat_path = $uploadPdfAkta('file_kop_surat', $file_kop_surat_path, 'kop_surat');
    $file_pengalaman_path = $uploadPdfAkta('file_pengalaman', $file_pengalaman_path, 'pengalaman');
    $file_akta_perubahan_path = $uploadPdfAkta('file_akta_perubahan', $file_akta_perubahan_path, 'akta_perubahan');

    /*
     ---------------------------------------------------------------
      INSERT atau UPDATE ke tabel akta_perusahaan
     ---------------------------------------------------------------
    */
    if ($is_new_akta) {
        $sqlIns = "INSERT INTO akta_perusahaan
            (user_id, code_member, instansi_id,
             no_akta_pendirian, tgl_akta_pendirian, notaris_pendirian,
             no_akta_perubahan, tgl_akta_perubahan, notaris_perubahan,
             file_akta_pendirian, file_kop_surat, file_pengalaman, file_akta_perubahan,
             created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmtIns = $pdo->prepare($sqlIns);
        $stmtIns->execute([
            (int)$user_id,
            $code_member,
            $instansi_id,
            $no_akta_pendirian,
            $tgl_akta_pendirian,
            $notaris_pendirian,
            $no_akta_perubahan ?: null,
            $tgl_akta_perubahan ?: null,
            $notaris_perubahan ?: null,
            $file_akta_pendirian_path ?: '',
            $file_kop_surat_path ?: '',
            $file_pengalaman_path ?: '',    
            $file_akta_perubahan_path ?: ''
        ]);

        if (file_exists(__DIR__ . '/../includes/notify.php')) {
            require_once __DIR__ . '/../includes/notify.php';
            $akta_id = $pdo->lastInsertId();
            createNotification(
                $pdo,
                $user_id,
                "create_akta",
                "Perusahaan " . ($companyForAkta['name'] ?? 'Perusahaan') . " telah menambahkan data akta perusahaan.",
                $akta_id,
                "akta"
            );
        }

        header("Location: company_data.php?akta_success=1#akta");
        exit;

    } else {
        $sqlUpd = "UPDATE akta_perusahaan
                   SET no_akta_pendirian = ?, tgl_akta_pendirian = ?, notaris_pendirian = ?,
                       no_akta_perubahan = ?, tgl_akta_perubahan = ?, notaris_perubahan = ?,
                       file_akta_pendirian = ?, file_kop_surat = ?, file_pengalaman = ?, file_akta_perubahan = ?, updated_at = NOW()
                   WHERE user_id = ?";
        $stmtUpd = $pdo->prepare($sqlUpd);
        $stmtUpd->execute([
            $no_akta_pendirian,
            $tgl_akta_pendirian,
            $notaris_pendirian,
            $no_akta_perubahan ?: null,
            $tgl_akta_perubahan ?: null,
            $notaris_perubahan ?: null,
            $file_akta_pendirian_path ?: '',
            $file_kop_surat_path ?: '',
            $file_pengalaman_path ?: '',
            $file_akta_perubahan_path ?: '',
            (int)$user_id
        ]);

        if (file_exists(__DIR__ . '/../includes/notify.php')) {
            require_once __DIR__ . '/../includes/notify.php';
            $akta_id = (int)$aktaRow['id'];
            createNotification(
                $pdo,
                $user_id,
                "update_akta",
                "Perusahaan " . ($companyForAkta['name'] ?? 'Perusahaan') . " melakukan perubahan data akta perusahaan.",
                $akta_id,
                "akta"
            );
        }

        header("Location: company_data.php?akta_update=1#akta");
        exit;
    }
}

/*
 ---------------------------------------------------------------------
  HANDLE POST untuk TAB KEUANGAN
 ---------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['tab'] ?? '') === 'keuangan')) {

    // Pastikan user_id valid dari SESSION
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
    if ($user_id <= 0) {
        die('User tidak valid. Silakan login ulang.');
    }

    // Sanitasi input teks
    $no_rekening       = isset($_POST['no_rekening']) ? trim($_POST['no_rekening']) : '';
    $pemilik_rekening  = isset($_POST['pemilik_rekening']) ? trim($_POST['pemilik_rekening']) : '';
    $nama_bank         = isset($_POST['nama_bank']) ? trim($_POST['nama_bank']) : '';
    $cabang_bank       = isset($_POST['cabang_bank']) ? trim($_POST['cabang_bank']) : '';

    // Validasi minimal
    if ($no_rekening === '' || $pemilik_rekening === '' || $nama_bank === '') {
        header("Location: company_data.php?keu_error=1#keuangan");
        exit;
    }

    // Ambil data perusahaan untuk code_member & instansi_id
    $stmtCompany = $pdo->prepare("SELECT id, code_member, instansi_id, name FROM companies WHERE user_id = ? LIMIT 1");
    $stmtCompany->execute([$user_id]);
    $companyForKeu = $stmtCompany->fetch(PDO::FETCH_ASSOC);

    $code_member = $companyForKeu['code_member'] ?? null;
    $instansi_id = $companyForKeu['instansi_id'] ?? null;

    // Cek apakah data keuangan sudah ada
    $stmtKeu = $pdo->prepare("SELECT * FROM keuangan WHERE user_id = ? LIMIT 1");
    $stmtKeu->execute([$user_id]);
    $keuRow = $stmtKeu->fetch(PDO::FETCH_ASSOC);
    $is_new_keu = !$keuRow;

    // Default: pakai file lama jika tidak di-upload ulang
    $file_rek_koran_path    = $keuRow['file_rek_koran'] ?? '';
    $file_buku_rekening_path= $keuRow['file_buku_rekening'] ?? '';
    $file_neraca_path       = $keuRow['file_neraca'] ?? '';
    $file_labarugi_path     = $keuRow['file_labarugi'] ?? '';

    // Konfigurasi direktori upload: /htdocs/storage_secure/keuangan/
    $htdocsRoot = dirname(__DIR__, 3); // __DIR__ = .../htdocs/eproc-unpatti/pengadaan/public
    $uploadDir  = $htdocsRoot . '/storage_secure/keuangan/';

    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }

    // Helper upload file PDF
    $uploadPdfKeu = function($fieldName, $currentPath, $prefix) use ($uploadDir, $htdocsRoot, $user_id) {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
            return $currentPath; // tidak ada file baru
        }

        if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            header("Location: company_data.php?keu_uploaderror=1#keuangan");
            exit;
        }

        $allowedExt = ['pdf'];
        $maxSize    = 2 * 1024 * 1024; // 2 MB

        $originalName = $_FILES[$fieldName]['name'];
        $tmpName      = $_FILES[$fieldName]['tmp_name'];
        $size         = $_FILES[$fieldName]['size'];

        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt)) {
            header("Location: company_data.php?keu_filetype=1#keuangan");
            exit;
        }

        if ($size > $maxSize) {
            header("Location: company_data.php?keu_filesize=1#keuangan");
            exit;
        }

        if (function_exists('mime_content_type')) {
            $mime = mime_content_type($tmpName);
            if ($mime !== 'application/pdf') {
                header("Location: company_data.php?keu_filetype=1#keuangan");
                exit;
            }
        }

        if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
            header("Location: company_data.php?keu_uploadfail=1#keuangan");
            exit;
        }

        $newFileName = $prefix . '_' . $user_id . '_' . time() . '.pdf';
        $destPathAbs = $uploadDir . $newFileName;
        $destPathRel = 'storage_secure/keuangan/' . $newFileName;

        if (move_uploaded_file($tmpName, $destPathAbs)) {
            // Hapus file lama jika ada
            if (!empty($currentPath)) {
                $oldAbs = $htdocsRoot . '/' . ltrim($currentPath, '/');
                if (file_exists($oldAbs)) {
                    @unlink($oldAbs);
                }
            }
            return $destPathRel;
        } else {
            header("Location: company_data.php?keu_uploadfail=1#keuangan");
            exit;
        }
    };

    // Proses masing-masing file (semua PDF, opsional)
    $file_rek_koran_path     = $uploadPdfKeu('file_rek_koran',     $file_rek_koran_path,     'rek_koran');
    $file_buku_rekening_path = $uploadPdfKeu('file_buku_rekening', $file_buku_rekening_path, 'buku_rek');
    $file_neraca_path        = $uploadPdfKeu('file_neraca',        $file_neraca_path,        'neraca');
    $file_labarugi_path      = $uploadPdfKeu('file_labarugi',      $file_labarugi_path,      'labarugi');

    /*
     ---------------------------------------------------------------
      INSERT atau UPDATE ke tabel keuangan
     ---------------------------------------------------------------
    */
    if ($is_new_keu) {
        $sqlIns = "INSERT INTO keuangan
            (user_id, code_member, instansi_id,
             no_rekening, pemilik_rekening, nama_bank, cabang_bank,
             file_rek_koran, file_buku_rekening, file_neraca, file_labarugi,
             created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmtIns = $pdo->prepare($sqlIns);
        $stmtIns->execute([
            (int)$user_id,
            $code_member,
            $instansi_id,
            $no_rekening,
            $pemilik_rekening,
            $nama_bank,
            $cabang_bank ?: null,
            $file_rek_koran_path ?: '',
            $file_buku_rekening_path ?: '',
            $file_neraca_path ?: '',
            $file_labarugi_path ?: ''
        ]);

        if (file_exists(__DIR__ . '/../includes/notify.php')) {
            require_once __DIR__ . '/../includes/notify.php';
            $keu_id = $pdo->lastInsertId();
            createNotification(
                $pdo,
                $user_id,
                "create_keuangan",
                "Perusahaan " . ($companyForKeu['name'] ?? 'Perusahaan') . " telah menambahkan data keuangan.",
                $keu_id,
                "keuangan"
            );
        }

        header("Location: company_data.php?keu_success=1#keuangan");
        exit;

    } else {
        $sqlUpd = "UPDATE keuangan
                   SET no_rekening = ?, pemilik_rekening = ?, nama_bank = ?, cabang_bank = ?,
                       file_rek_koran = ?, file_buku_rekening = ?, file_neraca = ?, file_labarugi = ?,
                       updated_at = NOW()
                   WHERE user_id = ?";
        $stmtUpd = $pdo->prepare($sqlUpd);
        $stmtUpd->execute([
            $no_rekening,
            $pemilik_rekening,
            $nama_bank,
            $cabang_bank ?: null,
            $file_rek_koran_path ?: '',
            $file_buku_rekening_path ?: '',
            $file_neraca_path ?: '',
            $file_labarugi_path ?: '',
            (int)$user_id
        ]);

        if (file_exists(__DIR__ . '/../includes/notify.php')) {
            require_once __DIR__ . '/../includes/notify.php';
            $keu_id = (int)$keuRow['id'];
            createNotification(
                $pdo,
                $user_id,
                "update_keuangan",
                "Perusahaan " . ($companyForKeu['name'] ?? 'Perusahaan') . " melakukan perubahan data keuangan.",
                $keu_id,
                "keuangan"
            );
        }

        header("Location: company_data.php?keu_update=1#keuangan");
        exit;
    }
}

/*
 ---------------------------------------------------------------------
  HANDLE POST untuk TAB PAJAK
 ---------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['tab'] ?? '') === 'pajak')) {

    // Pastikan user_id valid dari SESSION
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
    if ($user_id <= 0) {
        die('User tidak valid. Silakan login ulang.');
    }

    // Sanitasi input teks
    $npwp_perusahaan = isset($_POST['npwp_perusahaan']) ? trim($_POST['npwp_perusahaan']) : '';
    $npwp_direktur   = isset($_POST['npwp_direktur']) ? trim($_POST['npwp_direktur']) : '';

    // Validasi minimal
    if ($npwp_perusahaan === '' || $npwp_direktur === '') {
        header("Location: company_data.php?pajak_error=1#pajak");
        exit;
    }

    // Ambil data perusahaan untuk code_member & instansi_id
    $stmtCompany = $pdo->prepare("SELECT id, code_member, instansi_id, name FROM companies WHERE user_id = ? LIMIT 1");
    $stmtCompany->execute([$user_id]);
    $companyForPajak = $stmtCompany->fetch(PDO::FETCH_ASSOC);

    $code_member = $companyForPajak['code_member'] ?? null;
    $instansi_id = $companyForPajak['instansi_id'] ?? null;

    // Cek apakah data pajak sudah ada
    $stmtPajak = $pdo->prepare("SELECT * FROM pajak WHERE user_id = ? LIMIT 1");
    $stmtPajak->execute([$user_id]);
    $pajakRow = $stmtPajak->fetch(PDO::FETCH_ASSOC);
    $is_new_pajak = !$pajakRow;

    // Default: pakai file lama jika tidak di-upload ulang
    $file_tanda_daftar_path   = $pajakRow['file_tanda_daftar'] ?? '';
    $file_npwp_perusahaan_path= $pajakRow['file_npwp_perusahaan'] ?? '';
    $file_npwp_direktur_path  = $pajakRow['file_npwp_direktur'] ?? '';
    $file_lapor_pajak_path    = $pajakRow['file_lapor_pajak'] ?? '';

    // Konfigurasi direktori upload: /htdocs/storage_secure/pajak/
    // __DIR__ = .../htdocs/eproc-unpatti/pengadaan/public
    $htdocsRoot = dirname(__DIR__, 3);
    $uploadDir  = $htdocsRoot . '/storage_secure/pajak/';

    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }

    // Helper upload file PDF
    $uploadPdfPajak = function($fieldName, $currentPath, $prefix) use ($uploadDir, $htdocsRoot, $user_id) {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
            return $currentPath; // tidak ada file baru
        }

        if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            header("Location: company_data.php?pajak_uploaderror=1#pajak");
            exit;
        }

        $allowedExt = ['pdf'];
        $maxSize    = 2 * 1024 * 1024; // 2 MB

        $originalName = $_FILES[$fieldName]['name'];
        $tmpName      = $_FILES[$fieldName]['tmp_name'];
        $size         = $_FILES[$fieldName]['size'];

        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt)) {
            header("Location: company_data.php?pajak_filetype=1#pajak");
            exit;
        }

        if ($size > $maxSize) {
            header("Location: company_data.php?pajak_filesize=1#pajak");
            exit;
        }

        if (function_exists('mime_content_type')) {
            $mime = mime_content_type($tmpName);
            if ($mime !== 'application/pdf') {
                header("Location: company_data.php?pajak_filetype=1#pajak");
                exit;
            }
        }

        if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
            header("Location: company_data.php?pajak_uploadfail=1#pajak");
            exit;
        }

        $newFileName = $prefix . '_' . $user_id . '_' . time() . '.pdf';
        $destPathAbs = $uploadDir . $newFileName;
        $destPathRel = 'storage_secure/pajak/' . $newFileName;

        if (move_uploaded_file($tmpName, $destPathAbs)) {
            // Hapus file lama jika ada
            if (!empty($currentPath)) {
                $oldAbs = $htdocsRoot . '/' . ltrim($currentPath, '/');
                if (file_exists($oldAbs)) {
                    @unlink($oldAbs);
                }
            }
            return $destPathRel;
        } else {
            header("Location: company_data.php?pajak_uploadfail=1#pajak");
            exit;
        }
    };

    // Proses masing-masing file (semua PDF, opsional)
    $file_tanda_daftar_path    = $uploadPdfPajak('file_tanda_daftar',    $file_tanda_daftar_path,    'tanda_daftar');
    $file_npwp_perusahaan_path = $uploadPdfPajak('file_npwp_perusahaan', $file_npwp_perusahaan_path, 'npwp_perusahaan');
    $file_npwp_direktur_path   = $uploadPdfPajak('file_npwp_direktur',   $file_npwp_direktur_path,   'npwp_direktur');
    $file_lapor_pajak_path     = $uploadPdfPajak('file_lapor_pajak',     $file_lapor_pajak_path,     'lapor_pajak');

    /*
     ---------------------------------------------------------------
      INSERT atau UPDATE ke tabel pajak
     ---------------------------------------------------------------
    */
    if ($is_new_pajak) {
        $sqlIns = "INSERT INTO pajak
            (user_id, code_member, instansi_id,
             npwp_perusahaan, npwp_direktur,
             file_tanda_daftar, file_npwp_perusahaan, file_npwp_direktur, file_lapor_pajak,
             created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmtIns = $pdo->prepare($sqlIns);
        $stmtIns->execute([
            (int)$user_id,
            $code_member,
            $instansi_id,
            $npwp_perusahaan,
            $npwp_direktur,
            $file_tanda_daftar_path ?: '',
            $file_npwp_perusahaan_path ?: '',
            $file_npwp_direktur_path ?: '',
            $file_lapor_pajak_path ?: ''
        ]);

        if (file_exists(__DIR__ . '/../includes/notify.php')) {
            require_once __DIR__ . '/../includes/notify.php';
            $pajak_id = $pdo->lastInsertId();
            createNotification(
                $pdo,
                $user_id,
                "create_pajak",
                "Perusahaan " . ($companyForPajak['name'] ?? 'Perusahaan') . " telah menambahkan data pajak.",
                $pajak_id,
                "pajak"
            );
        }

        header("Location: company_data.php?pajak_success=1#pajak");
        exit;

    } else {
        $sqlUpd = "UPDATE pajak
                   SET npwp_perusahaan = ?, npwp_direktur = ?,
                       file_tanda_daftar = ?, file_npwp_perusahaan = ?, file_npwp_direktur = ?, file_lapor_pajak = ?,
                       updated_at = NOW()
                   WHERE user_id = ?";
        $stmtUpd = $pdo->prepare($sqlUpd);
        $stmtUpd->execute([
            $npwp_perusahaan,
            $npwp_direktur,
            $file_tanda_daftar_path ?: '',
            $file_npwp_perusahaan_path ?: '',
            $file_npwp_direktur_path ?: '',
            $file_lapor_pajak_path ?: '',
            (int)$user_id
        ]);

        if (file_exists(__DIR__ . '/../includes/notify.php')) {
            require_once __DIR__ . '/../includes/notify.php';
            $pajak_id = (int)$pajakRow['id'];
            createNotification(
                $pdo,
                $user_id,
                "update_pajak",
                "Perusahaan " . ($companyForPajak['name'] ?? 'Perusahaan') . " melakukan perubahan data pajak.",
                $pajak_id,
                "pajak"
            );
        }

        header("Location: company_data.php?pajak_update=1#pajak");
        exit;
    }
}

/*
 ---------------------------------------------------------------------
  HANDLE POST untuk TAB PEMILIK & PENGURUS
 ---------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['tab'] ?? '') === 'pemilik')) {

    // Pastikan user_id valid dari SESSION
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
    if ($user_id <= 0) {
        die('User tidak valid. Silakan login ulang.');
    }

    // Sanitasi input teks
    $pemilik                   = isset($_POST['pemilik']) ? trim($_POST['pemilik']) : '';
    $jenis_identitas_pemilik   = isset($_POST['jenis_identitas_pemilik']) ? trim($_POST['jenis_identitas_pemilik']) : '';
    $nomor_identitas_pemilik   = isset($_POST['nomor_identitas_pemilik']) ? trim($_POST['nomor_identitas_pemilik']) : '';

    $direktur                  = isset($_POST['direktur']) ? trim($_POST['direktur']) : '';
    $jenis_identitas_direktur  = isset($_POST['jenis_identitas_direktur']) ? trim($_POST['jenis_identitas_direktur']) : '';
    $nomor_identitas_direktur  = isset($_POST['nomor_identitas_direktur']) ? trim($_POST['nomor_identitas_direktur']) : '';

    // Validasi minimal
    if (
        $pemilik === '' || $jenis_identitas_pemilik === '' || $nomor_identitas_pemilik === '' ||
        $direktur === '' || $jenis_identitas_direktur === '' || $nomor_identitas_direktur === ''
    ) {
        header("Location: company_data.php?pengurus_error=1#pemilik");
        exit;
    }

    // Ambil data perusahaan untuk code_member & instansi_id
    $stmtCompany = $pdo->prepare("SELECT id, code_member, instansi_id, name FROM companies WHERE user_id = ? LIMIT 1");
    $stmtCompany->execute([$user_id]);
    $companyForPengurus = $stmtCompany->fetch(PDO::FETCH_ASSOC);

    $code_member = $companyForPengurus['code_member'] ?? null;
    $instansi_id = $companyForPengurus['instansi_id'] ?? null;

    // Cek apakah data pengurus sudah ada
    $stmtPengurus = $pdo->prepare("SELECT * FROM pengurus WHERE user_id = ? LIMIT 1");
    $stmtPengurus->execute([$user_id]);
    $pengurusRow = $stmtPengurus->fetch(PDO::FETCH_ASSOC);
    $is_new_pengurus = !$pengurusRow;

    // Default: pakai file lama jika tidak di-upload ulang
    $file_kartu_pemilik_path  = $pengurusRow['file_kartu_pemilik'] ?? '';
    $file_kartu_direktur_path = $pengurusRow['file_kartu_direktur'] ?? '';

    // Konfigurasi direktori upload: /htdocs/storage_secure/pengurus/
    // __DIR__ = .../htdocs/eproc-unpatti/pengadaan/public
    $htdocsRoot = dirname(__DIR__, 3);
    $uploadDir  = $htdocsRoot . '/storage_secure/pengurus/';

    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }

    // Helper upload file PDF
    $uploadPdfPengurus = function($fieldName, $currentPath, $prefix) use ($uploadDir, $htdocsRoot, $user_id) {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
            return $currentPath; // tidak ada file baru
        }

        if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            header("Location: company_data.php?pengurus_uploaderror=1#pemilik");
            exit;
        }

        $allowedExt = ['pdf'];
        $maxSize    = 2 * 1024 * 1024; // 2 MB

        $originalName = $_FILES[$fieldName]['name'];
        $tmpName      = $_FILES[$fieldName]['tmp_name'];
        $size         = $_FILES[$fieldName]['size'];

        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt)) {
            header("Location: company_data.php?pengurus_filetype=1#pemilik");
            exit;
        }

        if ($size > $maxSize) {
            header("Location: company_data.php?pengurus_filesize=1#pemilik");
            exit;
        }

        if (function_exists('mime_content_type')) {
            $mime = mime_content_type($tmpName);
            if ($mime !== 'application/pdf') {
                header("Location: company_data.php?pengurus_filetype=1#pemilik");
                exit;
            }
        }

        if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
            header("Location: company_data.php?pengurus_uploadfail=1#pemilik");
            exit;
        }

        $newFileName = $prefix . '_' . $user_id . '_' . time() . '.pdf';
        $destPathAbs = $uploadDir . $newFileName;
        $destPathRel = 'storage_secure/pengurus/' . $newFileName;

        if (move_uploaded_file($tmpName, $destPathAbs)) {
            // Hapus file lama jika ada
            if (!empty($currentPath)) {
                $oldAbs = $htdocsRoot . '/' . ltrim($currentPath, '/');
                if (file_exists($oldAbs)) {
                    @unlink($oldAbs);
                }
            }
            return $destPathRel;
        } else {
            header("Location: company_data.php?pengurus_uploadfail=1#pemilik");
            exit;
        }
    };

    // Proses masing-masing file (PDF, opsional)
    $file_kartu_pemilik_path  = $uploadPdfPengurus('file_kartu_pemilik',  $file_kartu_pemilik_path,  'kartu_pemilik');
    $file_kartu_direktur_path = $uploadPdfPengurus('file_kartu_direktur', $file_kartu_direktur_path, 'kartu_direktur');

    /*
     ---------------------------------------------------------------
      INSERT atau UPDATE ke tabel pengurus
     ---------------------------------------------------------------
    */
    if ($is_new_pengurus) {
        $sqlIns = "INSERT INTO pengurus
            (user_id, code_member, instansi_id,
             pemilik, jenis_identitas_pemilik, nomor_identitas_pemilik,
             direktur, jenis_identitas_direktur, nomor_identitas_direktur,
             file_kartu_pemilik, file_kartu_direktur,
             created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmtIns = $pdo->prepare($sqlIns);
        $stmtIns->execute([
            (int)$user_id,
            $code_member,
            $instansi_id,
            $pemilik,
            $jenis_identitas_pemilik,
            $nomor_identitas_pemilik,
            $direktur,
            $jenis_identitas_direktur,
            $nomor_identitas_direktur,
            $file_kartu_pemilik_path ?: '',
            $file_kartu_direktur_path ?: ''
        ]);

        if (file_exists(__DIR__ . '/../includes/notify.php')) {
            require_once __DIR__ . '/../includes/notify.php';
            $pengurus_id = $pdo->lastInsertId();
            createNotification(
                $pdo,
                $user_id,
                "create_pengurus",
                "Perusahaan " . ($companyForPengurus['name'] ?? 'Perusahaan') . " telah menambahkan data pemilik & pengurus.",
                $pengurus_id,
                "pemilik"
            );
        }

        header("Location: company_data.php?pengurus_success=1#pemilik");
        exit;

    } else {
        $sqlUpd = "UPDATE pengurus
                   SET pemilik = ?, jenis_identitas_pemilik = ?, nomor_identitas_pemilik = ?,
                       direktur = ?, jenis_identitas_direktur = ?, nomor_identitas_direktur = ?,
                       file_kartu_pemilik = ?, file_kartu_direktur = ?,
                       updated_at = NOW()
                   WHERE user_id = ?";
        $stmtUpd = $pdo->prepare($sqlUpd);
        $stmtUpd->execute([
            $pemilik,
            $jenis_identitas_pemilik,
            $nomor_identitas_pemilik,
            $direktur,
            $jenis_identitas_direktur,
            $nomor_identitas_direktur,
            $file_kartu_pemilik_path ?: '',
            $file_kartu_direktur_path ?: '',
            (int)$user_id
        ]);

        if (file_exists(__DIR__ . '/../includes/notify.php')) {
            require_once __DIR__ . '/../includes/notify.php';
            $pengurus_id = (int)$pengurusRow['id'];
            createNotification(
                $pdo,
                $user_id,
                "update_pengurus",
                "Perusahaan " . ($companyForPengurus['name'] ?? 'Perusahaan') . " melakukan perubahan data pemilik & pengurus.",
                $pengurus_id,
                "pemilik"
            );
        }

        header("Location: company_data.php?pengurus_update=1#pemilik");
        exit;
    }
}

/*
 ---------------------------------------------------------------------
  HANDLE POST untuk TAB TENAGA AHLI
 ---------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['tab'] ?? '') === 'tenaga_ahli')) {

    // Pastikan user login
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit;
    }

    $user_id = (int)$_SESSION['user_id'];
    $action  = $_POST['action'] ?? 'add';

    // Ambil data perusahaan untuk code_member & instansi_id
    $stmtCompany = $pdo->prepare("SELECT id, code_member, instansi_id, name FROM companies WHERE user_id = ? LIMIT 1");
    $stmtCompany->execute([$user_id]);
    $companyForTA = $stmtCompany->fetch(PDO::FETCH_ASSOC);

    $code_member = $companyForTA['code_member'] ?? null;
    $instansi_id = $companyForTA['instansi_id'] ?? null;

    // Konfigurasi direktori upload: /htdocs/storage_secure/tenaga_ahli/
    // __DIR__ = .../htdocs/eproc-unpatti/pengadaan/public
    $htdocsRoot  = dirname(__DIR__, 3); // -> .../htdocs
    $uploadDirTA = $htdocsRoot . '/storage_secure/tenaga_ahli/';

    if (!is_dir($uploadDirTA)) {
        @mkdir($uploadDirTA, 0755, true);
    }

    /**
     * Upload file PDF tenaga ahli (CV / Sertifikat)
     * @param string      $fieldName   nama field di form, misal 'file_cv'
     * @param string      $prefix      prefix nama file, misal 'cv_ta' atau 'sert_ta'
     * @param string|null $oldRelPath  path lama (relatif dari htdocs), akan dihapus jika ada
     * @return string path relatif baru untuk disimpan di DB
     */
    $uploadPdfTA = function($fieldName, $prefix, $oldRelPath = null) use ($uploadDirTA, $htdocsRoot, $user_id) {

        // Tidak ada file baru → pakai path lama
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
            return $oldRelPath ?? '';
        }

        if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            header("Location: company_data.php?ta_uploaderror=1#tenaga_ahli");
            exit;
        }

        $allowedExt = ['pdf'];
        $maxSize    = 2 * 1024 * 1024; // 2 MB

        $originalName = $_FILES[$fieldName]['name'];
        $tmpName      = $_FILES[$fieldName]['tmp_name'];
        $size         = $_FILES[$fieldName]['size'];

        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt)) {
            header("Location: company_data.php?ta_filetype=1#tenaga_ahli");
            exit;
        }

        if ($size > $maxSize) {
            header("Location: company_data.php?ta_filesize=1#tenaga_ahli");
            exit;
        }

        if (function_exists('mime_content_type')) {
            $mime = mime_content_type($tmpName);
            if ($mime !== 'application/pdf') {
                header("Location: company_data.php?ta_filetype=1#tenaga_ahli");
                exit;
            }
        }

        if (!is_dir($uploadDirTA) || !is_writable($uploadDirTA)) {
            header("Location: company_data.php?ta_uploadfail=1#tenaga_ahli");
            exit;
        }

        $newFileName = $prefix . '_' . $user_id . '_' . time() . '.pdf';
        $destPathAbs = $uploadDirTA . $newFileName;
        $destPathRel = 'storage_secure/tenaga_ahli/' . $newFileName; // disimpan di DB

        if (move_uploaded_file($tmpName, $destPathAbs)) {

            // Hapus file lama jika ada
            if (!empty($oldRelPath)) {
                $oldAbs = $htdocsRoot . '/' . ltrim($oldRelPath, '/');
                if (file_exists($oldAbs)) {
                    @unlink($oldAbs);
                }
            }

            return $destPathRel;
        } else {
            header("Location: company_data.php?ta_uploadfail=1#tenaga_ahli");
            exit;
        }
    };

    /*
     ---------------------------------------------------------------
      ACTION: DELETE TENAGA AHLI
     ---------------------------------------------------------------
    */
    if ($action === 'delete') {
        $ta_id = isset($_POST['ta_id']) ? (int)$_POST['ta_id'] : 0;

        if ($ta_id > 0) {
            // Ambil data lama untuk hapus file
            $stmtOld = $pdo->prepare("SELECT file_cv, file_sertifikat FROM tenaga_ahli WHERE id = ? AND user_id = ? LIMIT 1");
            $stmtOld->execute([$ta_id, $user_id]);
            $old = $stmtOld->fetch(PDO::FETCH_ASSOC);

            if ($old) {
                // Hapus file fisik jika ada
                foreach (['file_cv', 'file_sertifikat'] as $f) {
                    if (!empty($old[$f])) {
                        $oldAbs = $htdocsRoot . '/' . ltrim($old[$f], '/');
                        if (file_exists($oldAbs)) {
                            @unlink($oldAbs);
                        }
                    }
                }

                // Hapus row
                $del = $pdo->prepare("DELETE FROM tenaga_ahli WHERE id = ? AND user_id = ?");
                $del->execute([$ta_id, $user_id]);
            }
        }

        header("Location: company_data.php?ta_deleted=1#tenaga_ahli");
        exit;
    }

    /*
     ---------------------------------------------------------------
      ACTION: ADD TENAGA AHLI
     ---------------------------------------------------------------
    */
    if ($action === 'add') {

        $nama       = trim($_POST['nama'] ?? '');
        $jabatan    = trim($_POST['jabatan'] ?? '');
        $pendidikan = trim($_POST['pendidikan'] ?? '');
        $keahlian   = trim($_POST['keahlian'] ?? '');
        $pengalaman_tahun = isset($_POST['pengalaman_tahun']) ? (int)$_POST['pengalaman_tahun'] : 0;

        if ($nama === '' || $jabatan === '' || $pendidikan === '') {
            header("Location: company_data.php?ta_error=1#tenaga_ahli");
            exit;
        }

        // Upload file (opsional)
        $file_cv_path         = $uploadPdfTA('file_cv', 'cv_ta', null);
        $file_sertifikat_path = $uploadPdfTA('file_sertifikat', 'sert_ta', null);

        $sqlIns = "INSERT INTO tenaga_ahli
            (user_id, code_member, instansi_id,
             nama, jabatan, pendidikan, keahlian, pengalaman_tahun,
             file_cv, file_sertifikat,
             created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

        $stmtIns = $pdo->prepare($sqlIns);
        $stmtIns->execute([
            $user_id,
            $code_member,
            $instansi_id,
            $nama,
            $jabatan,
            $pendidikan,
            $keahlian ?: null,
            $pengalaman_tahun ?: null,
            $file_cv_path ?: '',
            $file_sertifikat_path ?: ''
        ]);

        header("Location: company_data.php?ta_success=1#tenaga_ahli");
        exit;
    }

    /*
     ---------------------------------------------------------------
      ACTION: EDIT TENAGA AHLI
     ---------------------------------------------------------------
    */
    if ($action === 'edit') {

        $ta_id = isset($_POST['ta_id']) ? (int)$_POST['ta_id'] : 0;
        if ($ta_id <= 0) {
            header("Location: company_data.php?ta_edit_error=1#tenaga_ahli");
            exit;
        }

        $nama       = trim($_POST['nama'] ?? '');
        $jabatan    = trim($_POST['jabatan'] ?? '');
        $pendidikan = trim($_POST['pendidikan'] ?? '');
        $keahlian   = trim($_POST['keahlian'] ?? '');
        $pengalaman_tahun = isset($_POST['pengalaman_tahun']) ? (int)$_POST['pengalaman_tahun'] : 0;

        if ($nama === '' || $jabatan === '' || $pendidikan === '') {
            header("Location: company_data.php?ta_edit_error=1#tenaga_ahli");
            exit;
        }

        // Ambil data lama
        $stmtOld = $pdo->prepare("SELECT * FROM tenaga_ahli WHERE id = ? AND user_id = ? LIMIT 1");
        $stmtOld->execute([$ta_id, $user_id]);
        $old = $stmtOld->fetch(PDO::FETCH_ASSOC);

        if (!$old) {
            header("Location: company_data.php?ta_edit_error=1#tenaga_ahli");
            exit;
        }

        // File: jika user tidak upload baru → tetap pakai lama
        $file_cv_path = $uploadPdfTA('file_cv', 'cv_ta', $old['file_cv'] ?? null);
        $file_sertifikat_path = $uploadPdfTA('file_sertifikat', 'sert_ta', $old['file_sertifikat'] ?? null);

        $upd = $pdo->prepare("
            UPDATE tenaga_ahli SET 
                nama = ?, 
                jabatan = ?, 
                pendidikan = ?, 
                pengalaman_tahun = ?, 
                keahlian = ?, 
                file_cv = ?, 
                file_sertifikat = ?, 
                updated_at = NOW()
            WHERE id = ? AND user_id = ?
        ");

        $upd->execute([
            $nama,
            $jabatan,
            $pendidikan,
            $pengalaman_tahun,
            $keahlian,
            $file_cv_path ?: '',
            $file_sertifikat_path ?: '',
            $ta_id,
            $user_id
        ]);

        header("Location: company_data.php?ta_edit_success=1#tenaga_ahli");
        exit;
    }
}




/* ------------------------------------------------------------------
   Setelah POST handling selesai (redirect sudah terjadi jika POST),
   kita sekarang boleh include header/menu dan menampilkan HTML.
   ------------------------------------------------------------------ */

// Ambil data perusahaan (untuk ditampilkan pada tab)
$stmt = $pdo->prepare("SELECT * FROM companies WHERE user_id = ?");
$stmt->execute([$user_id]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);
$is_new = !$company;

// Ambil data alamat (untuk tab Alamat)
$stmtAddrView = $pdo->prepare("SELECT * FROM address WHERE user_id = ? LIMIT 1");
$stmtAddrView->execute([$user_id]);
$address = $stmtAddrView->fetch(PDO::FETCH_ASSOC);
$is_new_address = !$address;

// Ambil data ijin usaha
$stmtIzinView = $pdo->prepare("SELECT * FROM ijin_usaha WHERE user_id = ? LIMIT 1");
$stmtIzinView->execute([$user_id]);
$izin = $stmtIzinView->fetch(PDO::FETCH_ASSOC);
$is_new_izin = !$izin;

// Ambil data akta perusahaan
$stmtAktaView = $pdo->prepare("SELECT * FROM akta_perusahaan WHERE user_id = ? LIMIT 1");
$stmtAktaView->execute([$user_id]);
$akta = $stmtAktaView->fetch(PDO::FETCH_ASSOC);
$is_new_akta = !$akta;

// Ambil data keuangan
$stmtKeuView = $pdo->prepare("SELECT * FROM keuangan WHERE user_id = ? LIMIT 1");
$stmtKeuView->execute([$user_id]);
$keuangan = $stmtKeuView->fetch(PDO::FETCH_ASSOC);
$is_new_keu = !$keuangan;

// Ambil data pajak
$stmtPajakView = $pdo->prepare("SELECT * FROM pajak WHERE user_id = ? LIMIT 1");
$stmtPajakView->execute([$user_id]);
$pajak = $stmtPajakView->fetch(PDO::FETCH_ASSOC);
$is_new_pajak = !$pajak;

// Ambil data pemilik & pengurus
$stmtPengurusView = $pdo->prepare("SELECT * FROM pengurus WHERE user_id = ? LIMIT 1");
$stmtPengurusView->execute([$user_id]);
$pengurus = $stmtPengurusView->fetch(PDO::FETCH_ASSOC);
$is_new_pengurus = !$pengurus;

// Ambil daftar tenaga ahli
$stmtTAView = $pdo->prepare("SELECT * FROM tenaga_ahli WHERE user_id = ? ORDER BY id ASC");
$stmtTAView->execute([$user_id]);
$tenaga_ahli_list = $stmtTAView->fetchAll(PDO::FETCH_ASSOC);


include 'public_header.php';
include 'public_menu.php';
?>

<style>
    .nav-tabs .nav-link.active {
        font-weight: bold;
    }

    /* Pastikan option select berwarna hitam */
    select.form-select option {
        color: #000 !important;
    }
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container py-1">

            <div class="card shadow-sm">
                <div class="card-header bg-info text-white text-center">
                    <h4 class="mb-0">Data & Dokumen Perusahaan</h4>
                </div>

                <div class="card-body">

                    <!-- ========== BOOTSTRAP 5 TOGGLE TABS ========== -->
                    <ul class="nav nav-tabs" id="companyTabs" role="tablist">

                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="identitas-tab" data-bs-toggle="tab"
                                data-bs-target="#identitas" type="button" role="tab">Identitas</button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="alamat-tab" data-bs-toggle="tab"
                                data-bs-target="#alamat" type="button" role="tab">Alamat</button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="izin-tab" data-bs-toggle="tab"
                                data-bs-target="#izin" type="button" role="tab">Izin Usaha</button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="akta-tab" data-bs-toggle="tab"
                                data-bs-target="#akta" type="button" role="tab">Akta Perusahaan</button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="keuangan-tab" data-bs-toggle="tab"
                                data-bs-target="#keuangan" type="button" role="tab">Keuangan</button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pajak-tab" data-bs-toggle="tab"
                                data-bs-target="#pajak" type="button" role="tab">Pajak</button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pemilik-tab" data-bs-toggle="tab"
                                data-bs-target="#pemilik" type="button" role="tab">Pemilik & Pengurus</button>
                        </li>

                        <!-- <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pengalaman-tab" data-bs-toggle="tab"
                                data-bs-target="#pengalaman" type="button" role="tab">Pengalaman</button>
                        </li> -->

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tenaga-ahli-tab" data-bs-toggle="tab"
                                data-bs-target="#tenaga_ahli" type="button" role="tab">Tenaga Ahli</button>
                        </li>

                        <!-- <li class="nav-item" role="presentation">
                            <button class="nav-link" id="peralatan-tab" data-bs-toggle="tab"
                                data-bs-target="#peralatan" type="button" role="tab">Peralatan</button>
                        </li> -->

                    </ul>

                    <div class="tab-content mt-3" id="companyTabsContent">

                        <!-- ===================== TAB IDENTITAS ===================== -->
                        <div class="tab-pane fade show active" id="identitas" role="tabpanel">

                            <div class="col-md-12 grid-margin stretch-card">
                                <div class="card bg-light">
                                    <div class="card-header bg-light text-black text-center">
                                        <h4 class="mb-0">Identitas Perusahaan</h4>
                                    </div>
                                    <div class="card-body">

                                        <?php if (isset($_GET['success'])): ?>
                                            <div class="alert alert-success">Data berhasil disimpan.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['update'])): ?>
                                            <div class="alert alert-success">Data berhasil diperbarui.</div>
                                        <?php endif; ?>

                                        <form method="POST" novalidate>
                                            <!-- hidden tab identifier -->
                                            <input type="hidden" name="tab" value="identitas">

                                            <!-- STATUS (badge) -->
                                            <?php
                                            $status = $company['status'] ?? 'draft';
                                            $badge_map = [
                                                'draft' => 'secondary',
                                                'submitted' => 'primary',
                                                'under_review' => 'warning',
                                                'verified' => 'success',
                                                'rejected' => 'danger'
                                            ];
                                            $badge_class = $badge_map[$status] ?? 'secondary';
                                            ?>
                                            <div class="mb-3">
                                                <label class="form-label"><strong>Status Verifikasi 🔒</strong></label><br>
                                                <span class="badge bg-<?= htmlspecialchars($badge_class) ?> px-3 py-2" style="font-size:1rem;">
                                                    <?= htmlspecialchars(strtoupper(str_replace('_', ' ', $status))) ?>
                                                </span>
                                            </div>

                                            <!-- NOTES ADMIN (readonly) -->
                                            <div class="mb-4">
                                                <label class="form-label"><strong>Catatan dari Admin 🔒</strong></label>
                                                <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($company['notes'] ?? '') ?></textarea>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <!-- CODE MEMBER -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Kode Member 🔒</label>
                                                        <input type="text" class="form-control" value="<?= htmlspecialchars($company['code_member'] ?? '') ?>" readonly>
                                                    </div>

                                                    <!-- NAMA PERUSAHAAN -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Perusahaan 🔒</label>
                                                        <input type="text" class="form-control" value="<?= htmlspecialchars($company['name'] ?? '') ?>" readonly>
                                                    </div>

                                                    <!-- EMAIL -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Email 🔒</label>
                                                        <input type="email" class="form-control" value="<?= htmlspecialchars($company['email'] ?? '') ?>" readonly>
                                                    </div>

                                                    <!-- PHONE -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Telepon ✏️</label>
                                                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($company['phone'] ?? '') ?>">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <!-- OWNERSHIP -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Jenis Kepemilikan ✏️</label>
                                                        <select name="ownership" class="form-select" required>
                                                            <option value="Swasta" <?= (($company['ownership'] ?? '') === 'Swasta') ? 'selected' : '' ?>>Swasta</option>
                                                            <option value="Publik" <?= (($company['ownership'] ?? '') === 'Publik') ? 'selected' : '' ?>>Publik</option>
                                                        </select>
                                                    </div>

                                                    <!-- ESTABLISHED -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Tanggal Berdiri ✏️</label>
                                                        <input type="date" name="established" class="form-control" value="<?= htmlspecialchars($company['established'] ?? '') ?>">
                                                    </div>

                                                    <!-- WEBSITE -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Website ✏️</label>
                                                        <input type="url" name="website" class="form-control" placeholder="https://..." value="<?= htmlspecialchars($company['website'] ?? '') ?>">
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-success px-4">
                                                    <?= $is_new ? 'Simpan Identitas' : 'Update Identitas' ?>
                                                </button>
                                            </div>

                                            <div class="alert alert-info p-2 mt-3">
                                                <strong>Keterangan:</strong><br>
                                                🔒 <span class="badge badge-locked text-black">Tidak bisa diedit - Hubungi Admin Eproc-Unpatti</span><br>
                                                ✏️ <span class="badge badge-editable text-black">Bisa diedit</span>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- ===================== TAB ALAMAT ===================== -->
                        
                        <div class="tab-pane fade" id="alamat" role="tabpanel">
                             
                            <div class="col-md-12 grid-margin stretch-card">
                                <div class="card bg-light">
                                    <div class="card-header bg-light text-black text-center">
                                        <h4 class="mb-0">Alamat Perusahaan</h4>
                                    </div>
                                    <div class="card-body">

                                        <?php if (isset($_GET['alamat_success'])): ?>
                                            <div class="alert alert-success">Alamat berhasil disimpan.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['alamat_update'])): ?>
                                            <div class="alert alert-success">Alamat berhasil diperbarui.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['alamat_error'])): ?>
                                            <div class="alert alert-danger"> <strong> Lengkapi semua field alamat yang wajib diisi. </strong> </div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['alamat_filetype'])): ?>
                                            <div class="alert alert-danger">Tipe file domisili harus <strong> PDF. </strong></div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['alamat_filesize'])): ?>
                                            <div class="alert alert-danger">Ukuran file domisili maksimal <strong> 2MB. </strong></div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['alamat_uploadfail']) || isset($_GET['alamat_uploaderror'])): ?>
                                            <div class="alert alert-danger">Terjadi kesalahan saat mengunggah file domisili.</div>
                                        <?php endif; ?>

                                        <?php
                                        $verified_by = $address['verified_by'] ?? null;
                                        $verified_at = $address['verified_at'] ?? null;

                                        if ($verified_by && $verified_at) {
                                            $alamat_status_text  = 'TERVERIFIKASI';
                                            $alamat_badge_class  = 'success';
                                        } else {
                                            $alamat_status_text  = 'BELUM DIVERIFIKASI';
                                            $alamat_badge_class  = 'secondary';
                                        }
                                        ?>

                                        <div class="mb-3">
                                            <label class="form-label"><strong>Status Verifikasi Alamat 🔒</strong></label><br>
                                            <span class="badge bg-<?= htmlspecialchars($alamat_badge_class) ?> px-3 py-2" style="font-size:1rem;">
                                                <?= htmlspecialchars($alamat_status_text) ?>
                                            </span>
                                            <?php if ($verified_at): ?>
                                                <small class="text-muted ms-2">pada: <?= htmlspecialchars($verified_at) ?></small>
                                            <?php endif; ?>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label"><strong>Catatan dari Admin (Alamat) 🔒</strong></label>
                                            <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($address['notes'] ?? '') ?></textarea>
                                        </div>

                                        <form method="POST" enctype="multipart/form-data" novalidate>
                                            <input type="hidden" name="tab" value="alamat">

                                            <div class="row">
                                                <div class="col-md-6">

                                                    <div class="mb-3">
                                                        <label class="form-label">Provinsi ✏️</label>
                                                        <input type="text" name="provinsi" class="form-control"
                                                            value="<?= htmlspecialchars($address['provinsi'] ?? '') ?>" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Kabupaten/Kota ✏️</label>
                                                        <input type="text" name="kabupaten" class="form-control"
                                                            value="<?= htmlspecialchars($address['kabupaten'] ?? '') ?>" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Kecamatan ✏️</label>
                                                        <input type="text" name="kecamatan" class="form-control"
                                                            value="<?= htmlspecialchars($address['kecamatan'] ?? '') ?>" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Kelurahan/Desa ✏️</label>
                                                        <input type="text" name="kelurahan" class="form-control"
                                                            value="<?= htmlspecialchars($address['kelurahan'] ?? '') ?>" required>
                                                    </div>

                                                </div>

                                                <div class="col-md-6">

                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Jalan / Detail Alamat ✏️</label>
                                                        <input type="text" name="jalan" class="form-control"
                                                            placeholder="Jl. Contoh No. 10, RT/RW dsb."
                                                            value="<?= htmlspecialchars($address['jalan'] ?? '') ?>" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Kode Pos ✏️</label>
                                                        <input type="text" name="kodepos" class="form-control"
                                                            value="<?= htmlspecialchars($address['kodepos'] ?? '') ?>" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">File Surat Domisili / Keterangan Alamat ✏️</label>                          
                                                        <?php if (!empty($address['file_domisili'])): ?>
                                                            <div class="mb-2">
                                                                <a href="/<?= htmlspecialchars($address['file_domisili']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                    Lihat file saat ini
                                                                </a>
                                                            </div>
                                                            <?php endif; ?>


                                                        <input type="file" name="file_domisili" class="form-control">
                                                        <small class="text-muted">
                                                            (*** Hanya menerima Format: PDF, maks 2MB ***)
                                                        </small>
                                                    </div>
                                                    
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-success px-4">
                                                    <?= $is_new_address ? 'Simpan Alamat' : 'Update Alamat' ?>
                                                </button>
                                            </div>

                                            <div class="alert alert-info p-2 mt-3">
                                                <strong>Keterangan:</strong><br>
                                                🔒 <span class="badge badge-locked text-black">Tidak bisa diedit - Hanya Admin yang dapat mengubah status/verifikasi & catatan.</span><br>
                                                ✏️ <span class="badge badge-editable text-black">Bisa diedit oleh perusahaan (alamat & file domisili).</span>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- ===================== TAB IZIN USAHA ===================== -->                        
                        <!-- <div class="tab-pane fade" id="izin" role="tabpanel">
                            <div class="alert alert-warning">
                                Halaman <strong>ini</strong> belum diimplementasikan.
                            </div>
                        </div> -->
                      
                        <div class="tab-pane fade" id="izin" role="tabpanel">

                            <div class="col-md-12 grid-margin stretch-card">
                                <div class="card bg-light">
                                    <div class="card-header bg-light text-black text-center">
                                        <h4 class="mb-0">Izin Usaha</h4>
                                    </div>
                                    <div class="card-body">

                                        <!-- Alert pesan -->
                                        <?php if (isset($_GET['izin_success'])): ?>
                                            <div class="alert alert-success">Data izin usaha berhasil disimpan.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['izin_update'])): ?>
                                            <div class="alert alert-success">Data izin usaha berhasil diperbarui.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['izin_error'])): ?>
                                            <div class="alert alert-danger">Lengkapi semua field teks yang wajib diisi.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['izin_filetype'])): ?>
                                            <div class="alert alert-danger">Semua berkas izin usaha harus berformat <strong>PDF</strong>.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['izin_filesize'])): ?>
                                            <div class="alert alert-danger">Ukuran setiap file maksimal <strong>2 MB</strong>.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['izin_uploadfail']) || isset($_GET['izin_uploaderror'])): ?>
                                            <div class="alert alert-danger">Terjadi kesalahan saat mengunggah file izin usaha.</div>
                                        <?php endif; ?>

                                        <?php
                                        $izin_verified_by = $izin['verified_by'] ?? null;
                                        $izin_verified_at = $izin['verified_at'] ?? null;

                                        if ($izin_verified_by && $izin_verified_at) {
                                            $izin_status_text  = 'TERVERIFIKASI';
                                            $izin_badge_class  = 'success';
                                        } else {
                                            $izin_status_text  = 'BELUM DIVERIFIKASI';
                                            $izin_badge_class  = 'secondary';
                                        }
                                        ?>

                                        <!-- Status verifikasi -->
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Status Verifikasi Izin Usaha 🔒</strong></label><br>
                                            <span class="badge bg-<?= htmlspecialchars($izin_badge_class) ?> px-3 py-2" style="font-size:1rem;">
                                                <?= htmlspecialchars($izin_status_text) ?>
                                            </span>
                                            <?php if ($izin_verified_at): ?>
                                                <small class="text-muted ms-2">pada: <?= htmlspecialchars($izin_verified_at) ?></small>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Catatan admin -->
                                        <div class="mb-4">
                                            <label class="form-label"><strong>Catatan Admin (Izin Usaha) 🔒</strong></label>
                                            <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($izin['notes'] ?? '') ?></textarea>
                                        </div>

                                        <form method="POST" enctype="multipart/form-data" novalidate>
                                            <input type="hidden" name="tab" value="izin">

                                            <div class="row">
                                                <div class="col-md-6">

                                                    <div class="mb-3">
                                                        <label class="form-label">Kualifikasi Usaha ✏️</label>
                                                        <!-- <input type="text" name="kualifikasi_usaha" class="form-control"
                                                            value="<?= htmlspecialchars($izin['kualifikasi_usaha'] ?? '') ?>" required> -->
                                                        <select name="kualifikasi_usaha" class="form-select" required>
                                                            <?php $usahaVal = $izin['kualifikasi_usaha'] ?? ''; ?>
                                                            <option value="">-- Pilih --</option>
                                                            <option value="Mikro" <?= $usahaVal === 'Mikro' ? 'selected' : '' ?>>Mikro</option>
                                                            <option value="Mikro" <?= $usahaVal === 'Kecil' ? 'selected' : '' ?>>Kecil</option>
                                                            <option value="Menengah" <?= $usahaVal === 'Menengah' ? 'selected' : '' ?>>Menengah</option>
                                                            <option value="Besar" <?= $usahaVal === 'Besar' ? 'selected' : '' ?>>Besar</option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Kualifikasi Pengadaan ✏️</label>
                                                        <!-- <input type="text" name="kualifikasi_pengadaan" class="form-control"
                                                            value="<?= htmlspecialchars($izin['kualifikasi_pengadaan'] ?? '') ?>" required> -->
                                                        <select name="kualifikasi_pengadaan" class="form-select" required>
                                                            <?php $pengadaanVal = $izin['kualifikasi_pengadaan'] ?? ''; ?>
                                                            <option value="">-- Pilih --</option>
                                                            <option value="Konsultan" <?= $pengadaanVal === 'Konsultan' ? 'selected' : '' ?>>Konsultan</option>
                                                            <option value="Jasa Konstruksi" <?= $pengadaanVal === 'Jasa Konstruksi' ? 'selected' : '' ?>>Jasa Konstruksi</option>
                                                            <option value="Pengadaan Barang" <?= $pengadaanVal === 'Pengadaan Barang' ? 'selected' : '' ?>>Pengadaan Barang</option>
                                                            <option value="Jasa Lainnya" <?= $pengadaanVal === 'Jasa Lainnya' ? 'selected' : '' ?>>Jasa Lainnya</option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Status Pengusaha Kena Pajak (PKP) ✏️</label>
                                                        <select name="pkp" class="form-select" required>
                                                            <?php $pkpVal = $izin['pkp'] ?? ''; ?>
                                                            <option value="">-- Pilih --</option>
                                                            <option value="PKP" <?= $pkpVal === 'PKP' ? 'selected' : '' ?>>PKP</option>
                                                            <option value="Non PKP" <?= $pkpVal === 'Non PKP' ? 'selected' : '' ?>>Non PKP</option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Nomor Induk Berusaha (NIB) ✏️</label>
                                                        <input type="text" name="nib" class="form-control"
                                                            value="<?= htmlspecialchars($izin['nib'] ?? '') ?>" required>
                                                    </div>

                                                    <hr>

                                                    <!-- FILE NIB -->
                                                    <div class="mb-3">
                                                        <label class="form-label">File NIB (PDF) ✏️</label>
                                                        <?php if (!empty($izin['file_nib'])): ?>
                                                            <div class="mb-2">
                                                                <a href="/<?= htmlspecialchars($izin['file_nib']) ?>" target="_blank"
                                                                class="btn btn-sm btn-outline-secondary">
                                                                    Lihat / Download NIB
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>
                                                        <input type="file" name="file_nib" class="form-control">
                                                        <small class="text-muted">Format PDF, maks 2MB.</small>
                                                        <hr>
                                                    </div>

                                                    <!-- FILE SERTIFIKAT BADAN USAHA -->
                                                    <div class="mb-3">
                                                        <label class="form-label">File Sertifikat Badan Usaha (SBU) ✏️</label>
                                                        <?php if (!empty($izin['file_sert_badan_usaha'])): ?>
                                                            <div class="mb-2">
                                                                <a href="/<?= htmlspecialchars($izin['file_sert_badan_usaha']) ?>" target="_blank"
                                                                class="btn btn-sm btn-outline-secondary">
                                                                    Lihat / Download SBU
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>
                                                        <input type="file" name="file_sert_badan_usaha" class="form-control">
                                                        <small class="text-muted">Format PDF, maks 2MB.</small>
                                                        <hr>
                                                    </div>

                                                </div>

                                                <div class="col-md-6">

                                                    <!-- FILE IZIN USAHA UTAMA -->
                                                    <div class="mb-3">
                                                        <label class="form-label">File Izin Usaha (SIUP/Izin Usaha Lain) ✏️</label>

                                                        <?php if (!empty($izin['file_ijin_usaha'])): ?>
                                                            <div class="mb-2 d-flex flex-wrap gap-2 align-items-center">
                                                                <a href="/<?= htmlspecialchars($izin['file_ijin_usaha']) ?>" target="_blank"
                                                                class="btn btn-sm btn-outline-primary">
                                                                    Lihat / Download Izin Usaha
                                                                </a>
                                                            </div>

                                                            <!-- tampilkan preview -->
                                                            <?php
                                                            // $ext_iu = strtolower(pathinfo($izin['file_ijin_usaha'], PATHINFO_EXTENSION));
                                                            // if ($ext_iu === 'pdf'):
                                                            ?>
                                                                <!-- <div class="mt-2">
                                                                    <label class="form-label d-block">Preview Izin Usaha:</label>
                                                                    <div class="border rounded" style="height: 350px; overflow: hidden;">
                                                                        <iframe
                                                                            src="/<?= htmlspecialchars($izin['file_ijin_usaha']) ?>#toolbar=0&navpanes=0"
                                                                            style="width: 100%; height: 100%; border: none;"
                                                                        ></iframe>
                                                                    </div>
                                                                </div> -->
                                                            <?php 
                                                            // endif; 
                                                            ?>
                                                            <!-- END tampilkan preview -->

                                                        <?php endif; ?>

                                                        <div class="mt-2">
                                                            <input type="file" name="file_ijin_usaha" class="form-control">
                                                            <small class="text-muted">Format PDF, maks 2MB.</small>
                                                        </div>
                                                        <hr>
                                                    </div>


                                                    <!-- FILE SKA KONSTRUKSI -->
                                                    <div class="mb-3">
                                                        <label class="form-label">File SKA Konstruksi (jika ada) ✏️</label>
                                                        <?php if (!empty($izin['file_ska_konstruksi'])): ?>
                                                            <div class="mb-2">
                                                                <a href="/<?= htmlspecialchars($izin['file_ska_konstruksi']) ?>" target="_blank"
                                                                class="btn btn-sm btn-outline-secondary">
                                                                    Lihat / Download SKA
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>
                                                        <input type="file" name="file_ska_konstruksi" class="form-control">
                                                        <small class="text-muted">Format PDF, maks 2MB.</small>
                                                        <hr>
                                                    </div>

                                                    <!-- FILE SKT KONSTRUKSI -->
                                                    <div class="mb-3">
                                                        <label class="form-label">File SKT Konstruksi (jika ada) ✏️</label>
                                                        <?php if (!empty($izin['file_skt_konstruksi'])): ?>
                                                            <div class="mb-2">
                                                                <a href="/<?= htmlspecialchars($izin['file_skt_konstruksi']) ?>" target="_blank"
                                                                class="btn btn-sm btn-outline-secondary">
                                                                    Lihat / Download SKT
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>
                                                        <input type="file" name="file_skt_konstruksi" class="form-control">
                                                        <small class="text-muted">Format PDF, maks 2MB.</small>
                                                        <hr>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-success px-4">
                                                    <?= $is_new_izin ? 'Simpan Izin Usaha' : 'Update Izin Usaha' ?>
                                                </button>
                                            </div>

                                            <div class="alert alert-info p-2 mt-3">
                                                <strong>Keterangan:</strong><br>
                                                🔒 <span class="badge badge-locked text-black">Tidak bisa diedit - Hanya Admin yang dapat mengubah verifikasi & catatan.</span><br>
                                                ✏️ <span class="badge badge-editable text-black">Bisa diedit oleh perusahaan.</span>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ===================== TAB AKTA PERUSAHAAN ===================== -->
                        <!-- <div class="tab-pane fade" id="akta" role="tabpanel">
                            <div class="alert alert-warning">
                                Halaman <strong>ini</strong> belum diimplementasikan.
                            </div>
                        </div> -->
                        
                        <div class="tab-pane fade" id="akta" role="tabpanel">

                            <div class="col-md-12 grid-margin stretch-card">
                                <div class="card bg-light">
                                    <div class="card-header bg-light text-black text-center">
                                        <h4 class="mb-0">Akta Perusahaan</h4>
                                    </div>
                                    <div class="card-body">

                                        <!-- Alert pesan -->
                                        <?php if (isset($_GET['akta_success'])): ?>
                                            <div class="alert alert-success">Data akta perusahaan berhasil disimpan.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['akta_update'])): ?>
                                            <div class="alert alert-success">Data akta perusahaan berhasil diperbarui.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['akta_error'])): ?>
                                            <div class="alert alert-danger">Lengkapi data akta pendirian (nomor, tanggal, nama notaris).</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['akta_filetype'])): ?>
                                            <div class="alert alert-danger">Semua file akta harus berformat <strong>PDF</strong>.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['akta_filesize'])): ?>
                                            <div class="alert alert-danger">Ukuran setiap file maksimal <strong>2 MB</strong>.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['akta_uploadfail']) || isset($_GET['akta_uploaderror'])): ?>
                                            <div class="alert alert-danger">Terjadi kesalahan saat mengunggah file akta.</div>
                                        <?php endif; ?>

                                        <?php
                                        $akta_verified_by = $akta['verified_by'] ?? null;
                                        $akta_verified_at = $akta['verified_at'] ?? null;

                                        if ($akta_verified_by && $akta_verified_at) {
                                            $akta_status_text = 'TERVERIFIKASI';
                                            $akta_badge_class = 'success';
                                        } else {
                                            $akta_status_text = 'BELUM DIVERIFIKASI';
                                            $akta_badge_class = 'secondary';
                                        }
                                        ?>

                                        <!-- Status verifikasi -->
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Status Verifikasi Akta Perusahaan 🔒</strong></label><br>
                                            <span class="badge bg-<?= htmlspecialchars($akta_badge_class) ?> px-3 py-2" style="font-size:1rem;">
                                                <?= htmlspecialchars($akta_status_text) ?>
                                            </span>
                                            <?php if ($akta_verified_at): ?>
                                                <small class="text-muted ms-2">pada: <?= htmlspecialchars($akta_verified_at) ?></small>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Catatan admin -->
                                        <div class="mb-4">
                                            <label class="form-label"><strong>Catatan Admin (Akta) 🔒</strong></label>
                                            <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($akta['notes'] ?? '') ?></textarea>
                                        </div>

                                        <form method="POST" enctype="multipart/form-data" novalidate>
                                            <input type="hidden" name="tab" value="akta">

                                            <div class="row">
                                                <div class="col-md-6">

                                                    <h5 class="mb-2">Akta Pendirian</h5>
                                                    <div class="mb-3">
                                                        <label class="form-label">Nomor Akta Pendirian ✏️</label>
                                                        <input type="text" name="no_akta_pendirian" class="form-control"
                                                            value="<?= htmlspecialchars($akta['no_akta_pendirian'] ?? '') ?>" required/>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Tanggal Akta Pendirian ✏️</label>
                                                        <input type="date" name="tgl_akta_pendirian" class="form-control"
                                                            value="<?= htmlspecialchars($akta['tgl_akta_pendirian'] ?? '') ?>" required/>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Notaris Akta Pendirian ✏️</label>
                                                        <input type="text" name="notaris_pendirian" class="form-control"
                                                            value="<?= htmlspecialchars($akta['notaris_pendirian'] ?? '') ?>" required>
                                                    </div>
                                                    <hr><br>

                                                    <!-- FILE AKTA PENDIRIAN -->
                                                    <div class="mb-3">
                                                        <label class="form-label">File Akta Pendirian (PDF) ✏️</label>

                                                        <?php if (!empty($akta['file_akta_pendirian'])): ?>
                                                            <div class="mb-2 d-flex flex-wrap gap-2 align-items-center">
                                                                <a href="/<?= htmlspecialchars($akta['file_akta_pendirian']) ?>" target="_blank"
                                                                class="btn btn-sm btn-outline-primary">
                                                                    Lihat / Download Akta Pendirian
                                                                </a>
                                                            </div>

                                                            <!-- Preview Akta  -->
                                                            <?php
                                                            // $ext_ap = strtolower(pathinfo($akta['file_akta_pendirian'], PATHINFO_EXTENSION));
                                                            // if ($ext_ap === 'pdf'):
                                                            ?>
                                                                <!-- <div class="mt-2">
                                                                    <label class="form-label d-block">Preview Akta Pendirian:</label>
                                                                    <div class="border rounded" style="height: 350px; overflow: hidden;">
                                                                        <iframe
                                                                            src="/<?= htmlspecialchars($akta['file_akta_pendirian']) ?>#toolbar=0&navpanes=0"
                                                                            style="width: 100%; height: 100%; border: none;"
                                                                        ></iframe>
                                                                    </div>
                                                                    <small class="text-muted">
                                                                        Jika preview tidak muncul, gunakan tombol "Lihat / Download" di atas.
                                                                    </small>
                                                                </div> -->
                                                            <?php 
                                                            // endif; 
                                                            ?>
                                                            <!-- END Preview Akta  -->
                                                        <?php endif; ?>

                                                        <div class="mt-2">
                                                            <input type="file" name="file_akta_pendirian" class="form-control" required>
                                                            <small class="text-muted">Format PDF, maks 2MB.</small>
                                                        </div>
                                                    </div>

                                                    <hr>
                                                    <br>
                                                     <!-- FILE KOP SURAT  PERUSAHAAN -->
                                                        <div class="mb-3">
                                                            <label class="form-label">File Kop Surat Perusahaan ✏️</label>

                                                            <?php if (!empty($akta['file_kop_surat'])): ?>
                                                                <div class="mb-2 d-flex flex-wrap gap-2 align-items-center">
                                                                    <a href="/<?= htmlspecialchars($akta['file_kop_surat']) ?>" target="_blank"
                                                                    class="btn btn-sm btn-outline-secondary">
                                                                        Lihat / Download Kop Surat Perusahaan
                                                                    </a>
                                                                </div>
                                                            <?php endif; ?>

                                                            <input type="file" name="file_kop_surat" class="form-control">
                                                            <small class="text-muted">Format PDF, maks 2MB.</small>
                                                        </div>
                                                </div>

                                                <div class="col-md-6">

                                                    <!-- FILE DAFTAR PENGALAMAN -->
                                                        <div class="mb-3">
                                                            <label class="form-label">File daftar pengalaman ✏️</label>

                                                            <?php if (!empty($akta['file_pengalaman'])): ?>
                                                                <div class="mb-2 d-flex flex-wrap gap-2 align-items-center">
                                                                    <a href="/<?= htmlspecialchars($akta['file_pengalaman']) ?>" target="_blank"
                                                                    class="btn btn-sm btn-outline-secondary">
                                                                        Lihat / Download File Daftar Pengalaman
                                                                    </a>
                                                                </div>
                                                            <?php endif; ?>

                                                            <input type="file" name="file_pengalaman" class="form-control">
                                                            <small class="text-muted">Format PDF, maks 2MB.</small>
                                                        </div>
                                                        <hr>
                                                        
                                                    <h5 class="mt-4 mb-2">Akta Perubahan (jika ada)</h5>
                                                        <div class="mb-3">
                                                            <label class="form-label">Nomor Akta Perubahan</label>
                                                            <input type="text" name="no_akta_perubahan" class="form-control"
                                                                value="<?= htmlspecialchars($akta['no_akta_perubahan'] ?? '') ?>">
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Tanggal Akta Perubahan (jika ada)</label>
                                                            <input type="date" name="tgl_akta_perubahan" class="form-control"
                                                                value="<?= htmlspecialchars($akta['tgl_akta_perubahan'] ?? '') ?>">
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Nama Notaris Akta Perubahan (jika ada)</label>
                                                            <input type="text" name="notaris_perubahan" class="form-control"
                                                                value="<?= htmlspecialchars($akta['notaris_perubahan'] ?? '') ?>">
                                                        </div>
                                                        <br>
                                                        <hr><br>

                                                        <!-- FILE AKTA PERUBAHAN -->
                                                        <div class="mb-3">
                                                            <label class="form-label">File Akta Perubahan (PDF, jika ada) ✏️</label>

                                                            <?php if (!empty($akta['file_akta_perubahan'])): ?>
                                                                <div class="mb-2 d-flex flex-wrap gap-2 align-items-center">
                                                                    <a href="/<?= htmlspecialchars($akta['file_akta_perubahan']) ?>" target="_blank"
                                                                    class="btn btn-sm btn-outline-secondary">
                                                                        Lihat / Download Akta Perubahan
                                                                    </a>
                                                                </div>
                                                            <?php endif; ?>

                                                            <input type="file" name="file_akta_perubahan" class="form-control">
                                                            <small class="text-muted">Format PDF, maks 2MB.</small>
                                                        </div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-success px-4">
                                                    <?= $is_new_akta ? 'Simpan Akta Perusahaan' : 'Update Akta Perusahaan' ?>
                                                </button>
                                            </div>

                                            <div class="alert alert-info p-2 mt-3">
                                                <strong>Keterangan:</strong><br>
                                                🔒 <span class="badge badge-locked text-black">Tidak bisa diedit - Hanya Admin yang dapat mengubah verifikasi & catatan.</span><br>
                                                ✏️ <span class="badge badge-editable text-black">Bisa diedit oleh perusahaan.</span>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>

                        </div>


                        <!-- ===================== TAB KEUANGAN ===================== -->
                        <!-- <div class="tab-pane fade" id="keuangan" role="tabpanel">
                            <div class="alert alert-warning">
                                Halaman <strong>ini</strong> belum diimplementasikan.
                            </div>
                        </div> -->
                       
                        <div class="tab-pane fade" id="keuangan" role="tabpanel">

                            <div class="col-md-12 grid-margin stretch-card">
                                <div class="card bg-light">
                                    <div class="card-header bg-light text-black text-center">
                                        <h4 class="mb-0">Data Keuangan Perusahaan</h4>
                                    </div>
                                    <div class="card-body">

                                        <!-- Alert pesan -->
                                        <?php if (isset($_GET['keu_success'])): ?>
                                            <div class="alert alert-success">Data keuangan berhasil disimpan.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['keu_update'])): ?>
                                            <div class="alert alert-success">Data keuangan berhasil diperbarui.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['keu_error'])): ?>
                                            <div class="alert alert-danger">Lengkapi minimal No. Rekening, Pemilik Rekening, dan Nama Bank.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['keu_filetype'])): ?>
                                            <div class="alert alert-danger">Semua dokumen keuangan harus berformat <strong>PDF</strong>.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['keu_filesize'])): ?>
                                            <div class="alert alert-danger">Ukuran setiap file maksimal <strong>2 MB</strong>.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['keu_uploadfail']) || isset($_GET['keu_uploaderror'])): ?>
                                            <div class="alert alert-danger">Terjadi kesalahan saat mengunggah file keuangan.</div>
                                        <?php endif; ?>

                                        <?php
                                        $keu_verified_by = $keuangan['verified_by'] ?? null;
                                        $keu_verified_at = $keuangan['verified_at'] ?? null;

                                        if ($keu_verified_by && $keu_verified_at) {
                                            $keu_status_text = 'TERVERIFIKASI';
                                            $keu_badge_class = 'success';
                                        } else {
                                            $keu_status_text = 'BELUM DIVERIFIKASI';
                                            $keu_badge_class = 'secondary';
                                        }
                                        ?>

                                        <!-- Status verifikasi -->
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Status Verifikasi Data Keuangan 🔒</strong></label><br>
                                            <span class="badge bg-<?= htmlspecialchars($keu_badge_class) ?> px-3 py-2" style="font-size:1rem;">
                                                <?= htmlspecialchars($keu_status_text) ?>
                                            </span>
                                            <?php if ($keu_verified_at): ?>
                                                <small class="text-muted ms-2">pada: <?= htmlspecialchars($keu_verified_at) ?></small>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Catatan admin -->
                                        <div class="mb-4">
                                            <label class="form-label"><strong>Catatan Admin (Keuangan) 🔒</strong></label>
                                            <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($keuangan['notes'] ?? '') ?></textarea>
                                        </div>

                                        <form method="POST" enctype="multipart/form-data" novalidate>
                                            <input type="hidden" name="tab" value="keuangan">

                                            <div class="row">
                                                <div class="col-md-6">

                                                    <div class="mb-3">
                                                        <label class="form-label">Nomor Rekening Utama ✏️</label>
                                                        <input type="text" name="no_rekening" class="form-control"
                                                            value="<?= htmlspecialchars($keuangan['no_rekening'] ?? '') ?>" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Pemilik Rekening ✏️</label>
                                                        <input type="text" name="pemilik_rekening" class="form-control"
                                                            value="<?= htmlspecialchars($keuangan['pemilik_rekening'] ?? '') ?>" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Bank ✏️</label>
                                                        <input type="text" name="nama_bank" class="form-control"
                                                            value="<?= htmlspecialchars($keuangan['nama_bank'] ?? '') ?>" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Cabang Bank</label>
                                                        <input type="text" name="cabang_bank" class="form-control"
                                                            value="<?= htmlspecialchars($keuangan['cabang_bank'] ?? '') ?>">
                                                    </div>
                                                    <hr>
                                                    <br>

                                                    <!-- BUKU REKENING -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Scan Buku Rekening (PDF) ✏️</label>
                                                        <?php if (!empty($keuangan['file_buku_rekening'])): ?>
                                                            <div class="mb-2">
                                                                <a href="/<?= htmlspecialchars($keuangan['file_buku_rekening']) ?>" target="_blank"
                                                                class="btn btn-sm btn-outline-secondary">
                                                                    Lihat / Download Buku Rekening
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>
                                                        <input type="file" name="file_buku_rekening" class="form-control">
                                                        <small class="text-muted">Format PDF, maks 2MB.</small>
                                                    </div>

                                                </div>

                                                <div class="col-md-6">

                                                    <!-- REKENING KORAN -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Rekening Koran (PDF) ✏️</label>

                                                        <?php if (!empty($keuangan['file_rek_koran'])): ?>
                                                            <div class="mb-2 d-flex flex-wrap gap-2 align-items-center">
                                                                <a href="/<?= htmlspecialchars($keuangan['file_rek_koran']) ?>" target="_blank"
                                                                class="btn btn-sm btn-outline-primary">
                                                                    Lihat / Download Rekening Koran
                                                                </a>
                                                            </div>

                                                            <!-- Preview Rekening -->
                                                            <?php
                                                            // $ext_rk = strtolower(pathinfo($keuangan['file_rek_koran'], PATHINFO_EXTENSION));
                                                            // if ($ext_rk === 'pdf'):
                                                            ?>
                                                                <!-- <div class="mt-2">
                                                                    <label class="form-label d-block">Preview Rekening Koran:</label>
                                                                    <div class="border rounded" style="height: 300px; overflow: hidden;">
                                                                        <iframe
                                                                            src="/<?= htmlspecialchars($keuangan['file_rek_koran']) ?>#toolbar=0&navpanes=0"
                                                                            style="width: 100%; height: 100%; border: none;"
                                                                        ></iframe>
                                                                    </div>
                                                                </div> -->
                                                            <?php 
                                                            // endif; 
                                                            ?>
                                                            <!-- END Preview Rekening -->

                                                        <?php endif; ?>

                                                        <div class="mt-2">
                                                            <input type="file" name="file_rek_koran" class="form-control">
                                                            <small class="text-muted">Format PDF, maks 2MB.</small>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <br>
                                                      
                                                    <!-- NERACA -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Laporan Neraca (PDF) ✏️</label>
                                                        <?php if (!empty($keuangan['file_neraca'])): ?>
                                                            <div class="mb-2">
                                                                <a href="/<?= htmlspecialchars($keuangan['file_neraca']) ?>" target="_blank"
                                                                class="btn btn-sm btn-outline-secondary">
                                                                    Lihat / Download Neraca
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>
                                                        <input type="file" name="file_neraca" class="form-control">
                                                        <small class="text-muted">Format PDF, maks 2MB.</small>
                                                    </div>
                                                    <hr>
                                                    <br>

                                                    <!-- LABA RUGI -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Laporan Laba Rugi (PDF) ✏️</label>
                                                        <?php if (!empty($keuangan['file_labarugi'])): ?>
                                                            <div class="mb-2">
                                                                <a href="/<?= htmlspecialchars($keuangan['file_labarugi']) ?>" target="_blank"
                                                                class="btn btn-sm btn-outline-secondary">
                                                                    Lihat / Download Laba Rugi
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>
                                                        <input type="file" name="file_labarugi" class="form-control">
                                                        <small class="text-muted">Format PDF, maks 2MB.</small>
                                                    </div>
                                                    <hr>
                                                    
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-success px-4">
                                                    <?= $is_new_keu ? 'Simpan Data Keuangan' : 'Update Data Keuangan' ?>
                                                </button>
                                            </div>

                                            <div class="alert alert-info p-2 mt-3">
                                                <strong>Keterangan:</strong><br>
                                                🔒 <span class="badge badge-locked text-black">Verifikasi & catatan hanya dapat diubah oleh Admin.</span><br>
                                                ✏️ <span class="badge badge-editable text-black">Kolom di atas dapat diubah oleh perusahaan.</span>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>

                        </div>


                        <!-- ===================== TAB PAJAK ===================== -->
                        <!-- <div class="tab-pane fade" id="pajak" role="tabpanel">
                            <div class="alert alert-warning">
                                Halaman <strong>ini</strong> belum diimplementasikan.
                            </div>
                        </div> -->
                        
                        <div class="tab-pane fade" id="pajak" role="tabpanel">

                            <div class="col-md-12 grid-margin stretch-card">
                                <div class="card bg-light">
                                    <div class="card-header bg-light text-black text-center">
                                        <h4 class="mb-0">Data Pajak Perusahaan</h4>
                                    </div>
                                    <div class="card-body">

                                        <!-- Alert pesan -->
                                        <?php if (isset($_GET['pajak_success'])): ?>
                                            <div class="alert alert-success">Data pajak berhasil disimpan.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['pajak_update'])): ?>
                                            <div class="alert alert-success">Data pajak berhasil diperbarui.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['pajak_error'])): ?>
                                            <div class="alert alert-danger">Lengkapi minimal NPWP Perusahaan dan NPWP Direktur.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['pajak_filetype'])): ?>
                                            <div class="alert alert-danger">Semua dokumen pajak harus berformat <strong>PDF</strong>.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['pajak_filesize'])): ?>
                                            <div class="alert alert-danger">Ukuran setiap file maksimal <strong>2 MB</strong>.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['pajak_uploadfail']) || isset($_GET['pajak_uploaderror'])): ?>
                                            <div class="alert alert-danger">Terjadi kesalahan saat mengunggah file pajak.</div>
                                        <?php endif; ?>

                                        <?php
                                        $pajak_verified_by = $pajak['verified_by'] ?? null;
                                        $pajak_verified_at = $pajak['verified_at'] ?? null;

                                        if ($pajak_verified_by && $pajak_verified_at) {
                                            $pajak_status_text = 'TERVERIFIKASI';
                                            $pajak_badge_class = 'success';
                                        } else {
                                            $pajak_status_text = 'BELUM DIVERIFIKASI';
                                            $pajak_badge_class = 'secondary';
                                        }
                                        ?>

                                        <!-- Status verifikasi -->
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Status Verifikasi Data Pajak 🔒</strong></label><br>
                                            <span class="badge bg-<?= htmlspecialchars($pajak_badge_class) ?> px-3 py-2" style="font-size:1rem;">
                                                <?= htmlspecialchars($pajak_status_text) ?>
                                            </span>
                                            <?php if ($pajak_verified_at): ?>
                                                <small class="text-muted ms-2">pada: <?= htmlspecialchars($pajak_verified_at) ?></small>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Catatan admin -->
                                        <div class="mb-4">
                                            <label class="form-label"><strong>Catatan Admin (Pajak) 🔒</strong></label>
                                            <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($pajak['notes'] ?? '') ?></textarea>
                                        </div>

                                        <form method="POST" enctype="multipart/form-data" novalidate>
                                            <input type="hidden" name="tab" value="pajak">

                                            <div class="row">
                                                <div class="col-md-6">

                                                    <div class="mb-3">
                                                        <label class="form-label">NPWP Perusahaan ✏️</label>
                                                        <input type="text" name="npwp_perusahaan" class="form-control"
                                                            value="<?= htmlspecialchars($pajak['npwp_perusahaan'] ?? '') ?>" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">NPWP Direktur Utama ✏️</label>
                                                        <input type="text" name="npwp_direktur" class="form-control"
                                                            value="<?= htmlspecialchars($pajak['npwp_direktur'] ?? '') ?>" required>
                                                    </div>
                                                    <hr>
                                                    <br>

                                                    <!-- FILE TANDA DAFTAR -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Tanda Daftar Perusahaan (PDF) ✏️</label>

                                                        <?php if (!empty($pajak['file_tanda_daftar'])): ?>
                                                            <div class="mb-2 d-flex flex-wrap gap-2 align-items-center">
                                                                <a href="/<?= htmlspecialchars($pajak['file_tanda_daftar']) ?>" target="_blank"
                                                                class="btn btn-sm btn-outline-primary">
                                                                    Lihat / Download Tanda Daftar
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>

                                                        <input type="file" name="file_tanda_daftar" class="form-control">
                                                        <small class="text-muted">Format PDF, maks 2MB.</small>
                                                    </div>

                                                </div>

                                                <div class="col-md-6">

                                                    <!-- FILE NPWP PERUSAHAAN -->
                                                    <div class="mb-3">
                                                        <label class="form-label">File NPWP Perusahaan (PDF) ✏️</label>

                                                        <?php if (!empty($pajak['file_npwp_perusahaan'])): ?>
                                                            <div class="mb-2 d-flex flex-wrap gap-2 align-items-center">
                                                                <a href="/<?= htmlspecialchars($pajak['file_npwp_perusahaan']) ?>" target="_blank"
                                                                class="btn btn-sm btn-outline-secondary">
                                                                    Lihat / Download NPWP Perusahaan
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>

                                                        <input type="file" name="file_npwp_perusahaan" class="form-control">
                                                        <small class="text-muted">Format PDF, maks 2MB.</small>
                                                    </div>
                                                    <hr>
                                                    <br>

                                                    <!-- FILE NPWP DIREKTUR -->
                                                    <div class="mb-3">
                                                        <label class="form-label">File NPWP Direktur Utama (PDF) ✏️</label>

                                                        <?php if (!empty($pajak['file_npwp_direktur'])): ?>
                                                            <div class="mb-2 d-flex flex-wrap gap-2 align-items-center">
                                                                <a href="/<?= htmlspecialchars($pajak['file_npwp_direktur']) ?>" target="_blank"
                                                                class="btn btn-sm btn-outline-secondary">
                                                                    Lihat / Download NPWP Direktur
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>

                                                        <input type="file" name="file_npwp_direktur" class="form-control">
                                                        <small class="text-muted">Format PDF, maks 2MB.</small>
                                                    </div>
                                                    <hr>
                                                    <br>

                                                    <!-- FILE LAPOR PAJAK -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Bukti Lapor Pajak (PDF, SPT Tahunan) ✏️</label>

                                                        <?php if (!empty($pajak['file_lapor_pajak'])): ?>
                                                            <div class="mb-2 d-flex flex-wrap gap-2 align-items-center">
                                                                <a href="/<?= htmlspecialchars($pajak['file_lapor_pajak']) ?>" target="_blank"
                                                                class="btn btn-sm btn-outline-secondary">
                                                                    Lihat / Download Bukti Lapor Pajak
                                                                </a>
                                                            </div>

                                                            <!-- Preview Bukti Lapor Pajak -->
                                                            <?php
                                                            // $ext_lp = strtolower(pathinfo($pajak['file_lapor_pajak'], PATHINFO_EXTENSION));
                                                            // if ($ext_lp === 'pdf'):
                                                            ?>
                                                                <!-- <div class="mt-2">
                                                                    <label class="form-label d-block">Preview Bukti Lapor Pajak:</label>
                                                                    <div class="border rounded" style="height: 300px; overflow: hidden;">
                                                                        <iframe
                                                                            src="/<?= htmlspecialchars($pajak['file_lapor_pajak']) ?>#toolbar=0&navpanes=0"
                                                                            style="width: 100%; height: 100%; border: none;"
                                                                        ></iframe>
                                                                    </div>
                                                                    <small class="text-muted">
                                                                        Jika preview tidak muncul, gunakan tombol "Lihat / Download" di atas.
                                                                    </small>
                                                                </div> -->
                                                            <?php 
                                                            // endif; 
                                                            ?>
                                                            <!-- END Preview Bukti Lapor Pajak -->
                                                        <?php endif; ?>

                                                        <input type="file" name="file_lapor_pajak" class="form-control mt-2">
                                                        <small class="text-muted">Format PDF, maks 2MB.</small>
                                                    </div>
                                                    <hr>
                                                    <br>

                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-success px-4">
                                                    <?= $is_new_pajak ? 'Simpan Data Pajak' : 'Update Data Pajak' ?>
                                                </button>
                                            </div>

                                            <div class="alert alert-info p-2 mt-3">
                                                <strong>Keterangan:</strong><br>
                                                🔒 <span class="badge badge-locked text-black">Verifikasi & catatan hanya dapat diubah oleh Admin.</span><br>
                                                ✏️ <span class="badge badge-editable text-black">Kolom di atas dapat diubah oleh perusahaan.</span>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>

                        </div>


                        <!-- ===================== TAB PEMILIK & PENGURUS ===================== -->
                        <!-- <div class="tab-pane fade" id="pemilik" role="tabpanel">
                            <div class="alert alert-warning">
                                Halaman <strong>ini</strong> belum diimplementasikan.
                            </div>
                        </div> -->
                        
                        <div class="tab-pane fade" id="pemilik" role="tabpanel">

                            <div class="col-md-12 grid-margin stretch-card">
                                <div class="card bg-light">
                                    <div class="card-header bg-light text-black text-center">
                                        <h4 class="mb-0">Pemilik & Pengurus Perusahaan</h4>
                                    </div>
                                    <div class="card-body">

                                        <!-- Alert pesan -->
                                        <?php if (isset($_GET['pengurus_success'])): ?>
                                            <div class="alert alert-success">Data pemilik & pengurus berhasil disimpan.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['pengurus_update'])): ?>
                                            <div class="alert alert-success">Data pemilik & pengurus berhasil diperbarui.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['pengurus_error'])): ?>
                                            <div class="alert alert-danger">
                                                Lengkapi seluruh data pemilik dan direktur (nama, jenis identitas, nomor identitas).
                                            </div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['pengurus_filetype'])): ?>
                                            <div class="alert alert-danger">File kartu identitas harus berformat <strong>PDF</strong>.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['pengurus_filesize'])): ?>
                                            <div class="alert alert-danger">Ukuran setiap file maksimal <strong>2 MB</strong>.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['pengurus_uploadfail']) || isset($_GET['pengurus_uploaderror'])): ?>
                                            <div class="alert alert-danger">Terjadi kesalahan saat mengunggah file kartu identitas.</div>
                                        <?php endif; ?>

                                        <?php
                                        $pengurus_verified_by = $pengurus['verified_by'] ?? null;
                                        $pengurus_verified_at = $pengurus['verified_at'] ?? null;

                                        if ($pengurus_verified_by && $pengurus_verified_at) {
                                            $pengurus_status_text = 'TERVERIFIKASI';
                                            $pengurus_badge_class = 'success';
                                        } else {
                                            $pengurus_status_text = 'BELUM DIVERIFIKASI';
                                            $pengurus_badge_class = 'secondary';
                                        }
                                        ?>

                                        <!-- Status verifikasi -->
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Status Verifikasi Pemilik & Pengurus 🔒</strong></label><br>
                                            <span class="badge bg-<?= htmlspecialchars($pengurus_badge_class) ?> px-3 py-2" style="font-size:1rem;">
                                                <?= htmlspecialchars($pengurus_status_text) ?>
                                            </span>
                                            <?php if ($pengurus_verified_at): ?>
                                                <small class="text-muted ms-2">pada: <?= htmlspecialchars($pengurus_verified_at) ?></small>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Catatan admin -->
                                        <div class="mb-4">
                                            <label class="form-label"><strong>Catatan Admin (Pemilik & Pengurus) 🔒</strong></label>
                                            <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($pengurus['notes'] ?? '') ?></textarea>
                                        </div>

                                        <form method="POST" enctype="multipart/form-data" novalidate>
                                            <input type="hidden" name="tab" value="pemilik">

                                            <div class="row">
                                                <!-- KOLOM KIRI: PEMILIK -->
                                                <div class="col-md-6">
                                                    <h5 class="mb-3">Data Pemilik Perusahaan</h5>

                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Pemilik ✏️</label>
                                                        <input type="text" name="pemilik" class="form-control"
                                                            value="<?= htmlspecialchars($pengurus['pemilik'] ?? '') ?>" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Jenis Identitas Pemilik ✏️</label>
                                                        <select name="jenis_identitas_pemilik" class="form-select" required>
                                                            <?php
                                                            $jenis_pemilik = $pengurus['jenis_identitas_pemilik'] ?? '';
                                                            $opts = ['KTP', 'Paspor', 'KITAS', 'Lainnya'];
                                                            foreach ($opts as $opt):
                                                            ?>
                                                                <option value="<?= $opt ?>" <?= ($jenis_pemilik === $opt) ? 'selected' : '' ?>>
                                                                    <?= $opt ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Nomor Identitas Pemilik ✏️</label>
                                                        <input type="text" name="nomor_identitas_pemilik" class="form-control"
                                                            value="<?= htmlspecialchars($pengurus['nomor_identitas_pemilik'] ?? '') ?>" required>
                                                    </div>
                                                    <hr><br>

                                                    <!-- FILE KARTU PEMILIK -->
                                                    <div class="mb-3">
                                                        <label class="form-label">File Kartu Identitas Pemilik (PDF) ✏️</label>

                                                        <?php if (!empty($pengurus['file_kartu_pemilik'])): ?>
                                                            <div class="mb-2 d-flex flex-wrap gap-2 align-items-center">
                                                                <a href="/<?= htmlspecialchars($pengurus['file_kartu_pemilik']) ?>" target="_blank"
                                                                class="btn btn-sm btn-outline-primary">
                                                                    Lihat / Download Kartu Pemilik
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>

                                                        <input type="file" name="file_kartu_pemilik" class="form-control">
                                                        <small class="text-muted">Format PDF, maks 2MB.</small>
                                                    </div>
                                                </div>

                                                <!-- KOLOM KANAN: DIREKTUR -->
                                                <div class="col-md-6">
                                                    <h5 class="mb-3">Data Direktur Utama</h5>

                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Direktur Utama ✏️</label>
                                                        <input type="text" name="direktur" class="form-control"
                                                            value="<?= htmlspecialchars($pengurus['direktur'] ?? '') ?>" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Jenis Identitas Direktur ✏️</label>
                                                        <select name="jenis_identitas_direktur" class="form-select" required>
                                                            <?php
                                                            $jenis_dir = $pengurus['jenis_identitas_direktur'] ?? '';
                                                            $opts = ['KTP', 'Paspor', 'KITAS', 'Lainnya'];
                                                            foreach ($opts as $opt):
                                                            ?>
                                                                <option value="<?= $opt ?>" <?= ($jenis_dir === $opt) ? 'selected' : '' ?>>
                                                                    <?= $opt ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Nomor Identitas Direktur ✏️</label>
                                                        <input type="text" name="nomor_identitas_direktur" class="form-control"
                                                            value="<?= htmlspecialchars($pengurus['nomor_identitas_direktur'] ?? '') ?>" required>
                                                    </div>
                                                    <hr><br>

                                                    <!-- FILE KARTU DIREKTUR -->
                                                    <div class="mb-3">
                                                        <label class="form-label">File Kartu Identitas Direktur (PDF) ✏️</label>

                                                        <?php if (!empty($pengurus['file_kartu_direktur'])): ?>
                                                            <div class="mb-2 d-flex flex-wrap gap-2 align-items-center">
                                                                <a href="/<?= htmlspecialchars($pengurus['file_kartu_direktur']) ?>" target="_blank"
                                                                class="btn btn-sm btn-outline-secondary">
                                                                    Lihat / Download Kartu Direktur
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>

                                                        <input type="file" name="file_kartu_direktur" class="form-control">
                                                        <small class="text-muted">Format PDF, maks 2MB.</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-success px-4">
                                                    <?= $is_new_pengurus ? 'Simpan Data Pemilik & Pengurus' : 'Update Data Pemilik & Pengurus' ?>
                                                </button>
                                            </div>

                                            <div class="alert alert-info p-2 mt-3">
                                                <strong>Keterangan:</strong><br>
                                                🔒 <span class="badge badge-locked text-black">Verifikasi & catatan hanya dapat diubah oleh Admin.</span><br>
                                                ✏️ <span class="badge badge-editable text-black">Kolom di atas dapat diubah oleh perusahaan.</span>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>

                        </div>


                        <!-- ===================== TAB PENGALAMAN ===================== -->
                        <!-- <div class="tab-pane fade" id="pengalaman" role="tabpanel">
                            <div class="alert alert-warning">
                                Halaman <strong>ini</strong> belum diimplementasikan.
                            </div>
                        </div> -->

                        <!-- ===================== TAB TENAGA AHLI ===================== -->
                        <!-- <div class="tab-pane fade" id="tenaga_ahli" role="tabpanel">
                            <div class="alert alert-warning">
                                Halaman <strong>ini</strong> belum diimplementasikan.
                            </div>
                        </div> -->
                       
                       <div class="tab-pane fade" id="tenaga_ahli" role="tabpanel">

                            <div class="col-md-12 grid-margin stretch-card">
                                <div class="card bg-light">
                                    <div class="card-header bg-light text-black text-center">
                                        <h4 class="mb-0">Daftar Tenaga Ahli / Staf Ahli</h4>
                                    </div>
                                    <div class="card-body">

                                        <!-- Alert pesan -->
                                        <?php if (isset($_GET['ta_success'])): ?>
                                            <div class="alert alert-success">Tenaga ahli baru berhasil ditambahkan.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['ta_deleted'])): ?>
                                            <div class="alert alert-success">Data tenaga ahli berhasil dihapus.</div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['ta_error'])): ?>
                                            <div class="alert alert-danger">
                                                Lengkapi minimal Nama, Jabatan, dan Pendidikan tenaga ahli.
                                            </div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['ta_edit_success'])): ?>
                                            <div class="alert alert-success">
                                                Data tenaga ahli berhasil diperbarui.
                                            </div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['ta_edit_error'])): ?>
                                            <div class="alert alert-danger">
                                                Terjadi kesalahan saat mengedit tenaga ahli. Pastikan data sudah lengkap.
                                            </div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['ta_filetype'])): ?>
                                            <div class="alert alert-danger">
                                                File CV dan Sertifikat harus berformat <strong>PDF</strong>.
                                            </div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['ta_filesize'])): ?>
                                            <div class="alert alert-danger">
                                                Ukuran setiap file maksimal <strong>2 MB</strong>.
                                            </div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['ta_uploadfail']) || isset($_GET['ta_uploaderror'])): ?>
                                            <div class="alert alert-danger">
                                                Terjadi kesalahan saat mengunggah file tenaga ahli.
                                            </div>
                                        <?php endif; ?>

                                        <!-- FORM TAMBAH TENAGA AHLI -->
                                        <div class="mb-4 p-3 border rounded bg-white">
                                            <h5 class="mb-3">Tambah Tenaga Ahli</h5>

                                            <form method="POST" enctype="multipart/form-data" novalidate>
                                                <input type="hidden" name="tab" value="tenaga_ahli">
                                                <input type="hidden" name="action" value="add">

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <!-- NAMA -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Nama Tenaga Ahli ✏️</label>
                                                            <input type="text" name="nama" class="form-control" required>
                                                        </div>

                                                        <!-- JABATAN -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Jabatan / Posisi ✏️</label>
                                                            <input type="text" name="jabatan" class="form-control" placeholder="Misal: Ahli K3, Site Manager" required>
                                                        </div>

                                                        <!-- PENDIDIKAN -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Pendidikan Terakhir ✏️</label>
                                                            <input type="text" name="pendidikan" class="form-control" placeholder="Misal: S1 Teknik Sipil" required>
                                                        </div>

                                                        <!-- PENGALAMAN TAHUN -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Pengalaman Kerja (tahun) ✏️</label>
                                                            <input type="number" name="pengalaman_tahun" class="form-control" min="0" step="1">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <!-- KEAHLIAN -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Keahlian Utama / Spesialisasi ✏️</label>
                                                            <textarea name="keahlian" class="form-control" rows="3" placeholder="Misal: Manajemen Proyek, Pengawasan Struktur, dsb."></textarea>
                                                        </div>

                                                        <!-- FILE CV -->
                                                        <div class="mb-3">
                                                            <label class="form-label">CV Tenaga Ahli (PDF) ✏️</label>
                                                            <input type="file" name="file_cv" class="form-control">
                                                            <small class="text-muted">Format PDF, maks 2MB (opsional).</small>
                                                        </div>
                                                        <hr><br>

                                                        <!-- FILE SERTIFIKAT -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Sertifikat Pendukung (PDF) ✏️</label>
                                                            <input type="file" name="file_sertifikat" class="form-control">
                                                            <small class="text-muted">Format PDF, maks 2MB (opsional).</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-end">
                                                    <button type="submit" class="btn btn-success px-4">
                                                        Tambah Tenaga Ahli
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- DAFTAR TENAGA AHLI -->
                                        <h5 class="mb-3">Daftar Tenaga Ahli Terdaftar</h5>

                                        <?php if (empty($tenaga_ahli_list)): ?>
                                            <div class="alert alert-info">
                                                Belum ada tenaga ahli yang didaftarkan. Silakan tambahkan melalui formulir di atas.
                                            </div>
                                        <?php else: ?>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered align-middle">
                                                    <thead class="table-light">
                                                        <tr class="text-center">
                                                            <th style="width: 40px;">#</th>
                                                            <th>Nama</th>
                                                            <th>Jabatan</th>
                                                            <th>Pendidikan</th>
                                                            <th>Pengalaman (th)</th>
                                                            <th>Keahlian</th>
                                                            <th>Dokumen</th>
                                                            <th>Status Verifikasi 🔒</th>
                                                            <th>Catatan Admin 🔒</th>
                                                            <th style="width: 100px;">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $no = 1; ?>
                                                        <?php foreach ($tenaga_ahli_list as $ta): ?>
                                                            <tr>
                                                                <td class="text-center"><?= $no++; ?></td>
                                                                <td><?= htmlspecialchars($ta['nama'] ?? '') ?></td>
                                                                <td><?= htmlspecialchars($ta['jabatan'] ?? '') ?></td>
                                                                <td><?= htmlspecialchars($ta['pendidikan'] ?? '') ?></td>
                                                                <td class="text-center">
                                                                    <?= htmlspecialchars($ta['pengalaman_tahun'] ?? '-') ?>
                                                                </td>
                                                                <td><?= nl2br(htmlspecialchars($ta['keahlian'] ?? '')) ?></td>
                                                                <td>
                                                                    <?php if (!empty($ta['file_cv'])): ?>
                                                                        <a href="/<?= htmlspecialchars($ta['file_cv']) ?>" target="_blank" class="btn btn-sm btn-outline-primary mb-1">
                                                                            CV
                                                                        </a>
                                                                    <?php endif; ?>
                                                                    <?php if (!empty($ta['file_sertifikat'])): ?>
                                                                        <a href="/<?= htmlspecialchars($ta['file_sertifikat']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary mb-1">
                                                                            Sertifikat
                                                                        </a>
                                                                    <?php endif; ?>
                                                                </td>

                                                                <?php
                                                                $ta_verified_by = $ta['verified_by'] ?? null;
                                                                $ta_verified_at = $ta['verified_at'] ?? null;

                                                                if ($ta_verified_by && $ta_verified_at) {
                                                                    $ta_status_text  = 'TERVERIFIKASI';
                                                                    $ta_badge_class  = 'success';
                                                                } else {
                                                                    $ta_status_text  = 'BELUM DIVERIFIKASI';
                                                                    $ta_badge_class  = 'secondary';
                                                                }
                                                                ?>
                                                                <td class="text-center">
                                                                    <span class="badge bg-<?= htmlspecialchars($ta_badge_class) ?>">
                                                                        <?= htmlspecialchars($ta_status_text) ?>
                                                                    </span>
                                                                    <?php if ($ta_verified_at): ?>
                                                                        <br>
                                                                        <small class="text-muted">
                                                                            <?= htmlspecialchars($ta_verified_at) ?>
                                                                        </small>
                                                                    <?php endif; ?>
                                                                </td>

                                                                <td>
                                                                    <?php if (!empty($ta['notes'])): ?>
                                                                        <small><?= nl2br(htmlspecialchars($ta['notes'])) ?></small>
                                                                    <?php else: ?>
                                                                        <span class="text-muted">-</span>
                                                                    <?php endif; ?>
                                                                </td>

                                                                <td class="text-center">
                                                                    <div class="d-flex flex-column gap-1">

                                                                        <!-- TOMBOl EDIT -->
                                                                        <button 
                                                                            type="button" 
                                                                            class="btn btn-sm btn-warning"
                                                                            onclick='editTenagaAhli(<?= json_encode($ta, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>)'>
                                                                            Edit
                                                                        </button>

                                                                        <!-- TOMBOl HAPUS -->
                                                                        <form method="POST" onsubmit="return confirm('Yakin ingin menghapus tenaga ahli ini?');">
                                                                            <input type="hidden" name="tab" value="tenaga_ahli">
                                                                            <input type="hidden" name="action" value="delete">
                                                                            <input type="hidden" name="ta_id" value="<?= (int)$ta['id'] ?>">
                                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                                Hapus
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php endif; ?>

                                        <div class="alert alert-info p-2 mt-3">
                                            <strong>Keterangan:</strong><br>
                                            ✏️ Data tenaga ahli dapat diedit oleh perusahaan (nama, jabatan, pendidikan, keahlian, pengalaman, file CV & sertifikat).<br>
                                            🔒 Status verifikasi dan catatan admin hanya dapat diubah oleh Admin Eproc Unpatti.
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- ===========================================
                                MODAL EDIT TENAGA AHLI
                            =========================================== -->
                            <div class="modal fade" id="modalEditTA" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form method="POST" enctype="multipart/form-data" class="modal-content">
                                        <input type="hidden" name="tab" value="tenaga_ahli">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="ta_id" id="edit_ta_id">

                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Edit Tenaga Ahli</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">Nama *</label>
                                                    <input type="text" name="nama" id="edit_nama" class="form-control" required>

                                                    <label class="form-label mt-3">Jabatan *</label>
                                                    <input type="text" name="jabatan" id="edit_jabatan" class="form-control" required>

                                                    <label class="form-label mt-3">Pendidikan *</label>
                                                    <input type="text" name="pendidikan" id="edit_pendidikan" class="form-control" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Pengalaman (tahun) *</label>
                                                    <input type="number" name="pengalaman_tahun" id="edit_pengalaman" class="form-control" min="0" required>

                                                    <label class="form-label mt-3">Keahlian *</label>
                                                    <textarea name="keahlian" id="edit_keahlian" class="form-control" rows="3" required></textarea>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">CV Tenaga Ahli (PDF)</label>

                                                    <div class="mb-1" id="edit_cv_wrapper" style="display:none;">
                                                        <small>CV saat ini:
                                                            <a href="#" target="_blank" id="edit_cv_link">Lihat CV</a>
                                                        </small>
                                                    </div>

                                                    <input type="file" name="file_cv" class="form-control">
                                                    <small class="text-muted">Jika tidak memilih file, CV lama tetap digunakan.</small>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Sertifikat (PDF)</label>

                                                    <div class="mb-1" id="edit_sert_wrapper" style="display:none;">
                                                        <small>Sertifikat saat ini:
                                                            <a href="#" target="_blank" id="edit_sert_link">Lihat Sertifikat</a>
                                                        </small>
                                                    </div>

                                                    <input type="file" name="file_sertifikat" class="form-control">
                                                    <small class="text-muted">Jika tidak memilih file, sertifikat lama tetap digunakan.</small>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button>
                                            <button class="btn btn-warning" type="submit">Simpan Perubahan</button>
                                        </div>

                                    </form>
                                </div>
                            </div>

                        </div>

                        <script>
                        function editTenagaAhli(ta) {
                            // Isi field teks
                            document.getElementById('edit_ta_id').value        = ta.id ?? '';
                            document.getElementById('edit_nama').value         = ta.nama ?? '';
                            document.getElementById('edit_jabatan').value      = ta.jabatan ?? '';
                            document.getElementById('edit_pendidikan').value   = ta.pendidikan ?? '';
                            document.getElementById('edit_pengalaman').value   = ta.pengalaman_tahun ?? '';
                            document.getElementById('edit_keahlian').value     = ta.keahlian ?? '';

                            // CV lama
                            const cvWrapper = document.getElementById('edit_cv_wrapper');
                            const cvLink    = document.getElementById('edit_cv_link');
                            if (ta.file_cv) {
                                cvWrapper.style.display = 'block';
                                cvLink.href = '/' + ta.file_cv.replace(/^\/+/, '');
                            } else {
                                cvWrapper.style.display = 'none';
                                cvLink.href = '#';
                            }

                            // Sertifikat lama
                            const sertWrapper = document.getElementById('edit_sert_wrapper');
                            const sertLink    = document.getElementById('edit_sert_link');
                            if (ta.file_sertifikat) {
                                sertWrapper.style.display = 'block';
                                sertLink.href = '/' + ta.file_sertifikat.replace(/^\/+/, '');
                            } else {
                                sertWrapper.style.display = 'none';
                                sertLink.href = '#';
                            }

                            // Tampilkan modal
                            const modalEl = document.getElementById('modalEditTA');
                            const modal   = new bootstrap.Modal(modalEl);
                            modal.show();
                        }
                        </script>



                        <!-- ===================== TAB PERALATAN ===================== -->
                        <!-- <div class="tab-pane fade" id="peralatan" role="tabpanel">
                            <div class="alert alert-warning">
                                Halaman <strong>ini</strong> belum diimplementasikan.
                            </div>
                        </div> -->

                    </div>

                </div>
            </div>

        </div>
    </div>

    <?php include 'public_footer.php'; ?>
</div>

<!-- Optional: jika ingin tetap membuka tab tertentu dari query string -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const url = new URL(window.location.href);
        const hash = url.hash;
        const params = new URLSearchParams(url.search);
        // if query param tab present, show that tab
        const tabFromParam = params.get('tab');
        const target = tabFromParam ? tabFromParam : (hash ? hash.replace('#', '') : null);
        if (target) {
            const tabEl = document.querySelector(`#companyTabs button[data-bs-target="#${target}"]`);
            if (tabEl) {
                const tab = new bootstrap.Tab(tabEl);
                tab.show();
            }
        }
    });
</script>