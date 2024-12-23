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

    @include('components.utils')

    @include('components.js')

    @stack('after-js')
  </body>
</html>