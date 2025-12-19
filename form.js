document.addEventListener("DOMContentLoaded", function () {
    console.log("Form.js Başarıyla Yüklendi! 🚀");

    // Tüm formları yöneten ana fonksiyon
    function setupForm(formId, btnId, btnTextId, loaderId) {
        const formElement = document.getElementById(formId);

        if (formElement) {
            console.log(formId + " bulundu ve dinleniyor..."); // Konsola bilgi ver

            formElement.addEventListener("submit", function (event) {
                event.preventDefault();

                var form = this;
                var btn = document.getElementById(btnId);
                var btnText = document.getElementById(btnTextId);
                var btnLoader = document.getElementById(loaderId);

                // Butonları kilitle
                if (btn) btn.disabled = true;
                if (btnText) btnText.textContent = "GÖNDERİLİYOR...";
                if (btnLoader) btnLoader.classList.remove("d-none");

                var formData = new FormData(form);

                fetch("mail_gonder.php", {
                    method: "POST",
                    body: formData
                })
                    .then(response => response.text()) // Önce metin olarak al (Hata görmek için)
                    .then(text => {
                        console.log("Sunucu Cevabı:", text); // Konsola bas

                        try {
                            // Gelen metni JSON'a çevirmeyi dene
                            const data = JSON.parse(text);

                            if (data.status === "success") {
                                Swal.fire({
                                    title: 'Harika! 🌟',
                                    text: data.message,
                                    icon: 'success',
                                    iconColor: '#D4AF37',
                                    confirmButtonText: 'TAMAM',
                                    background: '#0a0a0f',
                                    color: '#fff',
                                    confirmButtonColor: '#D4AF37'
                                });
                                form.reset();
                            } else {
                                // PHP tarafında bilerek gönderilen hata
                                Swal.fire({
                                    title: 'Bir Sorun Var!',
                                    text: data.message,
                                    icon: 'error',
                                    background: '#0a0a0f',
                                    color: '#fff',
                                    confirmButtonColor: '#D4AF37'
                                });
                            }
                        } catch (e) {
                            // JSON DEĞİLSE (Yani sunucu PHP hatası bastıysa)
                            Swal.fire({
                                title: 'Sunucu Hatası! ⚠️',
                                html: 'Sunucu şu hatayı döndürdü:<br><code>' + text.substring(0, 200) + '...</code>',
                                icon: 'warning',
                                background: '#0a0a0f',
                                color: '#fff',
                                confirmButtonColor: '#D4AF37'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Ağ Hatası',
                            text: 'İnternet bağlantınızı kontrol edin.',
                            icon: 'error',
                            confirmButtonColor: '#D4AF37'
                        });
                    })
                    .finally(() => {
                        if (btn) btn.disabled = false;
                        if (btnText) btnText.textContent = "GÖNDER 🚀";
                        if (btnLoader) btnLoader.classList.add("d-none");
                    });
            });
        } else {
            console.error(formId + " SAYFADA BULUNAMADI! ID'leri kontrol et.");
        }
    }

    // 1. İletişim Sayfasındaki Formu Kur
    setupForm("contactForm", "submitBtn", "btnText", "btnLoader");

    // 2. Danışmanlık/Randevu Sayfasındaki Formu Kur
    setupForm("appointmentForm", "appSubmitBtn", "appBtnText", "appBtnLoader");
});