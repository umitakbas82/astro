document.addEventListener("DOMContentLoaded", function () {

    // Tüm formları yöneten ana fonksiyon
    function setupForm(formId, btnId, btnTextId, loaderId) {
        const formElement = document.getElementById(formId);

        if (formElement) {
            formElement.addEventListener("submit", function (event) {
                event.preventDefault();

                var form = this;
                var btn = document.getElementById(btnId);
                var btnText = document.getElementById(btnTextId);
                var btnLoader = document.getElementById(loaderId);

                // Yükleniyor...
                if (btn) btn.disabled = true;
                if (btnText) btnText.textContent = "GÖNDERİLİYOR...";
                if (btnLoader) btnLoader.classList.remove("d-none");

                var formData = new FormData(form);

                fetch("mail_gonder.php", {
                    method: "POST",
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "success") {
                            Swal.fire({
                                title: 'Harika! 🌟',
                                text: data.message || 'Bilgileriniz bize ulaştı.',
                                icon: 'success',
                                iconColor: '#D4AF37',
                                confirmButtonText: 'TAMAM',
                                background: '#0a0a0f',
                                color: '#fff',
                                confirmButtonColor: '#D4AF37'
                            });
                            form.reset();
                        } else {
                            Swal.fire({
                                title: 'Bir Sorun Var!',
                                text: data.message || 'Gönderilemedi.',
                                icon: 'error',
                                iconColor: '#d33',
                                confirmButtonText: 'TEKRAR DENE',
                                background: '#0a0a0f',
                                color: '#fff',
                                confirmButtonColor: '#D4AF37'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Bağlantı Hatası',
                            text: 'Sunucuya ulaşılamadı. Lütfen internetinizi kontrol edin.',
                            icon: 'warning',
                            iconColor: '#D4AF37',
                            confirmButtonText: 'TAMAM',
                            background: '#0a0a0f',
                            color: '#fff',
                            confirmButtonColor: '#D4AF37'
                        });
                    })
                    .finally(() => {
                        if (btn) btn.disabled = false;
                        if (btnText) btnText.textContent = "GÖNDER "; // Buton yazısı eski haline döner
                        if (btnLoader) btnLoader.classList.add("d-none");
                    });
            });
        }
    }

    // 1. İletişim Sayfasındaki Formu Kur
    setupForm("contactForm", "submitBtn", "btnText", "btnLoader");

    // 2. Danışmanlık/Randevu Sayfasındaki Formu Kur
    setupForm("appointmentForm", "appSubmitBtn", "appBtnText", "appBtnLoader");

});