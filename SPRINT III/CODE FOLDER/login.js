// ==== PASSWORD VISIBILITY TOGGLE ====
const pwdField = document.getElementById("login-password");
const toggleBtn = document.getElementById("toggle-password");

if (toggleBtn) {
    toggleBtn.addEventListener("click", () => {
        pwdField.type = pwdField.type === "password" ? "text" : "password";
        toggleBtn.textContent = pwdField.type === "password" ? "Show" : "Hide";
    });
}

// ==== BASIC FRONTEND VALIDATION ====
const form = document.getElementById("loginForm");

form.addEventListener("submit", function(e) {
    const email = form.email.value.trim();
    const password = form.password.value.trim();

    if (!email || !password) {
        e.preventDefault();
        alert("Please fill in all fields.");
        return;
    }

    if (!email.includes("@")) {
        e.preventDefault();
        alert("Please enter a valid email address.");
        return;
    }

    if (password.length < 6) {
        e.preventDefault();
        alert("Password must be at least 6 characters.");
        return;
    }
});

// ==== INPUT FOCUS STYLE EFFECTS ====
const inputs = document.querySelectorAll(".input-field");

inputs.forEach(input => {
    input.addEventListener("focus", () => {
        input.style.borderColor = "#6A0DAD";
        input.style.boxShadow = "0 0 6px rgba(106,13,173,0.3)";
    });
    input.addEventListener("blur", () => {
        input.style.borderColor = "#ccc";
        input.style.boxShadow = "none";
    });
});

// ==== BUTTON HOVER EFFECT ====
const btn = document.querySelector(".btn-main");

if (btn) {
    btn.addEventListener("mouseover", () => {
        btn.style.transform = "scale(1.05)";
    });
    btn.addEventListener("mouseout", () => {
        btn.style.transform = "scale(1)";
    });
}
