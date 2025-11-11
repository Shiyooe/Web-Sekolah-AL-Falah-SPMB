document.addEventListener("DOMContentLoaded", function () {
  const savedData = localStorage.getItem("ppdbFormData");
  if (!savedData) {
    window.location.href = "from.html";
    return;
  }
  const data = JSON.parse(savedData);
  const siswaSummary = document.getElementById("siswa-summary");
  const ortuSummary = document.getElementById("ortu-summary");
  const waliSummary = document.getElementById("wali-summary");
  const waliSection = document.getElementById("wali-section");
  
  const fieldLabels = {
    // Siswa
    nama: "Nama Lengkap",
    tempat_lahir: "Tempat Lahir",
    tanggal_lahir: "Tanggal Lahir",
    anak_ke: "Anak Ke",
    jk: "Jenis Kelamin",
    alamat: "Alamat",
    telepon: "No. Telepon",
    asal_sekolah: "Asal Sekolah",
    nisn: "NISN",
    hobby: "Hobby",
    citacita: "Cita-cita",
    ukuran_baju: "Ukuran Baju",
    jurusan1: "Jurusan Pilihan 1",
    jurusan2: "Jurusan Pilihan 2",
    // Ortu
    no_kk: "No. Kartu Keluarga",
    nama_ayah: "Nama Ayah",
    pekerjaan_ayah: "Pekerjaan Ayah",
    tempat_lahir_ayah: "Tempat Lahir Ayah",
    tanggal_lahir_ayah: "Tanggal Lahir Ayah",
    ktp_ayah: "No. KTP Ayah",
    telepon_ayah: "No. Telepon Ayah",
    nama_ibu: "Nama Ibu",
    pekerjaan_ibu: "Pekerjaan Ibu",
    tempat_lahir_ibu: "Tempat Lahir Ibu",
    tanggal_lahir_ibu: "Tanggal Lahir Ibu",
    ktp_ibu: "No. KTP Ibu",
    telepon_ibu: "No. Telepon Ibu",
    // Wali
    nama_wali: "Nama Wali",
    tempat_lahir_wali: "Tempat Lahir Wali",
    tanggal_lahir_wali: "Tanggal Lahir Wali",
    ktp_wali: "No. KTP Wali",
    no_tlp_wali: "No. Telepon Wali",
    pekerjaan_wali: "Pekerjaan Wali",
  };
  
  let totalItems = 0;
  
  function createSummaryItem(container, key, value) {
    // Tanggal pendaftaran tidak ditampilkan di sini, karena sudah ada di from.html
    if (key === "tgl_daftar" || key === "tipe_pengisi") return;

    if (!value || value.trim() === "") return;
    const item = document.createElement("div");
    item.className = "summary-item";
    item.style.animationDelay = `${totalItems++ * 0.05}s`;
    item.innerHTML = `<label>${fieldLabels[key] || key}</label><span>${value}</span>`;
    container.appendChild(item);
  }
  
  // === Tampilkan data siswa ===
  const siswaFields = [
    "nama",
    "tempat_lahir",
    "tanggal_lahir",
    "anak_ke",
    "jk",
    "alamat",
    "telepon",
    "asal_sekolah",
    "nisn",
    "hobby",
    "citacita",
    "ukuran_baju",
  ];
  siswaFields.forEach((key) => createSummaryItem(siswaSummary, key, data[key]));
  
  // === Tampilkan JURUSAN di bagian siswa ===
  createSummaryItem(siswaSummary, "jurusan1", data.jurusan1);
  if (data.jurusan2) createSummaryItem(siswaSummary, "jurusan2", data.jurusan2);
  
  // === Tampilkan ortu atau wali ===
  const tipe = data.tipe_pengisi;
  if (tipe === "ortu") {
    const ortuFields = [
      "no_kk",
      "nama_ayah",
      "pekerjaan_ayah",
      "tempat_lahir_ayah",
      "tanggal_lahir_ayah",
      "ktp_ayah",
      "telepon_ayah",
      "nama_ibu",
      "pekerjaan_ibu",
      "tempat_lahir_ibu",
      "tanggal_lahir_ibu",
      "ktp_ibu",
      "telepon_ibu",
    ];
    ortuFields.forEach((key) => createSummaryItem(ortuSummary, key, data[key]));
    // Kosongkan bagian Wali jika pengisi adalah ortu
    waliSection.style.display = "none";

  } else if (tipe === "wali") {
    waliSection.style.display = "block";
    const waliFields = [
      "nama_wali",
      "tempat_lahir_wali",
      "tanggal_lahir_wali",
      "ktp_wali",
      "no_tlp_wali",
      "pekerjaan_wali",
    ];
    waliFields.forEach((key) => createSummaryItem(waliSummary, key, data[key]));
    // Kosongkan bagian Ortu jika pengisi adalah wali
    ortuSummary.innerHTML = 'Data tidak diisi karena pengisi adalah Wali.';
  }
  
  // Animasi tombol
  document.querySelector(".action-buttons").style.animationDelay = `${totalItems * 0.05 + 0.2}s`;
  
  // Tombol Edit
  document.getElementById("editBtn").onclick = () => {
    // Kembali ke form.html. Data akan dimuat ulang dari localStorage
    window.location.href = "from.html";
  };
  
  // Tombol Konfirmasi - KIRIM DATA JSON KE BACKEND
  document.getElementById("confirmBtn").onclick = async () => {
    const confirmBtn = document.getElementById("confirmBtn");
    
    // Disable button dan ubah teks
    confirmBtn.disabled = true;
    confirmBtn.textContent = "Mengirim data...";
    
    try {
      // Kirim data yang ada di objek 'data' ke backend sebagai JSON
      const response = await fetch('prosesfrom.php', {
        method: 'POST',
        headers: {
          // Ganti Content-Type menjadi application/json
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
      });
      
      const result = await response.json();
      
      if (result.success) {
        // Simpan ID pendaftaran ke localStorage untuk halaman selesai
        localStorage.setItem('id_pendaftaran', result.id_pendaftaran);
        
        // Hapus data form dari localStorage setelah berhasil disimpan ke DB
        localStorage.removeItem("ppdbFormData");
        
        // Redirect ke halaman selesai (asumsi Anda punya selesai.html)
        window.location.href = "selesai.html";
      } else {
        // Tampilkan error
        alert('Gagal menyimpan data: ' + result.message);
        confirmBtn.disabled = false;
        confirmBtn.textContent = "Konfirmasi & Selesai";
      }
    } catch (error) {
      // Tampilkan error jika request gagal
      alert('Terjadi kesalahan saat mengirim data: ' + error.message);
      confirmBtn.disabled = false;
      confirmBtn.textContent = "Konfirmasi & Selesai";
    }
  };
});