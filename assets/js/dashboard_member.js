const body= document.querySelector("body"),
    sidebar = body.querySelector(".sidebar"),
    toggle = body.querySelector(".toggle");

// Make sidebar scrollable
if (sidebar) sidebar.style.overflowY = "auto";

// Ensure sidebar only toggles on arrow (toggle) click
if (toggle) {
    toggle.addEventListener("click", (e) => {
        e.stopPropagation();
        sidebar.classList.toggle("close");
    });
}

// Prevent nav link clicks from toggling sidebar
sidebar.querySelectorAll('.nav-link a').forEach(link => {
    link.addEventListener('click', (e) => {
        e.stopPropagation();
    });
});

// Loop through all dropdown buttons to toggle between hiding and showing its dropdown content
var dropdown = document.getElementsByClassName("dropdown-btn");
var i;

for (i = 0; i < dropdown.length; i++) {
    dropdown[i].addEventListener("click", function() {
        this.classList.toggle("active");
        // Find the dropdown container that comes after this button's parent li
        var parentLi = this.closest('li');
        var dropdownContent = parentLi.nextElementSibling;
        console.log("Dropdown content:", dropdownContent);
        if (dropdownContent.style.opacity === "1") {
            // Animate out first, then hide
            dropdownContent.style.opacity = "0";
            dropdownContent.style.transform = "translateY(-10px)";
            setTimeout(() => {
                dropdownContent.style.display = "none";
            }, 400); // Wait for animation to complete
            console.log("Hiding dropdown");
        } else {
            dropdownContent.style.display = "block";
            // Use setTimeout to allow display to take effect before animating
            setTimeout(() => {
                dropdownContent.style.opacity = "1";
                dropdownContent.style.transform = "translateY(0)";
            }, 10);
            console.log("Showing dropdown");
        }
    });
}
document.getElementById("logoutBtn").addEventListener("click", function(event) {
    event.preventDefault(); // stop normal link navigation

    fetch("../../controllers/AuthController.php?action=logout")
        .then(() => {
            // Force redirect to login page after logout
            window.location.href = "../../views/auth/login.php";
        })
        .catch(err => {
            console.error("Logout failed:", err);
        });
});