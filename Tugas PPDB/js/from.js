// === SET TANGGAL OTOMATIS ===
document.addEventListener("DOMContentLoaded", () => {
  const tglInput = document.getElementById("tgl_daftar");
  const today = new Date();
  const hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"][
    today.getDay()
  ];
  const bulan = [
    "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember",
  ][today.getMonth()];
  tglInput.value = `${hari}, ${today.getDate()} ${bulan} ${today.getFullYear()}`;

  // Tambahkan kode untuk mengisi form jika ada data dari localStorage
  const savedData = localStorage.getItem("ppdbFormData");
  if (savedData) {
    const data = JSON.parse(savedData);
    // Isi form dengan data yang tersimpan
    for (const [key, value] of Object.entries(data)) {
      const input = document.getElementById(key);
      if (input && input.name !== 'tgl_daftar') {
          input.value = value;
      }
    }
    // Trigger event change untuk menampilkan bagian Ortu/Wali yang benar
    const tipePengisiSelect = document.getElementById("tipe_pengisi");
    if (tipePengisiSelect.value) {
        // Panggil fungsi change handler secara eksplisit untuk mengatur required
        tipePengisiSelect.dispatchEvent(new Event('change'));
    }
  }
});

// === TAMPILKAN BAGIAN ORTU / WALI & ATUR REQUIRED ===
const tipePengisi = document.getElementById("tipe_pengisi");
const bagianOrtu = document.getElementById("bagianOrtu");
const bagianWali = document.getElementById("bagianWali");

tipePengisi.addEventListener("change", function () {
    const ortuFields = bagianOrtu.querySelectorAll("input, select");
    const waliFields = bagianWali.querySelectorAll("input, select");

    if (this.value === "ortu") {
        bagianOrtu.classList.remove("hidden");
        bagianWali.classList.add("hidden");
        
        // FIX: Atur atribut required secara dinamis
        // Pastikan field ortu menjadi required
        ortuFields.forEach((i) => (i.required = true));
        // Pastikan field wali TIDAK required (walaupun hidden)
        waliFields.forEach((i) => (i.required = false));
        
    } else if (this.value === "wali") {
        bagianWali.classList.remove("hidden");
        bagianOrtu.classList.add("hidden");
        
        // FIX: Atur atribut required secara dinamis
        // Pastikan field wali menjadi required
        waliFields.forEach((i) => (i.required = true));
        // Pastikan field ortu TIDAK required (karena hidden)
        ortuFields.forEach((i) => (i.required = false));
        
    } else {
        bagianOrtu.classList.add("hidden");
        bagianWali.classList.add("hidden");
        // Pastikan semua field tidak required jika tidak ada pilihan
        ortuFields.forEach((i) => (i.required = false));
        waliFields.forEach((i) => (i.required = false));
    }
});

// === SIMPAN KE LOCALSTORAGE & REDIRECT ===
document.getElementById("ppdb-form").addEventListener("submit", function (e) {
  e.preventDefault();

  const jurusan1 = document.getElementById("jurusan1").value;
  const jurusan2 = document.getElementById("jurusan2").value;

  if (jurusan1 && jurusan2 && jurusan1 === jurusan2) {
    alert("Pilihan jurusan 1 dan 2 tidak boleh sama.");
    return;
  }

  // Kumpulkan semua data form menjadi objek JavaScript
  const formData = new FormData(this);
  const data = {};
  for (const [key, value] of formData.entries()) {
    data[key] = value;
  }
  // Tambahkan tanggal daftar (sesuai yang ditampilkan)
  data['tgl_daftar'] = document.getElementById("tgl_daftar").value;
  
  // Hapus data ORTU/WALI yang tidak relevan (untuk kebersihan data sebelum disimpan ke localStorage)
  if (data.tipe_pengisi === 'wali') {
      const ortuFields = ["no_kk", "nama_ayah", "pekerjaan_ayah", "tempat_lahir_ayah", "tanggal_lahir_ayah", "ktp_ayah", "telepon_ayah", "nama_ibu", "pekerjaan_ibu", "tempat_lahir_ibu", "tanggal_lahir_ibu", "ktp_ibu", "telepon_ibu"];
      ortuFields.forEach(key => delete data[key]);
  } else if (data.tipe_pengisi === 'ortu') {
      const waliFields = ["nama_wali", "tempat_lahir_wali", "tanggal_lahir_wali", "ktp_wali", "no_tlp_wali", "pekerjaan_wali"];
      waliFields.forEach(key => delete data[key]);
  }


  // Simpan data ke localStorage
  localStorage.setItem("ppdbFormData", JSON.stringify(data));

  // Redirect ke halaman konfirmasi
  window.location.href = "pemilihan.html";
});