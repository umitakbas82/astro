<?php
// Hataları görelim
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

// --- PHPMailer Dosyalarını Çağır (Bunlar sunucuda kalmalı) ---
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $ad_soyad = strip_tags(trim($_POST["Ad_Soyad"] ?? ''));
    $email = filter_var(trim($_POST["Email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $mesaj = strip_tags(trim($_POST["Mesaj"] ?? ''));
    
    // Randevu Detayları
    $dogum_tarihi = $_POST["Dogum_Tarihi"] ?? '';
    $dogum_saati = $_POST["Dogum_Saati"] ?? '';
    $dogum_yeri = $_POST["Dogum_Yeri"] ?? '';
    $analiz_turu = $_POST["Analiz_Turu"] ?? '';

    if (empty($ad_soyad) || empty($email)) {
        echo json_encode(["status" => "error", "message" => "İsim ve E-posta zorunludur."]);
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // --- HOSTING SMTP AYARLARI ---
        $mail->isSMTP();                                            
        $mail->Host       = 'mail.burcyoraastro.com'; // Genelde budur veya 'localhost'
        $mail->SMTPAuth   = true;                                   
        
        // --- BURAYI DOLDURMAN ÇOK ÖNEMLİ ---
        $mail->Username   = 'info@burcyoraastro.com';  // Hosting'de açtığın mail
        $mail->Password   = 'burcyora123'; // O mailin şifresi
        // -------------------------------------

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Güvenli bağlantı
        $mail->Port       = 465; // Genelde 465'tir. Çalışmazsa 587 dene.
        $mail->CharSet    = 'UTF-8';

        // KİMDEN: Kendi sitemizden
        $mail->setFrom('info@burcyoraastro.com', 'Burçyora Web Sitesi');
        
        // KİME: Yine info adresine (Gmail yok!)
        $mail->addAddress('info@burcyoraastro.com');     

        // Yanıtla diyince MÜŞTERİ SEÇİLSİN
        $mail->addReplyTo($email, $ad_soyad);

        // İçerik Ayarları
        $mail->isHTML(true);                                  
        
        $konu_basligi = !empty($dogum_tarihi) ? "Randevu: $ad_soyad" : "Mesaj: $ad_soyad";
        $mail->Subject = $konu_basligi;

        $icerik = "<h3>Web Sitesinden Yeni Mesaj </h3>";
        $icerik .= "<p><strong>Gönderen:</strong> $ad_soyad</p>";
        $icerik .= "<p><strong>E-Posta:</strong> $email</p>";

        if (!empty($dogum_tarihi)) {
            $icerik .= "<div style='background:#f3f3f3; padding:10px; border-left:4px solid #D4AF37;'>";
            $icerik .= "<strong>Randevu Bilgileri:</strong><br>";
            $icerik .= "Tarih: $dogum_tarihi <br>";
            $icerik .= "Saat: $dogum_saati <br>";
            $icerik .= "Yer: $dogum_yeri <br>";
            $icerik .= "Analiz: $analiz_turu";
            $icerik .= "</div>";
        }

        $icerik .= "<p><strong>Mesaj:</strong><br>$mesaj</p>";
        
        $mail->Body = $icerik;

        $mail->send();
        echo json_encode(["status" => "success", "message" => "Mesajınız başarıyla iletildi!"]);

    } catch (Exception $e) {
        // Hata verirse detayını görelim
        echo json_encode(["status" => "error", "message" => "Mail Hatası: {$mail->ErrorInfo}"]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Hatalı istek."]);
}
?>