// Admin Members Validation JavaScript
// Contains all validation functions for member forms

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

function validateDate(dateString) {
    const date = new Date(dateString);
    return date instanceof Date && !isNaN(date);
}

function validateAddMemberForm(form) {
    let isValid = true;
    const errors = [];

    const userOption = form.querySelector('input[name="user_option"]:checked').value;
    const fullName = form.querySelector('#add_full_name').value.trim();
    const email = form.querySelector('#add_email').value.trim();
    const phone = form.querySelector('#add_phone').value.trim();
    const membershipType = form.querySelector('#add_membership_type').value;
    const startDate = form.querySelector('#add_start_date').value;
    const endDate = form.querySelector('#add_end_date').value;
    const status = form.querySelector('#add_status').value;

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

    // Membership Type validation
    if (!membershipType) {
        errors.push({ field: 'add_membership_type', message: 'Membership Type is required.' });
        isValid = false;
    }

    // Start Date validation
    if (!startDate) {
        errors.push({ field: 'add_start_date', message: 'Start Date is required.' });
        isValid = false;
    } else if (!validateDate(startDate)) {
        errors.push({ field: 'add_start_date', message: 'Please enter a valid start date.' });
        isValid = false;
    } else {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const start = new Date(startDate);
        if (start < today) {
            errors.push({ field: 'add_start_date', message: 'Start Date cannot be in the past.' });
            isValid = false;
        }
    }

    // End Date validation
    if (!endDate) {
        errors.push({ field: 'add_end_date', message: 'End Date is required.' });
        isValid = false;
    } else if (!validateDate(endDate)) {
        errors.push({ field: 'add_end_date', message: 'Please enter a valid end date.' });
        isValid = false;
    } else if (startDate && new Date(endDate) <= new Date(startDate)) {
        errors.push({ field: 'add_end_date', message: 'End Date must be after Start Date.' });
        isValid = false;
    }

    // Status validation
    if (!status) {
        errors.push({ field: 'add_status', message: 'Status is required.' });
        isValid = false;
    }

    displayValidationErrors('addMemberModal', errors);
    return isValid;
}

function validateEditMemberForm(form) {
    let isValid = true;
    const errors = [];

    const fullName = form.querySelector('#edit_full_name').value.trim();
    const email = form.querySelector('#edit_email').value.trim();
    const phone = form.querySelector('#edit_phone').value.trim();
    const membershipType = form.querySelector('#edit_membership_type').value;
    const startDate = form.querySelector('#edit_start_date').value;
    const endDate = form.querySelector('#edit_end_date').value;
    const status = form.querySelector('#edit_status').value;

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

    // Membership Type validation
    if (!membershipType) {
        errors.push({ field: 'edit_membership_type', message: 'Membership Type is required.' });
        isValid = false;
    }

    // Start Date validation
    if (!startDate) {
        errors.push({ field: 'edit_start_date', message: 'Start Date is required.' });
        isValid = false;
    } else if (!validateDate(startDate)) {
        errors.push({ field: 'edit_start_date', message: 'Please enter a valid start date.' });
        isValid = false;
    }

    // End Date validation
    if (!endDate) {
        errors.push({ field: 'edit_end_date', message: 'End Date is required.' });
        isValid = false;
    } else if (!validateDate(endDate)) {
        errors.push({ field: 'edit_end_date', message: 'Please enter a valid end date.' });
        isValid = false;
    } else if (new Date(endDate) <= new Date(startDate)) {
        errors.push({ field: 'edit_end_date', message: 'End Date must be after Start Date.' });
        isValid = false;
    }

    // Status validation
    if (!status) {
        errors.push({ field: 'edit_status', message: 'Status is required.' });
        isValid = false;
    }

    displayValidationErrors('editMemberModal', errors);
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
