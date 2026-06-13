function copyUrl(elementId, btn) {
  const text = document.getElementById(elementId)?.innerText?.trim();
  if (!text) return;

  const textarea = document.createElement('textarea');
  textarea.value = text;
  textarea.style.position = 'fixed';
  textarea.style.top      = '0';
  textarea.style.left     = '0';
  textarea.style.opacity  = '0';
  document.body.appendChild(textarea);
  textarea.focus();
  textarea.select();

  try {
    document.execCommand('copy');
    const original = btn.innerHTML;
    btn.innerHTML = '✅';
    btn.classList.add('copied');
    setTimeout(() => {
      btn.innerHTML = original;
      btn.classList.remove('copied');
    }, 2000);
  } catch (e) {
    alert('Copy failed. Please copy manually.');
  }

  document.body.removeChild(textarea);
}

// ─── Toggle Password Visibility ──────────────────────
function togglePassword(inputId, btn) {
  const input = document.getElementById(inputId);
  if (!input) return;
  if (input.type === 'password') {
    input.type   = 'text';
    btn.innerHTML = '🙈';
  } else {
    input.type   = 'password';
    btn.innerHTML = '👁';
  }
}

// ─── Modal Open / Close ───────────────────────────────
function openModal(id)  { document.getElementById(id)?.classList.add('open'); }
function closeModal(id) { document.getElementById(id)?.classList.remove('open'); }

// Close modal on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
  });
});

// ─── Delete Confirm (Dashboard) ──────────────────────
function confirmDelete(linkId) {
  const input = document.getElementById('deleteLinkId');
  if (input) input.value = linkId;
  openModal('deleteModal');
}

// ─── Register: Password Match Validation ─────────────
const passInput    = document.getElementById('password');
const confirmInput = document.getElementById('confirm_password');
const passError    = document.getElementById('pass-match-error');
const registerBtn  = document.getElementById('registerBtn');

if (confirmInput && passInput) {
  confirmInput.addEventListener('input', function () {
    if (this.value && this.value !== passInput.value) {
      passError.style.display = 'block';
      confirmInput.classList.add('error');
      if (registerBtn) registerBtn.disabled = true;
    } else {
      passError.style.display = 'none';
      confirmInput.classList.remove('error');
      if (registerBtn) registerBtn.disabled = false;
    }
  });
}

// ─── Form Submit: Loading State ───────────────────────
document.querySelectorAll('form').forEach(form => {
  form.addEventListener('submit', function () {
    const btn = this.querySelector('button[type="submit"]');
    if (btn && !btn.disabled) {
      btn.disabled  = true;
      btn.innerHTML = '<span class="spinner"></span> Processing...';
    }
  });
});

// ─── Auto-hide Alerts ─────────────────────────────────
document.querySelectorAll('.alert').forEach(alert => {
  setTimeout(() => {
    alert.style.transition = 'opacity 0.4s';
    alert.style.opacity    = '0';
    setTimeout(() => alert.remove(), 400);
  }, 4000);
});

// ─── Best Link Click Count (Dashboard) ───────────────
// ── BACKEND: This will be replaced with real data from PHP
// For now JS just finds the max number in the click-count cells
const clickCounts = document.querySelectorAll('.click-count');
const bestEl      = document.getElementById('best-link-clicks');
if (bestEl && clickCounts.length > 0) {
  const max = Math.max(...[...clickCounts].map(el => parseInt(el.innerText) || 0));
  bestEl.textContent = max;
}