class UserTableManager {
  constructor() {
    this.searchInput = document.getElementById("search");
    this.pageLengthSelect = document.getElementById("page-length");
    this.loading = document.getElementById("loading");
    this.table = document.getElementById("users-table");
    this.tbody = document.querySelector("#users-table tbody");
    this.pagination = document.getElementById("pagination");
    this.currentPage = 1;
    this.modalUsers = new bootstrap.Modal(
      document.getElementById("modal-users")
    );
    this.buttonAdd = document.getElementById("btn-add");
    this.userForm = document.getElementById("user-form");
    this.buttonSave = this.modalUsers._element.querySelector(
      ".modal-footer button#btn-save"
    );
    this.checkBoxUserOutletAll = this.modalUsers._element.querySelector(
      'input[name="check_all"]'
    );

    this.outletForm = document.getElementById("outlet-form");
    this.modalOutlets = new bootstrap.Modal(
      document.getElementById("modal-outlets")
    );
    this.buttonSaveOutlet = this.modalOutlets._element.querySelector(
      ".modal-footer button#btn-save"
    );
    this.checkBoxOutletAll = this.modalOutlets._element.querySelector(
      'input[name="check_all"]'
    );

    this.initializeEventListeners();
    this.fetchUsers();
  }

  initializeEventListeners() {
    this.searchInput.addEventListener("input", () => this.handleSearch());
    this.pageLengthSelect.addEventListener("change", () =>
      this.handlePageLengthChange()
    );
    this.buttonAdd.addEventListener("click", () => this.handleAddUser());

    this.checkBoxUserOutletAll.addEventListener("change", () =>
      this.handleCheckAll(this.checkBoxUserOutletAll, this.userForm)
    );
    this.buttonSave.addEventListener("click", () => this.handleSaveUser());
    this.userForm.addEventListener("submit", (event) => {
      event.preventDefault();
      this.handleSaveUser();
    });

    this.checkBoxOutletAll.addEventListener("change", () =>
      this.handleCheckAll(this.checkBoxOutletAll, this.outletForm)
    );
    this.buttonSaveOutlet.addEventListener("click", () =>
      this.handleSaveOutletUser()
    );
    this.outletForm.addEventListener("submit", (event) => {
      event.preventDefault();
      this.handleSaveOutletUser();
    });
  }

  handleCheckAll(checkbox, form) {
    const isChecked = checkbox.checked;
    form
      .querySelectorAll('input[name="outlet_id[]"]')
      .forEach((checkbox) => (checkbox.checked = isChecked));
  }

  handleSearch() {
    if (this.searchTimeout) {
      clearTimeout(this.searchTimeout);
    }

    this.searchTimeout = setTimeout(() => {
      this.currentPage = 1;
      this.fetchUsers();
    }, 200);
  }

  handlePageLengthChange() {
    this.currentPage = 1;
    this.fetchUsers();
  }

  async fetchUsers() {
    try {
      this.showLoading();
      const response = await fetch(this.buildUrl(), {
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
      });
      const data = await response.json();

      this.renderTable(data.data);
      this.renderPagination(data);
    } catch (error) {
      console.error("Error fetching users:", error);
    } finally {
      this.hideLoading();
    }
  }

  buildUrl() {
    const params = new URLSearchParams({
      page: this.currentPage,
      length: this.pageLengthSelect.value,
      search: this.searchInput.value,
    });
    return `/admin/get?${params.toString()}`;
  }

  renderTable(users) {
    this.tbody.innerHTML = users.length
      ? users.map((user, key) => this.createUserRow(user, key)).join("")
      : this.createEmptyRow();

    // Add click event listeners to edit buttons
    this.tbody.querySelectorAll("button.btn-edit").forEach((button, index) => {
      button.addEventListener("click", () => {
        this.handleEditUser(users[index]);
      });
    });

    this.tbody
      .querySelectorAll("button.btn-edit-outlet")
      .forEach((button, index) => {
        button.addEventListener("click", () => {
          this.handleEdiOutlettUser(users[index]);
        });
      });
  }

  createUserRow(user, key) {
    const rowNo =
      (this.currentPage - 1) * parseInt(this.pageLengthSelect.value) + key + 1;
    return `
      <tr>
        <td>${rowNo}</td>
        <td>${user.name}</td>
        <td>${user.username}</td>
        <td>
          <button class="btn btn-primary btn-edit">Edit</button>
          <button class="btn btn-primary btn-edit-outlet">Outlet</button>
          <button class="btn btn-primary btn-edit-menu">Menu</button>
        </td>
      </tr>
    `;
  }

  createEmptyRow() {
    const countTh = this.table.querySelectorAll("thead th").length;
    return `
      <tr>
        <td colspan="${countTh}" class="text-center">No users found</td>
      </tr>
    `;
  }

  renderPagination(data) {
    const paginationInfo = this.createPaginationInfo(data);
    const paginationLinks = this.createPaginationLinks(data);

    this.pagination.innerHTML = `
      <div class="d-flex w-100 justify-content-between align-items-center">
        <div>${paginationInfo}</div>
        <nav>
          <ul class="pagination mb-0">
            ${paginationLinks}
          </ul>
        </nav>
      </div>
    `;
    this.addPaginationEventListeners();
  }

  createPaginationInfo(data) {
    const start = (data.current_page - 1) * data.per_page + 1;
    const end = Math.min(start + data.per_page - 1, data.total);
    return `Showing ${start} to ${end} of ${data.total} entries`;
  }

  createPaginationLinks(data) {
    const prevButton = this.createNavigationButton(
      data.current_page === 1,
      data.current_page - 1,
      "chevron-left",
      "Previous"
    );

    const nextButton = this.createNavigationButton(
      data.current_page === data.last_page,
      data.current_page + 1,
      "chevron-right",
      "Next",
      true
    );

    const pageNumbers = Array.from({ length: data.last_page }, (_, i) => i + 1)
      .map((page) => this.createPaginationLink(page, data.current_page))
      .join("");

    return `${prevButton}${pageNumbers}${nextButton}`;
  }

  createNavigationButton(isDisabled, page, icon, text, isNext = false) {
    const iconHtml = `<img src="/static/svg/outline/${icon}.svg">`;
    const content = isNext ? `${text} ${iconHtml}` : `${iconHtml} ${text}`;

    return `
      <li class="page-item ${isDisabled ? "disabled" : ""}">
        <a class="page-link" href="#" data-page="${page}">${content}</a>
      </li>
    `;
  }

  createPaginationLink(page, currentPage) {
    return `
      <li class="page-item ${page === currentPage ? "active" : ""}">
        <a class="page-link" href="#" data-page="${page}">${page}</a>
      </li>
    `;
  }

  addPaginationEventListeners() {
    document.querySelectorAll("#pagination .page-link").forEach((link) => {
      link.addEventListener("click", (event) => {
        event.preventDefault();
        this.currentPage = parseInt(event.target.getAttribute("data-page"));
        this.fetchUsers();
      });
    });
  }

  handleAddUser() {
    const modalBody = this.modalUsers._element.querySelector(".modal-body");
    const idInput = modalBody.querySelector('input[name="id"]');
    const checkBoxOutlet = modalBody.querySelector("#checkbox-outlet");
    const outletIdCheckbox = modalBody.querySelectorAll(
      'input[name="outlet_id[]"]'
    );

    //set value
    idInput.value = "";

    //set required to password
    modalBody
      .querySelector('input[name="password"]')
      .setAttribute("required", "true");
    modalBody.querySelector('label[for="password"]').classList.add("required");

    //show checkbox outlet
    checkBoxOutlet.style.display = "block";
    this.checkBoxUserOutletAll.checked = false;
    //set checked checkbox to false
    outletIdCheckbox.forEach((checkbox) => (checkbox.checked = false));

    //show modal
    this.modalUsers.show();

    //set focus to first input in modal
    setTimeout(() => {
      const firstVisibleInput = Array.from(
        modalBody.querySelectorAll("input")
      ).find((input) => input.offsetParent !== null);
      if (firstVisibleInput) firstVisibleInput.focus();
    }, 200);
  }

  handleEditUser(user) {
    const modalBody = this.modalUsers._element.querySelector(".modal-body");
    const idInput = modalBody.querySelector('input[name="id"]');
    const nameInput = modalBody.querySelector('input[name="name"]');
    const usernameInput = modalBody.querySelector('input[name="username"]');
    const checkBoxOutlet = modalBody.querySelector("#checkbox-outlet");
    //set value
    idInput.value = user.id;
    nameInput.value = user.name;
    usernameInput.value = user.username;

    //set not required to password
    modalBody
      .querySelector('input[name="password"]')
      .removeAttribute("required");
    modalBody
      .querySelector('label[for="password"]')
      .classList.remove("required");

    //hide checkbox outlet
    checkBoxOutlet.style.display = "none";

    //show modal
    this.modalUsers.show();

    //set focus to first input in modal
    setTimeout(() => {
      const firstVisibleInput = Array.from(
        modalBody.querySelectorAll("input")
      ).find((input) => input.offsetParent !== null);
      if (firstVisibleInput) firstVisibleInput.focus();
    }, 200);
  }

  handleEdiOutlettUser(user) {
    const modalBody = this.modalOutlets._element.querySelector(".modal-body");
    const userIdInput = modalBody.querySelector('input[name="user_id"]');
    const nameInput = modalBody.querySelector('input[name="name"]');
    const outletIdCheckbox = modalBody.querySelectorAll(
      'input[name="outlet_id[]"]'
    );

    //set value
    userIdInput.value = user.id;
    nameInput.value = user.name;
    //clear all checkbox
    this.checkBoxOutletAll.checked = false;
    outletIdCheckbox.forEach((checkbox) => (checkbox.checked = false));
    const outletIds = user.outlet_user
      ? user.outlet_user.map((outlet) => outlet.outlet_id)
      : [];
    //set checked checkbox
    outletIds.forEach((outletId) => {
      const checkbox = modalBody.querySelector(
        `input[name="outlet_id[]"][value="${outletId}"]`
      );
      if (checkbox) checkbox.checked = true;
    });
    //show modal
    this.modalOutlets.show();
  }

  async handleSaveUser() {
    if (!this.validateForm(this.userForm)) return;

    const formData = this.getFormData();
    if (!formData.id) {
      if (!this.validateCheckboxes(this.userForm, "outlet_id[]")) return;
      formData.outlet_id = Array.from(
        this.userForm.querySelectorAll(`input[name="outlet_id[]"]:checked`)
      ).map((checkbox) => checkbox.value);
    }
    const url = formData.id ? "/admin/put" : "/admin/store";

    const modalBody = this.modalUsers._element.querySelector(".modal-body");
    clearValidationErrors(modalBody);
    this.buttonSave.disabled = true;

    try {
      const response = await submitForm(url, formData);

      if (response.ok) {
        await this.handleSuccessfulSave();
      } else {
        await this.handleSaveError(response);
      }
    } finally {
      this.buttonSave.removeAttribute("disabled");
    }
  }

  async handleSaveOutletUser() {
    if (!this.validateForm(this.outletForm)) return;
    if (!this.validateCheckboxes(this.outletForm)) return;

    const modalBody = this.modalOutlets._element.querySelector(".modal-body");
    const inputs = {
      user_id: modalBody.querySelector('input[name="user_id"]').value,
      outlet_id: Array.from(
        modalBody.querySelectorAll(`input[name="outlet_id[]"]:checked`)
      ).map((checkbox) => checkbox.value),
    };

    const url = "/admin/store-outlet";

    const response = await submitForm(url, inputs);

    if (response.ok) {
      this.modalOutlets.hide();
      showToast("Outlet saved successfully");
      this.fetchUsers();
    } else {
      const data = await response.json();
      if (data.message) {
        showToast(`Error save outlet: ${data.message}`, "error");
      }
    }
  }

  validateCheckboxes(form, checkboxName = "outlet_id[]") {
    const checkboxes = form.querySelectorAll(`input[name="${checkboxName}"]`);
    const isChecked = Array.from(checkboxes).some(
      (checkbox) => checkbox.checked
    );

    if (!isChecked) {
      showToast("Please select at least one option", "error");
    }

    return isChecked;
  }

  validateForm(form) {
    return form.checkValidity() || (form.reportValidity(), false);
  }

  getFormData() {
    const modalBody = this.modalUsers._element.querySelector(".modal-body");
    const inputs = {
      id: modalBody.querySelector('input[name="id"]').value,
      name: modalBody.querySelector('input[name="name"]').value,
      username: modalBody.querySelector('input[name="username"]').value,
      password: modalBody.querySelector('input[name="password"]').value,
      _token: document.querySelector('meta[name="csrf-token"]').content,
      _method: modalBody.querySelector('input[name="id"]').value
        ? "PUT"
        : "POST",
    };

    if (!inputs.password) delete inputs.password;
    return inputs;
  }

  async handleSuccessfulSave() {
    this.modalUsers.hide();
    this.fetchUsers();
    this.resetForm();
    showToast("User saved successfully");
  }

  resetForm() {
    const inputs =
      this.modalUsers._element.querySelectorAll(".modal-body input");
    inputs.forEach((input) => (input.value = ""));
  }

  async handleSaveError(response) {
    const data = await response.json();

    if (data.message) {
      showToast(`Error save user: ${data.message}`, "error");
    }

    if (data.errors) {
      const modalBody = this.modalUsers._element.querySelector(".modal-body");
      displayValidationErrors(modalBody, data.errors);
    }
  }

  showLoading() {
    this.loading.style.display = "block";
    this.table.style.display = "none";
  }

  hideLoading() {
    this.loading.style.display = "none";
    this.table.style.display = "table";
  }
}

document.addEventListener("DOMContentLoaded", () => new UserTableManager());
