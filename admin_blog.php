<?php
session_start();

// --- AYARLAR ---
$kullanici_adi = "admin"; // YENİ KULLANICI ADI
$sifre = "1234";          // ŞİFRE
$json_dosyasi = "blog_data.json";

// --- KATEGORİ LİSTESİ ---
$kategoriler = ["Bugünün Enerjisi", "Haftalık Yorum", "Yeni Ay–Dolunay"];

// --- ÇIKIŞ İŞLEMİ ---
if (isset($_GET['cikis'])) {
    session_destroy();
    header("Location: admin_blog.php");
    exit;
}

// --- GİRİŞ KONTROLÜ (GÜNCELLENDİ) ---
if (isset($_POST['giris_yap'])) {
    // Hem Kullanıcı Adı Hem Şifre Doğru Mu?
    if ($_POST['username'] == $kullanici_adi && $_POST['password'] == $sifre) {
        $_SESSION['admin'] = true;
        header("Location: admin_blog.php");
        exit;
    } else {
        $hata = "Kullanıcı adı veya şifre hatalı!";
    }
}

// --- KAYIT VE GÜNCELLEME İŞLEMİ ---
if (isset($_POST['yazi_kaydet']) && isset($_SESSION['admin'])) {
    
    if (!file_exists('uploads')) { mkdir('uploads', 0777, true); }

    $duzenlenecek_id = $_POST['duzenlenecek_id'];
    $mevcut_resim = $_POST['mevcut_resim_yolu'];
    
    $secilen_kategori = $_POST['kategori'];
    if(empty($secilen_kategori)) { $secilen_kategori = "Genel"; }

    $resim_yolu = $mevcut_resim;
    if (empty($resim_yolu)) { $resim_yolu = "assets/default-blog.jpg"; }

    if (isset($_FILES['resim']) && $_FILES['resim']['error'] == 0) {
        $hedef_klasor = "uploads/";
        $dosya_adi = time() . "_" . basename($_FILES["resim"]["name"]);
        $hedef_dosya = $hedef_klasor . $dosya_adi;
        if (move_uploaded_file($_FILES["resim"]["tmp_name"], $hedef_dosya)) {
            $resim_yolu = $hedef_dosya;
        }
    }

    if (file_exists($json_dosyasi)) {
        $mevcut_veriler = json_decode(file_get_contents($json_dosyasi), true);
    } else {
        $mevcut_veriler = [];
    }
    if (!is_array($mevcut_veriler)) $mevcut_veriler = [];

    // GÜNCELLEME Mİ? YENİ Mİ?
    if (!empty($duzenlenecek_id)) {
        foreach ($mevcut_veriler as &$yazi) {
            if ($yazi['id'] == $duzenlenecek_id) {
                $yazi['baslik']   = $_POST['baslik'];
                $yazi['kategori'] = $secilen_kategori;
                $yazi['ozet']     = $_POST['ozet'];
                $yazi['icerik']   = $_POST['icerik'];
                $yazi['resim']    = $resim_yolu;
                $yazi['tarih']    = date("d.m.Y");
                break;
            }
        }
        $basari = "Yazı başarıyla güncellendi! ♻️";
    } else {
        $yeni_yazi = [
            'id'       => uniqid(),
            'tarih'    => date("d.m.Y"),
            'kategori' => $secilen_kategori,
            'baslik'   => $_POST['baslik'],
            'ozet'     => $_POST['ozet'],
            'icerik'   => $_POST['icerik'], 
            'resim'    => $resim_yolu
        ];
        array_unshift($mevcut_veriler, $yeni_yazi);
        $basari = "Blog yazısı başarıyla yayınlandı! 🚀";
    }

    file_put_contents($json_dosyasi, json_encode($mevcut_veriler, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// --- SİLME ---
if (isset($_GET['sil']) && isset($_SESSION['admin'])) {
    $sil_id = $_GET['sil'];
    if (file_exists($json_dosyasi)) {
        $veriler = json_decode(file_get_contents($json_dosyasi), true);
        if(is_array($veriler)){
            $yeni_veriler = array_filter($veriler, function($y) use ($sil_id) { return $y['id'] != $sil_id; });
            file_put_contents($json_dosyasi, json_encode(array_values($yeni_veriler), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }
    header("Location: admin_blog.php");
    exit;
}

// --- DÜZENLEME MODU VERİLERİ ---
$val_id = ""; $val_baslik = ""; $val_ozet = ""; $val_icerik = ""; $val_resim = ""; $val_kategori = "";
$form_baslik = "Yeni Yazı Ekle"; $btn_text = "YAYIMLA ";

if (isset($_GET['duzenle']) && isset($_SESSION['admin'])) {
    $duzenle_id = $_GET['duzenle'];
    if (file_exists($json_dosyasi)) {
        $veriler = json_decode(file_get_contents($json_dosyasi), true);
        foreach ($veriler as $v) {
            if ($v['id'] == $duzenle_id) {
                $val_id = $v['id'];
                $val_baslik = $v['baslik'];
                $val_kategori = isset($v['kategori']) ? $v['kategori'] : "";
                $val_ozet = $v['ozet'];
                $val_icerik = $v['icerik'];
                $val_resim = $v['resim'];
                $form_baslik = "Yazıyı Düzenle ✏️";
                $btn_text = "GÜNCELLE ♻️";
                break;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Yöneticisi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <style>
        body { background-color: #0a0a0f; color: #fff; font-family: sans-serif; }
        .btn-gold { background-color: #D4AF37; color: #000; border: none; font-weight: bold; }
        .btn-gold:hover { background-color: #f1c40f; }
        .note-editor .note-editing-area { background-color: #fff; color: #000; }
        .note-toolbar { background-color: #eee; color: #000; }
        .card-header { color: #D4AF37 !important; font-weight: bold; }
    </style>
</head>
<body>

<?php if (!isset($_SESSION['admin'])) { ?>
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card bg-dark border-warning p-4" style="width: 400px;">
            <h3 class="text-center text-warning mb-4">  BURCYORA BLOG </h3>
            <?php if(isset($hata)) { echo "<div class='alert alert-danger'>$hata</div>"; } ?>
            <form method="post">
                <input type="text" name="username" class="form-control mb-3" placeholder="Kullanıcı Adı" required>
                <input type="password" name="password" class="form-control mb-3" placeholder="Şifre" required>
                <button type="submit" name="giris_yap" class="btn btn-gold w-100">GİRİŞ YAP</button>
            </form>
        </div>
    </div>
    <?php exit; ?>
<?php } ?>

    <div class="container mt-5 pb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-warning">Blog Yöneticisi</h2>
            <div>
                <a href="admin_blog.php" class="btn btn-outline-light btn-sm me-2">Yeni Ekle</a>
                <a href="?cikis=1" class="btn btn-outline-danger btn-sm">Çıkış Yap</a>
            </div>
        </div>

        <?php if(isset($basari)) { echo "<div class='alert alert-success'>$basari</div>"; } ?>

        <div class="card bg-dark border-secondary mb-5">
            <div class="card-header border-secondary"><?php echo $form_baslik; ?></div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="duzenlenecek_id" value="<?php echo $val_id; ?>">
                    <input type="hidden" name="mevcut_resim_yolu" value="<?php echo $val_resim; ?>">

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-warning">Kategori</label>
                            <select name="kategori" class="form-select">
                                <option value="">Kategori Seçin...</option>
                                <?php foreach($kategoriler as $kat): ?>
                                    <option value="<?php echo $kat; ?>" <?php if($val_kategori == $kat) echo 'selected'; ?>>
                                        <?php echo $kat; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-8 mb-3">
                            <label class="form-label text-warning">Başlık</label>
                            <input type="text" name="baslik" class="form-control" value="<?php echo htmlspecialchars($val_baslik); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-warning">Kapak Resmi</label>
                        <input type="file" name="resim" class="form-control">
                        <?php if(!empty($val_resim)): ?>
                            <small class="text-muted">Mevcut: <a href="<?php echo $val_resim; ?>" target="_blank" class="text-warning">Gör</a></small>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-warning">Kısa Özet</label>
                        <input type="text" name="ozet" class="form-control" value="<?php echo htmlspecialchars($val_ozet); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-warning">İçerik</label>
                        <textarea id="summernote" name="icerik"><?php echo $val_icerik; ?></textarea>
                    </div>

                    <button type="submit" name="yazi_kaydet" class="btn btn-gold w-100"><?php echo $btn_text; ?></button>
                </form>
            </div>
        </div>

        <h4 class="text-white mb-3">Yayındaki Yazılar</h4>
        <table class="table table-dark table-hover border-secondary">
            <thead>
                <tr>
                    <th width="100">Tarih</th>
                    <th width="150">Kategori</th>
                    <th width="80">Resim</th>
                    <th>Başlık</th>
                    <th width="150" class="text-end">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (file_exists($json_dosyasi)) {
                    $yazilar = json_decode(file_get_contents($json_dosyasi), true);
                    if (!empty($yazilar) && is_array($yazilar)) {
                        foreach ($yazilar as $yazi) {
                            $kat = isset($yazi['kategori']) ? $yazi['kategori'] : '-';
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($yazi['tarih']) . "</td>";
                            echo "<td><span class='badge bg-warning text-dark'>" . htmlspecialchars($kat) . "</span></td>";
                            echo "<td><img src='" . htmlspecialchars($yazi['resim']) . "' width='50' style='border-radius:4px;'></td>";
                            echo "<td>" . htmlspecialchars($yazi['baslik']) . "</td>";
                            echo "<td class='text-end'>";
                            echo "<a href='?duzenle=" . $yazi['id'] . "' class='btn btn-sm btn-warning me-2'>Düzenle</a>";
                            echo "<a href='?sil=" . $yazi['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Sil?\")'>Sil</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center text-muted'>Henüz yazı yok.</td></tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                placeholder: 'Yazınızı buraya yazın...',
                tabsize: 2,
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });
    </script>
</body>
</html>