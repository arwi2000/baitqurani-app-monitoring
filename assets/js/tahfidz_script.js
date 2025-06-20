$(document).ready(function() {
    // Event listener saat input NIS berubah (misalnya setelah mengetik)
    $('#tambah_nis').on('keyup', function() {
        var nis = $(this).val();
        
        // Hapus nilai sebelumnya
        $('#tambah_nama').val('');
        $('#tambah_kelas').val('');
        $('#tambah_jenis_kelamin').val('');

        // Set placeholder kembali ke default jika NIS kosong
        if (nis.length === 0) {
            $('#tambah_nama').attr('placeholder', 'Masukkan Nama');
            return; // Berhenti jika NIS kosong
        }

        // Dapatkan BASE_URL dari atribut data pada body atau elemen lain jika memungkinkan,
        // atau hardcode jika Anda yakin path-nya statis
        // Contoh ini mengasumsikan BASE_URL akan diatur di HTML (lihat perubahan tahfidz.php)
        var baseUrl = $('body').data('base-url'); 
        if (!baseUrl) {
            // Fallback jika data-base-url tidak ditemukan, sesuaikan path ini!
            baseUrl = '/bq-tahfidz'; // Contoh: ganti dengan BASE_URL di config.php Anda
        }

        $.ajax({
            url: baseUrl + '/admin/tahfidz/get_santri_data.php', // Path ke file AJAX
            type: 'GET',
            data: { nis: nis },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#tambah_nama').val(response.nama_lengkap);
                    $('#tambah_kelas').val(response.kelas);
                    $('#tambah_jenis_kelamin').val(response.jenis_kelamin);
                    $('#tambah_nama').attr('placeholder', 'Nama akan terisi otomatis'); // Kembalikan placeholder default
                } else {
                    // Jika NIS tidak ditemukan, kosongkan dan ubah placeholder
                    $('#tambah_nama').val('').attr('placeholder', 'NIS tidak ditemukan');
                    $('#tambah_kelas').val('');
                    $('#tambah_jenis_kelamin').val('');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + status + " - " + error);
                // alert("Terjadi kesalahan saat mengambil data."); // Opsional: Tampilkan pesan error
                $('#tambah_nama').val('').attr('placeholder', 'Error saat mengambil data');
                $('#tambah_kelas').val('');
                $('#tambah_jenis_kelamin').val('');
            }
        });
    });

    // Reset form saat modal ditutup
    $('#modalTambahTahfidz').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset(); // Reset semua input di form
        $('#tambah_nama').attr('placeholder', 'Masukkan Nama'); // Kembalikan placeholder default
        // Pastikan pilihan select kembali ke default "Pilih..."
        $('#tambah_kelas').val(''); 
        $('#tambah_jenis_kelamin').val(''); 
        // Jika ada select lain yang perlu direset ke pilihan pertama/default
        // $('#tambah_tahfidz').val(''); 
    });
});