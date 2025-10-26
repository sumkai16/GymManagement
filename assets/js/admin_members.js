// Admin Members Page JavaScript
// Handles member management functionality including filters, modals, and form validation

// ===========================================
// FILTER AND SORTING FUNCTIONALITY
// ===========================================

function applyFilters() {
    const filterStatus = document.getElementById('filter_status').value;
    const filterMembership = document.getElementById('filter_membership').value;
    const sortBy = document.getElementById('sort_by').value;
    const sortOrder = document.getElementById('sort_order').value;

    const params = new URLSearchParams(window.location.search);
    params.set('filter_status', filterStatus);
    params.set('filter_membership', filterMembership);
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

function openAddMemberModal() {
    document.getElementById('addMemberModal').style.display = 'block';
    // Reset to default state
    document.querySelector('input[name="user_option"][value="new"]').checked = true;
    toggleUserFields();
    clearValidationErrors('addMemberModal');
}

function openEditMemberModal(memberId, fullName, email, phone, address, membershipType, startDate, endDate, status) {
    document.getElementById('edit_member_id').value = memberId;
    document.getElementById('edit_full_name').value = fullName;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_phone').value = phone;
    document.getElementById('edit_address').value = address;
    document.getElementById('edit_membership_type').value = membershipType;
    document.getElementById('edit_start_date').value = startDate;
    document.getElementById('edit_end_date').value = endDate;
    document.getElementById('edit_status').value = status;
    document.getElementById('editMemberModal').style.display = 'block';
    clearValidationErrors('editMemberModal');
}

function openDeleteMemberModal(memberId, memberName) {
    document.getElementById('delete_member_id').value = memberId;
    document.getElementById('delete_member_name').textContent = memberName;
    document.getElementById('deleteMemberModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    clearValidationErrors(modalId);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modals = ['addMemberModal', 'editMemberModal', 'deleteMemberModal'];
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
    const addForm = document.querySelector('#addMemberModal form');
    const editForm = document.querySelector('#editMemberModal form');

    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            if (!validateAddMemberForm(this)) {
                e.preventDefault();
            }
        });
    }

    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            if (!validateEditMemberForm(this)) {
                e.preventDefault();
            }
        });
    }
});
