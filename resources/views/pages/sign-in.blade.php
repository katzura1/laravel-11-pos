<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    {!! Meta::toHtml() !!}
    @include('components.css')
    </style>
  </head>
  <body  class=" d-flex flex-column">
    <script src="/js/demo-theme.min.js"></script>
    <div class="page page-center">
      <div class="container container-normal py-4">
        <div class="row align-items-center g-4">
          <div class="col-lg">
            <div class="container-tight">
              <div class="text-center mb-4">
                @include('components.logo')
              </div>
              <div class="card card-md">
                <div class="card-body">
                  <h2 class="h2 text-center mb-4">Login to your account</h2>
                  <form action="./" method="get" autocomplete="off" novalidate>
                    <div class="mb-3">
                      <label class="form-label">Email address</label>
                      <input type="email" name="email" id="email" class="form-control" placeholder="your@email.com" autocomplete="off">
                    </div>
                    <div class="mb-2">
                      <label class="form-label">
                        Password
                      </label>
                      <div class="input-group input-group-flat">
                        <input type="password" name="password" id="password" class="form-control"  placeholder="Your password"  autocomplete="off">
                        <span class="input-group-text">
                          <a href="#" role="button" id="btn-show-password" class="link-secondary" title="Show password" data-bs-toggle="tooltip">
                            <img src="/static/svg/outline/eye.svg" class="icon" id="icon-password">
                          </a>
                        </span>
                      </div>
                    </div>
                    <div class="mb-2">
                      <label class="form-check">
                        <input type="checkbox" class="form-check-input"/>
                        <span class="form-check-label">Remember me on this device</span>
                      </label>
                    </div>
                    <div class="form-footer">
                      <button type="submit" class="btn btn-primary w-100">Sign in</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg d-none d-lg-block">
            <img src="/static/illustrations/undraw_secure_login_pdn4.svg" height="300" class="d-block mx-auto" alt="">
          </div>
        </div>
      </div>
    </div>
    <!-- Libs JS -->
    <!-- Tabler Core -->
    <script src="/js/tabler.min.js" defer></script>
    <script src="/js/pages/sign-in.js"></script>
  </body>
</html>