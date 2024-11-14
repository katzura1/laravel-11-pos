@extends('layout')

@section('content')
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="row mb-3">
              <div class="col d-flex align-items-center gap-2">
                  <select id="page-length" class="form-control form-select" style="width: auto; display: inline-block;">
                      <option value="10">10 Page</option>
                      <option value="25">25 Page</option>
                      <option value="50">50 Page</option>
                      <option value="100">100 Page</option>
                  </select>
                  <button class="btn btn-primary" id="btn-add">Add Product</button>
              </div>
              <div class="col-md-6 d-flex gap-2">
                  <input type="text" id="search" class="form-control" placeholder="Search...">
                  <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#column-visibility-modal">Column Visibility</button>
              </div>
            </div>
            <div id="loading" style="display: none;">Loading...</div>
            <table id="products-table" class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th class="column-code sortable" data-column="products.code">Code</th>
                        <th class="column-name sortable" data-column="products.name">Name</th>
                        <th class="column-image" data-column="products.image">Image</th>
                        <th class="column-supplier sortable" data-column="suppliers.name">Supplier</th>
                        <th class="column-brand sortable" data-column="brands.name">Brand</th>
                        <th class="column-sub_brand sortable" data-column="sub_brands.name">Sub Brand</th>
                        <th class="column-category sortable" data-column="product_categories.name">Category</th>
                        <th class="column-class sortable" data-column="products.class">Class</th>
                        <th class="column-buying_price sortable" data-column="products.buying_price">Buying Price</th>
                        <th class="column-selling_price sortable" data-column="products.selling_price">Selling Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Table rows will be inserted here by JavaScript -->
                </tbody>
            </table>
            <nav>
                <ul id="pagination" class="pagination m-0 ms-auto">
                    <!-- Pagination links will be inserted here by JavaScript -->
                </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal modal-blur" id="modal-products" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Form Data Product</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="POST" id="product-form">
              @csrf
              <input type="hidden" name="id">
              <div class="mb-3">
                <label class="form-label required" for="code">Code</label>
                <input type="text" class="form-control" name="code" placeholder="Input Code" maxlength="128" required>
              </div>
              <div class="mb-3">
                <label class="form-label required" for="name">Nama</label>
                <input type="text" class="form-control" name="name" placeholder="Input Name" maxlength="128" required>
              </div>
              <div class="mb-3">
                <label class="form-label required" for="supplier_id">Supplier</label>
                <select name="supplier_id" id="supplier_id" class="form-select" required></select>
              </div>
              <div class="mb-3">
                <div class="form-label required" for="brand_id">Brand</div>
                <select name="brand_id" id="brand_id" class="form-select" required></select>
              </div>
              <div class="mb-3">
                <div class="form-label required" for="sub_brand_id">Sub Brand</div>
                <select name="sub_brand_id" id="sub_brand_id" class="form-select" required></select>
              </div>
              <div class="mb-3">
                <div class="form-label required" for="product_category_id">Product Category</div>
                <select name="product_category_id" id="product_category_id" class="form-select" required></select>
              </div>
              <div class="mb-3">
                <div class="form-label required" for="class">Class</div>
                <input type="text" name="class" id="class" class="form-control" required></input>
              </div>
              <div class="mb-3">
                <div class="form-label required" for="buying_price">Buying Price</div>
                <input type="number" class="form-control" name="buying_price" placeholder="Input Buying Price" min="0" required>
              </div>
              <div class="mb-3">
                <div class="form-label required" for="selling_price">Selling Price</div>
                <input type="number" class="form-control" name="selling_price" placeholder="Input Selling Price" min="0" required>
              </div>
              <div class="mb-3">
                <div class="form-label required" for="image">Image</div>
                <input type="file" class="form-control" name="image" required>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="btn-save">Save changes</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Column Visibility Modal -->
    <div class="modal modal-blur fade" id="column-visibility-modal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Column Visibility</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="column-visibility-form">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="products.code" id="column-code" checked>
                <label class="form-check-label" for="column-code">Code</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="products.name" id="column-name" checked>
                <label class="form-check-label" for="column-name">Name</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="suppliers.name" id="column-supplier" checked>
                <label class="form-check-label" for="column-supplier">Supplier</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="brands.name" id="column-brand" checked>
                <label class="form-check-label" for="column-brand">Brand</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="sub_brands.name" id="column-sub_brand" checked>
                <label class="form-check-label" for="column-sub_brand">Sub Brand</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="product_categories.name" id="column-category" checked>
                <label class="form-check-label" for="column-category">Category</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="products.class" id="column-class" checked>
                <label class="form-check-label" for="column-class">Class</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="products.buying_price" id="column-buying_price" checked>
                <label class="form-check-label" for="column-buying_price">Buying Price</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="products.selling_price" id="column-selling_price" checked>
                <label class="form-check-label" for="column-selling_price">Selling Price</label>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="btn-apply-column-visibility">Apply</button>
          </div>
        </div>
      </div>
    </div>
@endsection 

@push('after-js')
<script src="/js/pages/product.js"></script>
@endpush