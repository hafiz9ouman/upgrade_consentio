<!DOCTYPE html>
<html lang="en">

<head><meta charset="gb18030">

  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>D3GRC</title>
  <link rel="icon" href="{{ url('image/favicon.ico')}}" type="image/png">

  <!-- Custom fonts for this template-->
  <link href="{{ url('frontend/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="{{ url('frontend/css/sb-admin-2.min.css')}}" rel="stylesheet">

</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{url('/')}}">
        <div class="sidebar-brand-icon">
          <img style="width: 197px; height: 30px; margin: 10px;" src="{{url('image')}}/{{DB::table('login_img_settings')->first()->image}}">
        </div>
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item active">
        <a class="nav-link" href="{{url('/')}}">
          <i class="fas fa-fw fa-tachometer-alt" ></i>
          <span>Dashboard</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider">

      <!-- Heading -->
      <div class="sidebar-heading">
        Interface
      </div>

      <!-- Nav Item - Pages Collapse Menu -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
          <i class="fas fa-fw fa-user"></i>
          <span>Profile</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <!-- <h6 class="collapse-header">Custom Components:</h6> -->
            <a class="collapse-item" href="{{url('profile/7')}}">View Profile</a>
            <!-- <a class="collapse-item" href="cards.html">Cards</a> -->
          </div>
        </div>
      </li>

      <!-- Nav Item - Utilities Collapse Menu -->
     

      <!-- Divider -->

      <!-- Heading -->
      

      <!-- Nav Item - Pages Collapse Menu -->
      

      <!-- Nav Item - Charts -->
    

      <!-- Nav Item - Tables -->
      

      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block">

      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>

          <!-- Topbar Search -->
          <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
            <div class="input-group">
              <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
              <div class="input-group-append">
                <button class="btn btn-primary" type="button">
                  <i class="fas fa-search fa-sm"></i>
                </button>
              </div>
            </div>
          </form>

          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">

            <!-- Nav Item - Search Dropdown (Visible Only XS) -->
            <li class="nav-item dropdown no-arrow d-sm-none">
              <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
              </a>
              <!-- Dropdown - Messages -->
              <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                  <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                      <button class="btn btn-primary" type="button">
                        <i class="fas fa-search fa-sm"></i>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </li>

            <!-- Nav Item - Alerts -->
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Alerts -->
                <span class="badge badge-danger badge-counter">3+</span>
              </a>
              <!-- Dropdown - Alerts -->
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">
                  Alerts Center
                </h6>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="mr-3">
                    <div class="icon-circle bg-primary">
                      <i class="fas fa-file-alt text-white"></i>
                    </div>
                  </div>
                  <div>
                    <div class="small text-gray-500">December 12, 2019</div>
                    <span class="font-weight-bold">A new monthly report is ready to download!</span>
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="mr-3">
                    <div class="icon-circle bg-success">
                      <i class="fas fa-donate text-white"></i>
                    </div>
                  </div>
                  <div>
                    <div class="small text-gray-500">December 7, 2019</div>
                    $290.29 has been deposited into your account!
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="mr-3">
                    <div class="icon-circle bg-warning">
                      <i class="fas fa-exclamation-triangle text-white"></i>
                    </div>
                  </div>
                  <div>
                    <div class="small text-gray-500">December 2, 2019</div>
                    Spending Alert: We've noticed unusually high spending for your account.
                  </div>
                </a>
                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
              </div>
            </li>

            <!-- Nav Item - Messages -->
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-envelope fa-fw"></i>
                <!-- Counter - Messages -->
                <span class="badge badge-danger badge-counter">7</span>
              </a>
              <!-- Dropdown - Messages -->
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                <h6 class="dropdown-header">
                  Message Center
                </h6>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="https://source.unsplash.com/fn_BT9fwg_E/60x60" alt="">
                    <div class="status-indicator bg-success"></div>
                  </div>
                  <div class="font-weight-bold">
                    <div class="text-truncate">Hi there! I am wondering if you can help me with a problem I've been having.</div>
                    <div class="small text-gray-500">Emily Fowler · 58m</div>
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="https://source.unsplash.com/AU4VPcFN4LE/60x60" alt="">
                    <div class="status-indicator"></div>
                  </div>
                  <div>
                    <div class="text-truncate">I have the photos that you ordered last month, how would you like them sent to you?</div>
                    <div class="small text-gray-500">Jae Chun · 1d</div>
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="https://source.unsplash.com/CS2uCrpNzJY/60x60" alt="">
                    <div class="status-indicator bg-warning"></div>
                  </div>
                  <div>
                    <div class="text-truncate">Last month's report looks great, I am very happy with the progress so far, keep up the good work!</div>
                    <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60" alt="">
                    <div class="status-indicator bg-success"></div>
                  </div>
                  <div>
                    <div class="text-truncate">Am I a good boy? The reason I ask is because someone told me that people say this to all dogs, even if they aren't good...</div>
                    <div class="small text-gray-500">Chicken the Dog · 2w</div>
                  </div>
                </a>
                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
              </div>
            </li>

            <div class="topbar-divider d-none d-sm-block"></div>

            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Valerie Luna</span>
                <img class="img-profile rounded-circle" src="https://source.unsplash.com/QAB-WJcbgJk/60x60">
              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">
                  <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                  Profile
                </a>
                <a class="dropdown-item" href="#">
                  <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                  Settings
                </a>
                <a class="dropdown-item" href="#">
                  <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                  Activity Log
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Logout
                </a>
              </div>
            </li>

          </ul>

        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
<!--  -->
      <div class="container">
        <section class="user_profile">
          <div class="left-side-bar">
            <h1>Email</h1>
            <button class="btn btn-sm btn-primary"  id="selectAll">Select All</button>
            <div class="profile_info">
          <form method="post" action="{{url('')}}" enctype="multipart/form-data">
            {{ csrf_field() }}
                <div class="table-responsive">
        <table class="table" id="notify">
          <thead class="back_blue">
            <tr>
              <th>Select</th>
              <th>Email</th>
              <th>Name</th>
            </tr>
          </thead>
          <tbody>
            @foreach($user as $row)
            <tr>
              <td>
              <input type="checkbox" class="accepted" name="game_playedID[]" value={{$row->id}}>
              </td>
              <td> {{$row->email}} </td>
              <td> {{$row->name}} </td>
            </tr>
            @endforeach
          </tbody>
        </table>
          <div class="tile-footer text-right">
           
          </div>
      </div>    
      <table style="width: 50%;margin: 0 auto; background-color: #fff;padding: 30px 30px 10px;border-spacing: 0px;    border-collapse: collapse;">
      <thead>
        <tr>
          <th></th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr style="display: flex;flex-direction: row; padding: 30px 30px 0;">
          <td><img src="img/D3GDPR41.png" style="width: 80%;margin-bottom: 20px;"></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td><strong style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;padding: 30px 30px 0;color: #CFCFCF; font-size: 14px;">HELLO THERE,</strong></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td><h3 style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;font-size: 36px; color: #66BECD; letter-spacing: 2px; text-align: center;">I'm a new Text block<br>ready for your content.</h3></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td><img src="img/divider.png"></td>
        </tr>
        <tr>
          <td><p style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;letter-spacing: 1px;width: 80%;text-align: justify;color: #555555; margin: 40px auto;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ac turpis tincidunt, convallis lorem consectetur, varius ipsum. Quisque mauris sem, tempus sit amet massa ac, efficitur tristique turpis. Aliquam maximus aliquam odio non facilisis. Mauris pellentesque blandit posuere.</p></td>
        </tr>
        <tr>
          <td><p style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;letter-spacing: 1px;width: 80%;margin:0px auto 40px;text-align: justify;color: #555555;">Donec rutrum, risus vitae aliquet fringilla, velit ligula placerat ligula, eget dapibus velit lacus et nibh. Nulla non lorem eget erat iaculis egestas a sed felis. Ut nec semper nisi. Cras in hendrerit lorem. Integer iaculis ex diam.</p></td>
        </tr>
        <tr>
          <td style="text-align: center;"><img src="img/animation.gif" style="width: 50%;"></td>
        </tr>
        <tr>
          <td style="text-align: center; background-color: #E3FAFF;"><img src="img/avatar7.png" style="width: 20%; border-radius: 50%; margin: 30px 0;"></td>
        </tr>
        <tr>
          <td style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;padding: 4px;text-align: center; background-color: #E3FAFF; font-size: 14px;"><span>SHARE</span></td>
        </tr>
        <tr style="padding: 14px;text-align: center;display: flex;flex-direction: row;justify-content: center; background-color: #E3FAFF;">
          <td><a href="#"><img src="img/facebook@2x.png" style="width: 60%;"></a></td>
          <td><a href="#"><img src="img/instagram@2x.png" style="width: 60%;"></a></td>
          <td><a href="#"><img src="img/linkedin@2x.png" style="width: 60%;"></a></td>
          <td><a href="#"><img src="img/twitter@2x.png" style="width: 60%;"></a></td>
        </tr>
        <tr style="padding: 14px 0 0;text-align: center;display: flex;flex-direction: row;justify-content: center;">
          <td>Mobile phone <a href="tel:00000000" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;color: #555555; text-decoration: none;">00000000</a></td>
          <td><a href="mailto:hello@info.com" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:17px;margin-left: 20px; color: #555555; text-decoration: none;font-size:14px; font-weight: bold;">hello@info.com</a></td>
          <td style="text-align: right;"><img src="img/D3GDPR41.png" style="width: 40%;margin-bottom: 20px;"></td>
        </tr>
    </table> 
     <button class="btn btn-primary" type="submit" id="id_complete" disabled="disabled">Share Link</button>
              </form>
            </div>
          </section>
        </div>

      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; Your Website 2019</span>
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


  <!-- Bootstrap core JavaScript-->
  <script src="{{url('frontend/js/jquery.min.js')}}"></script>
  <script src="{{url('frontend/js/bootstrap.bundle.min.js')}}"></script>

  <!-- Core plugin JavaScript-->
  <script src="{{url('frontend/js/jquery.easing.min.js')}}"></script>

  <!-- Custom scripts for all pages-->
  <script src="{{url('frontend/js/sb-admin-2.min.js')}}"></script>

  <script>
    
    $('.accepted').change(function () {
  if ($('.accepted:checked').length > 0)
    $('#id_complete').removeAttr('disabled');
  else
    $('#id_complete').attr('disabled','disabled');
});




  $('body').on('click', '#selectAll', function () {
    if ($(this).hasClass('allChecked')) {     
        $('input[type="checkbox"]', '#notify').prop('checked', false);
        $('#id_complete').attr('disabled','disabled');
    } else {
      $('#id_complete').removeAttr('disabled');
        $('input[type="checkbox"]', '#notify').prop('checked', true);        
    }
    $(this).toggleClass('allChecked');
  })

  </script>
  <!-- Page level plugins -->
  <!-- <script src="{{url('frontend/js/Chart.min.js')}}"></script> -->

  <!-- Page level custom scripts -->
  <!-- <script src="{{url('frontend/js/chart-area-demo.js')}}"></script> -->
  <!-- <script src="{{url('frontend/js/chart-pie-demo.js')}}"></script> -->

</body>

</html>
