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
                  <button class="btn btn-primary" id="btn-add">Add Cashier</button>
              </div>
              <div class="col-md-6 d-flex flex-row gap-2">
                <select class="form-select" id="search-outlet">
                  <option value="">-- Semua Outlet --</option>
                  @foreach ($outlets as $outlet)
                  <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                  @endforeach
                </select>
                  <input type="text" id="search" class="form-control" placeholder="Search...">
              </div>
            </div>
            <div id="loading" style="display: none;">Loading...</div>
            <table id="cashiers-table" class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Outlet</th>
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
    <div class="modal modal-blur fade" id="modal-cashiers" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Form Data Cashier</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="POST" id="cashier-form">
              @csrf
              <input type="hidden" name="role" value="cashier">
              <input type="hidden" name="id">
              <div class="mb-3">
                <label class="form-label required" for="name">Nama</label>
                <input type="text" class="form-control" name="name" placeholder="Input Name" maxlength="128">
              </div>
              <div class="mb-3">
                <label class="form-label required" for="username">Username</label>
                <input type="text" class="form-control" name="username" placeholder="Input Username" maxlength="128">
              </div>
              <div class="mb-3">
                <div class="form-label required" for="outlet_id">Outlet</div>
                <select class="form-select" name="outlet_id" required>
                  <option value="">-- Pilih Outlet --</option>
                  @foreach ($outlets as $outlet)
                  <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label required" for="password">Password</label>
                <input type="text" class="form-control" name="password" placeholder="Input Password" maxlength="25">
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
@endsection 

@push('after-js')
<script src="/js/pages/users/cashier.js"></script>
@endpush