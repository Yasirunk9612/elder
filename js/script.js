// =============================================
// Elder Care Management - Enhanced UI Scripts
// =============================================

// Existing Validation (Enhanced with inline feedback)
function validateRegisterForm() {
    const form = document.forms["registerForm"];  
    let name = form["name"].value.trim();
    let email = form["email"].value.trim();
    let password = form["password"].value;
    let role = form["role"].value;
    let valid = true;

    clearFieldErrors(form);
    if (!name) { markFieldInvalid(form["name"], "Name is required"); valid = false; }
    if (!email) { markFieldInvalid(form["email"], "Email is required"); valid = false; }
    if (password.length < 6) { markFieldInvalid(form["password"], "At least 6 characters"); valid = false; }
    if (!role) { markFieldInvalid(form["role"], "Choose a role"); valid = false; }

    if (!valid) showToast("Please correct highlighted fields", "error");
    return valid;
}

function validateLoginForm() {
    const form = document.forms["loginForm"];  
    let email = form["email"].value.trim();
    let password = form["password"].value;
    clearFieldErrors(form);
    let valid = true;
    if (!email) { markFieldInvalid(form["email"], "Email required"); valid = false; }
    if (!password) { markFieldInvalid(form["password"], "Password required"); valid = false; }
    if (!valid) showToast("Please fill required fields", "error");
    return valid;
}

// Toggle password visibility
function togglePassword(id) {
    const field = document.getElementById(id);
    if (!field) return;
    field.type = field.type === "password" ? "text" : "password";
}

// Inline field error helpers
function markFieldInvalid(input, msg) {
    if (!input) return; 
    input.classList.add('error');
    let hint = document.createElement('div');
    hint.className = 'field-hint';
    hint.style.color = 'var(--color-danger)';
    hint.textContent = msg;
    input.parentElement?.appendChild(hint);
}
function clearFieldErrors(form) {
    [...form.querySelectorAll('.error')].forEach(el => el.classList.remove('error'));
    [...form.querySelectorAll('.field-hint')].forEach(h => h.remove());
}

// Password Strength Meter
function initPasswordStrength(selector, meterSelector) {
    const input = document.querySelector(selector);
    const meter = document.querySelector(meterSelector);
    if (!input || !meter) return;
    const bar = document.createElement('span');
    meter.appendChild(bar);
    input.addEventListener('input', () => {
        const score = passwordStrengthScore(input.value);
        bar.style.width = (score * 25) + '%';
    });
}
function passwordStrengthScore(pw) {
    if (!pw) return 0;
    let score = 0;
    if (pw.length >= 6) score++;
    if (/[A-Z]/.test(pw)) score++;
    if (/[0-9]/.test(pw)) score++;
    if (/[^A-Za-z0-9]/.test(pw)) score++;
    return score; // 0 - 4
}


// Table Filter (client-side)
function initTableFilter() {
    document.querySelectorAll('[data-table-filter]')
        .forEach(input => {
            input.addEventListener('input', () => {
                const value = input.value.toLowerCase();
                const tableId = input.getAttribute('data-target');
                const table = document.getElementById(tableId);
                if (!table) return;
                table.querySelectorAll('tbody tr').forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(value) ? '' : 'none';
                });
            });
        });
}

// Modal Confirm (replaces native confirm for deletes)
function initConfirmModal() {
    const backdrop = document.getElementById('confirm-backdrop');
    if (!backdrop) return;
    const msgEl = backdrop.querySelector('[data-confirm-message]');
    const yesBtn = backdrop.querySelector('[data-confirm-yes]');
    const noBtn = backdrop.querySelector('[data-confirm-no]');
    let confirmResolve = null;

    function open(message) {
        msgEl.textContent = message;
        backdrop.classList.add('active');
        return new Promise(res => { confirmResolve = res; });
    }
    function close() { backdrop.classList.remove('active'); }
    yesBtn.addEventListener('click', () => { close(); confirmResolve && confirmResolve(true); });
    noBtn.addEventListener('click', () => { close(); confirmResolve && confirmResolve(false); });
    backdrop.addEventListener('click', e => { if (e.target === backdrop) { close(); confirmResolve && confirmResolve(false); } });

    window.customConfirm = open; // expose globally
}

// Toast Notifications
function ensureToastContainer() {
    let c = document.querySelector('.toast-container');
    if (!c) {
        c = document.createElement('div');
        c.className = 'toast-container';
        document.body.appendChild(c);
    }
    return c;
}
function showToast(message, type='info', timeout=3500) {
    const c = ensureToastContainer();
    const el = document.createElement('div');
    el.className = `toast ${type}`;
    el.textContent = message;
    c.appendChild(el);
    setTimeout(() => {
        el.style.opacity = '0';
        el.style.transform = 'translateX(35px)';
        setTimeout(()=> el.remove(), 400);
    }, timeout);
}

// Enhance delete links with custom confirm if present
function interceptDeleteLinks() {
    document.querySelectorAll('a[data-delete="true"]').forEach(a => {
        a.addEventListener('click', e => {
            if (window.customConfirm) {
                e.preventDefault();
                const url = a.getAttribute('href');
                customConfirm('Are you sure you want to delete this record?').then(ok => { if (ok) window.location = url; });
            }
        });
    });
}

// DOM Ready Setup
document.addEventListener('DOMContentLoaded', () => {
    initPasswordStrength('#regPass', '.password-meter');
    initTableFilter();
    initConfirmModal();
    interceptDeleteLinks();
    initDoctorEditModal();
});

// Accessibility: close modals with Escape
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        const backdrop = document.getElementById('confirm-backdrop');
        if (backdrop && backdrop.classList.contains('active')) backdrop.classList.remove('active');
        const editBackdrop = document.getElementById('edit-backdrop');
        if (editBackdrop && editBackdrop.classList.contains('active')) editBackdrop.classList.remove('active');
    }
});

// Doctor Edit Modal logic
function initDoctorEditModal() {
    const editBackdrop = document.getElementById('edit-backdrop');
    if (!editBackdrop) return;
    const form = document.getElementById('edit-form');
    const cancelBtn = form.querySelector('[data-cancel]');
    const idEl = document.getElementById('edit_id');
    const userEl = document.getElementById('edit_user_id');
    const typeEl = document.getElementById('edit_health_type');
    const valEl = document.getElementById('edit_value');
    const notesEl = document.getElementById('edit_notes');

    function openWith(btn) {
        idEl.value = btn.getAttribute('data-id') || '';
        const userId = btn.getAttribute('data-user_id') || '';
        if (userId) userEl.value = userId;
        typeEl.value = btn.getAttribute('data-health_type') || '';
        valEl.value = btn.getAttribute('data-value') || '';
        notesEl.value = btn.getAttribute('data-notes') || '';
        editBackdrop.classList.add('active');
    }

    // Direct listeners if buttons exist at load
    document.querySelectorAll('button[data-edit="1"]').forEach(btn => {
        btn.addEventListener('click', () => openWith(btn));
    });

    // Delegated listener to be safe
    document.addEventListener('click', (e) => {
        const target = e.target.closest('button[data-edit="1"]');
        if (target) {
            e.preventDefault();
            openWith(target);
        }
    });

    cancelBtn.addEventListener('click', () => editBackdrop.classList.remove('active'));
    editBackdrop.addEventListener('click', (e) => { if (e.target === editBackdrop) editBackdrop.classList.remove('active'); });
}

