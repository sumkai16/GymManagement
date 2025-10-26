// Admin Trainers Page JavaScript
// Handles trainer management functionality including filters, modals, and form validation

// ===========================================
// FILTER AND SORTING FUNCTIONALITY
// ===========================================

function applyFilters() {
    const filterSpecialty = document.getElementById('filter_specialty').value;
    const sortBy = document.getElementById('sort_by').value;
    const sortOrder = document.getElementById('sort_order').value;

    const params = new URLSearchParams(window.location.search);
    params.set('filter_specialty', filterSpecialty);
    params.set('sort_by', sortBy);
    params.set('sort_order', sortOrder);

    window.location.href = window.location.pathname + '?' + params.toString();
}

function clearFilters() {
    window.location.href = window.location.pathname;
}

// ===========================================
// MODAL MANAGEMENT
// ===========================================

function toggleUserFields() {
    const userOption = document.querySelector('input[name="user_option"]:checked').value;
    const newUserFields = document.getElementById('newUserFields');
    const existingUserFields = document.getElementById('existingUserFields');
    const usernameInput = document.getElementById('add_username');
    const passwordInput = document.getElementById('add_password');
    const existingUserSelect = document.getElementById('existing_user_id');

    if (userOption === 'new') {
        newUserFields.style.display = 'block';
        existingUserFields.style.display = 'none';
        usernameInput.required = true;
        passwordInput.required = true;
        existingUserSelect.required = false;
    } else {
        newUserFields.style.display = 'none';
        existingUserFields.style.display = 'block';
        usernameInput.required = false;
        passwordInput.required = false;
        existingUserSelect.required = true;
    }
}

function openAddTrainerModal() {
    document.getElementById('addTrainerModal').style.display = 'block';
    // Reset to default state
    document.querySelector('input[name="user_option"][value="new"]').checked = true;
    toggleUserFields();
    clearValidationErrors('addTrainerModal');
}

function openEditTrainerModal(trainerId, fullName, specialty, phone, email) {
    document.getElementById('edit_trainer_id').value = trainerId;
    document.getElementById('edit_full_name').value = fullName;
    document.getElementById('edit_specialty').value = specialty;
    document.getElementById('edit_phone').value = phone;
    document.getElementById('edit_email').value = email;
    document.getElementById('editTrainerModal').style.display = 'block';
    clearValidationErrors('editTrainerModal');
}

function openDeleteTrainerModal(trainerId, trainerName) {
    document.getElementById('delete_trainer_id').value = trainerId;
    document.getElementById('delete_trainer_name').textContent = trainerName;
    document.getElementById('deleteTrainerModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    clearValidationErrors(modalId);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modals = ['addTrainerModal', 'editTrainerModal', 'deleteTrainerModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        const modalContent = modal.querySelector('.modal-content');
        if (modal.style.display !== 'none' && modalContent && !modalContent.contains(event.target)) {
            closeModal(modalId);
        }
    });
}

// ===========================================
// FORM SUBMISSION HANDLERS
// ===========================================

document.addEventListener('DOMContentLoaded', function() {
    const addForm = document.querySelector('#addTrainerModal form');
    const editForm = document.querySelector('#editTrainerModal form');

    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            if (!validateAddTrainerForm(this)) {
                e.preventDefault();
            }
        });
    }

    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            if (!validateEditTrainerForm(this)) {
                e.preventDefault();
            }
        });
    }
});
