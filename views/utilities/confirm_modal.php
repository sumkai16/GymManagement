<?php
/**
 * Confirmation Modal Component
 * Reusable confirmation modal for delete/cancel actions
 * 
 * Usage:
 * $confirmData = [
 *     'id' => 'unique-modal-id',
 *     'title' => 'Confirm Action',
 *     'message' => 'Are you sure you want to proceed?',
 *     'confirmText' => 'Confirm',
 *     'cancelText' => 'Cancel',
 *     'confirmAction' => 'functionName() or URL',
 *     'confirmButtonClass' => 'danger (optional)',
 *     'show' => true/false
 * ];
 * include '../utilities/confirm_modal.php';
 */

// Get confirmation modal data (if passed)
$confirmId = $confirmData['id'] ?? 'confirm-modal';
$confirmTitle = $confirmData['title'] ?? 'Confirm Action';
$confirmMessage = $confirmData['message'] ?? 'Are you sure you want to proceed?';
$confirmText = $confirmData['confirmText'] ?? 'Confirm';
$cancelText = $confirmData['cancelText'] ?? 'Cancel';
$confirmAction = $confirmData['confirmAction'] ?? '';
$confirmButtonClass = $confirmData['confirmButtonClass'] ?? 'primary';
$showConfirm = $confirmData['show'] ?? false;
?>

<?php if ($showConfirm): ?>
<div id="<?= htmlspecialchars($confirmId) ?>" class="confirm-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="confirm-title" style="display: flex;">
    <div class="confirm-modal-container">
        <div class="confirm-modal">
            <!-- Modal Header -->
            <div class="confirm-modal-header">
                <div class="confirm-modal-icon-wrapper">
                    <i class='bx bx-error-circle'></i>
                </div>
                <h3 class="confirm-modal-title" id="confirm-title"><?= htmlspecialchars($confirmTitle) ?></h3>
                <button class="confirm-modal-close-btn" onclick="closeConfirmModal('<?= htmlspecialchars($confirmId) ?>')" aria-label="Close modal">
                    <i class='bx bx-x'></i>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="confirm-modal-body">
                <p class="confirm-modal-message"><?= htmlspecialchars($confirmMessage) ?></p>
            </div>
            
            <!-- Modal Footer -->
            <div class="confirm-modal-footer">
                <button class="confirm-modal-btn confirm-modal-btn-cancel" onclick="closeConfirmModal('<?= htmlspecialchars($confirmId) ?>')">
                    <i class='bx bx-x'></i>
                    <?= htmlspecialchars($cancelText) ?>
                </button>
                <button class="confirm-modal-btn confirm-modal-btn-<?= htmlspecialchars($confirmButtonClass) ?>" onclick="handleConfirmAction('<?= htmlspecialchars($confirmId) ?>')" id="<?= htmlspecialchars($confirmId) ?>-confirm-btn">
                    <i class='bx bx-check'></i>
                    <?= htmlspecialchars($confirmText) ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 💅 Confirmation Modal Styles -->
<style>
.confirm-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
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

.confirm-modal-container {
    animation: slideUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    max-width: 450px;
    width: 100%;
}

.confirm-modal {
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 20px;
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.25),
        0 8px 30px rgba(0, 0, 0, 0.15),
        inset 0 1px 0 rgba(255, 255, 255, 0.6);
    overflow: hidden;
    border: 1px solid rgba(220, 53, 69, 0.2);
    position: relative;
}

.confirm-modal::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #dc3545 0%, #f87171 100%);
}

/* Modal Header */
.confirm-modal-header {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 24px 28px 20px;
    border-bottom: 1px solid rgba(220, 53, 69, 0.15);
    position: relative;
}

.confirm-modal-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    flex-shrink: 0;
    background: linear-gradient(135deg, #dc3545 0%, #f87171 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

.confirm-modal-title {
    margin: 0;
    flex: 1;
    font-size: 20px;
    font-weight: 700;
    color: #1a202c;
    letter-spacing: -0.5px;
}

.confirm-modal-close-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    flex-shrink: 0;
}

.confirm-modal-close-btn:hover {
    background: rgba(220, 53, 69, 0.15);
    transform: rotate(90deg) scale(1.1);
    color: #dc3545;
}

/* Modal Body */
.confirm-modal-body {
    padding: 24px 28px;
}

.confirm-modal-message {
    margin: 0;
    font-size: 15px;
    line-height: 1.6;
    color: #4a5568;
    font-weight: 500;
    text-align: center;
}

/* Modal Footer */
.confirm-modal-footer {
    padding: 16px 28px 24px;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    border-top: 1px solid rgba(220, 53, 69, 0.15);
}

.confirm-modal-btn {
    padding: 12px 24px;
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
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.confirm-modal-btn-cancel {
    background: #6c757d;
    color: white;
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
}

.confirm-modal-btn-cancel:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
    background: #5a6268;
}

.confirm-modal-btn-primary {
    background: linear-gradient(135deg, #1a9ba8 0%, #4dd0e1 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(26, 155, 168, 0.3);
}

.confirm-modal-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(26, 155, 168, 0.4);
}

.confirm-modal-btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #f87171 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

.confirm-modal-btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
}

.confirm-modal-btn:active {
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
    .confirm-modal-container {
        max-width: 100%;
    }
    
    .confirm-modal-header {
        padding: 20px 20px 16px;
    }
    
    .confirm-modal-body {
        padding: 20px;
    }
    
    .confirm-modal-footer {
        padding: 16px 20px 20px;
        flex-direction: column-reverse;
    }
    
    .confirm-modal-btn {
        width: 100%;
        justify-content: center;
    }
    
    .confirm-modal-icon-wrapper {
        width: 40px;
        height: 40px;
        font-size: 24px;
    }
}
</style>

<!-- ⚙️ JavaScript for Confirmation Modal Control -->
<script>
// Store confirmation actions in a global object
window.confirmModalActions = window.confirmModalActions || {};

function closeConfirmModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.animation = 'fadeOut 0.3s ease';
        setTimeout(() => {
            modal.style.display = 'none';
            // Clean up the action
            delete window.confirmModalActions[modalId];
        }, 300);
    }
}

function handleConfirmAction(modalId) {
    // Get the stored action for this modal
    const action = window.confirmModalActions[modalId];
    if (action) {
        // Close modal first
        closeConfirmModal(modalId);
        
        // Execute the action
        if (typeof action === 'function') {
            action();
        } else if (typeof action === 'string') {
            // If it's a string, it could be a form submission
            if (action.startsWith('submitForm:')) {
                const formId = action.replace('submitForm:', '');
                const form = document.getElementById(formId);
                if (form) {
                    form.submit();
                }
            } else {
                // Otherwise try to execute as a function call or URL
                try {
                    eval(action);
                } catch(e) {
                    console.error('Error executing confirm action:', e);
                }
            }
        }
    }
}

// Close modal on overlay click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('confirm-modal-overlay')) {
        closeConfirmModal(e.target.id);
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const openModal = document.querySelector('.confirm-modal-overlay[style*="flex"]');
        if (openModal) {
            closeConfirmModal(openModal.id);
        }
    }
});

// Fade out animation
if (!document.getElementById('confirm-modal-fadeout-style')) {
    const style = document.createElement('style');
    style.id = 'confirm-modal-fadeout-style';
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
}
</script>
<?php endif; ?>

