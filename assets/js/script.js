document.addEventListener('DOMContentLoaded', () => {
  // Alert for successful login
  (function () {
    const params = new URLSearchParams(window.location.search);
    const loginStatus = params.get('login');
    if (loginStatus === 'success') {
      alert('Login berhasil! Selamat datang!');
      // Menghapus parameter 'login' dari URL tanpa me-reload halaman
      window.history.replaceState({}, document.title, window.location.pathname);
    }
  })();

  // Main UI elements for sidebar functionality
  const hamburger = document.querySelector('.hamburger-icon');
  const sidebar = document.querySelector('.sidebar');
  const main = document.querySelector('.main');
  const overlay = document.querySelector('.overlay');

  // Basic check to ensure essential UI elements exist before adding listeners
  if (!sidebar || !main || !overlay || !hamburger) {
    console.warn('One or more essential UI elements for sidebar are missing. Sidebar functionality may not work.');
    // Exit if essential elements are not found to prevent further errors
    return;
  }

  // Helper functions for sidebar state
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

  // Event listeners for sidebar
  hamburger.addEventListener('click', toggleSidebar);

  document.addEventListener('click', (e) => {
    const clickOutsideSidebar =
      !sidebar.contains(e.target) &&
      !hamburger.contains(e.target) &&
      // Check if click is on overlay itself, or an element not part of dropdown
      !overlay.contains(e.target) &&
      !e.target.closest('.tahfidz-dropdown'); // Exclude clicks inside dropdowns

    if (isOpen() && clickOutsideSidebar) {
      closeSidebar();
    }
  });

  overlay.addEventListener('click', closeSidebar);

  // Responsive sidebar behavior
  function handleResponsiveSidebar() {
    // Close sidebar on smaller screens, open on larger screens
    if (window.innerWidth <= 1024) {
      closeSidebar();
    } else {
      openSidebar();
    }
  }
  // Initialize on load
  handleResponsiveSidebar();
  // Re-evaluate on window resize
  window.addEventListener('resize', handleResponsiveSidebar);

  // Sidebar dropdown functionality for '.tahfidz-dropdown'
  document.querySelectorAll('.tahfidz-toggle-dropdown').forEach(toggle => {
    toggle.addEventListener('click', (e) => {
      e.preventDefault(); // Prevent default link behavior
      e.stopPropagation(); // Stop event bubbling to avoid closing sidebar immediately

      const parent = toggle.closest('.tahfidz-dropdown');

      // Close other open dropdowns
      document.querySelectorAll('.tahfidz-dropdown.active').forEach(d => {
        if (d !== parent) { // Don't close the current dropdown
          d.classList.remove('active');
        }
      });
      // Toggle current dropdown's active state
      parent.classList.toggle('active');
    });
  });

  // DataTables Initialization - using jQuery's $(document).ready()
  // This ensures jQuery and DataTables plugins are loaded
  if (typeof jQuery !== 'undefined' && $.fn.DataTable) {
    $(document).ready(function () {
      let dataTableGabungan; // Deklarasikan variabel di luar untuk bisa diakses oleh filter

      /**
       * Initializes DataTables for a given table ID.
       * Includes destruction before initialization to prevent reinitialization errors.
       * @param {string} tableId The ID of the HTML table (e.g., '#data-santri').
       * @param {number} pageLength The number of rows per page.
       */
      function initializeDataTable(tableId, pageLength) {
        // Check if DataTables is already initialized on this table
        if ($.fn.DataTable.isDataTable(tableId)) {
          $(tableId).DataTable().destroy(); // Destroy previous instance
        }

        // Initialize DataTables
        const tableInstance = $(tableId).DataTable({
          autoWidth: false,
          pageLength: pageLength,
          language: {
            search: "Cari:",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            paginate: {
              previous: "Sebelum",
              next: "Selanjutnya"
            },
            zeroRecords: "Tidak ada data yang ditemukan", // Tambah pesan jika data kosong
            emptyTable: "Tidak ada data dalam tabel" // Tambah pesan jika tabel kosong
          },
          columnDefs: [
            { targets: '_all', className: 'text-center' } // Rata tengah semua kolom
          ],
          // Custom DOM layout for DataTables (search on right, info on left, pagination on right)
          // 'l' (length changing input) has been removed to hide "Show X entries"
          dom: '<"row"<"col-sm-12 col-md-6"><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
        });

        // Set text color for DataTables filter and info elements
        $(tableId + '_filter label, ' + tableId + '_info').css('color', 'black');

        return tableInstance; // Mengembalikan instance DataTables
      }

      // Initialize each table present in the application
      // Pastikan Anda hanya menginisialisasi tabel yang ada di halaman saat ini
      // If #data-santri, #data-program, #data-tahfidz are not on this specific page,
      // these lines might cause warnings but won't break anything.
      // You might want to wrap them in checks like `if ($('#data-santri').length) { ... }`
      initializeDataTable('#data-santri', 10);
      initializeDataTable('#data-program', 6);
      initializeDataTable('#data-tahfidz', 10);

      // Inisialisasi DataTables untuk tabel gabungan (rekap)
      dataTableGabungan = initializeDataTable('#data-gabungan', 6);

      // ============================================================================================================
      // Custom Filters for #data-gabungan table
      // ============================================================================================================

      // Fungsi untuk menerapkan semua filter
      function applyFilters() {
        const filterKelas = $('#filterKelas').val();
        const filterJenisKelamin = $('#filterJenisKelamin').val();
        const filterTanggal = $('#filterTanggal').val();
        const filterJenisTahfidz = $('#filterJenisTahfidz').val();

        // Clear existing custom filters
        // Ini penting agar filter tidak menumpuk setiap kali applyFilters dipanggil
        $.fn.dataTable.ext.search = [];

        // Filter by Kelas (Kolom ke-4, indeks 3)
        if (filterKelas) {
          $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            // Hanya terapkan filter ini untuk tabel dengan ID 'data-gabungan'
            if (settings.nTable.id !== 'data-gabungan') {
              return true; // Lewati jika bukan tabel yang ditarget
            }
            return data[3] === filterKelas; // Kolom "Kelas"
          });
        }

        // Filter by Jenis Kelamin (Kolom ke-5, indeks 4)
        if (filterJenisKelamin) {
          $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            if (settings.nTable.id !== 'data-gabungan') {
              return true;
            }
            return data[4] === filterJenisKelamin; // Kolom "Jenis Kelamin"
          });
        }

        // Filter by Jenis Tahfidz (Kolom ke-7, indeks 6)
        if (filterJenisTahfidz) {
          $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            if (settings.nTable.id !== 'data-gabungan') {
              return true;
            }
            return data[6] === filterJenisTahfidz; // Kolom "Jenis Tahfidz"
          });
        }

        // Filter by Tanggal (Kolom ke-6, indeks 5)
        // Pastikan format tanggal di tabel cocok dengan input type="date" (YYYY-MM-DD)
        if (filterTanggal) {
          $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            if (settings.nTable.id !== 'data-gabungan') {
              return true;
            }
            const tableDate = data[5]; // Tanggal dari kolom tabel "Tanggal Tahfidz"
            // Jika tanggal di tabel "Belum Ada", jangan difilter
            if (tableDate === 'Belum Ada') {
              return false;
            }
            return tableDate === filterTanggal;
          });
        }

        // Redraw the table to apply filters
        dataTableGabungan.draw();
      }

      // Event listener untuk perubahan pada dropdown filter
      // Hanya attach jika elemen filter ada di DOM
      $('#filterKelas, #filterJenisKelamin, #filterJenisTahfidz, #filterTanggal').on('change', function () {
        applyFilters();
      });


      // Event listener untuk tombol reset
      $('#resetFilters').on('click', function () {
        $('#filterKelas').val('');
        $('#filterJenisKelamin').val('');
        $('#filterJenisTahfidz').val('');
        $('#filterTanggal').val(''); // Reset input tanggal
        applyFilters(); // Apply filters again to clear them
      });

      // PENTING: Panggil applyFilters() sekali saat halaman dimuat
      // untuk memastikan filter awal (kosong) diterapkan dan DataTables berfungsi.
      applyFilters();


      // ============================================================================================================
      // Modal Form Reset Functionality (unchanged from previous versions)
      // ============================================================================================================

      /**
       * Helper function to reset all input, select, checkbox, and radio elements
       * within a given modal element. Typically used for "Add" modals to ensure a clean state.
       * @param {HTMLElement} modalElement The modal DOM element to reset.
       */
      function resetModalInputs(modalElement) {
        // Select common input types
        const inputs = modalElement.querySelectorAll('input[type="text"], input[type="number"], textarea, input[type="date"], input[type="email"], input[type="password"]');
        const selects = modalElement.querySelectorAll('select');
        const checkboxes = modalElement.querySelectorAll('input[type="checkbox"]');
        const radios = modalElement.querySelectorAll('input[type="radio"]');

        // Reset text/number/textarea/date/email/password inputs
        inputs.forEach(input => {
          input.value = '';
        });

        // Reset select elements
        selects.forEach(select => {
          // Try to find a default empty option or reset to first option
          const defaultOption = Array.from(select.options).find(option => option.value === '');
          if (defaultOption) {
            select.value = ''; // Set to empty string if default empty option exists
          } else if (select.options.length > 0) {
            select.selectedIndex = 0; // Otherwise, reset to the first option
          }
        });

        // Uncheck checkboxes
        checkboxes.forEach(checkbox => {
          checkbox.checked = false;
        });

        // Uncheck radio buttons
        radios.forEach(radio => {
          radio.checked = false;
        });
      }

      // Attach event listeners to reset "Tambah" modals when they are hidden
      const allModalTambah = document.querySelectorAll('[id^="modalTambah"]');
      if (allModalTambah.length > 0) {
        allModalTambah.forEach(modal => {
          modal.addEventListener('hidden.bs.modal', () => {
            resetModalInputs(modal);
            console.log(`All fields in "Tambah" modal (ID: ${modal.id}) reset.`);
          });
        });
      } else {
        console.info('No modals with ID starting with "modalTambah" found. Reset functionality for add forms will not apply.');
      }

      // Attach event listeners to reset "Ubah" modals' forms when they are hidden
      // Note: form.reset() resets to initial values, which might not be empty for edit forms.
      const allModalUbah = document.querySelectorAll('[id^="modalUbah"]');
      if (allModalUbah.length > 0) {
        allModalUbah.forEach(modal => {
          modal.addEventListener('hidden.bs.modal', () => {
            const formInsideModal = modal.querySelector('form');
            if (formInsideModal) {
              formInsideModal.reset(); // Resets form fields to their initial values (e.g., loaded from DB)
              console.log(`"Ubah" form (ID: ${modal.id}) reset to original data values.`);
            } else {
              console.warn(`Form element not found inside "Ubah" modal (ID: ${modal.id}). Reset functionality skipped.`);
            }
          });
        });
      } else {
        console.info('No modals with ID starting with "modalUbah" found. Reset functionality for edit forms will not apply.');
      }
    }); // End of $(document).ready(function() { ... });
  } else {
    console.warn('jQuery or DataTables library not found. DataTables features will not be available.');
  }
}); // End of DOMContentLoaded