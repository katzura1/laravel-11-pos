class ProductPageManager {
  constructor() {
    this.searchInput = document.getElementById("search");
    this.pageLengthSelect = document.getElementById("page-length");
    this.loading = document.getElementById("loading");
    this.table = document.getElementById("products-table");
    this.tbody = document.querySelector("#products-table tbody");
    this.pagination = document.getElementById("pagination");
    this.currentPage = 1;
    this.sortColumn = null;
    this.sortDirection = null;
    this.columns = [
      "code",
      "name",
      "supplier",
      "brand",
      "sub_brand",
      "category",
      "class",
      "buying_price",
      "selling_price",
    ];
    this.columnVisibilityForm = document.getElementById(
      "column-visibility-form"
    );
    this.applyColumnVisibilityButton = document.getElementById(
      "btn-apply-column-visibility"
    );
    this.localStorageKey = "productPageColumnVisibility";

    this.initializeEventListeners();
    this.loadColumnVisibilitySettings();
    this.fetchProducts();
  }

  initializeEventListeners() {
    this.searchInput.addEventListener("input", () => this.handleSearch());
    this.pageLengthSelect.addEventListener("change", () =>
      this.handlePageLengthChange()
    );
    this.applyColumnVisibilityButton.addEventListener("click", () =>
      this.handleColumnVisibilityChange()
    );
    document.querySelectorAll(".sortable").forEach((header) => {
      header.addEventListener("click", () => this.handleSort(header));
    });
  }

  handleSort(header) {
    const column = header.getAttribute("data-column");
    if (this.sortColumn === column) {
      this.sortDirection = this.sortDirection === "asc" ? "desc" : "asc";
    } else {
      this.sortColumn = column;
      this.sortDirection = "asc";
    }
    this.updateSortStyles();
    this.fetchProducts();
  }

  updateSortStyles() {
    document.querySelectorAll(".sortable").forEach((header) => {
      header.classList.remove("sort-asc", "sort-desc");
      if (header.getAttribute("data-column") === this.sortColumn) {
        header.classList.add(
          this.sortDirection === "asc" ? "sort-asc" : "sort-desc"
        );
      }
    });
  }

  handleSearch() {
    if (this.searchTimeout) {
      clearTimeout(this.searchTimeout);
    }

    this.searchTimeout = setTimeout(() => {
      this.currentPage = 1;
      this.fetchProducts();
    }, 200);
  }

  handlePageLengthChange() {
    this.currentPage = 1;
    this.fetchProducts();
  }

  handleColumnVisibilityChange() {
    const selectedOptions = this.getSelectedColumns();
    this.updateColumnVisibility(selectedOptions);
    this.saveColumnVisibilitySettings(selectedOptions);
    this.closeColumnVisibilityModal();
  }

  getSelectedColumns() {
    return Array.from(
      this.columnVisibilityForm.querySelectorAll("input[type=checkbox]:checked")
    ).map((checkbox) => checkbox.value);
  }

  updateColumnVisibility(selectedOptions) {
    this.columns.forEach((column) => {
      const columnElements = document.querySelectorAll(`.column-${column}`);
      columnElements.forEach((element) => {
        element.style.display = selectedOptions.includes(column) ? "" : "none";
      });
    });
  }

  saveColumnVisibilitySettings(selectedOptions) {
    localStorage.setItem(this.localStorageKey, JSON.stringify(selectedOptions));
  }

  loadColumnVisibilitySettings() {
    const savedSettings = JSON.parse(
      localStorage.getItem(this.localStorageKey)
    );
    if (savedSettings) {
      this.updateColumnVisibility(savedSettings);
      this.updateColumnVisibilityForm(savedSettings);
    }
  }

  updateColumnVisibilityForm(savedSettings) {
    this.columns.forEach((column) => {
      const checkbox = this.columnVisibilityForm.querySelector(
        `input[value=${column}]`
      );
      if (checkbox) {
        checkbox.checked = savedSettings.includes(column);
      }
    });
  }

  closeColumnVisibilityModal() {
    const modal = bootstrap.Modal.getInstance(
      document.getElementById("column-visibility-modal")
    );
    modal.hide();
  }

  async fetchProducts() {
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
      console.error("Error fetching products:", error);
    } finally {
      this.hideLoading();
    }
  }

  buildUrl() {
    const params = new URLSearchParams({
      page: this.currentPage,
      length: this.pageLengthSelect.value,
      search: this.searchInput.value,
      sort_column: this.sortColumn,
      sort_direction: this.sortDirection,
    });
    return `/product/get?${params.toString()}`;
  }

  renderTable(products) {
    this.tbody.innerHTML = products.length
      ? products
          .map((product, key) => this.createProductRow(product, key))
          .join("")
      : this.createEmptyRow();

    // Add click event listeners to edit buttons
    this.tbody.querySelectorAll("button.btn-edit").forEach((button, index) => {
      button.addEventListener("click", async () => {
        // console.log("Edit product:", products[index]);
        await this.handleEditProduct(products[index]);
      });
    });

    // Add click event listeners to delete buttons
    this.tbody
      .querySelectorAll("button.btn-delete")
      .forEach((button, index) => {
        button.addEventListener("click", () => {
          // console.log("Delete product:", products[index]);
          this.handleDeleteProduct(products[index]);
        });
      });
  }

  createProductRow(product, key) {
    const rowNo =
      (this.currentPage - 1) * parseInt(this.pageLengthSelect.value) + key + 1;
    return `
      <tr>
        <td>${rowNo}</td>
        <td class="column-code">${product.code}</td>
        <td class="column-name">${product.name}</td>
        <td class="column-supplier">${product.supplier_name}</td>
        <td class="column-brand">${product.brand_name}</td>
        <td class="column-sub_brand">${product.sub_brand_name}</td>
        <td class="column-category">${product.product_category_name}</td>
        <td class="column-class">${product.class}</td>
        <td class="column-buying_price">${product.buying_price}</td>
        <td class="column-selling_price">${product.selling_price}</td>
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
        <td colspan="${countTh}" class="text-center">No product found</td>
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
        this.fetchProducts();
      });
    });
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

document.addEventListener("DOMContentLoaded", function () {
  new ProductPageManager();
});
