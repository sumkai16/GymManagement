<?php
/**
 * Modern Modal Component
 * Reusable modal for displaying messages, confirmations, and alerts
 * 
 * Usage:
 * $modalData = [
 *     'type' => 'success|error|warning|info',
 *     'title' => 'Modal Title (optional)',
 *     'message' => 'Your message here',
 *     'show' => true/false
 * ];
 * include '../utilities/modal.php';
 */

// Get modal data (if passed)
$modalType = $modalData['type'] ?? null;
$modalTitle = $modalData['title'] ?? null;
$modalMessage = $modalData['message'] ?? $modalData['msg'] ?? '';
$showModal = $modalData['show'] ?? false;
$modalId = $modalData['id'] ?? 'default-modal';
?>

<?php if ($showModal && $modalMessage): ?>
<div id="<?= htmlspecialchars($modalId) ?>" class="modern-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-title" style="display: flex;">
    <div class="modern-modal-container">
        <div class="modern-modal <?= htmlspecialchars($modalType ?? 'info') ?>">
            <!-- Modal Header -->
            <div class="modal-header">
                <div class="modal-icon-wrapper">
                    <?php if ($modalType === 'success'): ?>
                        <i class='bx bx-check-circle'></i>
                    <?php elseif ($modalType === 'error'): ?>
                        <i class='bx bx-error-circle'></i>
                    <?php elseif ($modalType === 'warning'): ?>
                        <i class='bx bx-error'></i>
                    <?php else: ?>
                        <i class='bx bx-info-circle'></i>
                    <?php endif; ?>
                </div>
                <?php if ($modalTitle): ?>
                    <h3 class="modal-title" id="modal-title"><?= htmlspecialchars($modalTitle) ?></h3>
                <?php endif; ?>
                <button class="modal-close-btn" onclick="closeModernModal('<?= htmlspecialchars($modalId) ?>')" aria-label="Close modal">
                    <i class='bx bx-x'></i>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body">
                <p class="modal-message"><?= htmlspecialchars($modalMessage) ?></p>
            </div>
            
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button class="modal-btn modal-btn-primary" onclick="closeModernModal('<?= htmlspecialchars($modalId) ?>')">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 💅 Modern Modal Styles -->
<style>
.modern-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 20px;
    box-sizing: border-box;
}

.modern-modal-container {
    animation: slideUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    max-width: 500px;
    width: 100%;
}

.modern-modal {
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 20px;
    box-shadow: 
        0 20px 60px rgba(26, 155, 168, 0.25),
        0 8px 30px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.6);
    overflow: hidden;
    border: 1px solid rgba(77, 208, 225, 0.2);
    position: relative;
}

.modern-modal::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #1a9ba8 0%, #4dd0e1 100%);
}

.modern-modal.success::before {
    background: linear-gradient(90deg, #1a9ba8 0%, #4dd0e1 100%);
}

.modern-modal.error::before {
    background: linear-gradient(90deg, #ef4444 0%, #f87171 100%);
}

.modern-modal.warning::before {
    background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%);
}

/* Modal Header */
.modal-header {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 24px 28px 20px;
    border-bottom: 1px solid rgba(77, 208, 225, 0.15);
    position: relative;
}

.modal-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    flex-shrink: 0;
    background: linear-gradient(135deg, #1a9ba8 0%, #4dd0e1 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(26, 155, 168, 0.3);
}

.modern-modal.success .modal-icon-wrapper {
    background: linear-gradient(135deg, #1a9ba8 0%, #4dd0e1 100%);
    box-shadow: 0 4px 15px rgba(26, 155, 168, 0.3);
}

.modern-modal.error .modal-icon-wrapper {
    background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
}

.modern-modal.warning .modal-icon-wrapper {
    background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
}

.modal-title {
    margin: 0;
    flex: 1;
    font-size: 20px;
    font-weight: 700;
    color: #1a202c;
    letter-spacing: -0.5px;
}

.modal-close-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    background: rgba(77, 208, 225, 0.1);
    color: #1a9ba8;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    flex-shrink: 0;
}

.modal-close-btn:hover {
    background: rgba(26, 155, 168, 0.15);
    transform: rotate(90deg) scale(1.1);
    color: #1a9ba8;
}

/* Modal Body */
.modal-body {
    padding: 24px 28px;
}

.modal-message {
    margin: 0;
    font-size: 15px;
    line-height: 1.6;
    color: #4a5568;
    font-weight: 500;
}

/* Modal Footer */
.modal-footer {
    padding: 16px 28px 24px;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    border-top: 1px solid rgba(77, 208, 225, 0.15);
}

.modal-btn {
    padding: 12px 28px;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative;
    overflow: hidden;
}

.modal-btn-primary {
    background: linear-gradient(135deg, #1a9ba8 0%, #4dd0e1 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(26, 155, 168, 0.3);
}

.modal-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(26, 155, 168, 0.4);
}

.modal-btn-primary:active {
    transform: translateY(0);
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideUp {
    from {
        transform: translateY(30px) scale(0.95);
        opacity: 0;
    }
    to {
        transform: translateY(0) scale(1);
        opacity: 1;
    }
}

/* Responsive Design */
@media (max-width: 640px) {
    .modern-modal-container {
        max-width: 100%;
    }
    
    .modal-header {
        padding: 20px 20px 16px;
    }
    
    .modal-body {
        padding: 20px;
    }
    
    .modal-footer {
        padding: 16px 20px 20px;
    }
    
    .modal-icon-wrapper {
        width: 40px;
        height: 40px;
        font-size: 24px;
    }
}
</style>

<!-- ⚙️ JavaScript for Modal Control -->
<script>
function closeModernModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.animation = 'fadeOut 0.3s ease';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }
}

// Close modal on overlay click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modern-modal-overlay')) {
        closeModernModal(e.target.id);
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const openModal = document.querySelector('.modern-modal-overlay[style*="flex"], .modern-modal-overlay:not([style*="none"])');
        if (openModal) {
            closeModernModal(openModal.id);
        }
    }
});

// Fade out animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>
<?php endif; ?>

