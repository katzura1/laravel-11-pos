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
                  <button class="btn btn-primary" id="btn-add">Add Admin</button>
              </div>
              <div class="col-md-4">
                  <input type="text" id="search" class="form-control" placeholder="Search...">
              </div>
            </div>
            <div id="loading" style="display: none;">Loading...</div>
            <table id="users-table" class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Username</th>
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
    <div class="modal modal-blur" id="modal-users" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Form Data Admin</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="POST" id="user-form">
              @csrf
              <input type="hidden" name="role" value="admin">
              <input type="hidden" name="id">
              <div class="mb-3">
                <label class="form-label required" for="name">Nama</label>
                <input type="text" class="form-control" name="name" placeholder="Input Name" maxlength="128" required>
              </div>
              <div class="mb-3">
                <label class="form-label required" for="username">Username</label>
                <input type="text" class="form-control" name="username" placeholder="Input Username" maxlength="128" required>
              </div>
              <div class="mb-3">
                <label class="form-label required" for="password">Password</label>
                <input type="text" class="form-control" name="password" placeholder="Input Password" maxlength="25" required>
              </div>
              <div class="mb-3" id="checkbox-outlet">
                <div class="d-flex gap-4">
                  <div class="form-label required">Outlet</div>
                  <!-- Checkbox all -->
                  <label class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="check_all">
                    <span class="form-check-label">Pilih Semua</span>
                  </label>
                </div>
                <div>
                  @foreach ($outlets as $outlet)
                  <label class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="outlet_id[]" value="{{ $outlet->id }}">
                    <span class="form-check-label">{{ $outlet->name }}</span>
                  </label>
                  @endforeach
                </div>
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

    <div class="modal modal-blur" id="modal-outlets" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Form Data Outlet</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="POST" id="outlet-form">
              @csrf
              <input type="hidden" name="user_id">
              <div class="mb-3">
                <label class="form-label" for="name">Nama</label>
                <input type="text" class="form-control" name="name" readonly>
              </div>
              <div class="mb-3">
                <div class="d-flex gap-4">
                  <div class="form-label required">Outlet</div>
                  <!-- Checkbox all -->
                  <label class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="check_all">
                    <span class="form-check-label">Pilih Semua</span>
                  </label>
                </div>
                <div>
                  @foreach ($outlets as $outlet)
                  <label class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="outlet_id[]" value="{{ $outlet->id }}">
                    <span class="form-check-label">{{ $outlet->name }}</span>
                  </label>
                  @endforeach
                </div>
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

    <div class="modal modal-blur" id="modal-menus" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Form Data Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="POST" id="menu-form">
              @csrf
              <input type="hidden" name="user_id">
              <div class="mb-3">
                <label class="form-label" for="name">Nama</label>
                <input type="text" class="form-control" name="name" readonly>
              </div>
              @foreach ($menus as $item)
              <div class="mb-3">
                <div class="d-flex gap-4">
                  <div class="form-label required">{{ $item->name }}</div>
                  <!-- Checkbox all -->
                  <label class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="check_all" value="{{ $item->id }}">
                    <span class="form-check-label">Pilih Semua</span>
                  </label>
                </div>
                <div>
                  @foreach ($item->children??[] as $child)
                  <label class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="menu_id[]" data-menu-parent="{{ $child->parent_id }}" value="{{ $child->id }}">
                    <span class="form-check-label">{{ $child->name }}</span>
                  </label>
                  @endforeach
                </div>
              </div>
              @endforeach
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
<script src="/js/pages/users/admin.js"></script>
@endpush