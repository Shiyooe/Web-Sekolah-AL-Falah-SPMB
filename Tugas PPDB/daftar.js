const form = document.getElementById("registerForm");
const msg = document.getElementById("msg");
const pass = document.getElementById("password");
const confirm = document.getElementById("confirm");
const email = document.getElementById("email");
const username = document.getElementById("username");

form.addEventListener("submit", async (e) => {
  e.preventDefault();
  msg.style.display = "none";
  msg.textContent = "";

  // Validasi input
  if (!email.value.trim()) {
    msg.textContent = "Email wajib diisi.";
    msg.style.display = "block";
    email.focus();
    return;
  }

  if (!username.value.trim()) {
    msg.textContent = "Username wajib diisi.";
    msg.style.display = "block";
    username.focus();
    return;
  }

  if (!pass.value) {
    msg.textContent = "Password wajib diisi.";
    msg.style.display = "block";
    pass.focus();
    return;
  }

  if (pass.value !== confirm.value) {
    msg.textContent = "Konfirmasi password tidak sesuai.";
    msg.style.display = "block";
    confirm.focus();
    return;
  }

  // Persiapkan data
  const formData = new URLSearchParams();
  formData.append('gmail', email.value.trim());
  formData.append('username', username.value.trim());
  formData.append('password', pass.value);

  // Disable form selama proses
  const submitBtn = form.querySelector('button[type="submit"]');
  const originalBtnText = submitBtn.textContent;
  submitBtn.disabled = true;
  submitBtn.textContent = "Mendaftar...";

  try {
    // Kirim data ke server
    const response = await fetch('prosesregistrasi.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: formData.toString()
    });

    const data = await response.json();
    
    if (data.success) {
      alert(data.message || "Pendaftaran berhasil!");
      window.location.href = "../index.html";
    } else {
      msg.textContent = data.message || "Gagal mendaftar. Silakan coba lagi.";
      msg.style.display = "block";
      submitBtn.disabled = false;
      submitBtn.textContent = originalBtnText;
    }
  } catch (error) {
    console.error('Error:', error);
    msg.textContent = "Terjadi kesalahan. Silakan coba lagi.";
    msg.style.display = "block";
    submitBtn.disabled = false;
    submitBtn.textContent = originalBtnText;
  }
});
