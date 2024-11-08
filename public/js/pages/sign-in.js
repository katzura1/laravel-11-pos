document.addEventListener("DOMContentLoaded", function () {
  const btnShowPassword = document.querySelector("#btn-show-password");
  const password = document.querySelector("#password");
  const iconPassword = password.nextElementSibling.querySelector("img.icon");

  btnShowPassword.addEventListener("click", function () {
    const isPasswordHidden = password.type === "password";
    password.type = isPasswordHidden ? "text" : "password";
    password.placeholder = isPasswordHidden ? "Hide password" : "Show password";
    this.title = isPasswordHidden ? "Hide password" : "Show password";
    this.setAttribute("data-bs-original-title", this.title);
    this.setAttribute("aria-label", this.title);
    iconPassword.src = isPasswordHidden
      ? "/static/svg/outline/eye-cancel.svg"
      : "/static/svg/outline/eye.svg";
  });
});
