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

    this.initializeEventListeners();
    this.fetchUsers();
  }

  initializeEventListeners() {
    this.searchInput.addEventListener("input", () => this.handleSearch());
    this.pageLengthSelect.addEventListener("change", () =>
      this.handlePageLengthChange()
    );
    this.buttonAdd.addEventListener("click", () => this.handleAddUser());
    this.buttonSave.addEventListener("click", () => this.handleSaveUser());
    this.userForm.addEventListener("submit", (event) => {
      event.preventDefault();
      this.handleSaveUser();
    });
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
        console.log("Edit user:", users[index]);
        this.handleEditUser(users[index]);
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
        <td>${user.role}</td>
        <td>
          <button class="btn btn-primary btn-edit">Edit</button>
        </td>
      </tr>
    `;
  }

  createEmptyRow() {
    return `
      <tr>
        <td colspan="5" class="text-center">No users found</td>
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

    //set value
    idInput.value = "";

    //set required to password
    modalBody
      .querySelector('input[name="password"]')
      .setAttribute("required", "true");
    modalBody.querySelector('label[for="password"]').classList.add("required");

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

  async handleSaveUser() {
    if (!this.validateForm()) return;

    const formData = this.getFormData();
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

  validateForm() {
    return (
      this.userForm.checkValidity() || (this.userForm.reportValidity(), false)
    );
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
      showToast(`Error save user: ${data.message}`);
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
