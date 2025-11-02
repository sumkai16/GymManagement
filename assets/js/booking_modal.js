// Booking Modal Logic (formerly in coaches.php)

// Show booking modal
function showBookingModal(trainerId, trainerName, trainerSpecialty, avatarHTML, errorMessage = null) {
    const modalId = 'booking-modal-' + trainerId;
    const minDate = new Date().toISOString().split('T')[0];

    // Remove existing modal if any
    const existingModal = document.getElementById(modalId);
    if (existingModal) {
        existingModal.remove();
    }

    // Build message HTML if error exists
    let messageHTML = '';
    if (errorMessage) {
        messageHTML = `<div class="booking-modal-message error">
            ${errorMessage}
        </div>`;
    }

    // Modal HTML, with avatarHTML used for image or fallback
    const modalHTML = `
        <div id="${modalId}" class="booking-modal-overlay" role="dialog" aria-modal="true" style="display: flex;">
            <div class="booking-modal-container">
                <div class="booking-modal">
                    <div class="booking-modal-header">
                        <h3 class="booking-modal-title">Book Training Session</h3>
                        <button class="booking-modal-close-btn" onclick="closeBookingModal('${modalId}')" aria-label="Close modal">
                            <i class='bx bx-x'></i>
                        </button>
                    </div>
                    <div class="booking-modal-body">
                        <div class="booking-trainer-info">
                            <div class="booking-trainer-avatar">
                                ${avatarHTML}
                            </div>
                            <div class="booking-trainer-details">
                                <h4>${trainerName}</h4>
                                <p>${trainerSpecialty}</p>
                            </div>
                        </div>
                        ${messageHTML}
                        <form method="POST" id="booking-form-${trainerId}" onsubmit="return validateBookingForm(${trainerId})">
                            <input type="hidden" name="book_trainer" value="1">
                            <input type="hidden" name="trainer_id" value="${trainerId}">
                            <div class="booking-form-group">
                                <label for="booking_date_${trainerId}">
                                    <i class='bx bx-calendar'></i> Select Date
                                </label>
                                <input type="date" id="booking_date_${trainerId}" name="booking_date" min="${minDate}" required>
                            </div>
                            <div class="booking-form-group">
                                <label for="booking_time_${trainerId}">
                                    <i class='bx bx-time'></i> Select Time
                                </label>
                                <input type="time" id="booking_time_${trainerId}" name="booking_time" required>
                            </div>
                            <div class="booking-modal-footer">
                                <button type="button" class="booking-modal-btn booking-modal-btn-secondary" onclick="closeBookingModal('${modalId}')">
                                    <i class='bx bx-x'></i>
                                    Cancel
                                </button>
                                <button type="submit" class="booking-modal-btn booking-modal-btn-primary">
                                    <i class='bx bx-check'></i>
                                    Confirm Booking
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Add modal to container
    const container = document.getElementById('booking-modal-container');
    container.innerHTML = modalHTML;

    // Add click handler for overlay
    const modal = document.getElementById(modalId);
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeBookingModal(modalId);
        }
    });

    // Add escape key handler
    const escapeHandler = function(e) {
        if (e.key === 'Escape') {
            closeBookingModal(modalId);
            document.removeEventListener('keydown', escapeHandler);
        }
    };
    document.addEventListener('keydown', escapeHandler);
}

function closeBookingModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.animation = 'fadeOut 0.3s ease';
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}

function validateBookingForm(trainerId) {
    const date = document.getElementById('booking_date_' + trainerId).value;
    const time = document.getElementById('booking_time_' + trainerId).value;
    if (!date || !time) {
        alert('Please select both date and time.');
        return false;
    }
    const selectedDate = new Date(date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    if (selectedDate < today) {
        alert('Cannot book sessions in the past.');
        return false;
    }
    return true;
}

// Auto-show modal for errors when window.bookingModalAutoOpenData is set (populated by PHP)
document.addEventListener('DOMContentLoaded', function() {
    if (window.bookingModalAutoOpenData) {
        showBookingModal(
            window.bookingModalAutoOpenData.trainerId,
            window.bookingModalAutoOpenData.trainerName,
            window.bookingModalAutoOpenData.trainerSpecialty,
            window.bookingModalAutoOpenData.avatarHTML,
            window.bookingModalAutoOpenData.errorMessage
        );
    }
});
