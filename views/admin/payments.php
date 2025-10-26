<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

require_once '../../controllers/AdminController.php';
$adminController = new AdminController();

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add_payment':
            $result = $adminController->addPayment(
                $_POST['member_id'] ?? 0,
                $_POST['amount'] ?? 0,
                $_POST['payment_type'] ?? '',
                $_POST['payment_method'] ?? '',
                $_POST['payment_date'] ?? '',
                $_POST['notes'] ?? ''
            );
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header("Location: payments.php");
            exit;

        case 'update_payment':
            $result = $adminController->updatePayment(
                $_POST['payment_id'] ?? 0,
                $_POST['amount'] ?? 0,
                $_POST['payment_type'] ?? '',
                $_POST['payment_method'] ?? '',
                $_POST['payment_date'] ?? '',
                $_POST['notes'] ?? ''
            );
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header("Location: payments.php");
            exit;

        case 'delete_payment':
            $result = $adminController->deletePayment($_POST['payment_id'] ?? 0);
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header("Location: payments.php");
            exit;
    }
}

// Handle filter and sort parameters
$filter_member = $_GET['filter_member'] ?? null;
$filter_payment_type = $_GET['filter_payment_type'] ?? null;
$filter_date_from = $_GET['filter_date_from'] ?? null;
$filter_date_to = $_GET['filter_date_to'] ?? null;
$sort_by = $_GET['sort_by'] ?? 'payment_date';
$sort_order = $_GET['sort_order'] ?? 'DESC';

// Get all payments with filters and sorting
$payments = $adminController->getAllPayments($filter_member, $filter_payment_type, $filter_date_from, $filter_date_to, $sort_by, $sort_order);
$members = $adminController->getAllMembers();
$username = $_SESSION['username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Management - FitNexus</title>
    <link rel="stylesheet" href="../../assets/css/member_styles.css">
    <link rel="stylesheet" href="../../assets/css/modal_styles.css">
    <link rel="stylesheet" href="../../assets/css/admin_users_styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <!-- Include Alert System -->
    <?php include '../utilities/alert.php'; ?>

    <!-- Include Dynamic Sidebar -->
    <?php include '../components/dynamic_sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="content-wrapper">
            <div class="dashboard-header">
                <h1>Payment Management</h1>
                <p>Track member payments, revenue, and financial transactions.</p>
            </div>

            <!-- Revenue Summary Cards -->
            <div class="quick-stats" style="margin-bottom: 30px;">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-credit-card'></i>
                    </div>
                    <div class="stat-content">
                        <h3>₱<?php echo number_format($adminController->getTotalRevenue()); ?></h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-calendar'></i>
                    </div>
                    <div class="stat-content">
                        <h3>₱<?php echo number_format($adminController->getMonthlyRevenue()); ?></h3>
                        <p>This Month</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-trending-up'></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $adminController->getPaymentCount(); ?></h3>
                        <p>Total Payments</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-check-circle'></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $adminController->getPendingPayments(); ?></h3>
                        <p>Pending</p>
                    </div>
                </div>
            </div>

            <!-- Header Actions: Add Payment Button and Filters -->
            <div class="header-actions" style="display: flex; flex-direction: row; align-items: center; margin-bottom: 20px; gap: 20px; justify-content: space-around;">
                <!-- Add Payment Button -->
                <button class="add-user-btn" onclick="openAddPaymentModal()">
                    <i class='bx bx-plus'></i> Add New Payment
                </button>

                <!-- Filters and Sorting -->
                <div class="filters-section" style="display: flex; flex-direction: column; gap: 10px; align-items: flex-start;">
                    <div class="filters-row" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                        <select id="filter_member" onchange="applyFilters()" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff; min-width: 140px;">
                            <option value="" disabled <?php echo (empty($filter_member)) ? 'selected' : ''; ?>>Filter by Member</option>
                            <option value="">All Members</option>
                            <?php foreach ($members as $member): ?>
                                <option value="<?php echo $member['member_id']; ?>" <?php echo ($filter_member == $member['member_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($member['full_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <select id="filter_payment_type" onchange="applyFilters()" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff; min-width: 140px;">
                            <option value="" disabled <?php echo (empty($filter_payment_type)) ? 'selected' : ''; ?>>Payment Type</option>
                            <option value="">All Types</option>
                            <option value="membership" <?php echo ($filter_payment_type === 'membership') ? 'selected' : ''; ?>>Membership</option>
                            <option value="personal_training" <?php echo ($filter_payment_type === 'personal_training') ? 'selected' : ''; ?>>Personal Training</option>
                            <option value="class" <?php echo ($filter_payment_type === 'class') ? 'selected' : ''; ?>>Class</option>
                            <option value="other" <?php echo ($filter_payment_type === 'other') ? 'selected' : ''; ?>>Other</option>
                        </select>

                        <input type="date" id="filter_date_from" onchange="applyFilters()" value="<?php echo $filter_date_from; ?>" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff;" placeholder="From Date">

                        <input type="date" id="filter_date_to" onchange="applyFilters()" value="<?php echo $filter_date_to; ?>" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff;" placeholder="To Date">

                        <select id="sort_by" onchange="applyFilters()" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff; min-width: 140px;">
                            <option value="" disabled <?php echo (empty($sort_by) || $sort_by === 'payment_date') ? '' : 'selected'; ?>>Sort by</option>
                            <option value="payment_date" <?php echo ($sort_by === 'payment_date') ? 'selected' : ''; ?>>Date</option>
                            <option value="amount" <?php echo ($sort_by === 'amount') ? 'selected' : ''; ?>>Amount</option>
                            <option value="member_name" <?php echo ($sort_by === 'member_name') ? 'selected' : ''; ?>>Member</option>
                            <option value="payment_type" <?php echo ($sort_by === 'payment_type') ? 'selected' : ''; ?>>Type</option>
                        </select>

                        <select id="sort_order" onchange="applyFilters()" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff; min-width: 140px;">
                            <option value="" disabled>Order</option>
                            <option value="DESC" <?php echo ($sort_order === 'DESC') ? 'selected' : ''; ?>>Descending</option>
                            <option value="ASC" <?php echo ($sort_order === 'ASC') ? 'selected' : ''; ?>>Ascending</option>
                        </select>

                        <button onclick="clearFilters()" style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer;">Clear Filters</button>
                    </div>
                </div>
            </div>

            <!-- Payments Table -->
            <div class="card">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Member</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Method</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($payment['payment_id']); ?></td>
                            <td><?php echo htmlspecialchars($payment['member_name']); ?></td>
                            <td>₱<?php echo number_format($payment['amount'], 2); ?></td>
                            <td>
                                <span class="role-badge role-member">
                                    <?php echo htmlspecialchars($payment['payment_type']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                            <td><?php echo htmlspecialchars(date('M d, Y', strtotime($payment['payment_date']))); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo htmlspecialchars($payment['status']); ?>">
                                    <?php echo htmlspecialchars($payment['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn btn-edit" onclick="openEditPaymentModal(<?php echo $payment['payment_id']; ?>, <?php echo $payment['member_id']; ?>, <?php echo $payment['amount']; ?>, '<?php echo htmlspecialchars($payment['payment_type']); ?>', '<?php echo htmlspecialchars($payment['payment_method']); ?>', '<?php echo $payment['payment_date']; ?>', '<?php echo htmlspecialchars($payment['notes'] ?? ''); ?>')" title="Edit Payment">
                                        <i class='bx bx-edit'></i> Edit
                                    </button>
                                    <button class="action-btn btn-delete" onclick="openDeletePaymentModal(<?php echo $payment['payment_id']; ?>, '<?php echo htmlspecialchars($payment['member_name']); ?>', <?php echo $payment['amount']; ?>)" title="Delete Payment">
                                        <i class='bx bx-trash'></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Payment Modal -->
    <div id="addPaymentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class='bx bx-credit-card'></i>
                <h3>Add New Payment</h3>
            </div>
            <form method="POST" action="payments.php">
                <input type="hidden" name="action" value="add_payment">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="add_member_id">Member:</label>
                            <select id="add_member_id" name="member_id" required>
                                <option value="">Select Member</option>
                                <?php foreach ($members as $member): ?>
                                    <option value="<?php echo $member['member_id']; ?>">
                                        <?php echo htmlspecialchars($member['full_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="add_amount">Amount:</label>
                            <input type="number" id="add_amount" name="amount" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="add_payment_type">Payment Type:</label>
                            <select id="add_payment_type" name="payment_type" required>
                                <option value="">Select Type</option>
                                <option value="membership">Membership</option>
                                <option value="personal_training">Personal Training</option>
                                <option value="class">Class</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="add_payment_method">Payment Method:</label>
                            <select id="add_payment_method" name="payment_method" required>
                                <option value="">Select Method</option>
                                <option value="cash">Cash</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="gcash">GCash</option>
                                <option value="paymaya">PayMaya</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="add_payment_date">Payment Date:</label>
                            <input type="date" id="add_payment_date" name="payment_date" required>
                        </div>
                        <div class="form-group">
                            <label for="add_notes">Notes:</label>
                            <textarea id="add_notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('addPaymentModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Payment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Payment Modal -->
    <div id="editPaymentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class='bx bx-edit'></i>
                <h3>Edit Payment</h3>
            </div>
            <form method="POST" action="payments.php">
                <input type="hidden" name="action" value="update_payment">
                <input type="hidden" id="edit_payment_id" name="payment_id">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_member_id">Member:</label>
                            <select id="edit_member_id" name="member_id" required>
                                <?php foreach ($members as $member): ?>
                                    <option value="<?php echo $member['member_id']; ?>">
                                        <?php echo htmlspecialchars($member['full_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_amount">Amount:</label>
                            <input type="number" id="edit_amount" name="amount" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_payment_type">Payment Type:</label>
                            <select id="edit_payment_type" name="payment_type" required>
                                <option value="membership">Membership</option>
                                <option value="personal_training">Personal Training</option>
                                <option value="class">Class</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_payment_method">Payment Method:</label>
                            <select id="edit_payment_method" name="payment_method" required>
                                <option value="cash">Cash</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="gcash">GCash</option>
                                <option value="paymaya">PayMaya</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_payment_date">Payment Date:</label>
                            <input type="date" id="edit_payment_date" name="payment_date" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_notes">Notes:</label>
                            <textarea id="edit_notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('editPaymentModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Payment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Payment Modal -->
    <div id="deletePaymentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class='bx bx-trash'></i>
                <h3>Delete Payment</h3>
            </div>
            <form method="POST" action="payments.php">
                <input type="hidden" name="action" value="delete_payment">
                <input type="hidden" id="delete_payment_id" name="payment_id">
                <div class="modal-body">
                    <p>Are you sure you want to delete payment for "<span id="delete_payment_member"></span>" (₱<span id="delete_payment_amount"></span>)?</p>
                    <p class="modal-subtext">This action cannot be undone.</p>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('deletePaymentModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Delete Payment</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function applyFilters() {
            const filterMember = document.getElementById('filter_member').value;
            const filterPaymentType = document.getElementById('filter_payment_type').value;
            const filterDateFrom = document.getElementById('filter_date_from').value;
            const filterDateTo = document.getElementById('filter_date_to').value;
            const sortBy = document.getElementById('sort_by').value;
            const sortOrder = document.getElementById('sort_order').value;

            const params = new URLSearchParams(window.location.search);
            params.set('filter_member', filterMember);
            params.set('filter_payment_type', filterPaymentType);
            params.set('filter_date_from', filterDateFrom);
            params.set('filter_date_to', filterDateTo);
            params.set('sort_by', sortBy);
            params.set('sort_order', sortOrder);

            window.location.href = window.location.pathname + '?' + params.toString();
        }

        function clearFilters() {
            window.location.href = window.location.pathname;
        }

        function openAddPaymentModal() {
            // Set today's date as default
            document.getElementById('add_payment_date').value = new Date().toISOString().split('T')[0];
            document.getElementById('addPaymentModal').style.display = 'block';
        }

        function openEditPaymentModal(paymentId, memberId, amount, paymentType, paymentMethod, paymentDate, notes) {
            document.getElementById('edit_payment_id').value = paymentId;
            document.getElementById('edit_member_id').value = memberId;
            document.getElementById('edit_amount').value = amount;
            document.getElementById('edit_payment_type').value = paymentType;
            document.getElementById('edit_payment_method').value = paymentMethod;
            document.getElementById('edit_payment_date').value = paymentDate;
            document.getElementById('edit_notes').value = notes;
            document.getElementById('editPaymentModal').style.display = 'block';
        }

        function openDeletePaymentModal(paymentId, memberName, amount) {
            document.getElementById('delete_payment_id').value = paymentId;
            document.getElementById('delete_payment_member').textContent = memberName;
            document.getElementById('delete_payment_amount').textContent = amount.toFixed(2);
            document.getElementById('deletePaymentModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = ['addPaymentModal', 'editPaymentModal', 'deletePaymentModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (event.target === modal) {
                    closeModal(modalId);
                }
            });
        }
    </script>
</body>
</html>
