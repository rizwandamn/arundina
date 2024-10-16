<?php
session_start();
require 'db.php'; // Koneksi ke database

// Cek apakah pengguna sudah login dan memiliki role dosen
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'dosen') {
    header("Location: login.php");
    exit();
}

// Ambil ID dokumen dari URL
if (isset($_GET['id'])) {
    $id_dokumen = intval($_GET['id']);

    // Cek apakah dokumen dengan ID tersebut ada
    $query = "SELECT * FROM dokumen WHERE id_dokumen = ?";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, 'i', $id_dokumen);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            // Cek apakah dokumen sudah ditandai oleh pengguna
            $queryCheck = "SELECT * FROM marked_dokumen WHERE id_dokumen = ? AND id_user = ?";
            if ($stmtCheck = mysqli_prepare($conn, $queryCheck)) {
                mysqli_stmt_bind_param($stmtCheck, 'ii', $id_dokumen, $_SESSION['id_user']);
                mysqli_stmt_execute($stmtCheck);
                $resultCheck = mysqli_stmt_get_result($stmtCheck);

                if (mysqli_num_rows($resultCheck) > 0) {
                    // Jika sudah ditandai, hapus dari marked_dokumen
                    $deleteQuery = "DELETE FROM marked_dokumen WHERE id_dokumen = ? AND id_user = ?";
                    if ($deleteStmt = mysqli_prepare($conn, $deleteQuery)) {
                        mysqli_stmt_bind_param($deleteStmt, 'ii', $id_dokumen, $_SESSION['id_user']);
                        if (mysqli_stmt_execute($deleteStmt)) {
                            $_SESSION['message'] = "Dokumen telah dihapus dari tanda.";
                        } else {
                            $_SESSION['message'] = "Terjadi kesalahan saat menghapus dokumen.";
                        }
                        mysqli_stmt_close($deleteStmt);
                    }
                } else {
                    // Jika belum ditandai, tambahkan ke marked_dokumen
                    $insertQuery = "INSERT INTO marked_dokumen (id_dokumen, id_user) VALUES (?, ?)";
                    if ($insertStmt = mysqli_prepare($conn, $insertQuery)) {
                        mysqli_stmt_bind_param($insertStmt, 'ii', $id_dokumen, $_SESSION['id_user']);
                        if (mysqli_stmt_execute($insertStmt)) {
                            $_SESSION['message'] = "Dokumen telah ditandai.";
                        } else {
                            $_SESSION['message'] = "Terjadi kesalahan saat menandai dokumen.";
                        }
                        mysqli_stmt_close($insertStmt);
                    }
                }
                mysqli_stmt_close($stmtCheck);
            }
        } else {
            $_SESSION['message'] = "Dokumen tidak ditemukan.";
        }

        mysqli_stmt_close($stmt);
    }
} else {
    $_SESSION['message'] = "ID dokumen tidak diberikan.";
}

// Redirect kembali ke halaman dokumen dan tampilkan pesan jika ada
if (isset($_SESSION['message'])) {
    echo "<div class='alert alert-info'>" . htmlspecialchars($_SESSION['message']) . "</div>";
    unset($_SESSION['message']); // Hapus pesan setelah ditampilkan
}

// Tutup koneksi
mysqli_close($conn);

?>
