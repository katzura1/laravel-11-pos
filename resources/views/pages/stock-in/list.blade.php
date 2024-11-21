@extends('layout')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="row mb-3 gap-3">
            <div class="col d-flex align-items-center gap-2">
              <select id="page-length" class="form-control form-select" style="width: auto; display: inline-block;">
                <option value="10">10 Page</option>
                <option value="25">25 Page</option>
                <option value="50">50 Page</option>
                <option value="100">100 Page</option>
              </select>
              <button class="btn btn-primary" id="btn-add">Add Transaction</button>
            </div>
            <div class="col-md-6 d-flex gap-2">
              <input type="text" id="search" class="form-control" placeholder="Search...">
              <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#column-visibility-modal">Column Visibility</button>
              <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#filter-modal">Filter</button>
            </div>
          </div>
          <div id="loading" style="display: none;">Loading...</div>
          <div class="table-responsive">
            <table id="stock-ins-table" class="table w-100">
              <thead>
                <tr>
                  <th>No</th>
                  <th class="sortable" data-column="stock_ins.stock_in_no">Stock In No</th>
                  <th class="sortable" data-column="stock_ins.stock_in_date">Stock In Date</th>
                  <th class="sortable" data-column="stock_ins.due_date">Due Date</th>
                  <th class="sortable" data-column="suppliers.name">Supplier</th>
                  <th class="sortable" data-column="outlets.name">Outlet</th>
                  <th class="sortable" data-column="users.name">User</th>
                  <th class="sortable" data-column="stock_ins.total_price">Total</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <!-- Table rows will be inserted here by JavaScript -->
              </tbody>
            </table>
          </div>
          <nav>
            <ul id="pagination" class="pagination m-0 ms-auto">
              <!-- Pagination links will be inserted here by JavaScript -->
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>

  <!-- Filter Modal -->
  <div class="modal modal-blur fade" id="filter-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Filter</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="filter-form">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="start-date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="start-date" name="start_date">
              </div>
              <div class="col-md-6 mb-3">
                <label for="end-date" class="form-label">End Date</label>
                <input type="date" class="form-control" id="end-date" name="end_date">
              </div>
            </div>
            <div class="mb-3">
              <label for="supplier" class="form-label">Supplier</label>
              <select id="supplier" class="form-control form-select" name="supplier_id">
                <option value="">-- All Supplier --</option>
                @foreach ($suppliers as $supplier)
                  <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label for="outlet-name" class="form-label">Outlet Name</label>
              <input type="text" class="form-control" value="{{ $outlet['name'] }}" readonly>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btn-apply-filter">Apply</button>
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
            <div class="d-flex flex-wrap gap-2">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="stock_ins.stock_in_no" id="column-stock_in_no" checked>
                <label class="form-check-label" for="column-stock_in_no">Stock In No</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="stock_ins.stock_in_date" id="column-stock_in_date" checked>
                <label class="form-check-label" for="column-stock_in_date">Stock In Date</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="stock_ins.due_date" id="column-due_date" checked>
                <label class="form-check-label" for="column-due_date">Due Date</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="suppliers.name" id="column-supplier" checked>
                <label class="form-check-label" for="column-supplier">Supplier</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="outlets.name" id="column-outlet" checked>
                <label class="form-check-label" for="column-outlet">Outlet</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="users.name" id="column-user" checked>
                <label class="form-check-label" for="column-user">User</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="stock_ins.total_price" id="column-total_price" checked>
                <label class="form-check-label" for="column-total_price">Total</label>
              </div>
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
  <script src="/js/pages/stock-in/list.js"></script>
@endpush
