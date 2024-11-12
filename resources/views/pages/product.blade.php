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
                        <th class="column-code sortable" data-column="code">Code</th>
                        <th class="column-name sortable" data-column="name">Name</th>
                        <th class="column-supplier sortable" data-column="supplier">Supplier</th>
                        <th class="column-brand sortable" data-column="brand">Brand</th>
                        <th class="column-sub_brand sortable" data-column="sub_brand">Sub Brand</th>
                        <th class="column-category sortable" data-column="category">Category</th>
                        <th class="column-class sortable" data-column="class">Class</th>
                        <th class="column-buying_price sortable" data-column="buying_price">Buying Price</th>
                        <th class="column-selling_price sortable" data-column="selling_price">Selling Price</th>
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
    <div class="modal modal-blur fade" id="modal-products" tabindex="-1" role="dialog" aria-hidden="true">
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
                <label class="form-label required" for="name">Nama</label>
                <input type="text" class="form-control" name="name" placeholder="Input Name" maxlength="128" required>
              </div>
              <div class="mb-3">
                <label class="form-label required" for="parent_id">Nama</label>
                <select name="parent_id" id="parent_id" class="form-select"></select>
              </div>
              <div class="mb-3">
                <label class="form-label required" for="position">Position</label>
                <input type="number" class="form-control" name="position" placeholder="Input Position Product" maxlength="128" required>
              </div>
              <div class="mb-3">
                <label class="form-label required" for="url">Url</label>
                <input type="text" class="form-control" name="url" placeholder="Input Url Product" maxlength="128" required>
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
                <input class="form-check-input" type="checkbox" value="code" id="column-code" checked>
                <label class="form-check-label" for="column-code">Code</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="name" id="column-name" checked>
                <label class="form-check-label" for="column-name">Name</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="supplier" id="column-supplier" checked>
                <label class="form-check-label" for="column-supplier">Supplier</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="brand" id="column-brand" checked>
                <label class="form-check-label" for="column-brand">Brand</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="sub_brand" id="column-sub_brand" checked>
                <label class="form-check-label" for="column-sub_brand">Sub Brand</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="category" id="column-category" checked>
                <label class="form-check-label" for="column-category">Category</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="class" id="column-class" checked>
                <label class="form-check-label" for="column-class">Class</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="buying_price" id="column-buying_price" checked>
                <label class="form-check-label" for="column-buying_price">Buying Price</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="selling_price" id="column-selling_price" checked>
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