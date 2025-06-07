// ===== ALERT LOGIN BERHASIL =====
(function () {
  const params = new URLSearchParams(window.location.search);
  const loginStatus = params.get('login');

  if (loginStatus === 'success') {
    alert('Login berhasil! Selamat datang!');
    // Hapus query parameter supaya alert gak muncul lagi saat reload
    window.history.replaceState({}, document.title, window.location.pathname);
  }
})();

// ===== DROPDOWN TOGGLE =====
document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
  toggle.addEventListener('click', function (e) {
    e.preventDefault();
    const parent = this.parentElement;
    parent.classList.toggle('active');
  });
});

// ===== ELEMENT =====
const hamburger = document.querySelector('.hamburger-icon');
const sidebar = document.querySelector('.sidebar');
const main = document.querySelector('.main');
const overlay = document.querySelector('.overlay');

// ===== SIDEBAR TOGGLE MANUAL (HAMBURGER) =====
hamburger.addEventListener('click', () => {
  const isSidebarClosed = sidebar.classList.contains('closed');

  sidebar.classList.toggle('closed');
  main.classList.toggle('sidebar-closed');

  if (isSidebarClosed) {
    overlay.style.display = 'block';
  } else {
    overlay.style.display = 'none';
  }
});

// ===== CLICK DI LUAR UNTUK MENUTUP SIDEBAR (SEMUA LAYAR) =====
document.addEventListener('click', (e) => {
  const isClickOutsideSidebar = !sidebar.contains(e.target);
  const isClickOutsideHamburger = !hamburger.contains(e.target);
  const isClickOutsideOverlay = !overlay.contains(e.target);
  const isSidebarOpen = !sidebar.classList.contains('closed');

  if (isSidebarOpen && isClickOutsideSidebar && isClickOutsideHamburger && isClickOutsideOverlay) {
    sidebar.classList.add('closed');
    main.classList.add('sidebar-closed');
    overlay.style.display = 'none';
  }
});

// ===== OVERLAY CLICK CLOSE SIDEBAR =====
overlay.addEventListener('click', () => {
  sidebar.classList.add('closed');
  main.classList.add('sidebar-closed');
  overlay.style.display = 'none';
});

// ===== RESPONSIVE AUTO-HIDE SIDEBAR ON LOAD/RESIZE =====
function handleResponsiveSidebar() {
  if (window.innerWidth <= 768) {
    sidebar.classList.add('closed');
    main.classList.add('sidebar-closed');
    overlay.style.display = 'none';
  } else {
    sidebar.classList.remove('closed');
    main.classList.remove('sidebar-closed');
    overlay.style.display = 'none';
  }
}

handleResponsiveSidebar();
window.addEventListener('resize', handleResponsiveSidebar);

$(document).ready(function () {
    $('#data-santri').DataTable();
});

