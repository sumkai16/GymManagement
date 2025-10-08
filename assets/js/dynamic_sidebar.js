/**
 * Dynamic Sidebar JavaScript
 * Handles sidebar toggle, mobile responsiveness, and active state management
 */

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

// Export for use in other scripts if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DynamicSidebar;
}
