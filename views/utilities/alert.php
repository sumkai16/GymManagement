<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Collect and clear messages
$errorMessage = $_SESSION['error'] ?? null;
$successMessage = $_SESSION['success'] ?? null;
$warningMessage = $_SESSION['warning'] ?? null;
unset($_SESSION['error'], $_SESSION['success'], $_SESSION['warning']);
?>

<!-- 🔔 Alert Component -->
<?php if ($errorMessage || $successMessage || $warningMessage): ?>
    <div class="alert-container">
        <?php if ($errorMessage): ?>
            <div class="alert error" id="alertBox">
                <span class="alert-icon">❌</span>
                <?= htmlspecialchars($errorMessage); ?>
                <span class="close-btn" onclick="closeAlert(this)">×</span>
            </div>
        <?php endif; ?>

        <?php if ($successMessage): ?>
            <div class="alert success" id="alertBox">
                <span class="alert-icon">✅</span>
                <?= htmlspecialchars($successMessage); ?>
                <span class="close-btn" onclick="closeAlert(this)">×</span>
            </div>
        <?php endif; ?>

        <?php if ($warningMessage): ?>
            <div class="alert warning" id="alertBox">
                <span class="alert-icon">⚠️</span>
                <?= htmlspecialchars($warningMessage); ?>
                <span class="close-btn" onclick="closeAlert(this)">×</span>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- 💅 Inline CSS (you can move to assets/css/style.css later) -->
<style>
.alert-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
    animation: slideIn 0.4s ease-out;
}

/* Base alert style */
.alert {
    padding: 14px 20px;
    border-radius: 8px;
    color: #fff;
    font-size: 15px;
    min-width: 260px;
    max-width: 400px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    opacity: 0.95;
    cursor: default;
    transition: opacity 0.3s ease;
}

/* Specific alert types */
.alert.error { background-color: #e74c3c; }     /* Red */
.alert.success { background-color: #2ecc71; }   /* Green */
.alert.warning { background-color: #f1c40f; color: #222; } /* Yellow */

.alert-icon {
    margin-right: 10px;
    font-size: 18px;
}

.close-btn {
    margin-left: 12px;
    font-weight: bold;
    cursor: pointer;
}

.close-btn:hover {
    opacity: 0.7;
}

/* Animation for appearance */
@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
</style>

<!-- ⚙️ JavaScript for Closing Alerts -->
<script>
function closeAlert(element) {
    const alertBox = element.closest(".alert");
    if (alertBox) {
        alertBox.style.opacity = "0";
        setTimeout(() => alertBox.remove(), 300);
    }
}

// Auto close all alerts after 4 seconds
setTimeout(() => {
    document.querySelectorAll(".alert").forEach(alert => {
        alert.style.opacity = "0";
        setTimeout(() => alert.remove(), 300);
    });
}, 4000);
</script>
