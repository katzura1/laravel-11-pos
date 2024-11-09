<!-- Libs JS -->
<!-- Tabler Core -->
<script src="/js/tabler.min.js" defer></script>
<script src="/js/demo.min.js" defer></script>
<script src="/js/theme.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const toastContainer = document.getElementById('toastContainer');

    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    const confirmButton = document.getElementById('confirmButton');

    window.showToast = function(message, delay = 5000) {
        const toastId = `toast-${Date.now()}`;
        const toastHTML = `
            <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="${delay}">
                <div class="toast-header">
                    <strong class="me-auto">Notification</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;
        toastContainer.insertAdjacentHTML('beforeend', toastHTML);

        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement);
        toast.show();

        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    window.clearValidationErrors = function(element) {
      element.querySelectorAll(".is-invalid").forEach((input) => {
        input.classList.remove("is-invalid");
        const error = input.nextElementSibling;
        if (error?.classList.contains("invalid-feedback")) {
          error.remove();
        }
      });
    }

    window.submitForm = async function (url, data) {
      return fetch(url, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify(data),
      });
    }

    window.displayValidationErrors = function(element, errors) {
      Object.entries(errors).forEach(([field, [error]]) => {
        const input = element.querySelector(`input[name="${field}"]`);
        input.classList.add("is-invalid");
        input.insertAdjacentHTML(
          "afterend",
          `<div class="invalid-feedback">${error}</div>`
        );
      });
    }

    window.showConfirmationDialog = function(message, onConfirm) {
        const modalBody = document.querySelector('#confirmationModal .modal-body');
        modalBody.textContent = message;

        confirmButton.onclick = function() {
            onConfirm();
            confirmationModal.hide();
        };

        confirmationModal.show();
    }
  });
</script>