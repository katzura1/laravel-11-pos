document.addEventListener("DOMContentLoaded", function () {
  const toastContainer = document.getElementById("toastContainer");
  const confirmationModalElement = document.getElementById("confirmationModal");
  const btnLogout = document.querySelector("a#btn-logout");

  //check if modal eleemnt exists
  if (confirmationModalElement) {
    const confirmationModal = new bootstrap.Modal(confirmationModalElement);
    const confirmButton = document.getElementById("confirmButton");

    window.showConfirmationDialog = function (message, onConfirm) {
      const modalBody = document.querySelector(
        "#confirmationModal .modal-body"
      );
      modalBody.textContent = message;

      confirmButton.onclick = function () {
        onConfirm();
        confirmationModal.hide();
      };

      confirmationModal.show();
    };
  }

  window.showToast = function (message, type = "success", delay = 5000) {
    const toastId = `toast-${Date.now()}`;
    const icon = type === "success" ? "✔️" : "❌";
    const bgColor = type === "success" ? "bg-success" : "bg-danger";
    const toastHTML = `
      <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="${delay}">
        <div class="toast-header">
          <strong class="me-auto">${icon} Notification</strong>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
          ${message}
        </div>
      </div>
    `;
    toastContainer.insertAdjacentHTML("beforeend", toastHTML);

    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement);
    toast.show();

    toastElement.addEventListener("hidden.bs.toast", () => {
      toastElement.remove();
    });
  };

  window.clearValidationErrors = function (element) {
    element.querySelectorAll(".is-invalid").forEach((input) => {
      input.classList.remove("is-invalid");
      const error = input.nextElementSibling;
      if (error?.classList.contains("invalid-feedback")) {
        error.remove();
      }
    });
  };

  window.submitForm = async function (url, data) {
    return fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        ...data,
        _token: document.querySelector('meta[name="csrf-token"]').content,
      }),
    });
  };

  window.validateForm = function (form) {
    return form.checkValidity() || (form.reportValidity(), false);
  };

  window.displayValidationErrors = function (element, errors) {
    Object.entries(errors).forEach(([field, [error]]) => {
      const input = element.querySelector(`[name="${field}"]`);
      input.classList.add("is-invalid");
      input.insertAdjacentHTML(
        "afterend",
        `<div class="invalid-feedback">${error}</div>`
      );
    });
  };

  btnLogout.addEventListener("click", async function (e) {
    e.preventDefault();

    const response = await submitForm("/logout", {});

    if (response.ok) {
      window.location.href = "/";
    } else {
      const data = await response.json();
      showToast(data.message ?? "Unknown Error", "error");
    }
  });
});
