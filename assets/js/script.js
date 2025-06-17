document.addEventListener('DOMContentLoaded', () => {
  // ===== ALERT LOGIN BERHASIL =====
  (function () {
    const params = new URLSearchParams(window.location.search);
    const loginStatus = params.get('login');
    if (loginStatus === 'success') {
      alert('Login berhasil! Selamat datang!');
      window.history.replaceState({}, document.title, window.location.pathname);
    }
  })();

  // ===== ELEMENT UTAMA =====
  const hamburger = document.querySelector('.hamburger-icon');
  const sidebar = document.querySelector('.sidebar');
  const main = document.querySelector('.main');
  const overlay = document.querySelector('.overlay');

  if (!sidebar || !main || !overlay || !hamburger) {
    console.warn('Elemen penting hilang.');
    return;
  }

  const isOpen = () => !sidebar.classList.contains('closed');
  const closeSidebar = () => {
    sidebar.classList.add('closed');
    main.classList.add('sidebar-closed');
    overlay.classList.remove('active');
  };
  const openSidebar = () => {
    sidebar.classList.remove('closed');
    main.classList.remove('sidebar-closed');
    overlay.classList.add('active');
  };
  const toggleSidebar = () => (isOpen() ? closeSidebar() : openSidebar());

  hamburger.addEventListener('click', toggleSidebar);

  document.addEventListener('click', (e) => {
    const clickOutsideSidebar =
      !sidebar.contains(e.target) &&
      !hamburger.contains(e.target) &&
      !overlay.contains(e.target) &&
      !e.target.closest('.tahfidz-dropdown');

    if (isOpen() && clickOutsideSidebar) {
      closeSidebar();
    }
  });

  overlay.addEventListener('click', closeSidebar);

  // ===== RESPONSIVE SIDEBAR =====
  function handleResponsiveSidebar() {
    window.innerWidth <= 1024 ? closeSidebar() : openSidebar();
  }
  handleResponsiveSidebar();
  window.addEventListener('resize', handleResponsiveSidebar);

  // ===== DROPDOWN SIDEBAR =====
  document.querySelectorAll('.tahfidz-toggle-dropdown').forEach(toggle => {
    toggle.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      const parent = toggle.closest('.tahfidz-dropdown');
      document.querySelectorAll('.tahfidz-dropdown.active').forEach(d => {
        if (d !== parent) d.classList.remove('active');
      });
      parent.classList.toggle('active');
    });
  });

  // ===== DATATABLES & ICON SEARCH =====
  if (typeof jQuery !== 'undefined' && $.fn.DataTable) {
    $(document).ready(function () {
      $('#data-santri').DataTable({
        autoWidth: false,
        pageLength: 8,
        language: {
          paginate: {
            previous: "Sebelum",
            next: "Selanjutnya"
          }
        },
        columnDefs: [
          { targets: '_all', className: 'text-center' }
        ]
      });

      // Ganti warna teks filter dan info
      $('#data-santri_filter label, #data-santri_info').css('color', 'black');
    });
  } else {
    console.warn('jQuery/DataTables tidak ditemukan.');
  }

});
