const passwordInput = document.getElementById("password");
const strengthText = document.getElementById("strength-text");
const strengthBar = document.getElementById("strength-bar");

if (passwordInput && strengthText && strengthBar) {
    passwordInput.addEventListener("input", () => {
        const pwd = passwordInput.value;

        let strength = 0;

        if (pwd.length >= 6) strength++;
        if (/[A-Z]/.test(pwd)) strength++;
        if (/[0-9]/.test(pwd)) strength++;
        if (/[^A-Za-z0-9]/.test(pwd)) strength++;

        if (strength <= 1) {
            strengthText.textContent = "Strength: Weak";
            strengthBar.style.background = "red";
            strengthBar.style.width = "25%";
        } else if (strength === 2) {
            strengthText.textContent = "Strength: Medium";
            strengthBar.style.background = "orange";
            strengthBar.style.width = "50%";
        } else if (strength === 3) {
            strengthText.textContent = "Strength: Good";
            strengthBar.style.background = "blue";
            strengthBar.style.width = "75%";
        } else {
            strengthText.textContent = "Strength: Strong";
            strengthBar.style.background = "green";
            strengthBar.style.width = "100%";
        }
    });
}

// Password toggle functionality
const togglePassword = document.getElementById("toggle-password");
const loginPassword = document.getElementById("login-password");

if (togglePassword && loginPassword) {
    togglePassword.addEventListener("click", () => {
        const type = loginPassword.getAttribute('type') === 'password' ? 'text' : 'password';
        loginPassword.setAttribute('type', type);
        togglePassword.textContent = type === 'password' ? 'Show' : 'Hide';
    });
}