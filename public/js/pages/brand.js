class BrandTableManager {
  constructor() {
    this.searchInput = document.getElementById("search");
    this.pageLengthSelect = document.getElementById("page-length");
    this.loading = document.getElementById("loading");
    this.table = document.getElementById("brands-table");
    this.tbody = document.querySelector("#brands-table tbody");
    this.pagination = document.getElementById("pagination");
    this.currentPage = 1;
    this.modalBrands = new bootstrap.Modal(
      document.getElementById("modal-brands")
    );
    this.buttonAdd = document.getElementById("btn-add");
    this.brandForm = document.getElementById("brand-form");
    this.buttonSave = this.modalBrands._element.querySelector(
      ".modal-footer button#btn-save"
    );

    this.initializeEventListeners();
    this.fetchBrands();
  }

  initializeEventListeners() {
    this.searchInput.addEventListener("input", () => this.handleSearch());
    this.pageLengthSelect.addEventListener("change", () =>
      this.handlePageLengthChange()
    );
    this.buttonAdd.addEventListener("click", () => this.handleAddBrand());
    this.buttonSave.addEventListener("click", () => this.handleSaveBrand());
    this.brandForm.addEventListener("submit", (event) => {
      event.preventDefault();
      this.handleSaveBrand();
    });
  }

  handleSearch() {
    if (this.searchTimeout) {
      clearTimeout(this.searchTimeout);
    }

    this.searchTimeout = setTimeout(() => {
      this.currentPage = 1;
      this.fetchBrands();
    }, 200);
  }

  handlePageLengthChange() {
    this.currentPage = 1;
    this.fetchBrands();
  }

  async fetchBrands() {
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
      console.error("Error fetching brands:", error);
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
    return `/brand/get?${params.toString()}`;
  }

  renderTable(brands) {
    this.tbody.innerHTML = brands.length
      ? brands.map((brand, key) => this.createBrandRow(brand, key)).join("")
      : this.createEmptyRow();

    // Add click event listeners to edit buttons
    this.tbody.querySelectorAll("button.btn-edit").forEach((button, index) => {
      button.addEventListener("click", () => {
        // console.log("Edit brand:", brands[index]);
        this.handleEditBrand(brands[index]);
      });
    });

    // Add click event listeners to delete buttons
    this.tbody
      .querySelectorAll("button.btn-delete")
      .forEach((button, index) => {
        button.addEventListener("click", () => {
          // console.log("Delete brand:", brands[index]);
          this.handleDeleteBrand(brands[index]);
        });
      });
  }

  createBrandRow(brand, key) {
    const rowNo =
      (this.currentPage - 1) * parseInt(this.pageLengthSelect.value) + key + 1;
    return `
      <tr>
        <td>${rowNo}</td>
        <td>${brand.name}</td>
        <td>
          <button class="btn btn-primary btn-edit">Edit</button>
          <button class="btn btn-danger btn-delete">Delete</button>
        </td>
      </tr>
    `;
  }

  createEmptyRow() {
    const countTh = this.table.querySelectorAll("thead th").length;
    return `
      <tr>
        <td colspan="${countTh}" class="text-center">No brands found</td>
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
        this.fetchBrands();
      });
    });
  }

  handleAddBrand() {
    const modalBody = this.modalBrands._element.querySelector(".modal-body");
    const idInput = modalBody.querySelector('input[name="id"]');

    //set value
    idInput.value = "";

    //show modal
    this.modalBrands.show();

    //set focus to first input in modal
    setTimeout(() => {
      const firstVisibleInput = Array.from(
        modalBody.querySelectorAll("input")
      ).find((input) => input.offsetParent !== null);
      if (firstVisibleInput) firstVisibleInput.focus();
    }, 200);
  }

  handleEditBrand(brand) {
    const modalBody = this.modalBrands._element.querySelector(".modal-body");
    const idInput = modalBody.querySelector('input[name="id"]');
    const nameInput = modalBody.querySelector('input[name="name"]');
    //set value
    idInput.value = brand.id;
    nameInput.value = brand.name;

    //show modal
    this.modalBrands.show();

    //set focus to first input in modal
    setTimeout(() => {
      const firstVisibleInput = Array.from(
        modalBody.querySelectorAll("input")
      ).find((input) => input.offsetParent !== null);
      if (firstVisibleInput) firstVisibleInput.focus();
    }, 200);
  }

  handleDeleteBrand(brand) {
    showConfirmationDialog(
      `Are you sure you want to delete ${brand.name}?`,
      () => this.deleteBrand(brand.id)
    );
  }

  async deleteBrand(brandId) {
    try {
      const url = `/brand/destroy`;
      const csrfToken = document.querySelector(
        'meta[name="csrf-token"]'
      ).content;

      const data = {
        id: brandId,
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
      console.error("Error deleting brand:", error);
    }
  }

  async handleSaveBrand() {
    if (!validateForm(this.brandForm)) return;

    const formData = this.getFormData();
    const url = formData.id ? "/brand/put" : "/brand/store";

    const modalBody = this.modalBrands._element.querySelector(".modal-body");
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
    const modalBody = this.modalBrands._element.querySelector(".modal-body");
    const inputs = {
      id: modalBody.querySelector('input[name="id"]').value,
      name: modalBody.querySelector('input[name="name"]').value,
      _token: document.querySelector('meta[name="csrf-token"]').content,
      _method: modalBody.querySelector('input[name="id"]').value
        ? "PUT"
        : "POST",
    };

    return inputs;
  }

  async handleSuccessfulSave() {
    this.modalBrands.hide();
    this.fetchBrands();
    this.resetForm();
    showToast("Brand saved successfully");
  }

  async handleSuccessfulDelete() {
    this.fetchBrands();
    showToast("Brand saved deleted");
  }

  resetForm() {
    const inputs =
      this.modalBrands._element.querySelectorAll(".modal-body input");
    inputs.forEach((input) => (input.value = ""));
  }

  async handleSaveError(response) {
    const data = await response.json();

    if (data.message) {
      showToast(
        `Failed save brand: ${data.message ?? "Unknown error"}`,
        "error"
      );
    }

    if (data.errors) {
      const modalBody = this.modalBrands._element.querySelector(".modal-body");
      displayValidationErrors(modalBody, data.errors);
    }
  }

  async handleDeleteError(response) {
    const data = await response.json();

    if (data.message) {
      showToast(
        `Failed delete brand: ${data.message ?? "Unknown error"}`,
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

document.addEventListener("DOMContentLoaded", () => new BrandTableManager());
