<?php
// Hataları görelim (Canlıda kapatabilirsin)
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

// --- PHPMailer ---
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "status" => "error",
        "message" => "Hatalı istek."
    ]);
    exit;
}

// --- FORM VERİLERİ ---
$ad_soyad = strip_tags(trim($_POST["Ad_Soyad"] ?? ''));
$email    = filter_var(trim($_POST["Email"] ?? ''), FILTER_SANITIZE_EMAIL);
$mesaj    = strip_tags(trim($_POST["Mesaj"] ?? ''));

// Randevu Bilgileri
$dogum_tarihi = strip_tags($_POST["Dogum_Tarihi"] ?? '');
$dogum_saati  = strip_tags($_POST["Dogum_Saati"] ?? '');
$dogum_yeri   = strip_tags($_POST["Dogum_Yeri"] ?? '');
$analiz_turu  = strip_tags($_POST["Analiz_Turu"] ?? '');

// Zorunlu alan kontrolü
if (empty($ad_soyad) || empty($email)) {
    echo json_encode([
        "status" => "error",
        "message" => "İsim ve E-posta zorunludur."
    ]);
    exit;
}

$mail = new PHPMailer(true);

try {
    // --- SMTP AYARLARI ---
    $mail->isSMTP();
    $mail->Host       = 'mail.burcyoraastro.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@burcyoraastro.com';
    $mail->Password   = 'burcyora123'; // canlıda env variable önerilir
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;
    $mail->CharSet    = 'UTF-8';

    // --- GÖNDEREN ---
    $mail->setFrom('info@burcyoraastro.com', 'Burcyora Web Sitesi');

    // --- ALICILAR (İKİSİNE BİRDEN) ---
    $mail->addAddress('info@burcyoraastro.com');
    $mail->addAddress('burcyoraastro@gmail.com');

    // --- CEVAPLA DİYİNCE KULLANICI ---
    $mail->addReplyTo($email, $ad_soyad);

    // --- İÇERİK ---
    $mail->isHTML(true);

    $mail->Subject = !empty($dogum_tarihi)
        ? "Yeni Randevu Talebi - $ad_soyad"
        : "Yeni İletişim Mesajı - $ad_soyad";

    $icerik  = "<h3>Web Sitesinden Yeni Form Gönderimi</h3>";
    $icerik .= "<p><strong>Ad Soyad:</strong> {$ad_soyad}</p>";
    $icerik .= "<p><strong>E-Posta:</strong> {$email}</p>";

    if (!empty($dogum_tarihi)) {
        $icerik .= "
        <div style='background:#f4f4f4;padding:12px;border-left:4px solid #D4AF37;margin:10px 0'>
            <strong>Randevu Bilgileri</strong><br>
            <b>Doğum Tarihi:</b> {$dogum_tarihi}<br>
            <b>Doğum Saati:</b> {$dogum_saati}<br>
            <b>Doğum Yeri:</b> {$dogum_yeri}<br>
            <b>Analiz Türü:</b> {$analiz_turu}
        </div>";
    }

    if (!empty($mesaj)) {
        $icerik .= "<p><strong>Mesaj:</strong><br>" . nl2br($mesaj) . "</p>";
    }

    $mail->Body = $icerik;

    // --- GÖNDER ---
    $mail->send();

    echo json_encode([
        "status" => "success",
        "message" => "Mesajınız başarıyla gönderildi."
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Mail gönderilemedi: " . $mail->ErrorInfo
    ]);
}
?>
