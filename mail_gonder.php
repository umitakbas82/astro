<?php
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- 1. Temel Verileri Al ---
    $ad_soyad = strip_tags(trim($_POST["Ad_Soyad"] ?? ''));
    $email = filter_var(trim($_POST["Email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $mesaj = strip_tags(trim($_POST["Mesaj"] ?? ''));
    
    // --- 2. Detaylı Veriler (Varsa Al) ---
    $dogum_tarihi = strip_tags(trim($_POST["Dogum_Tarihi"] ?? ''));
    $dogum_saati = strip_tags(trim($_POST["Dogum_Saati"] ?? ''));
    $dogum_yeri = strip_tags(trim($_POST["Dogum_Yeri"] ?? ''));
    $analiz_turu = strip_tags(trim($_POST["Analiz_Turu"] ?? ''));

    // --- 3. Boş Alan Kontrolü (Temel alanlar şart) ---
    if ( empty($ad_soyad) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Lütfen zorunlu alanları doldurun."]);
        exit;
    }

    // --- 4. Mail İçeriğini Hazırla ---
    $alici_email = "u.akbas1982@gmail.com"; 
    
    // Konuyu belirle (Detaylı form mu, basit iletişim mi?)
    if (!empty($dogum_tarihi)) {
        $konu = "🌟 Yeni Randevu Talebi: $ad_soyad";
    } else {
        $konu = "📩 Yeni İletişim Mesajı: $ad_soyad";
    }

    // Mail Gövdesi (HTML)
    $email_icerik = "
    <html>
    <head><title>Yeni Mesaj</title></head>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <h3 style='color: #D4AF37;'>Kaptan Köşküne Yeni Mesaj! ⚓</h3>
        <hr>
        <p><strong>Gönderen:</strong> $ad_soyad</p>
        <p><strong>E-Posta:</strong> $email</p>
    ";

    // Eğer Doğum Bilgileri Varsa Ekleyelim
    if (!empty($dogum_tarihi)) {
        $email_icerik .= "
        <div style='background: #f9f9f9; padding: 15px; border-left: 4px solid #D4AF37; margin: 10px 0;'>
            <h4 style='margin-top:0;'>🌌 Harita Bilgileri:</h4>
            <p><strong>Doğum Tarihi:</strong> $dogum_tarihi</p>
            <p><strong>Doğum Saati:</strong> $dogum_saati</p>
            <p><strong>Doğum Yeri:</strong> $dogum_yeri</p>
            <p><strong>Tercih Edilen Analiz:</strong> $analiz_turu</p>
        </div>
        ";
    }

    // Mesajı Ekle
    if (!empty($mesaj)) {
        $email_icerik .= "<p><strong>Mesaj / Notlar:</strong><br>$mesaj</p>";
    }

    $email_icerik .= "</body></html>";

    // --- 5. Başlıklar ve Gönderim ---
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Burçyora Web <u.akbas1982@gmail.com>" . "\r\n";
    $headers .= "Reply-To: $email" . "\r\n";

    if (mail($alici_email, $konu, $email_icerik, $headers)) {
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Talebiniz başarıyla alındı!"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Sunucu hatası: Mail gönderilemedi."]);
    }

} else {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Yetkisiz erişim."]);
}
?>