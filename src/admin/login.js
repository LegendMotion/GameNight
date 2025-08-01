import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.css';
import { showToast } from '../components/Toast.js';

document.getElementById('login-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);
  const resp = await fetch('/api/auth.php', { method: 'POST', body: formData });
  if (resp.ok) {
    showToast({ icon: 'success', title: 'Innlogging vellykket' });
    window.location.href = 'articles/index.php';
  } else {
    Swal.fire({
      icon: 'error',
      title: 'Feil e-post eller passord'
    });
  }
});
