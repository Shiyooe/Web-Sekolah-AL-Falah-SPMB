const form = document.getElementById("loginForm");
const msg = document.getElementById("msg");
const toggle = document.getElementById("togglePass");
const pass = document.getElementById("password");
const ident = document.getElementById("identifier");

toggle.addEventListener("click", () => {
  const shown = pass.type === "text";
  pass.type = shown ? "password" : "text";
  toggle.textContent = shown ? "Tampilkan" : "Sembunyikan";
});

form.addEventListener("submit", async (e) => {
  e.preventDefault();
  msg.style.display = "none";
  msg.textContent = "";

  const identifier = ident.value.trim();
  const password = pass.value;

  // Validasi input
  if (!identifier) {
    msg.textContent = "Masukkan email atau username.";
    msg.style.display = "block";
    ident.focus();
    return;
  }
  if (!password) {
    msg.textContent = "Masukkan kata sandi.";
    msg.style.display = "block";
    pass.focus();
    return;
  }

  // Tampilkan loading state
  const submitBtn = form.querySelector('button[type="submit"]');
  const originalBtnText = submitBtn.textContent;
  submitBtn.disabled = true;
  submitBtn.textContent = "Memproses...";

    try {
    // Kirim request ke server
    const formData = new FormData();
    formData.append('identifier', identifier);
    formData.append('password', password);

    const response = await fetch('proseslogin.php', {
      method: 'POST',
      body: formData,
      headers: {
        'Accept': 'application/json'
      }
    });    const data = await response.json();

    if (data.success) {
      // Login berhasil
      window.location.href = data.redirect;
    } else {
      // Login gagal
      msg.textContent = data.message;
      msg.style.display = "block";
    }
  } catch (error) {
    msg.textContent = "Terjadi kesalahan. Silakan coba lagi.";
    msg.style.display = "block";
  } finally {
    // Kembalikan tombol ke keadaan semula
    submitBtn.disabled = false;
    submitBtn.textContent = originalBtnText;
  }
  submitBtn.disabled = true;
  submitBtn.textContent = "Memproses...";

  // Kirim request ke server
  fetch("proseslogin.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: new URLSearchParams(payload).toString(),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.text().then(text => {
        try {
          return JSON.parse(text);
        } catch (e) {
          console.error('Server response:', text);
          throw new Error('Invalid JSON response from server');
        }
      });
    })
    .then((data) => {
      console.log('Server response:', data);
      if (data.success) {
        // Login berhasil
        window.location.href = data.redirect;
      } else {
        // Login gagal
        msg.textContent = data.message || 'Login gagal. Silakan coba lagi.';
        msg.style.display = "block";
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      msg.textContent = "Terjadi kesalahan. Silakan coba lagi.";
      msg.style.display = "block";
    })
    .finally(() => {
      submitBtn.disabled = false;
      submitBtn.textContent = originalBtnText;
    });
});
