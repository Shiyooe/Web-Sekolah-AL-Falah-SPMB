// Scroll-to-top button logic
window.addEventListener("scroll", function () {
  const scrollButton = document.querySelector(".scroll-top");
  if (scrollButton) {
    if (window.scrollY > 300) {
      scrollButton.classList.add("show");
    } else {
      scrollButton.classList.remove("show");
    }
  }

  // Animate sections on scroll
  const sections = document.querySelectorAll(".section");
  sections.forEach((section) => {
    const sectionTop = section.getBoundingClientRect().top;
    const sectionVisible = 150;
    if (sectionTop < window.innerHeight - sectionVisible) {
      section.classList.add("visible");
    }
  });
});

// Smooth scroll to top
function scrollToTop() {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
}

// Fungsi untuk mengatur tanggal hari ini
function setTodayDate() {
  const todayInput = document.getElementById("tanggal_pembayaran");
  if (todayInput) {
    const today = new Date();
    const formattedDate = today.toISOString().split("T")[0];
    todayInput.value = formattedDate;
  }
}

// Fungsi format Rupiah (dengan titik)
function formatRupiah(input) {
  let value = input.value.replace(/\D/g, "");
  if (value === "") {
    input.value = "";
    document.getElementById("jumlah_asli").value = "";
    return;
  }

  // Tambahkan titik dari belakang
  let formatted = "";
  for (let i = 0; i < value.length; i++) {
    if (i > 0 && (value.length - i) % 3 === 0) {
      formatted += ".";
    }
    formatted += value[i];
  }

  input.value = formatted;
  document.getElementById("jumlah_asli").value = value; // Simpan nilai asli (tanpa titik)
}

// Tangani perubahan pada jenis pembayaran
function handleJenisPembayaran() {
  const jenis = document.getElementById("jenis_pembayaran").value;
  const container = document.getElementById("jumlah-container");
  const displayInput = document.getElementById("jumlah_display");
  const hiddenInput = document.getElementById("jumlah_asli");

  if (jenis === "bayar_semua") {
    container.style.display = "block";
    displayInput.value = "3.350.000";
    displayInput.readOnly = true;
    hiddenInput.value = "3350000";
  } else if (jenis === "di_cicil") {
    container.style.display = "block";
    displayInput.value = "";
    displayInput.readOnly = false;
    hiddenInput.value = "";
    displayInput.oninput = function () {
      formatRupiah(this);
    };
  } else {
    container.style.display = "none";
    displayInput.value = "";
    hiddenInput.value = "";
  }
}

// Preview nama file upload
function setupFilePreview() {
  const fileInput = document.getElementById("bukti_pembayaran");
  const container = document.getElementById("uploadContainer");

  if (fileInput && container) {
    fileInput.addEventListener("change", function () {
      if (this.files.length > 0) {
        container.querySelector("p").innerHTML = `✅ File Dipilih: <strong>${this.files[0].name}</strong><br><small>Klik lagi untuk mengubah</small>`;
      } else {
        container.querySelector("p").innerHTML = "📁 Klik untuk upload atau drag & drop<br><small>Format: JPG, PNG (Max 5MB)</small>";
      }
    });
  }
}

// Reset form
function resetForm() {
  const form = document.getElementById("pembayaranForm");
  if (form) {
    form.reset();
    setTodayDate();
    const jumlahContainer = document.getElementById("jumlah-container");
    if (jumlahContainer) {
      jumlahContainer.style.display = "none";
    }
    const uploadContainer = document.getElementById("uploadContainer");
    if (uploadContainer) {
      uploadContainer.querySelector("p").innerHTML = "📁 Klik untuk upload atau drag & drop<br><small>Format: JPG, PNG (Max 5MB)</small>";
    }
  }
}

// Toggle form pembayaran online
function setupToggleForm() {
  const toggleBtn = document.getElementById("toggleFormBtn");
  const formContainer = document.getElementById("pembayaranFormContainer");
  const formSection = document.getElementById("form-pembayaran-section");

  if (toggleBtn && formContainer) {
    toggleBtn.addEventListener("click", function () {
      formContainer.classList.toggle("visible");

      if (formContainer.classList.contains("visible") && formSection) {
        formSection.scrollIntoView({ behavior: "smooth" });
        setTodayDate(); // Pastikan tanggal selalu di-update
      }
    });
  }
}

// Simpan metode pembayaran offline (dipanggil dari tombol)
function setPaymentMethod(method) {
  sessionStorage.setItem("paymentMethod", method);
}

// Validasi form pembayaran
function setupFormValidation() {
  const form = document.getElementById("pembayaranForm");
  
  if (form) {
    form.addEventListener("submit", function(e) {
      const jenis = document.getElementById("jenis_pembayaran").value;
      const jumlahValue = document.getElementById("jumlah_asli").value;
      const jumlah = parseInt(jumlahValue);
      const bukti = document.getElementById("bukti_pembayaran").files[0];

      // Validasi minimal cicilan
      if (jenis === "di_cicil" && (isNaN(jumlah) || jumlah < 500000)) {
        e.preventDefault();
        alert("❌ Jumlah cicilan minimal Rp 500.000!");
        return false;
      }

      // Validasi ukuran file
      if (bukti && bukti.size > 5 * 1024 * 1024) {
        e.preventDefault();
        alert("❌ Ukuran file terlalu besar! Maksimal 5MB.");
        return false;
      }

      // Konfirmasi sebelum submit
      if (!confirm("✅ Apakah data yang Anda masukkan sudah benar?")) {
        e.preventDefault();
        return false;
      }

      // Form akan di-submit ke prosesbayar.php, jangan redirect manual
      // Simpan metode pembayaran ke sessionStorage
      sessionStorage.setItem("paymentMethod", "online");
    });
  }
}

// Inisialisasi saat halaman dimuat
document.addEventListener("DOMContentLoaded", function () {
  setTodayDate();
  setupFilePreview();
  setupFormValidation();
  setupToggleForm();

  // Pasang event listener untuk jenis pembayaran
  const jenisSelect = document.getElementById("jenis_pembayaran");
  if (jenisSelect) {
    jenisSelect.addEventListener("change", handleJenisPembayaran);
  }
});