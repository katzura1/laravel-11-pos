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
      "products.code",
      "products.name",
      "suppliers.name",
      "brands.name",
      "sub_brands.name",
      "product_categories.name",
      "products.class",
      "products.buying_price",
      "products.selling_price",
    ];
    this.columnVisibilityForm = document.getElementById(
      "column-visibility-form"
    );
    this.applyColumnVisibilityButton = document.getElementById(
      "btn-apply-column-visibility"
    );
    this.localStorageKey = "productPageColumnVisibility";

    this.btnAdd = document.getElementById("btn-add");
    this.productForm = document.getElementById("product-form");
    this.productModal = new bootstrap.Modal(
      document.getElementById("modal-products")
    );
    this.btnSave = document.getElementById("btn-save");

    this.initializeEventListeners();
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

    this.btnAdd.addEventListener("click", () => this.handleAddProduct());

    this.productForm.addEventListener("submit", async (event) => {
      event.preventDefault();
      await this.handleSaveProduct();
    });

    this.btnSave.addEventListener("click", () => {
      this.productForm.dispatchEvent(new Event("submit"));
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
      const columnElements = document.querySelectorAll(
        `th[data-column="${column}"], td[data-column="${column}"]`
      );
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
        `input[value="${column}"]`
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

      this.loadColumnVisibilitySettings();
    } catch (error) {
      console.error("Error fetching products:", error);
      showToast("Failed to fetch products", "error");
    } finally {
      this.hideLoading();
    }
  }

  buildUrl() {
    const params = new URLSearchParams({
      page: this.currentPage,
      length: this.pageLengthSelect.value,
      search: this.searchInput.value,
      sort_column: this.sortColumn ?? "",
      sort_direction: this.sortDirection ?? "asc",
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
        <td data-column="products.code">${product.code}</td>
        <td data-column="products.name">${product.name}</td>
        <td data-column="suppliers.name">${product.supplier_name}</td>
        <td data-column="brands.name">${product.brand_name}</td>
        <td data-column="sub_brands.name">${product.sub_brand_name}</td>
        <td data-column="product_categories.name">${product.product_category_name}</td>
        <td data-column="products.class">${product.class}</td>
        <td data-column="products.buying_price">${product.buying_price}</td>
        <td data-column="products.selling_price">${product.selling_price}</td>
        <td class="d-flex gap-2">
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

  async handleAddProduct() {
    const modalBody = this.productModal._element.querySelector(".modal-body");
    const idInput = modalBody.querySelector(`input[name="id"]`);

    //set value of id input to null
    idInput.value = null;

    //fetch select option
    this.fetchSuppliers();
    await this.fetchBrands();
    this.fetchProductCategories();

    this.productModal.show();

    //set focus to first visible input text
    setFocustFirstInModal(modalBody);
  }

  async handleEditProduct(product) {
    const modalBody = this.productModal._element.querySelector(".modal-body");
    const idInput = modalBody.querySelector(`input[name="id"]`);
    const codeInput = modalBody.querySelector(`input[name="code"]`);
    const nameInput = modalBody.querySelector(`input[name="name"]`);
    const classInput = modalBody.querySelector(`input[name="class"]`);
    const buyingPriceInput = modalBody.querySelector(
      `input[name="buying_price"]`
    );
    const sellingPriceInput = modalBody.querySelector(
      `input[name="selling_price"]`
    );
    const supplierSelect = modalBody.querySelector(
      `select[name="supplier_id"]`
    );
    const brandSelect = modalBody.querySelector(`select[name="brand_id"]`);
    const subBrandSelect = modalBody.querySelector(
      `select[name="sub_brand_id"]`
    );
    const productCategorySelect = modalBody.querySelector(
      `select[name="product_category_id"]`
    );

    //set value of id input
    idInput.value = product.id;
    nameInput.value = product.name;
    codeInput.value = product.code;
    classInput.value = product.class;
    buyingPriceInput.value = product.buying_price;
    sellingPriceInput.value = product.selling_price;

    //fetch select option
    await this.fetchSuppliers();
    await this.fetchBrands();
    await this.fetchProductCategories();

    //set value of select option
    supplierSelect.value = product.supplier_id;
    brandSelect.value = product.brand_id;
    await this.fetchSubBrands(product.brand_id);
    subBrandSelect.value = product.sub_brand_id;
    productCategorySelect.value = product.product_category_id;

    this.productModal.show();

    //set focus to first visible input text
    setFocustFirstInModal(modalBody);
  }

  async fetchSuppliers() {
    await fetch("/product/get-suppliers", {
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        const modalBody =
          this.productModal._element.querySelector(".modal-body");
        const supplierSelect = modalBody.querySelector(
          `select[name="supplier_id"]`
        );
        supplierSelect.innerHTML = `<option value="">Select Supplier</option>`;
        supplierSelect.innerHTML += data
          .map((supplier) => {
            return `<option value="${supplier.id}">${supplier.name}</option>`;
          })
          .join("");
      });
  }

  async fetchBrands() {
    const modalBody = this.productModal._element.querySelector(".modal-body");
    const brandSelect = modalBody.querySelector(`select[name="brand_id"]`);

    //clear brand select options
    brandSelect.innerHTML = "";
    const subBrandSelect = modalBody.querySelector(
      `select[name="sub_brand_id"]`
    );
    subBrandSelect.innerHTML = "";

    const response = await fetch("/product/get-brands", {
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });

    if (response.ok) {
      const brands = await response.json();
      brandSelect.innerHTML = `<option value="">Select Brand</option>`;
      brandSelect.innerHTML += brands
        .map((brand) => {
          return `<option value="${brand.id}">${brand.name}</option>`;
        })
        .join("");

      //add event listener to fetch sub brands when brand changes
      brandSelect.addEventListener("change", () => {
        const val = brandSelect.value;
        this.fetchSubBrands(val);
      });
    } else {
      showToast("Failed to fetch brands", "error");
    }
  }

  async fetchSubBrands(brandId) {
    await fetch(`/product/get-sub-brands?brand_id=${brandId}`, {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        const modalBody =
          this.productModal._element.querySelector(".modal-body");
        const subBrandSelect = modalBody.querySelector(
          `select[name="sub_brand_id"]`
        );
        subBrandSelect.innerHTML = `<option value="">Select Sub Brand</option>`;
        subBrandSelect.innerHTML += data
          .map((subBrand) => {
            return `<option value="${subBrand.id}">${subBrand.name}</option>`;
          })
          .join("");
      });
  }

  async fetchProductCategories() {
    await fetch("/product/get-product-categories", {
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        const modalBody =
          this.productModal._element.querySelector(".modal-body");
        const productCategorySelect = modalBody.querySelector(
          `select[name="product_category_id"]`
        );
        //add options to product category select
        productCategorySelect.innerHTML = `<option value="">Select Product Category</option>`;
        productCategorySelect.innerHTML += data
          .map((productCategory) => {
            return `<option value="${productCategory.id}">${productCategory.name}</option>`;
          })
          .join("");
      });
  }

  async handleSaveProduct() {
    if (!validateForm(this.productForm)) return;

    const data = this.getFormData();
    let url = "/product/store";
    data["_method"] = "POST";
    if (data.id) {
      url = "/product/put";
      data["_method"] = "PUT";
    }

    const response = await submitForm(url, data);

    if (response.ok) {
      this.productModal.hide();
      this.fetchProducts();
      showToast("Product saved successfully", "success");
    } else {
      const data = await response.json();
      if (data.errors) {
        displayValidationErrors(this.productForm, data.errors);
      } else {
        showToast(data.message, "error");
      }
    }
  }

  getFormData() {
    const formData = new FormData(this.productForm);
    const data = {};
    formData.forEach((value, key) => {
      data[key] = value;
    });
    return data;
  }

  handleDeleteProduct(product) {
    showConfirmationDialog(
      "Are you sure you want to delete this product?",
      async () => {
        const response = await submitForm("/product/destroy", {
          id: product.id,
          _method: "DELETE",
        });
        if (response.ok) {
          this.fetchProducts();
          showToast("Product deleted successfully", "success");
        } else {
          const data = await response.json();
          showToast(data.message, "error");
        }
      }
    );
  }
}

document.addEventListener("DOMContentLoaded", function () {
  new ProductPageManager();
});
