class MenuTableManager {
  constructor() {
    this.searchInput = document.getElementById("search");
    this.pageLengthSelect = document.getElementById("page-length");
    this.loading = document.getElementById("loading");
    this.table = document.getElementById("menus-table");
    this.tbody = document.querySelector("#menus-table tbody");
    this.pagination = document.getElementById("pagination");
    this.currentPage = 1;
    this.modalMenus = new bootstrap.Modal(
      document.getElementById("modal-menus")
    );
    this.buttonAdd = document.getElementById("btn-add");
    this.menuForm = document.getElementById("menu-form");
    this.buttonSave = this.modalMenus._element.querySelector(
      ".modal-footer button#btn-save"
    );

    this.initializeEventListeners();
    this.fetchMenus();
  }

  initializeEventListeners() {
    this.searchInput.addEventListener("input", () => this.handleSearch());
    this.pageLengthSelect.addEventListener("change", () =>
      this.handlePageLengthChange()
    );
    this.buttonAdd.addEventListener(
      "click",
      async () => await this.handleAddMenu()
    );
    this.buttonSave.addEventListener("click", () => this.handleSaveMenu());
    this.menuForm.addEventListener("submit", (event) => {
      event.preventDefault();
      this.handleSaveMenu();
    });
  }

  handleSearch() {
    if (this.searchTimeout) {
      clearTimeout(this.searchTimeout);
    }

    this.searchTimeout = setTimeout(() => {
      this.currentPage = 1;
      this.fetchMenus();
    }, 200);
  }

  handlePageLengthChange() {
    this.currentPage = 1;
    this.fetchMenus();
  }

  async fetchMenus() {
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
      console.error("Error fetching menus:", error);
    } finally {
      this.hideLoading();
    }
  }

  async fetchParentSelect() {
    try {
      const response = await fetch("/menu/get-parents", {
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
      });
      const data = await response.json();

      this.renderParentSelect(data);
    } catch (error) {
      console.error("Error fetching parents:", error);
    }
  }

  renderParentSelect(parents) {
    const modalBody = this.modalMenus._element.querySelector(".modal-body");
    const parentIdSelect = modalBody.querySelector('select[name="parent_id"]');

    // Clear existing options
    parentIdSelect.innerHTML = "";

    // add default option
    parentIdSelect.innerHTML += `<option value="">Select parent</option>`;

    // Add parent options
    parentIdSelect.innerHTML += parents
      .map((parent) => `<option value="${parent.id}">${parent.name}</option>`)
      .join("");
  }

  buildUrl() {
    const params = new URLSearchParams({
      page: this.currentPage,
      length: this.pageLengthSelect.value,
      search: this.searchInput.value,
    });
    return `/menu/get?${params.toString()}`;
  }

  renderTable(menus) {
    this.tbody.innerHTML = menus.length
      ? menus.map((menu, key) => this.createMenuRow(menu, key)).join("")
      : this.createEmptyRow();

    // Add click event listeners to edit buttons
    this.tbody.querySelectorAll("button.btn-edit").forEach((button, index) => {
      button.addEventListener("click", async () => {
        // console.log("Edit menu:", menus[index]);
        await this.handleEditMenu(menus[index]);
      });
    });

    // Add click event listeners to delete buttons
    this.tbody
      .querySelectorAll("button.btn-delete")
      .forEach((button, index) => {
        button.addEventListener("click", () => {
          // console.log("Delete menu:", menus[index]);
          this.handleDeleteMenu(menus[index]);
        });
      });
  }

  createMenuRow(menu, key) {
    const rowNo =
      (this.currentPage - 1) * parseInt(this.pageLengthSelect.value) + key + 1;
    return `
      <tr>
        <td>${rowNo}</td>
        <td>${menu.name}</td>
        <td>${menu.parent_name}</td>
        <td>${menu.position}</td>
        <td>
          <button class="btn btn-primary btn-edit">Edit</button>
          <button class="btn btn-danger btn-delete">Delete</button>
        </td>
      </tr>
    `;
  }

  createEmptyRow() {
    return `
      <tr>
        <td colspan="5" class="text-center">No menus found</td>
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
        this.fetchMenus();
      });
    });
  }

  async handleAddMenu() {
    const modalBody = this.modalMenus._element.querySelector(".modal-body");
    const idInput = modalBody.querySelector('input[name="id"]');

    //set value
    idInput.value = "";

    //fetch parent select
    this.fetchParentSelect();

    //show modal
    this.modalMenus.show();

    //set focus to first input in modal
    setTimeout(() => {
      const firstVisibleInput = Array.from(
        modalBody.querySelectorAll("input")
      ).find((input) => input.offsetParent !== null);
      if (firstVisibleInput) firstVisibleInput.focus();
    }, 200);
  }

  async handleEditMenu(menu) {
    const modalBody = this.modalMenus._element.querySelector(".modal-body");
    const idInput = modalBody.querySelector('input[name="id"]');
    const nameInput = modalBody.querySelector('input[name="name"]');
    const parentIdSelect = modalBody.querySelector('select[name="parent_id"]');
    const positionInput = modalBody.querySelector('input[name="position"]');
    const urlInput = modalBody.querySelector('input[name="url"]');

    //fetch parent select
    await this.fetchParentSelect();

    //set value
    idInput.value = menu.id;
    nameInput.value = menu.name;
    parentIdSelect.value = menu.parent_id;
    positionInput.value = menu.position;
    urlInput.value = menu.url;

    //show modal
    this.modalMenus.show();

    //set focus to first input in modal
    setTimeout(() => {
      const firstVisibleInput = Array.from(
        modalBody.querySelectorAll("input")
      ).find((input) => input.offsetParent !== null);
      if (firstVisibleInput) firstVisibleInput.focus();
    }, 200);
  }

  handleDeleteMenu(menu) {
    showConfirmationDialog(
      `Are you sure you want to delete ${menu.name}?`,
      () => this.deleteMenu(menu.id)
    );
  }

  async deleteMenu(menuId) {
    try {
      const url = `/menu/destroy`;
      const csrfToken = document.querySelector(
        'meta[name="csrf-token"]'
      ).content;

      const data = {
        id: menuId,
        _token: csrfToken,
        _method: "DELETE",
      };

      const response = await submitForm(url, data);

      if (response.ok) {
        await this.handleSuccessfulDelete();
      } else {
        await this.handleDeleteError(response);
      }
    } catch (error) {
      console.error("Error deleting menu:", error);
    }
  }

  async handleSaveMenu() {
    if (!validateForm(this.menuForm)) return;

    const formData = this.getFormData();
    const url = formData.id ? "/menu/put" : "/menu/store";

    const modalBody = this.modalMenus._element.querySelector(".modal-body");
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

  getFormData() {
    const modalBody = this.modalMenus._element.querySelector(".modal-body");
    const inputs = {
      id: modalBody.querySelector('input[name="id"]').value,
      name: modalBody.querySelector('input[name="name"]').value,
      parent_id: modalBody.querySelector('select[name="parent_id"]').value,
      position: modalBody.querySelector('input[name="position"]').value,
      url: modalBody.querySelector('input[name="url"]').value,
      _token: document.querySelector('meta[name="csrf-token"]').content,
      _method: modalBody.querySelector('input[name="id"]').value
        ? "PUT"
        : "POST",
    };

    return inputs;
  }

  async handleSuccessfulSave() {
    this.modalMenus.hide();
    this.fetchMenus();
    this.resetForm();
    showToast("Menu saved successfully");
  }

  async handleSuccessfulDelete() {
    this.fetchMenus();
    showToast("Menu saved deleted");
  }

  resetForm() {
    const inputs =
      this.modalMenus._element.querySelectorAll(".modal-body input");
    inputs.forEach((input) => (input.value = ""));
  }

  async handleSaveError(response) {
    const data = await response.json();

    if (data.message) {
      showToast(
        `Failed save menu: ${data.message ?? "Unknown error"}`,
        "error"
      );
    }

    if (data.errors) {
      const modalBody = this.modalMenus._element.querySelector(".modal-body");
      displayValidationErrors(modalBody, data.errors);
    }
  }

  async handleDeleteError(response) {
    const data = await response.json();

    if (data.message) {
      showToast(
        `Failed delete menu: ${data.message ?? "Unknown error"}`,
        "error"
      );
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

document.addEventListener("DOMContentLoaded", () => new MenuTableManager());
