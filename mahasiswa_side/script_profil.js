// Tunggu hingga seluruh konten HTML dimuat
document.addEventListener("DOMContentLoaded", () => {
  const elements = {
    // Tombol
    simpanBtn: document.getElementById("simpanPerubahan"),
    editBtn: document.getElementById("editProfil"),
    kembaliBtn: document.getElementById("kembaliEdit"),
    btnUnggah: document.getElementById("btnUnggah"),
    btnHapus: document.getElementById("btnHapus"),

    // Bagian formulir
    formSection: document.getElementById("formProfil"),
    previewSection: document.getElementById("previewProfil"),

    // Input formulir
    nama: document.getElementById("nama"),
    nim: document.getElementById("nim"),
    email: document.getElementById("email"),
    jurusan: document.getElementById("jurusan"),
    prodi: document.getElementById("prodi"),
    deskripsi: document.getElementById("deskripsi"),
    sma: document.getElementById("sma"),
    tahunLulus: document.getElementById("tahunLulus"),
    kemampuan: document.getElementById("kemampuan"),

    // Elemen preview
    prevNama: document.getElementById("prevNama"),
    prevNim: document.getElementById("prevNim"),
    prevEmail: document.getElementById("prevEmail"),
    prevJurusan: document.getElementById("prevJurusan"),
    prevProdi: document.getElementById("prevProdi"),
    prevDeskripsi: document.getElementById("prevDeskripsi"),
    prevSma: document.getElementById("prevSma"),
    prevTahunLulus: document.getElementById("prevTahunLulus"),
    prevKemampuan: document.getElementById("prevKemampuan"),

    // Upload foto
    uploadInput: document.getElementById("uploadFoto"),
    previewFoto: document.getElementById("previewFoto"),
  };

  // Cek apakah semua elemen penting ditemukan untuk debugging
  if (!elements.simpanBtn || !elements.formSection) {
    console.error(
      "Beberapa elemen penting tidak ditemukan! Periksa kembali ID di HTML Anda."
    );
    return; // Hentikan eksekusi jika elemen tidak ada
  }

  // DATA APLIKASI
  const dataProdi = {
    "Teknik Informatika": [
      "D3 Teknik Informatika",
      "D4 Rekayasa Keamanan Siber",
      "D4 Rekayasa Perangkat Lunak",
      "D4 Teknologi Rekayasa Multimedia",
      "D4 Teknologi Rekayasa Sistem Elektronika",
      "S2 Terapan Teknologi Rekayasa Perangkat Lunak",
    ],
    "Teknik Elektro": [
      "D3 Elektronika",
      "D4 Teknik Elektronika",
      "D4 Teknologi Rekayasa Internet of Things (IoT)",
      "D4 Teknologi Rekayasa Sistem Elektronika",
    ],
    "Teknik Mesin": [
      "D3 Teknik Mesin",
      "D4 Teknologi Rekayasa Manufaktur",
      "D4 Teknologi Rekayasa Mekatronika",
      "D4 Teknologi Rekayasa Pengelasan",
    ],
    "Manajemen dan Bisnis": [
      "D3 Akuntansi",
      "D3 Administrasi Bisnis",
      "D4 Akuntansi Manajerial",
      "D4 Logistik Bisnis",
      "D4 Manajemen Rekayasa",
    ],
  };

  function toggleSections(showForm) {
    if (showForm) {
      elements.formSection.classList.remove("d-none");
      elements.previewSection.classList.add("d-none");
    } else {
      elements.formSection.classList.add("d-none");
      elements.previewSection.classList.remove("d-none");
    }
  }

  function updatePreviewData() {
    elements.prevNama.textContent = elements.nama.value || "-";
    elements.prevNim.textContent = elements.nim.value || "-";
    elements.prevEmail.textContent = elements.email.value || "-";
    elements.prevJurusan.textContent = elements.jurusan.value || "-";
    elements.prevProdi.textContent = elements.prodi.value || "-";
    elements.prevDeskripsi.textContent = elements.deskripsi.value || "-";
    elements.prevSma.textContent = elements.sma.value || "-";
    elements.prevTahunLulus.textContent = elements.tahunLulus.value || "-";
    elements.prevKemampuan.textContent = elements.kemampuan.value || "-";
  }

  function populateProdiDropdown(selectedJurusan) {
    elements.prodi.innerHTML =
      '<option value="">-- Pilih Program Studi --</option>';
    if (dataProdi[selectedJurusan]) {
      dataProdi[selectedJurusan].forEach((prodi) => {
        const option = document.createElement("option");
        option.value = prodi;
        option.textContent = prodi;
        elements.prodi.appendChild(option);
      });
    }
  }

  function handlePhotoUpload(file) {
    if (file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        elements.previewFoto.src = e.target.result;
        elements.previewFoto.style.objectFit = "cover";
      };
      reader.readAsDataURL(file);
    }
  }

  function removePhoto() {
    elements.previewFoto.src = "";
    elements.uploadInput.value = "";
  }

  elements.jurusan.addEventListener("change", () =>
    populateProdiDropdown(elements.jurusan.value)
  );
  elements.simpanBtn.addEventListener("click", () => {
    updatePreviewData();
    toggleSections(false);
  });
  elements.editBtn.addEventListener("click", () => toggleSections(true));
  elements.kembaliBtn.addEventListener("click", () => toggleSections(true));
  elements.btnUnggah.addEventListener("click", () =>
    elements.uploadInput.click()
  );
  elements.uploadInput.addEventListener("change", () =>
    handlePhotoUpload(elements.uploadInput.files[0])
  );
  elements.btnHapus.addEventListener("click", removePhoto);
});
