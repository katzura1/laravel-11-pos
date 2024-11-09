class LoginPage {
  constructor() {
    this.btnShowPassword = document.querySelector("#btn-show-password");
    this.password = document.querySelector("#password");
    this.iconPassword =
      this.password.nextElementSibling.querySelector("img.icon");
    this.username = document.querySelector("#username");
    this.loginForm = document.querySelector("#form-login");
    this.btnSignIn = document.querySelector("#btn-sign-in");

    this.initializeEventListeners();
  }

  initializeEventListeners() {
    this.btnShowPassword.addEventListener(
      "click",
      this.togglePassword.bind(this)
    );

    this.loginForm.addEventListener("submit", (event) => {
      event.preventDefault();
      this.handleLogin();
    });
  }

  togglePassword() {
    const isPasswordHidden = this.password.type === "password";
    this.password.type = isPasswordHidden ? "text" : "password";
    this.password.placeholder = isPasswordHidden
      ? "Hide password"
      : "Show password";
    this.btnShowPassword.title = isPasswordHidden
      ? "Hide password"
      : "Show password";
    this.btnShowPassword.setAttribute(
      "data-bs-original-title",
      this.btnShowPassword.title
    );
    this.btnShowPassword.setAttribute("aria-label", this.btnShowPassword.title);
    this.iconPassword.src = isPasswordHidden
      ? "/static/svg/outline/eye-cancel.svg"
      : "/static/svg/outline/eye.svg";
  }

  async handleLogin() {
    if (!validateForm(this.loginForm)) {
      return;
    }

    const formData = this.getFormData();
    const url = "/authenticate";

    this.btnShowPassword.parentElement.style.display = "block";
    clearValidationErrors(this.loginForm);
    this.btnSignIn.disabled = true;

    try {
      const response = await submitForm(url, formData);

      if (response.ok) {
        this.handleSuccessfulLogin();
      } else {
        await this.handleLoginError(response);
        this.btnShowPassword.parentElement.style.display = "none";
      }
    } finally {
      this.btnSignIn.removeAttribute("disabled");
    }
  }

  getFormData() {
    const inputs = {
      username: this.username.value,
      password: this.password.value,
      _token: document.querySelector('meta[name="csrf-token"]').content,
    };

    return inputs;
  }

  handleSuccessfulLogin() {
    showToast("Login successful");
    setTimeout(() => {
      window.location.href = "/dashboard";
    }, 300);
  }

  async handleLoginError(response) {
    const data = await response.json();

    if (data.message) {
      showToast(`Failed login: ${data.message ?? "Unknown error"}`, "error");
    }

    if (data.errors) {
      displayValidationErrors(this.loginForm, data.errors);
    }
  }
}

document.addEventListener("DOMContentLoaded", function () {
  new LoginPage();
});
