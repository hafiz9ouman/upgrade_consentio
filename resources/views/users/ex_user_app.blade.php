<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="gb18030">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>
        @if(View::hasSection('title'))
        @yield('title')
        @else
        Consentio | {{ __('We Manage Compliance') }}
        @endif
    </title>
    <link rel="icon" href=" https://dev.d3grc.com/newfavicon.png" type="image/png">
    <link href="{{ url('frontend/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->

    <link href="{{ url('frontend/css/sb-admin-2.min.css')}}" rel="stylesheet">
    <?php  // load this css in client to match admin form style
      if (isset($load_admin_css) && $load_admin_css == true): ?>

    <link rel="stylesheet" type="text/css" href="{{url('backend/css/main.css')}}">
    <?php endif; ?>

</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <!-- End of Sidebar -->
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
              <!-- Topbar -->
              <nav class="navbar navbar-expand topbar mb-4 static-top shadow" style="background-color: #fff;">
                  <div class="main_popo"
                      style="display: flex; align-items: center;justify-content: space-between;width: 100%;">
                      <a href="{{url('/')}}">
                          <div class="">
                              <img style="width: 191px; height: 38px; margin: 10px;"
                                  src="{{url('public/image')}}/{{DB::table('login_img_settings')->first()->image}}">
                          </div>
                      </a>
                      <!-- Sidebar Toggle (Topbar) -->
                      <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                          <i class="fa fa-bars"></i>
                      </button>
                      <div class="top_menu">
                          <div class="subform_Add_top">
                              @if(session('locale')=='fr')
                              <a class="dropdown-item" href="{{ url('language/en') }}">
                                  <img src="{{url('public/img/eng.png')}}"
                                      style=" width: 23px; margin-right: 5px;">English
                              </a>
                              @elseif(session('locale')=='en')
                              <a class="dropdown-item" href="{{ url('language/fr') }}">
                                  <img src="{{url('public/img/fr.png')}}"
                                      style=" width: 23px; margin-right: 5px;">French
                              </a>
                              @endif
                          </div>
                      </div>
                  </div>
              </nav>
              <!-- Begin Page Content -->
              <!-- Bootstrap core JavaScript-->
              <script src="{{url('frontend/js/jquery.min.js')}}"></script>
              <script src="{{url('frontend/js/bootstrap.bundle.min.js')}}"></script>
              <div style="margin-left:0px">
                  @yield('content')
              </div>
              <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->
            <!-- Footer -->
            <footer class="sticky-footer bg-white">
              <div class="container my-auto">
                <div class="copyright text-center my-auto">
                   <span>{{ __('Copyright') }} © Consentio | {{ __('We Manage Compliance') }}
                      {{ date('Y') }}</span>

                    </div>

                </div>

            </footer>

            <!-- End of Footer -->



        </div>

        <!-- End of Content Wrapper -->



    </div>

    <!-- End of Page Wrapper -->



    <!-- Scroll to Top Button-->

    <a class="scroll-to-top rounded" href="#page-top">

        <i class="fas fa-angle-up"></i>

    </a>









    <!-- Core plugin JavaScript-->

    <!-- <script src="{{url('frontend/js/jquery.easing.min.js')}}"></script> -->



    <!-- Custom scripts for all pages-->

    <script src="{{url('frontend/js/sb-admin-2.min.js')}}"></script>



    <!-- Page level plugins -->

    <script src="{{url('frontend/js/Chart.min.js')}}"></script>



    <!-- Page level custom scripts -->

    <script src="{{url('frontend/js/chart-area-demo.js')}}"></script>

    <script src="{{url('frontend/js/chart-pie-demo.js')}}"></script>



</body>



</html>