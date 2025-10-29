<?php
/**
 * Dynamic Sidebar Component
 * Reusable sidebar that adapts based on user role and configuration
 */

// Get user information from session
$user_id = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? 'User';
$user_role = $_SESSION['role'] ?? 'guest';

// Load sidebar configuration with absolute path
$config_path = __DIR__ . '/../../config/sidebar_config.php';
if (file_exists($config_path)) {
    $menu_configs = include $config_path;
} else {
    // Fallback configuration if file not found
    $menu_configs = [
        'admin' => [
            'title' => 'Admin Dashboard',
            'welcome_text' => 'Welcome,',
            'items' => [
                [
                    'icon' => 'bx-home-alt',
                    'text' => 'Dashboard',
                    'url' => 'admin_dashboard.php'
                ]
            ]
        ],
        'guest' => [
            'title' => 'Guest Dashboard',
            'welcome_text' => 'Welcome,',
            'items' => [
                [
                    'icon' => 'bx-home-alt',
                    'text' => 'Home',
                    'url' => '#home'
                ]
            ]
        ]
    ];
}

// Get current role configuration
$current_config = $menu_configs[$user_role] ?? $menu_configs['guest'];

// Determine the current page for active state
$current_page = basename($_SERVER['PHP_SELF']);

// Determine dashboard URL based on config
$dashboard_url = 'index.php';
foreach ($current_config['items'] as $item) {
    if (isset($item['text']) && stripos($item['text'], 'dashboard') !== false) {
        $dashboard_url = $item['url'];
        break;
    }
}
?>

<!-- Mobile Menu Toggle -->
<button class="mobile-menu-toggle" id="mobileMenuToggle">
    <i class='bx bx-menu'></i>
</button>

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Dynamic Sidebar -->
<nav class="sidebar close" id="dynamicSidebar">
    <header>
        <div class="logo">
            <a href="<?php echo htmlspecialchars($dashboard_url); ?>">
                <img src="../../assets/images/logo.png" alt="Logo" width="150">
            </a>
            <div class="text">
                <span class="welcome">Welcome,</span>
                <span class="member-name"><?php echo htmlspecialchars($username); ?>!</span>
            </div>
        </div>
        <i class='bx bx-chevron-right toggle'></i>
    </header>

    <div class="menu-bar">
        <div class="menu">
            <ul class="menu-links">
                <?php
                $current_section = null;
                $section_count = 0;
                foreach ($current_config['items'] as $idx => $item) {
                    if (isset($item['type']) && $item['type'] === 'section') {
                        if ($current_section !== null) {
                            // Close last section's items group
                            echo "</ul>\n";
                        }
                        $current_section = $item['label'];
                        $section_id = "collapsible-section-$section_count";
                        echo "<li class=\"menu-section collapsible-header\" data-collapse='#$section_id'>"
                            . "<span class=\"section-label\">" . htmlspecialchars($current_section) . "</span>"
                            . "<span class='collapse-arrow'>&#9660;</span>"
                            . "</li>\n";
                        echo "<ul class='menu-collapsible' id='$section_id'>\n";
                        $section_count++;
                        continue;
                    }
                    // Render normal or dropdown menu entries
                    $is_active = isset($item['active']) && $item['active'];
                    if (isset($item['url']) && basename($item['url']) === $current_page) $is_active = true;
                    if (isset($item['submenu'])) {
                        foreach ($item['submenu'] as $subitem) {
                            if (isset($subitem['url']) && basename($subitem['url']) === $current_page) $is_active = true;
                        }
                    }
                    if (isset($item['type']) && $item['type'] === 'dropdown') {
                        // Dropdown logic as before
                        echo "<li class=\"nav-link " . ($is_active ? 'active' : '') . "\">";
                        echo "<button class='dropdown-btn'><i class='bx " . htmlspecialchars($item['icon']) . " icon'></i><span class='text nav-text'>" . htmlspecialchars($item['text']) . "</span><i class='bx bx-chevron-down dropdown-arrow'></i></button></li>\n";
                        echo "<div class='dropdown-container'>";
                        foreach ($item['submenu'] as $subitem) {
                            echo "<a href='" . htmlspecialchars($subitem['url']) . "'><span class='text nav-text'>" . htmlspecialchars($subitem['text']) . "</span></a>\n";
                        }
                        echo "</div>\n";
                    } else {
                        echo "<li class=\"nav-link " . ($is_active ? 'active' : '') . "\"><a href=\"" . htmlspecialchars($item['url']) . "\" data-tooltip=\"" . htmlspecialchars($item['text']) . "\"><i class='bx " . htmlspecialchars($item['icon']) . " icon'></i><span class='text nav-text'>" . htmlspecialchars($item['text']) . "</span></a></li>\n";
                    }
                }
                if ($current_section !== null) {
                    echo "</ul>\n";
                }
                ?>
            </ul>
        </div>
        
        <!-- Logout Section -->
        <div class="bottom-content">
            <li class="nav-link logout">
                <a href="#" onclick="openLogoutModal()">
                    <i class='bx bx-log-out icon'></i>
                    <span class="text nav-text">Logout</span>
                </a>
            </li>
        </div>
    </div>
</nav>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <i class='bx bx-log-out'></i>
            <h3>Logout Confirmation</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to logout?</p>
            <p class="modal-subtext">You will need to sign in again to access your account.</p>
        </div>
        <div class="modal-actions">
            <button onclick="closeLogoutModal()" class="btn btn-secondary">Cancel</button>
            <button onclick="confirmLogout()" class="btn btn-primary">Logout</button>
        </div>
    </div>
</div>

<!-- Include Modal Styles -->
<link rel="stylesheet" href="../../assets/css/modal_styles.css">
<style>
.sidebar .menu-bar {
    overflow-y: auto;
    max-height: calc(100vh - 120px);
}
.menu-section.collapsible-header {
    cursor: pointer;
    user-select: none;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: background 0.2s;
}
.menu-section.collapsible-header:hover {
    background: #ececec;
}
.menu-section .collapse-arrow {
    transition: transform 0.25s cubic-bezier(.4,2,.41,.86);
    font-size: 13px;
    margin-left: 8px;
}
.menu-section.collapsed .collapse-arrow {
    transform: rotate(-90deg)
}
.menu-collapsible {
    display: block;
    padding: 0;
    margin: 0;
    transition: max-height 0.35s cubic-bezier(.4,2,.41,.86), opacity 0.18s linear;
    max-height: 1200px;
    opacity: 1;
    overflow: hidden;
}
.menu-collapsible.collapsed {
    max-height: 0;
    opacity: 0.4;
}
</style>

<!-- Include the dynamic sidebar JavaScript -->
<script>
// Dynamic Sidebar JavaScript - Inline to avoid path issues
class DynamicSidebar {
    constructor() {
        this.sidebar = document.getElementById('dynamicSidebar');
        this.toggle = document.querySelector('.toggle');
        this.mobileMenuToggle = document.getElementById('mobileMenuToggle');
        this.sidebarOverlay = document.getElementById('sidebarOverlay');
        this.body = document.body;
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupMobileResponsiveness();
        this.setupActiveStates();
        this.loadSidebarState();
    }

    setupEventListeners() {
        // Desktop sidebar toggle
        if (this.toggle) {
            this.toggle.addEventListener('click', () => {
                this.toggleSidebar();
            });
        }

        // Mobile menu toggle
        if (this.mobileMenuToggle) {
            this.mobileMenuToggle.addEventListener('click', () => {
                this.toggleMobileSidebar();
            });
        }

        // Close sidebar when clicking overlay
        if (this.sidebarOverlay) {
            this.sidebarOverlay.addEventListener('click', () => {
                this.closeMobileSidebar();
            });
        }

        // Close mobile sidebar when clicking outside
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && 
                !this.sidebar.contains(e.target) && 
                !this.mobileMenuToggle.contains(e.target)) {
                this.closeMobileSidebar();
            }
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            this.handleResize();
        });

        // Setup dropdown functionality
        this.setupDropdowns();
    }

    setupDropdowns() {
        // Loop through all dropdown buttons to toggle between hiding and showing its dropdown content
        const dropdowns = document.getElementsByClassName("dropdown-btn");
        
        for (let i = 0; i < dropdowns.length; i++) {
            dropdowns[i].addEventListener("click", function() {
                this.classList.toggle("active");
                // Find the dropdown container that comes after this button's parent li
                const parentLi = this.closest('li');
                const dropdownContent = parentLi.nextElementSibling;
                
                if (dropdownContent && dropdownContent.classList.contains('dropdown-container')) {
                    if (dropdownContent.style.opacity === "1") {
                        // Animate out first, then hide
                        dropdownContent.style.opacity = "0";
                        dropdownContent.style.transform = "translateY(-10px)";
                        setTimeout(() => {
                            dropdownContent.style.display = "none";
                        }, 400); // Wait for animation to complete
                    } else {
                        dropdownContent.style.display = "block";
                        // Use setTimeout to allow display to take effect before animating
                        setTimeout(() => {
                            dropdownContent.style.opacity = "1";
                            dropdownContent.style.transform = "translateY(0)";
                        }, 10);
                    }
                }
            });
        }
    }

    setupMobileResponsiveness() {
        // Check if we're on mobile on load
        if (window.innerWidth <= 768) {
            this.closeMobileSidebar();
        }
    }

    setupActiveStates() {
        // Update active states based on current URL
        const currentPath = window.location.pathname;
        const menuLinks = document.querySelectorAll('.menu-links .nav-link');
        
        menuLinks.forEach(link => {
            const href = link.querySelector('a').getAttribute('href');
            if (href && currentPath.includes(href.replace('../', ''))) {
                // Remove active class from all links
                menuLinks.forEach(l => l.classList.remove('active'));
                // Add active class to current link
                link.classList.add('active');
            }
        });
    }

    toggleSidebar() {
        if (this.sidebar) {
            this.sidebar.classList.toggle('close');
            this.saveSidebarState();
        }
    }

    toggleMobileSidebar() {
        if (this.sidebar) {
            this.sidebar.classList.toggle('mobile-open');
            this.sidebarOverlay.classList.toggle('active');
            this.body.classList.toggle('sidebar-open');
        }
    }

    closeMobileSidebar() {
        if (this.sidebar) {
            this.sidebar.classList.remove('mobile-open');
            this.sidebarOverlay.classList.remove('active');
            this.body.classList.remove('sidebar-open');
        }
    }

    handleResize() {
        if (window.innerWidth > 768) {
            // Desktop view - remove mobile classes
            this.sidebar.classList.remove('mobile-open');
            this.sidebarOverlay.classList.remove('active');
            this.body.classList.remove('sidebar-open');
        } else {
            // Mobile view - ensure sidebar is closed by default
            this.closeMobileSidebar();
        }
    }

    saveSidebarState() {
        const isClosed = this.sidebar.classList.contains('close');
        localStorage.setItem('sidebarState', isClosed ? 'closed' : 'open');
    }

    loadSidebarState() {
        const savedState = localStorage.getItem('sidebarState');
        if (savedState === 'closed') {
            this.sidebar.classList.add('close');
        } else if (savedState === 'open') {
            this.sidebar.classList.remove('close');
        }
    }

    // Public method to programmatically toggle sidebar
    toggle() {
        this.toggleSidebar();
    }

    // Public method to set sidebar state
    setState(isOpen) {
        if (isOpen) {
            this.sidebar.classList.remove('close');
        } else {
            this.sidebar.classList.add('close');
        }
        this.saveSidebarState();
    }
}

// Initialize the dynamic sidebar when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.dynamicSidebar = new DynamicSidebar();
});

// Logout Modal Functions
function openLogoutModal() {
    document.getElementById('logoutModal').style.display = 'block';
}

function closeLogoutModal() {
    document.getElementById('logoutModal').style.display = 'none';
}

function confirmLogout() {
    window.location.href = '../../controllers/AuthController.php?action=logout';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('logoutModal');
    if (event.target === modal) {
        closeLogoutModal();
    }
}
document.querySelectorAll('.menu-section.collapsible-header').forEach(function(section){
    section.addEventListener('click', function(){
        const collapseId = section.getAttribute('data-collapse');
        const group = document.querySelector(collapseId);
        section.classList.toggle('collapsed');
        group.classList.toggle('collapsed');
    });
});
</script>
