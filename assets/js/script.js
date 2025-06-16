document.addEventListener('DOMContentLoaded', () => {
  // ===== DROPDOWN TOGGLE =====
  const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
  dropdownToggles.forEach(toggle => {
    toggle.addEventListener('click', function (e) {
      e.preventDefault();
      const parent = this.closest('.dropdown');
      dropdownToggles.forEach(otherToggle => {
        if (otherToggle !== this) {
          const otherParent = otherToggle.closest('.dropdown');
          otherParent.classList.remove('active');
        }
      });
      parent.classList.toggle('active');
    });
  });

  // ===== SIDEBAR TOGGLE & OVERLAY =====
  const hamburger = document.querySelector('.hamburger-icon');
  const sidebar = document.querySelector('.sidebar');
  const main = document.querySelector('.main');
  const overlay = document.querySelector('.overlay');

  if (!sidebar || !main || !overlay) {
    console.warn('Elemen sidebar, main, atau overlay tidak ditemukan!');
    return;
  }

  function toggleOverlay() {
    const isSidebarClosed = sidebar.classList.contains('closed');
    overlay.style.display = isSidebarClosed ? 'none' : 'block';
  }

  if (hamburger) {
    hamburger.addEventListener('click', () => {
      sidebar.classList.toggle('closed');
      main.classList.toggle('sidebar-closed');
      toggleOverlay();
    });
  }

  document.addEventListener('click', (e) => {
    const clickInsideSidebar = sidebar.contains(e.target);
    const clickOnHamburger = hamburger && hamburger.contains(e.target);
    const clickOnOverlay = overlay.contains(e.target);
    const isSidebarOpen = !sidebar.classList.contains('closed');

    if (isSidebarOpen && !clickInsideSidebar && !clickOnHamburger && !clickOnOverlay) {
      sidebar.classList.add('closed');
      main.classList.add('sidebar-closed');
      toggleOverlay();
    }
  });

  overlay.addEventListener('click', () => {
    sidebar.classList.add('closed');
    main.classList.add('sidebar-closed');
    toggleOverlay();
  });

  function handleResponsiveSidebar() {
    if (window.innerWidth <= 768) {
      sidebar.classList.add('closed');
      main.classList.add('sidebar-closed');
    } else {
      sidebar.classList.remove('closed');
      main.classList.remove('sidebar-closed');
    }
    toggleOverlay();
  }

  handleResponsiveSidebar();
  window.addEventListener('resize', handleResponsiveSidebar);

  // ===== TAMPILKAN NAMA FILE PDF YANG DIPILIH =====
  const pdfFileInput = document.getElementById('pdfFile');
  const pdfFileName = document.getElementById('pdfFileName');

  if (pdfFileInput && pdfFileName) {
    pdfFileInput.addEventListener('change', () => {
      if (pdfFileInput.files.length > 0) {
        pdfFileName.textContent = "File yang dipilih: " + pdfFileInput.files[0].name;
      } else {
        pdfFileName.textContent = "";
      }
    });
  }


  // ===== AJAX: UPLOAD PROGRAM FILE =====
  const uploadForm = document.querySelector('form[action="upload-program.php"]');
  if (uploadForm) {
    uploadForm.addEventListener('submit', function (e) {
      e.preventDefault();
      const formData = new FormData(uploadForm);

      fetch('upload-program.php', {
        method: 'POST',
        body: formData
      })
        .then(res => res.text())
        .then(msg => {
          alert(msg);
          // Optional: bisa tambahkan refresh list atau reload halaman jika perlu
          location.reload(); // jika ingin langsung lihat hasil upload
        });
    });
  }

  // ===== AJAX: DELETE PROGRAM FILE =====
  document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      if (!confirm('Yakin ingin menghapus?')) return;

      const form = btn.closest('.delete-form');
      const formData = new FormData(form);

      fetch('delete-program.php', {
        method: 'POST',
        body: formData
      })
        .then(res => res.text())
        .then(msg => {
          alert(msg);
          location.reload(); // Reload list setelah hapus
        })
        .catch(err => {
          alert("Terjadi kesalahan saat menghapus file.");
          console.error(err);
        });
    });
  });


});
