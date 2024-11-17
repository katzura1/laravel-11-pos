@extends('layout')

@section('content')
    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <form method="POST" id="form-choose-outlet">
              @csrf
              <div class="mb-3">
                <label class="form-label">Please select outlet to continue.</label>
                <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">

                  @foreach ($userOutlets as $item)
                    <label class="form-selectgroup-item flex-fill">
                      <input type="radio" name="outlet_id" value="{{ $item->outlet_id }}" class="form-selectgroup-input">
                      <div class="form-selectgroup-label d-flex align-items-center p-3">
                        <div class="me-3">
                          <span class="form-selectgroup-check"></span>
                        </div>
                        <div>
                          <strong>{{ $item->outlet_name }}</strong>
                        </div>
                      </div>
                    </label>
                  @endforeach
                </div>
              </div>
            </form>
          </div>
          <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary" id="btn-save">Submit</button>
          </div>
        </div>
      </div>
    </div>
@endsection

@push('after-js')
  <script src="/js/pages/choose-outlet.js"></script>
@endpush