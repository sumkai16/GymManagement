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

<!-- 💅 Modern Alert Styles -->
<style>
.alert-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 15px;
    animation: slideIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

/* Base alert style */
.alert {
    padding: 16px 24px;
    border-radius: 12px;
    color: #fff;
    font-size: 15px;
    font-weight: 500;
    min-width: 300px;
    max-width: 450px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    cursor: default;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.alert::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
}

/* Specific alert types */
.alert.error { 
    background: linear-gradient(135deg, #ff6b6b, #ee5a52);
    box-shadow: 0 10px 30px rgba(255, 107, 107, 0.3);
}

.alert.success { 
    background: linear-gradient(135deg, #51cf66, #40c057);
    box-shadow: 0 10px 30px rgba(81, 207, 102, 0.3);
}

.alert.warning { 
    background: linear-gradient(135deg, #ffd43b, #fab005);
    color: #2c3e50;
    box-shadow: 0 10px 30px rgba(255, 212, 59, 0.3);
}

.alert-icon {
    margin-right: 12px;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
}

.close-btn {
    margin-left: 15px;
    font-weight: bold;
    font-size: 18px;
    cursor: pointer;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.close-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

/* Animation for appearance */
@keyframes slideIn {
    from { 
        transform: translateX(100%) scale(0.8); 
        opacity: 0; 
    }
    to { 
        transform: translateX(0) scale(1); 
        opacity: 1; 
    }
}

/* Hover effects */
.alert:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.25);
}

/* Responsive design */
@media (max-width: 480px) {
    .alert-container {
        top: 10px;
        right: 10px;
        left: 10px;
    }
    
    .alert {
        min-width: auto;
        max-width: none;
        padding: 14px 20px;
    }
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
