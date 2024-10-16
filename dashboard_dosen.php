<?php
session_start();
require 'db.php'; // Koneksi ke database

// Cek apakah pengguna adalah dosen dan sudah login
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'dosen') {
    header("Location: login.php");
    exit();
}

// Menampilkan semua error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Query untuk mencari dokumen
$query = "
    SELECT d.*
    FROM dokumen d
    JOIN marked_dokumen md ON d.id_dokumen = md.id_dokumen
    WHERE md.id_user = ? AND (d.title LIKE ? OR d.no_surat LIKE ?)
    ORDER BY d.created_at DESC
";

// Mempersiapkan statement SQL untuk menghindari SQL injection
if ($stmt = mysqli_prepare($conn, $query)) {
    $searchParam = "%" . $search . "%";
    mysqli_stmt_bind_param($stmt, 'iss', $_SESSION['id_user'], $searchParam, $searchParam); // Bind parameter id_user dan pencarian
    mysqli_stmt_execute($stmt); // Eksekusi statement
    $result = mysqli_stmt_get_result($stmt); // Ambil hasil eksekusi query

    if (!$result) {
        die("Query error: " . mysqli_error($conn));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Hasil Pencarian untuk "<?= htmlspecialchars($search); ?>"</h1>

        <!-- Tabel daftar dokumen -->
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul</th>
                    <th>No. Surat</th>
                    <th>Tanggal Surat</th>
                    <th>Kategori</th>
                    <th>Jenis</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($no++); ?></td>
                        <td><?= htmlspecialchars($row['title']); ?></td>
                        <td><?= htmlspecialchars($row['no_surat']); ?></td>
                        <td><?= htmlspecialchars($row['tanggal_surat']); ?></td>
                        <td><?= ucfirst(htmlspecialchars($row['kategori'])); ?></td>
                        <td><?= ucfirst(htmlspecialchars($row['jenis'])); ?></td>
                        <td>
                            <!-- Tombol aksi -->
                            <a href="preview_dokumen.php?id=<?= urlencode($row['id_dokumen']); ?>" class="btn btn-info btn-sm">Preview</a>
                            <a href="download_dokumen.php?id=<?= urlencode($row['id_dokumen']); ?>" class="btn btn-success btn-sm">Download</a>
                            <a href="mark_dokumen.php?id=<?= urlencode($row['id_dokumen']); ?>" class="btn btn-primary btn-sm">Tandai</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada hasil untuk pencarian ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Tombol kembali ke dashboard -->
        <a href="dashboard_dosen.php" class="btn btn-secondary">Kembali ke Dashboard</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html>
