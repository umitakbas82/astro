<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gökyüzü Günlüğü - Burcyora Astro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Montserrat:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="style.css" rel="stylesheet">
</head>
<body style="background-color: #0a0a0f;">
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <!-- <a class="navbar-brand logo-text" href="/index.html">BURCYORA ASTRO </a> -->
            <a class="navbar-brand d-flex flex-column" href="/index.php" style="line-height: 1;">
                <span class="logo-text">BURCYORA</span>
                <span class="logo-slogan small" style="color: white">Sezgisel & Analitik Astroloji</span>
            </a>


            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="/hakkimizda.html">Hakkımızda</a></li>
                        <li class="nav-item"><a class="nav-link" href="/danismanlikhizmetleri.html">Danışmanlık
                                Hizmetleri</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="/blog.php">Gökyüzü Günlüğü</a></li>
                        <li class="nav-item"><a class="nav-link" href="/nedenbiz.html">Neden Biz?</a></li>

                        <li class="nav-item"><a class="nav-link btn-iletisim" href="/iletisim.html">İletişim</a></li>
                    </ul>
                </ul>
            </div>
        </div>
    </nav>

    <section class="py-5 mt-5">
        <div class="container text-center pt-5">
            <h1 class="text-white" style="font-family: 'Cinzel', serif;">GÖKYÜZÜ <span class="text-gold">GÜNLÜĞÜ</span></h1>
            <p class="text-secondary">Yıldızların rehberliğinde güncel analizler ve makaleler.</p>
            <div class="mx-auto" style="width: 100px; height: 2px; background-color: #D4AF37;"></div>
        </div>
    </section>

    <section class="pb-5">
        <div class="container">
            <div class="row g-4">

            <?php
            $json_dosyasi = "blog_data.json";
            if (file_exists($json_dosyasi)) {
                $yazilar = json_decode(file_get_contents($json_dosyasi), true);
            } else {
                $yazilar = [];
            }

            if (!empty($yazilar)) {
                foreach ($yazilar as $yazi) {
                    echo '
                    <div class="col-md-4">
                        <div class="card h-100" style="background-color: #0f0f14; border: 1px solid rgba(212,175,55,0.2);">
                            <img src="'.$yazi['resim'].'" class="card-img-top" alt="Blog Görsel" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <span class="badge bg-warning text-dark mb-2">'.$yazi['tarih'].'</span>
                                <h5 class="card-title text-gold" style="font-family: \'Cinzel\', serif;">'.$yazi['baslik'].'</h5>
                                <p class="card-text text-secondary small">'.mb_substr($yazi['ozet'], 0, 100).'... </p>
                                <a href="blog_detay.php?id='.$yazi['id'].'" class="btn btn-sm btn-outline-warning w-100">DEVAMINI OKU &rarr;</a>
                            </div>
                        </div>
                    </div>
                    ';
                }
            } else {
                echo '<div class="col-12 text-center text-muted"><p>Henüz bir yazı paylaşılmadı.</p></div>';
            }
            ?>

            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>