<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {!! Meta::toHtml() !!}
    @include('components.css')

    @stack('after-css')
  </head>
  <body>
    <div class="page">
      @include('components.sidebar')
      @include('components.navbar')

      <div class="page-wrapper">
        <!-- Page header -->
        <div class="page-header d-print-none">
          <div class="container-xl">
            <div class="row g-2 align-items-center">
              <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle">
                  {{  $subtitle ?? "" }}
                </div>
                <h2 class="page-title">
                  {{ $title ?? "" }}
                </h2>
              </div>
            </div>
          </div>
        </div>

        <!-- Page body -->
        <div class="page-body">
          <div class="container-xl">
            @yield('content')
          </div>
        </div>
      </div>
    </div>

    <!-- Toast HTML -->
    <div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3"></div>

    <!-- Modal Confirmation -->
    <!-- Confirmation Modal HTML -->
    <div class="modal modal-blur fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  Are you sure you want to proceed?
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-primary" id="confirmButton">Confirm</button>
              </div>
          </div>
      </div>
    </div>

    @include('components.js')

    @stack('after-js')
  </body>
</html>