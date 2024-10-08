<!DOCTYPE html>

<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="../../assets/" data-template="vertical-menu-template" data-style="light">
<!-- Mirrored from demos.pixinvent.com/materialize-html-admin-template/html/vertical-menu-template/auth-login-basic.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 27 Jun 2024 03:12:37 GMT -->

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>
        HummaKost - Masuk ke akun anda
    </title>

    <meta name="description"
        content="Materialize – is the most developer friendly &amp; highly customizable Admin Dashboard Template." />
    <meta name="keywords"
        content="dashboard, material, material design, bootstrap 5 dashboard, bootstrap 5 design, bootstrap 5" />
    <!-- Canonical SEO -->
    <link rel="canonical" href="https://1.envato.market/materialize_admin" />

    <!-- ? PROD Only: Google Tag Manager (Default ThemeSelection: GTM-5DDHKGP, PixInvent: GTM-5J3LMKC) -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                "gtm.start": new Date().getTime(),
                event: "gtm.js"
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != "dataLayer" ? "&l=" + l : "";
            j.async = true;
            j.src = "../../../../www.googletagmanager.com/gtm5445.js?id=" + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, "script", "dataLayer", "GTM-5J3LMKC");
    </script>
    <!-- End Google Tag Manager -->

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/logo.png') }}" type="image/x-icon">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/" />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;ampdisplay=swap"
        rel="stylesheet" />

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="../../assets/vendor/fonts/remixicon/remixicon.css" />
    <link rel="stylesheet" href="../../assets/vendor/fonts/flag-icons.css" />

    <!-- Menu waves for no-customizer fix -->
    <link rel="stylesheet" href="../../assets/vendor/libs/node-waves/node-waves.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../../assets/vendor/css/rtl/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../../assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/typeahead-js/typeahead.css" />
    <!-- Vendor -->
    <link rel="stylesheet" href="../../assets/vendor/libs/%40form-validation/form-validation.css" />

    <link rel="stylesheet" href="sweetalert2.min.css">
    <!-- Page CSS -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- Page -->
    <link rel="stylesheet" href="../../assets/vendor/css/pages/page-auth.css" />

    <!-- Helpers -->
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="../../assets/vendor/js/template-customizer.js"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../../assets/js/config.js"></script>
    {!! htmlScriptTagJsApi() !!}
</head>

<body style="background-color: #f9f9f9">
    @if ($errors->any())
        <div id="toast-error" class="bs-toast toast toast-ex animate__animated my-2 fade show" role="alert"
            aria-live="assertive" aria-atomic="true" data-bs-delay="2000">
            <div class="toast-header">
                <i class="ri-error-warning-line me-2 text-danger"></i>
                <div class="me-auto fw-medium">Error</div>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: "Perhatian",
                    text: @json(session('error')),
                    showCancelButton: true,
                    cancelButtonText: "Tutup",
                    showConfirmButton: false,
                    icon: 'info',
                });
            });
        </script>
    @endif
    <!-- ?PROD Only: Google Tag Manager (noscript) (Default ThemeSelection: GTM-5DDHKGP, PixInvent: GTM-5J3LMKC) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5J3LMKC" height="0" width="0"
            style="display: none; visibility: hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <!-- Content -->

    <div class="position-relative">

        <div class="authentication-wrapper authentication-basic container-p-y p-4 p-sm-0">
            <div class="authentication-inner py-6">
                <!-- Login -->
                <div class="card p-md-7 p-1 shadow-lg">
                    <div class="position-absolute" style="top: 10px;left:10px;">
                        <a href="/" class="d-flex align-items-center">
                            <span class="material-symbols-outlined">
                                arrow_back
                            </span>
                        </a>
                    </div>
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mt-5">
                        <a href="#" class="app-brand-link gap-2">
                            <span class="app-brand-text demo text-heading fw-semibold">Login</span>
                        </a>
                    </div>
                    <!-- /Logo -->

                    <div class="card-body mt-1">
                        <h4 class="mb-1">Selamat datang di Hummakost! 👋</h4>
                        <p class="mb-5">
                            Silakan masuk ke akun Anda.
                        </p>

                        <form class="mb-5" method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="form-floating form-floating-outline mb-5">
                                <input id="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" autocomplete="email" autofocus>

                                <label for="email">Email</label>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div>
                                <div class="form-password-toggle">
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input id="password" type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                name="password" autocomplete="current-password">
                                            <label for="password">Password</label>
                                        </div>
                                        <span id="password-toggle" class="input-group-text cursor-pointer">
                                            <i id="password-icon" class="ri-eye-off-line"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @error('password')
                                <strong class="text-danger m-0" style="font-size: 13px">{{ $message }}</strong>
                            @enderror
                            <div class="mb-1 d-flex justify-content-end mt-1">
                                @if (Route::has('password.request'))
                                    <a class="btn-link float-end mb-1 mt-2" href="{{ route('password.request') }}">
                                        <span>Lupa kata sandi?</span>
                                    </a>
                                @endif
                            </div>
                            <div>
                                <style>
                                    .recaptcha-container {
                                        width: 100%;
                                        display: flex;
                                        justify-content: center;
                                        align-items: center;
                                        margin: 0 auto;
                                        padding: 10px;
                                        box-sizing: border-box;
                                    }

                                    .g-recaptcha {
                                        width: 100% !important;
                                        transform: scale(1);
                                        transform-origin: 0 0;
                                    }
                                </style>
                                <div class="recaptcha-container">
                                    {!! htmlFormSnippet([
                                        'theme' => 'light',
                                        'size' => 'normal',
                                        'tabindex' => '3',
                                        'callback' => 'callbackFunction',
                                        'expired-callback' => 'expiredCallbackFunction',
                                        'error-callback' => 'errorCallbackFunction',
                                    ]) !!} {{-- class="g-recaptcha" --}}
                                </div>
                            </div>

                            <div class="my-5">
                                <button type="submit" class="btn btn-primary d-grid w-100">
                                    {{ __('Login') }}
                                </button>
                            </div>
                        </form>
                        <h7>Belum Punya Akun? <a href="{{ route('register') }}">Daftar Disini!</a></h7>

                    </div>
                </div>
                <!-- /Login -->

                <img alt="mask" src="{{ asset('images/Group 65.png') }}"
                    class="position-absolute d-none d-lg-block" style="top: 6%;z-index: -1;left:30%;opacity:60%;"
                    data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="500" />
            </div>
        </div>
    </div>

    <!-- / Content -->


    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../assets/vendor/libs/popper/popper.js"></script>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../../assets/vendor/libs/i18n/i18n.js"></script>
    <script src="../../assets/vendor/libs/typeahead-js/typeahead.js"></script>
    <script src="../../assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="../../assets/vendor/libs/%40form-validation/popular.js"></script>
    <script src="../../assets/vendor/libs/%40form-validation/bootstrap5.js"></script>
    <script src="../../assets/vendor/libs/%40form-validation/auto-focus.js"></script>


    <!-- Main JS -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="../../assets/js/pages-auth.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordField = document.getElementById('password');
            const passwordToggle = document.getElementById('password-toggle');
            const passwordIcon = document.getElementById('password-icon');

            passwordToggle.addEventListener('click', function() {
                // Toggle password visibility
                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    passwordIcon.classList.remove('ri-eye-off-line');
                    passwordIcon.classList.add('ri-eye-line');
                } else {
                    passwordField.type = 'password';
                    passwordIcon.classList.remove('ri-eye-line');
                    passwordIcon.classList.add('ri-eye-off-line');
                }
            });
        });
    </script>
    <script>
        AOS.init();
    </script>


</body>

<!-- Mirrored from demos.pixinvent.com/materialize-html-admin-template/html/vertical-menu-template/auth-login-basic.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 27 Jun 2024 03:12:38 GMT -->

</html>

<!-- beautify ignore:end -->

{{-- @section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Login') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="row mb-3">
                                <label for="email"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="current-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                            {{ old('remember') ? 'checked' : '' }}>

                                        <label class="form-check-label" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Login') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection --}}
