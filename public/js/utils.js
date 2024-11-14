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

      setTimeout(() => {
        confirmButton.focus();
      }, 200);
    };
  }

  window.showToast = function (message, type = "success", delay = 1000) {
    const toastId = `toast-${Date.now()}`;
    const icon = type === "success" ? "✔️" : "❌";
    const toastHTML = `
      <div id="${toastId}" class="toast fade" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="${delay}">
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
      input.focus();
    });
  };

  //if btnLogout exists
  if (btnLogout) {
    btnLogout.addEventListener("click", async function (event) {
      event.preventDefault();
      showConfirmationDialog("Are you sure you want to logout?", async () => {
        const response = await submitForm("/logout", {});
        if (response.ok) {
          window.location.reload();
        } else {
          const data = await response.json();
          showToast(data.message, "danger");
        }
      });
    });
  }

  window.formattedDate = function (date) {
    //input yyyy-mm-dd
    //return with format Dec, 10 2024
    const options = { year: "numeric", month: "short", day: "numeric" };
    return new Date(date).toLocaleDateString("en-US", options);
  };

  window.setFocustFirstInModal = function (modalBody) {
    //set focus to first input in modal
    setTimeout(() => {
      const firstVisibleInput = Array.from(
        modalBody.querySelectorAll("input")
      ).find((input) => input.offsetParent !== null);
      if (firstVisibleInput) firstVisibleInput.focus();
    }, 200);
  };

  window.getUserMenus = function () {
    const currentUrl = window.location.pathname;

    const menuElement = (menu) => {
      return `
      <li class="nav-item">
        <a class="nav-link ${currentUrl == menu.url ? "active" : ""}" href="${
        menu.url
      }" >
          <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/home -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg>
          </span>
          <span class="nav-link-title">
            ${menu.title}
          </span>
        </a>
      </li>
      `;
    };
    const menuElementWithChildren = (menu) => {
      let parentWrap = `
      <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false" >
            <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/package -->
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12l0 9" /><path d="M12 12l-8 -4.5" /><path d="M16 5.25l-8 4.5" /></svg>
            </span>
            <span class="nav-link-title">
              ${menu.name}
            </span>
          </a>
          <div class="dropdown-menu">
            <div class="dropdown-menu-columns">
              <div class="dropdown-menu-column">
      `;
      let isOpen = false;
      parentWrap += menu.children
        .map((children) => {
          if (currentUrl == children.url) {
            isOpen = true;
          }
          return `
          <a class="dropdown-item ${
            currentUrl == children.url ? "active" : ""
          }" href="${children.url}">
            ${children.name}
          </a>
          `;
        })
        .join("");

      if (isOpen) {
        //replace nav-item with nav-item active
        parentWrap = parentWrap.replace(
          "nav-item dropdown",
          "nav-item dropdown active"
        );
        //dropdown-menu to dropdown-menu show
        parentWrap = parentWrap.replace("dropdown-menu", "dropdown-menu show");
      }

      parentWrap += `
             </div>
            </div>
          </div>
        </li>
      `;

      return parentWrap;
    };
    //get user menus
    fetch("/menu/get-user-menus", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        const nav = document.querySelector("#sidebar-menu > ul.navbar-nav");
        console.log(data);
        nav.innerHTML = data
          .map((menu) => {
            if (menu.children.length > 0) {
              return menuElementWithChildren(menu);
            } else {
              return menuElement(menu);
            }
          })
          .join("");
      });
  };

  //get user menus when has element id sidebar-menu
  if (document.getElementById("sidebar-menu")) {
    getUserMenus();
  }
});
