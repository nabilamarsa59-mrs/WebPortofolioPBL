document.addEventListener('DOMContentLoaded', () => {
    // Data untuk dropdown Prodi (sama seperti di halaman profil)
    const dataProdi = {
        "Teknik Informatika": ["D3 Teknik Informatika", "D4 Rekayasa Keamanan Siber", "D4 Rekayasa Perangkat Lunak", "D4 Teknologi Rekayasa Multimedia", "D4 Teknologi Rekayasa Sistem Elektronika", "S2 Terapan Teknologi Rekayasa Perangkat Lunak"],
        "Teknik Elektro": ["D3 Elektronika", "D4 Teknik Elektronika", "D4 Teknologi Rekayasa Internet of Things (IoT)", "D4 Teknologi Rekayasa Sistem Elektronika"],
        "Teknik Mesin": ["D3 Teknik Mesin", "D4 Teknologi Rekayasa Manufaktur", "D4 Teknologi Rekayasa Mekatronika", "D4 Teknologi Rekayasa Pengelasan"],
        "Manajemen dan Bisnis": ["D3 Akuntansi", "D3 Administrasi Bisnis", "D4 Akuntansi Manajerial", "D4 Logistik Bisnis", "D4 Manajemen Rekayasa"]
    };

    // Elemen-elemen DOM
    const gradeModal = document.getElementById('gradeModal');
    const modalProjectTitle = document.getElementById('modalProjectTitle');
    const modalStudentName = document.getElementById('modalStudentName');
    const modalStudentNim = document.getElementById('modalStudentNim');
    const modalStudentJurusan = document.getElementById('modalStudentJurusan');
    const modalStudentProdi = document.getElementById('modalStudentProdi'); // Baru
    const modalProjectKategori = document.getElementById('modalProjectKategori'); // Baru
    const modalProjectStatus = document.getElementById('modalProjectStatus');
    const modalProjectDescription = document.getElementById('modalProjectDescription');
    const saveGradeBtn = document.getElementById('saveGradeBtn');

    const statusFilter = document.getElementById('statusFilter');
    const jurusanFilter = document.getElementById('jurusanFilter');
    const prodiFilter = document.getElementById('prodiFilter');
    const kategoriFilter = document.getElementById('kategoriFilter');
    const searchInput = document.getElementById('searchInput');
    const projectItems = document.querySelectorAll('.project-item');
    const projectCount = document.getElementById('projectCount');

    // --- Logika Filter & Pencarian ---
    function applyFilters() {
        const statusValue = statusFilter.value;
        const jurusanValue = jurusanFilter.value;
        const prodiValue = prodiFilter.value;
        const kategoriValue = kategoriFilter.value;
        const searchValue = searchInput.value.toLowerCase();

        let visibleCount = 0;

        projectItems.forEach(item => {
            const itemStatus = item.dataset.status;
            const itemJurusan = item.dataset.jurusan;
            const itemProdi = item.dataset.prodi;
            const itemKategori = item.dataset.kategori;
            const itemNim = item.dataset.nim;
            const itemStudentName = item.dataset.studentName.toLowerCase();

            let isVisible = true;

            // Cek setiap filter
            if (statusValue !== 'all' && itemStatus !== statusValue) isVisible = false;
            if (jurusanValue !== 'all' && itemJurusan !== jurusanValue) isVisible = false;
            if (prodiValue !== 'all' && itemProdi !== prodiValue) isVisible = false;
            if (kategoriValue !== 'all' && itemKategori !== kategoriValue) isVisible = false;
            
            // Cek pencarian (berdasarkan NIM atau Nama)
            if (searchValue && !itemNim.includes(searchValue) && !itemStudentName.includes(searchValue)) {
                isVisible = false;
            }

            // Tampilkan atau sembunyikan item
            item.style.display = isVisible ? 'block' : 'none';
            if (isVisible) visibleCount++;
        });

        projectCount.textContent = visibleCount;
    }

    // Event Listeners untuk filter dan pencarian
    statusFilter.addEventListener('change', applyFilters);
    jurusanFilter.addEventListener('change', () => {
        populateProdiDropdown();
        applyFilters();
    });
    prodiFilter.addEventListener('change', applyFilters);
    kategoriFilter.addEventListener('change', applyFilters);
    searchInput.addEventListener('input', applyFilters);

    // Fungsi untuk mengisi dropdown Prodi
    function populateProdiDropdown() {
        const selectedJurusan = jurusanFilter.value;
        prodiFilter.innerHTML = '<option value="all">Semua Prodi</option>';
        prodiFilter.disabled = (selectedJurusan === 'all');

        if (selectedJurusan !== 'all' && dataProdi[selectedJurusan]) {
            dataProdi[selectedJurusan].forEach(prodi => {
                const option = document.createElement('option');
                option.value = prodi;
                option.textContent = prodi;
                prodiFilter.appendChild(option);
            });
        }
    }

    // --- Logika Modal (Diperbarui) ---
    gradeModal.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;
        const title = button.dataset.title;
        const student = button.dataset.student;
        const nim = button.dataset.nim;
        const jurusan = button.dataset.jurusan;
        const prodi = button.dataset.prodi; // Ambil data baru
        const kategori = button.dataset.kategori; // Ambil data baru
        const description = button.dataset.description;

        const projectCard = button.closest('.project-item');
        const status = projectCard.dataset.status;

        modalProjectTitle.textContent = title;
        modalStudentName.textContent = student;
        modalStudentNim.textContent = nim;
        modalStudentJurusan.textContent = jurusan;
        modalStudentProdi.textContent = prodi; // Tampilkan data baru
        modalProjectKategori.textContent = kategori; // Tampilkan data baru
        modalProjectDescription.textContent = description;

        if (status === 'sudah-dinilai') {
            modalProjectStatus.textContent = 'Sudah Dinilai';
            modalProjectStatus.className = 'badge bg-success';
            document.getElementById('gradeSelect').disabled = true;
            document.getElementById('commentText').disabled = true;
            saveGradeBtn.style.display = 'none';
        } else {
            modalProjectStatus.textContent = 'Belum Dinilai';
            modalProjectStatus.className = 'badge bg-warning';
            document.getElementById('gradeSelect').disabled = false;
            document.getElementById('commentText').disabled = false;
            saveGradeBtn.style.display = 'inline-block';
        }
    });

    gradeModal.addEventListener('hidden.bs.modal', () => {
        document.getElementById('gradeForm').reset();
    });

    saveGradeBtn.addEventListener('click', () => {
        const grade = document.getElementById('gradeSelect').value;
        const comment = document.getElementById('commentText').value;
        if (!grade) { alert('Silakan pilih nilai terlebih dahulu.'); return; }
        
        console.log('Menyimpan Penilaian:', { project: modalProjectTitle.textContent, student: modalStudentName.textContent, grade, comment });
        alert(`Penilaian untuk proyek "${modalProjectTitle.textContent}" berhasil disimpan!`);
        
        const modalInstance = bootstrap.Modal.getInstance(gradeModal);
        modalInstance.hide();
        setTimeout(() => location.reload(), 1000); // Simulasi refresh
    });

    // Inisialisasi
    applyFilters();
});