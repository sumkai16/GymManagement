// Admin Trainers Validation JavaScript
// Contains all validation functions for trainer forms

// ===========================================
// VALIDATION FUNCTIONS
// ===========================================

function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePhone(phone) {
    if (!phone) return true; // Optional field
    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
    return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
}

// ===========================================
// FORM VALIDATION FUNCTIONS
// ===========================================

function validateAddTrainerForm(form) {
    let isValid = true;
    const errors = [];

    const userOption = form.querySelector('input[name="user_option"]:checked').value;
    const fullName = form.querySelector('#add_full_name').value.trim();
    const email = form.querySelector('#add_email').value.trim();
    const phone = form.querySelector('#add_phone').value.trim();

    // Full Name validation
    if (!fullName) {
        errors.push({ field: 'add_full_name', message: 'Full Name is required.' });
        isValid = false;
    }

    // Email validation
    if (!email) {
        errors.push({ field: 'add_email', message: 'Email is required.' });
        isValid = false;
    } else if (!validateEmail(email)) {
        errors.push({ field: 'add_email', message: 'Please enter a valid email address.' });
        isValid = false;
    }

    // Phone validation
    if (phone && !validatePhone(phone)) {
        errors.push({ field: 'add_phone', message: 'Please enter a valid phone number.' });
        isValid = false;
    }

    // User account validation
    if (userOption === 'new') {
        const username = form.querySelector('#add_username').value.trim();
        const password = form.querySelector('#add_password').value.trim();

        if (!username) {
            errors.push({ field: 'add_username', message: 'Username is required.' });
            isValid = false;
        } else if (username.length < 3) {
            errors.push({ field: 'add_username', message: 'Username must be at least 3 characters long.' });
            isValid = false;
        }

        if (!password) {
            errors.push({ field: 'add_password', message: 'Password is required.' });
            isValid = false;
        } else if (password.length < 6) {
            errors.push({ field: 'add_password', message: 'Password must be at least 6 characters long.' });
            isValid = false;
        }
    } else {
        const existingUserId = form.querySelector('#existing_user_id').value;
        if (!existingUserId) {
            errors.push({ field: 'existing_user_id', message: 'Please select an existing user.' });
            isValid = false;
        }
    }

    displayValidationErrors('addTrainerModal', errors);
    return isValid;
}

function validateEditTrainerForm(form) {
    let isValid = true;
    const errors = [];

    const fullName = form.querySelector('#edit_full_name').value.trim();
    const email = form.querySelector('#edit_email').value.trim();
    const phone = form.querySelector('#edit_phone').value.trim();

    // Full Name validation
    if (!fullName) {
        errors.push({ field: 'edit_full_name', message: 'Full Name is required.' });
        isValid = false;
    }

    // Email validation
    if (!email) {
        errors.push({ field: 'edit_email', message: 'Email is required.' });
        isValid = false;
    } else if (!validateEmail(email)) {
        errors.push({ field: 'edit_email', message: 'Please enter a valid email address.' });
        isValid = false;
    }

    // Phone validation
    if (phone && !validatePhone(phone)) {
        errors.push({ field: 'edit_phone', message: 'Please enter a valid phone number.' });
        isValid = false;
    }

    displayValidationErrors('editTrainerModal', errors);
    return isValid;
}

function displayValidationErrors(modalId, errors) {
    // Clear previous errors
    clearValidationErrors(modalId);

    errors.forEach(error => {
        const field = document.getElementById(error.field);
        if (field) {
            field.style.borderColor = '#ef4444';
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error';
            errorDiv.textContent = error.message;
            errorDiv.style.color = '#ef4444';
            errorDiv.style.fontSize = '0.875rem';
            errorDiv.style.marginTop = '0.25rem';
            field.parentNode.appendChild(errorDiv);
        }
    });
}

function clearValidationErrors(modalId) {
    const modal = document.getElementById(modalId);
    const errorElements = modal.querySelectorAll('.field-error');
    errorElements.forEach(el => el.remove());

    const inputs = modal.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.style.borderColor = '#e5e7eb';
    });
}
