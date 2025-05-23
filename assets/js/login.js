const error = new URLSearchParams(window.location.search).get('error');

if (error === 'invalid') {
  alert('Login gagal! Username atau password salah.');
} else if (error === 'empty') {
  alert('Harap isi username dan password.');
}
