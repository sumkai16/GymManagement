/* =========================================== */
/* GUEST DASHBOARD JAVASCRIPT */
/* =========================================== */

// Debug mode detection
const urlParams = new URLSearchParams(window.location.search);
const debugMode = urlParams.get('debug') === '1';

// Debug logging function
function debugLog(message, data = null) {
    if (debugMode) {
        console.log(`🐛 [GUEST DASHBOARD] ${message}`, data || '');
    }
}

// Initialize debug mode
if (debugMode) {
    document.body.classList.add('debug');
    debugLog('Debug mode enabled');
}

// ===========================================
// SIDEBAR FUNCTIONALITY
// ===========================================
const body = document.querySelector("body");
const sidebar = body.querySelector(".sidebar");
const toggle = body.querySelector(".toggle");
const mobileMenuToggle = document.getElementById("mobileMenuToggle");
const sidebarOverlay = document.getElementById("sidebarOverlay");

// Desktop sidebar toggle functionality
toggle.addEventListener("click", () => {
    debugLog('Desktop sidebar toggle clicked');
    sidebar.classList.toggle("close");
    debugLog('Sidebar state:', sidebar.classList.contains('close') ? 'closed' : 'open');
});

// Mobile menu toggle functionality
if (mobileMenuToggle) {
    mobileMenuToggle.addEventListener("click", () => {
        debugLog('Mobile menu toggle clicked');
        sidebar.classList.toggle("open");
        sidebarOverlay.classList.toggle("active");
        debugLog('Mobile menu state:', sidebar.classList.contains('open') ? 'open' : 'closed');
    });
}

// Close mobile menu when clicking overlay
if (sidebarOverlay) {
    sidebarOverlay.addEventListener("click", () => {
        debugLog('Mobile menu overlay clicked');
        sidebar.classList.remove("open");
        sidebarOverlay.classList.remove("active");
        debugLog('Mobile menu closed via overlay');
    });
}

// Handle window resize
window.addEventListener("resize", () => {
    if (window.innerWidth > 768) {
        sidebar.classList.remove("open");
        if (sidebarOverlay) {
            sidebarOverlay.classList.remove("active");
        }
        debugLog('Mobile menu closed due to resize');
    }
});

// ===========================================
// NAVIGATION FUNCTIONALITY
// ===========================================
const navLinks = document.querySelectorAll('.nav-link a');
const dashboardSections = document.querySelectorAll('.dashboard-section');

debugLog('Navigation links found:', navLinks.length);
debugLog('Dashboard sections found:', dashboardSections.length);

// Handle navigation clicks
navLinks.forEach((link, index) => {
    debugLog(`Setting up navigation for link ${index}:`, link.getAttribute('href'));
    
    link.addEventListener('click', (e) => {
        e.preventDefault();
        const targetId = link.getAttribute('href').substring(1);
        debugLog('Navigation clicked:', targetId);
        
        // Remove active class from all nav links
        navLinks.forEach(navLink => {
            navLink.parentElement.classList.remove('active');
        });
        
        // Add active class to clicked link
        link.parentElement.classList.add('active');
        debugLog('Active nav link set to:', link.parentElement.dataset.section);
        
        // Hide all sections
        dashboardSections.forEach(section => {
            section.classList.remove('active');
        });
        
        // Show target section
        const targetSection = document.getElementById(targetId);
        if (targetSection) {
            targetSection.classList.add('active');
            debugLog('Active section set to:', targetId);
        } else {
            debugLog('ERROR: Target section not found:', targetId);
        }
        
        // Close mobile menu if open
        if (window.innerWidth <= 768 && sidebar.classList.contains('open')) {
            sidebar.classList.remove('open');
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('active');
            }
            debugLog('Mobile menu closed after navigation');
        }
    });
});

// ===========================================
// SUBSCRIPTION PLAN FUNCTIONALITY
// ===========================================
const planButtons = document.querySelectorAll('.plan-card .btn-primary');
debugLog('Plan buttons found:', planButtons.length);

planButtons.forEach((button, index) => {
    debugLog(`Setting up plan button ${index}`);
    
    button.addEventListener('click', (e) => {
        e.preventDefault();
        debugLog('Plan button clicked');
        
        // Get plan name
        const planCard = button.closest('.plan-card');
        const planName = planCard.querySelector('h3').textContent;
        debugLog('Plan selected:', planName);
        
        // Show subscription modal or redirect
        showSubscriptionModal(planName);
    });
});

// ===========================================
// MODAL FUNCTIONS
// ===========================================

// Membership payment modal function
function showSubscriptionModal(planName) {
    debugLog('Showing subscription modal for plan:', planName);
    
    // Create modal overlay
    const modalOverlay = document.createElement('div');
    modalOverlay.className = 'modal-overlay';
    modalOverlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    `;
    
    // Create modal content
    const modalContent = document.createElement('div');
    modalContent.style.cssText = `
        background: white;
        padding: 30px;
        border-radius: 15px;
        max-width: 500px;
        width: 90%;
        text-align: center;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    `;
    
    modalContent.innerHTML = `
        <div style="margin-bottom: 20px;">
            <i class="fas fa-user-tie" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 15px;"></i>
            <h2 style="color: var(--accent); margin-bottom: 10px;">${planName} Selected</h2>
            <p style="color: var(--text-color); margin-bottom: 25px;">To complete your membership, please visit our admin office to make payment in person.</p>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <p style="margin: 0; font-weight: 600; color: var(--accent);">Admin Office Hours:</p>
                <p style="margin: 5px 0 0 0; color: var(--text-color);">Monday - Friday: 9AM - 6PM</p>
                <p style="margin: 5px 0 0 0; color: var(--text-color);">Location: 123 Fitness Street, Admin Office</p>
            </div>
        </div>
        <div style="display: flex; gap: 15px; justify-content: center;">
            <button class="btn-cancel" style="
                background: #6c757d;
                color: white;
                border: none;
                padding: 12px 25px;
                border-radius: 8px;
                cursor: pointer;
                font-weight: 500;
                transition: all 0.3s ease;
            ">Close</button>
            <button class="btn-proceed" style="
                background: var(--primary-color);
                color: white;
                border: none;
                padding: 12px 25px;
                border-radius: 8px;
                cursor: pointer;
                font-weight: 500;
                transition: all 0.3s ease;
            ">Contact Admin</button>
        </div>
    `;
    
    modalOverlay.appendChild(modalContent);
    document.body.appendChild(modalOverlay);
    
    // Add event listeners
    modalOverlay.querySelector('.btn-cancel').addEventListener('click', () => {
        debugLog('Modal cancelled');
        document.body.removeChild(modalOverlay);
    });
    
    modalOverlay.querySelector('.btn-proceed').addEventListener('click', () => {
        debugLog('Modal proceed clicked - navigating to contact');
        // Navigate to contact section
        const contactSection = document.getElementById('contact');
        if (contactSection) {
            // Remove active class from current nav link
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // Add active class to contact nav link
            document.querySelector('a[href="#contact"]').parentElement.classList.add('active');
            
            // Hide all sections and show contact
            document.querySelectorAll('.dashboard-section').forEach(section => {
                section.classList.remove('active');
            });
            contactSection.classList.add('active');
            debugLog('Navigated to contact section');
        } else {
            debugLog('ERROR: Contact section not found');
        }
        document.body.removeChild(modalOverlay);
    });
    
    // Close modal when clicking overlay
    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) {
            debugLog('Modal closed by clicking overlay');
            document.body.removeChild(modalOverlay);
        }
    });
}

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Demo workout interaction
const demoWorkoutCard = document.querySelector('.demo-workout-card');
if (demoWorkoutCard) {
    demoWorkoutCard.addEventListener('click', () => {
        showUpgradePrompt();
    });
}

function showUpgradePrompt() {
    // Create upgrade prompt
    const upgradePrompt = document.createElement('div');
    upgradePrompt.className = 'upgrade-prompt-popup';
    upgradePrompt.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: linear-gradient(135deg, var(--primary-color), #1a9ba8);
        color: white;
        padding: 30px;
        border-radius: 15px;
        text-align: center;
        z-index: 10000;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    `;
    
    upgradePrompt.innerHTML = `
        <i class="fas fa-user-tie" style="font-size: 2.5rem; margin-bottom: 15px;"></i>
        <h3 style="margin-bottom: 15px; font-size: 1.3rem;">Membership Required</h3>
        <p style="margin-bottom: 25px; opacity: 0.9;">This feature is available for full members only. Visit our admin office to pay and become a member!</p>
        <div style="display: flex; gap: 15px; justify-content: center;">
            <button class="btn-close-demo" style="
                background: rgba(255, 255, 255, 0.2);
                color: white;
                border: 1px solid rgba(255, 255, 255, 0.3);
                padding: 10px 20px;
                border-radius: 8px;
                cursor: pointer;
                font-weight: 500;
            ">Close</button>
            <button class="btn-upgrade-demo" style="
                background: white;
                color: var(--primary-color);
                border: none;
                padding: 10px 20px;
                border-radius: 8px;
                cursor: pointer;
                font-weight: 600;
            ">Contact Admin</button>
        </div>
    `;
    
    document.body.appendChild(upgradePrompt);
    
    // Add event listeners
    upgradePrompt.querySelector('.btn-close-demo').addEventListener('click', () => {
        document.body.removeChild(upgradePrompt);
    });
    
    upgradePrompt.querySelector('.btn-upgrade-demo').addEventListener('click', () => {
        // Navigate to contact section
        const contactSection = document.getElementById('contact');
        if (contactSection) {
            // Remove active class from current nav link
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // Add active class to contact nav link
            document.querySelector('a[href="#contact"]').parentElement.classList.add('active');
            
            // Hide all sections and show contact
            document.querySelectorAll('.dashboard-section').forEach(section => {
                section.classList.remove('active');
            });
            contactSection.classList.add('active');
        }
        document.body.removeChild(upgradePrompt);
    });
}

// Contact form interaction (if contact form exists)
const contactCards = document.querySelectorAll('.contact-card');
contactCards.forEach(card => {
    card.addEventListener('click', () => {
        const contactType = card.querySelector('h3').textContent;
        handleContactClick(contactType);
    });
});

function handleContactClick(contactType) {
    switch(contactType) {
        case 'Phone Support':
            window.open('tel:+15551234567');
            break;
        case 'Email Support':
            window.open('mailto:support@gymmanagement.com');
            break;
        case 'Visit Us':
            // Could open maps or show address
            alert('Visit us at: 123 Fitness Street, City, State 12345');
            break;
        case 'Gym Hours':
            alert('Gym Hours:\n24/7 Access for Members\nStaff Available: 6AM-10PM');
            break;
    }
}

// ===========================================
// LOGOUT FUNCTIONALITY
// ===========================================
document.getElementById("logoutBtn").addEventListener("click", function(event) {
    event.preventDefault(); // stop normal link navigation
    
    debugLog('Logout button clicked');

    fetch("../controllers/AuthController.php?action=logout")
        .then(() => {
            debugLog('Logout successful, redirecting to login');
            // Force redirect to login page after logout
            window.location.href = "../views/auth/login.php";
        })
        .catch(err => {
            debugLog('Logout error:', err);
            console.error("Logout failed:", err);
        });
});

// ===========================================
// INITIALIZATION
// ===========================================
document.addEventListener('DOMContentLoaded', function() {
    debugLog('DOM Content Loaded - Initializing guest dashboard');
    
    // Set initial active section
    const homeSection = document.getElementById('home');
    if (homeSection) {
        homeSection.classList.add('active');
        debugLog('Home section activated');
    } else {
        debugLog('ERROR: Home section not found');
    }
    
    // Add loading animation
    const sections = document.querySelectorAll('.dashboard-section');
    debugLog('Setting up animations for sections:', sections.length);
    sections.forEach((section, index) => {
        section.style.animationDelay = `${index * 0.1}s`;
    });
    
    // Debug mode setup
    if (debugMode) {
        debugLog('Debug mode active - all features enabled');
        console.log('🔧 Available debug commands:');
        console.log('  - debugGuest.showAllSections()');
        console.log('  - debugGuest.hideAllSections()');
        console.log('  - debugGuest.showActiveSection()');
        console.log('  - debugGuest.testNavigation()');
    }
});

// Add scroll animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe elements for scroll animations
document.querySelectorAll('.stat-card, .plan-card, .trainer-card, .contact-card').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
});

// Add hover effects for interactive elements
document.querySelectorAll('.plan-card, .trainer-card, .contact-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});

// Newsletter signup (if implemented)
function handleNewsletterSignup(email) {
    // This would typically send data to a server
    console.log('Newsletter signup for:', email);
    alert('Thank you for your interest! You will be redirected to our registration page.');
    window.location.href = '../views/auth/register.php';
}

// Add keyboard navigation support
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        // Close any open modals
        const modals = document.querySelectorAll('.modal-overlay, .upgrade-prompt-popup');
        modals.forEach(modal => {
            if (modal.parentNode) {
                modal.parentNode.removeChild(modal);
            }
        });
    }
});

// Performance optimization: Lazy load images
const images = document.querySelectorAll('img[data-src]');
const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src;
            img.classList.remove('lazy');
            imageObserver.unobserve(img);
        }
    });
});

images.forEach(img => imageObserver.observe(img));

// ===========================================
// DEBUG UTILITIES
// ===========================================
if (debugMode) {
    // Add debug utilities to window object
    window.debugGuest = {
        // Show all sections for debugging
        showAllSections: function() {
            debugLog('Showing all sections for debugging');
            document.querySelectorAll('.dashboard-section').forEach(section => {
                section.style.display = 'block';
                section.style.border = '2px solid red';
                section.style.margin = '10px 0';
                section.style.padding = '10px';
                section.style.background = 'rgba(255, 0, 0, 0.1)';
            });
        },
        
        // Hide all sections
        hideAllSections: function() {
            debugLog('Hiding all sections');
            document.querySelectorAll('.dashboard-section').forEach(section => {
                section.style.display = 'none';
                section.style.border = 'none';
                section.style.background = 'transparent';
            });
        },
        
        // Show active section
        showActiveSection: function() {
            const active = document.querySelector('.dashboard-section.active');
            debugLog('Active section:', active ? active.id : 'None');
            if (active) {
                active.style.border = '3px solid green';
                active.style.background = 'rgba(0, 255, 0, 0.1)';
            }
            return active;
        },
        
        // Test navigation
        testNavigation: function() {
            debugLog('Testing navigation...');
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach((link, index) => {
                const section = link.dataset.section;
                const href = link.querySelector('a').getAttribute('href');
                debugLog(`Nav ${index}:`, { section, href });
            });
        },
        
        // Test all sections exist
        testSections: function() {
            debugLog('Testing all sections exist...');
            const sections = ['home', 'subscription', 'trainers', 'trial', 'contact'];
            sections.forEach(sectionId => {
                const element = document.getElementById(sectionId);
                debugLog(`Section ${sectionId}:`, element ? 'Found' : 'MISSING');
            });
        },
        
        // Get current state
        getCurrentState: function() {
            const activeSection = document.querySelector('.dashboard-section.active');
            const activeNav = document.querySelector('.nav-link.active');
            const sidebarState = document.querySelector('.sidebar').classList.contains('close') ? 'closed' : 'open';
            
            debugLog('Current state:', {
                activeSection: activeSection ? activeSection.id : 'None',
                activeNav: activeNav ? activeNav.dataset.section : 'None',
                sidebarState: sidebarState
            });
            
            return {
                activeSection: activeSection ? activeSection.id : null,
                activeNav: activeNav ? activeNav.dataset.section : null,
                sidebarState: sidebarState
            };
        }
    };
    
    debugLog('Debug utilities loaded. Use debugGuest.* to access them.');
}
