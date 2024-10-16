<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsip Keputusan & Surat Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> <!-- Tambahkan link ke file CSS eksternal -->
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Arsip Surat</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['username'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="container my-5">
        <div class="jumbotron text-center">
            <h1 class="display-4">Selamat Datang di Arsip Keputusan & Surat Tugas</h1>
            <p class="lead">Sistem ini menyimpan dokumen penting yang dapat diakses oleh Admin dan Dosen.</p>
        </div>

        <!-- Search Section -->
        <div class="search-container text-center">
            <form action="search.php" method="post">
                <input type="text" name="search" class="search-box" placeholder="Cari dokumen..." required>
                <button type="submit" class="search-button">Cari</button>
            </form>
        </div>
    </div>

    <!-- Dokumen Terbaru -->
    <div class="container">
        <h3>Dokumen Terbaru</h3>
        <div class="row">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title"><?= $row['title']; ?></h5>
                                <p class="card-text"><?= $row['deskripsi']; ?></p>
                                <p><strong>Kategori:</strong> <?= $row['kategori']; ?></p>
                                <p><strong>Jenis:</strong> <?= $row['jenis']; ?></p>
                                <p><strong>Tanggal:</strong> <?= $row['tanggal_surat']; ?></p>
                                <a href="preview_dokumen.php?id=<?= $row['id_dokumen']; ?>" class="btn btn-primary">Preview</a>
                                <a href="download_dokumen.php?id=<?= $row['id_dokumen']; ?>" class="btn btn-success">Download</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Tidak ada dokumen terbaru.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-primary text-center py-4">
        <p>&copy; 2024 Arsip Keputusan & Surat Tugas</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
