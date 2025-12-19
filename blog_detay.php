<?php
// ID kontrolü
if (!isset($_GET['id'])) {
    header("Location: blog.php");
    exit;
}

$id = $_GET['id'];
$json_dosyasi = "blog_data.json";
$yazilar = json_decode(file_get_contents($json_dosyasi), true);
$secilen_yazi = null;

foreach ($yazilar as $yazi) {
    if ($yazi['id'] == $id) {
        $secilen_yazi = $yazi;
        break;
    }
}

if (!$secilen_yazi) {
    echo "Yazı bulunamadı.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $secilen_yazi['baslik']; ?> - Burçyora Astro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Montserrat:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="style.css" rel="stylesheet">
    <style>
        /* Blog içeriği içindeki resimler taşmasın */
        .blog-content img { max-width: 100%; height: auto; border-radius: 5px; margin: 20px 0; }
        .blog-content { color: #ddd; line-height: 1.8; font-size: 1.1rem; }
        .blog-content h2, .blog-content h3 { color: #D4AF37; margin-top: 30px; font-family: 'Cinzel', serif; }
        .blog-content ul { list-style: square; color: #ccc; }
    </style>
</head>
<body style="background-color: #0a0a0f;">

    <nav class="navbar navbar-expand-lg fixed-top" style="background-color: rgba(10,10,15,0.95);">
        <div class="container">
            <a class="navbar-brand d-flex flex-column" href="index.html" style="line-height: 1;">
                <span class="logo-text">BURÇYORA ASTRO</span>
            </a>
            <a href="blog.php" class="btn btn-outline-gold btn-sm"><i class="bi bi-arrow-left"></i> Geri Dön</a>
        </div>
    </nav>

    <section class="py-5 mt-5">
        <div class="container pt-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    
                    <img src="<?php echo $secilen_yazi['resim']; ?>" class="w-100 rounded mb-4" alt="Kapak" style="max-height: 400px; object-fit: cover;">
                    
                    <div class="mb-4 text-center">
                        <span class="badge bg-warning text-dark"><?php echo $secilen_yazi['tarih']; ?></span>
                        <h1 class="text-white mt-3" style="font-family: 'Cinzel', serif;"><?php echo $secilen_yazi['baslik']; ?></h1>
                    </div>

                    <div class="blog-content">
                        <?php echo $secilen_yazi['icerik']; ?>
                    </div>

                    <div class="mt-5 pt-4 border-top border-secondary text-center">
                        <p class="text-muted">Bu yazıyı beğendiysen paylaşmayı unutma! 🌟</p>
                        <a href="blog.php" class="btn btn-gold">Diğer Yazıları Oku</a>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <footer class="text-center py-4 mt-5" style="border-top: 1px solid #333;">
        <small class="text-muted">&copy; 2025 Burçyora Astro.</small>
    </footer>

</body>
</html>