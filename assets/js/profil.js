document.addEventListener('DOMContentLoaded', () => {
  if (typeof jQuery !== 'undefined' && $.fn.DataTable) {
    $(document).ready(function () {

      function initializeDataTable(tableId, pageLength) {
        // Hancurkan instance DataTable sebelumnya jika ada
        if ($.fn.DataTable.isDataTable(tableId)) {
          $(tableId).DataTable().destroy();
        }

        // Inisialisasi DataTables
        const tableInstance = $(tableId).DataTable({
          autoWidth: false,
          pageLength: pageLength,
          responsive: false,
          scrollX: false, 
          language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ entri",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            paginate: {
              previous: "Sebelumnya",
              next: "Selanjutnya"
            },
            zeroRecords: "Tidak ada data yang ditemukan",
            emptyTable: "Tidak ada data dalam tabel"
          },
          columnDefs: [
            { targets: '_all', className: 'text-center' }
          ],
          // Aktifkan "Show entries" dropdown
          dom: '<"row"<"col-sm-12 col-md-6"><"col-sm-12 col-md-6"f>>' +
            '<"row"<"col-sm-12"tr>>' +
            '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
        });

        // Warna teks pada filter dan info
        $(tableId + '_filter label, ' + tableId + '_info').css('color', 'black');

        return tableInstance;
      }

      // Hanya inisialisasi jika elemen ada
      if ($('#data-profil').length) {
        initializeDataTable('#data-profil', 6);
      }

    });
  } else {
    console.warn('jQuery atau DataTables tidak tersedia. Pastikan semua file JS sudah dimuat.');
  }
});
