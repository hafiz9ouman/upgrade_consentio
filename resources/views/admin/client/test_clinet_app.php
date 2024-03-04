<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="gb18030">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>
            @if (View::hasSection('title'))
            @yield('title')
            @else
            Consentio | {{ __('We Manage Compliance') }}
            @endif
        </title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href=" {{ url('newfavicon.png') }}" type="image/png">
        <script src="{{ url('backend/js/sweetalert.js') }}"></script>
        <link rel="stylesheet" href="{{ url('backend/css/sweetalert.css') }}">
        <!-- Custom fonts for this template-->
        <link href="{{ url('frontend/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link  href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
        <link href="{{ url('frontend/css/sb-admin-2.min.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog==" crossorigin="anonymous" />
        <link href="{{ url('frontend/css/table.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/datatables.min.css" />
        <!-- // load this css in client to match admin form style -->
        @if(isset($load_admin_css) && $load_admin_css == true))
        <link rel="stylesheet" type="text/css" href="{{ url('backend/css/main.css') }}">
        @endif
        <!-- BOXicon -->
        <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
        <script src="https://unpkg.com/boxicons@latest/dist/boxicons.js"></script>
    </head>
    <body id="page-top">
        <div class="overlay_sidebar"></div>
        <!-- Page Wrapper -->
        <div id="wrapper">
            <!-- Sidebar -->
            <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
                <!-- Sidebar - Brand -->

                <a class="sidebar-brand d-flex align-items-center justify-content-center " href="{{ url('/dashboard') }}">
                    <div class="sidebar-brand-icon">
                        <!-- <img style="width: 100%; height: 30px;" src="{{ url('image/D3GDPR41.png') }}"> -->
                        @if (!empty($company_logo))
                            <div style="padding:40px">
                                <img style="max-width:100%;height:auto;width:auto;" src="{{ url('img/' . $company_logo) }}">
                            </div>
                        @else
                            <img style="max-width: 100%; max-height: 30%; background-color: black;" src="{{ url('_organisation.png') }}">
                        @endif
                    </div>
                </a>
                <!-- Heading -->
                <!-- Nav Item - Pages Collapse Menu -->
                @if(Auth::user()->role != 3 || true)
                    @if (in_array('Dashboard', $data))
                        <li class="nav-item {{ Request::segment(1) == 'dashboard' ? 'active' : '' }}">
                            <a class="nav-link collapsed active_clr" href="{{ url('/dashboard') }}">
                                <i class='bx bxs-dashboard'></i>
                                <span>{{ __('dbrd') }}</span>
                            </a>
                        </li>
                    @endif
                @endif

                @if (in_array('My Assigned Forms', $data) || in_array('Manage Forms', $data) || in_array('Completed Forms', $data))
                    <li class="nav-item {{ strpos(url()->current(), 'Forms/') !== false ? 'active' : '' }}">
                        <a class="nav-link  {{ strpos(url()->current(), 'Forms/') !== false ? '' : 'collapsed' }}" href="#"
                            data-toggle="collapse" data-target=".collapseAssessment" aria-expanded="true"
                            aria-controls="collapseAssessment">
                            <i class='bx bx-file'></i>
                            <span>{{ __('Assessment Form') }}</span>
                        </a>
                        @if (Auth::user()->role == 2 || Auth::user()->user_type == 1 || Auth::user()->role == 3)
                            <div id=""
                                class="collapseAssessment collapse {{ strpos(url()->current(), 'Forms/') !== false ? 'show' : '' }}"
                                aria-labelledby="headingAssessment" data-parent="#accordionSidebar">
                                @if (in_array('Manage Forms', $data))
                                    <div class="bg-white py-2 collapse-inner rounded">
                                        <a class="collapse-item <?php if (Request::segment(2) == 'FormsList') { echo 'active'; } ?>" href="{{ url('Forms/FormsList') }}">{{ __('Manage Forms') }}</a>
                                    </div>
                                @endif
                            </div>
                        @endif
                        <div id=""
                            class="collapseAssessment collapse {{ strpos(url()->current(), 'Forms/') !== false ? 'show' : '' }}" aria-labelledby="headingAssessment" data-parent="#accordionSidebar">
                            @if (in_array('My Assigned Forms', $data))
                                <div class="bg-white py-2 collapse-inner rounded">
                                    <a class="collapse-item <?php if (Request::segment(2) == 'ClientUserFormsList') { echo 'active'; } ?>" href="{{ route('client_user_subforms_list') }}">{{ __('My assigned Forms') }}</a>
                                </div>
                            @endif
                        </div>
                        @if (Auth::user()->role == 2 || Auth::user()->user_type == 1 || Auth::user()->role == 3)
                            <div id=""
                                class="collapseAssessment collapse {{ strpos(url()->current(), 'Forms/') !== false ? 'show' : '' }}"
                                aria-labelledby="headingAssessment" data-parent="#accordionSidebar">
                                @if (in_array('Completed Forms', $data))
                                    <div class="bg-white py-2 collapse-inner rounded">
                                        <a class="collapse-item <?php if (Request::segment(2) == 'CompletedFormsList') { echo 'active'; } ?>" href="{{ url('Forms/CompletedFormsList') }}">{{ __('Completed Forms') }}</a>
                                    </div>
                                @endif
                            </div>
                        @endif
                        <div id=""
                            class="collapseAssessment collapse {{ strpos(url()->current(), 'Forms/') !== false ? 'show' : '' }}"
                            aria-labelledby="headingAssessment" data-parent="#accordionSidebar">
                            @if (in_array('Generated Forms', $data))
                                <div class="bg-white py-2 collapse-inner rounded">
                                    <a class="collapse-item <?php if (Request::segment(2) == 'All_Generated_Forms') { echo 'active'; } ?>"  href="{{ route('client_site_all_generated_forms') }}">{{ __('Generated Forms') }}</a>
                                </div>
                            @endif
                        </div>
                        <!-- Forms/All_Generated_Forms -->

                    </li>
                @endif

                <!-- under_dev -->

                @if (in_array('Manage Audits', $data) || in_array('Completed Audits', $data) || in_array('Assigned Audits', $data))
                    <li class="nav-item {{ strpos(url()->current(), 'Forms/') !== false ? 'active' : '' }}">
                        <a class="nav-link  {{ strpos(url()->current(), 'Forms/') !== false ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target=".collapseAudit" aria-expanded="true" aria-controls="collapseAudit">
                            <i class='bx bx-file'></i>
                            <span>{{ __('Audit Register') }}</span>
                        </a>
                        @if (Auth::user()->role == 2 || Auth::user()->user_type == 1 || Auth::user()->role == 3)
                            <div id="" class="collapseAudit collapse {{ strpos(url()->current(), 'Forms/') !== false ? 'show' : '' }}" aria-labelledby="headingAssessment" data-parent="#accordionSidebar">
                                @if (in_array('Manage Audits', $data)) 
                                    <div class="bg-white py-2 collapse-inner rounded">
                                        <a class="collapse-item <?php if (Request::segment(2) == 'FormsList') { echo 'active';} ?>" href="{{ url('Forms/FormsList') }}">{{ __('Manage Audits') }}</a>
                                    </div>
                                @endif
                            </div>
                        @endif
                        <div id="" class="collapseAudit collapse {{ strpos(url()->current(), 'Forms/') !== false ? 'show' : '' }}"
                            aria-labelledby="headingAssessment" data-parent="#accordionSidebar">
                            @if (in_array('Assigned Audits', $data))
                                <div class="bg-white py-2 collapse-inner rounded">
                                    <a class="collapse-item <?php if (Request::segment(2) == 'ClientUserFormsList') { echo 'active';} ?>" href="{{ route('client_user_subforms_list') }}">{{ __('Assigned Audits') }}</a>
                                </div>
                            @endif
                        </div>
                        @if (Auth::user()->role == 2 || Auth::user()->user_type == 1 || Auth::user()->role == 3)
                            <div id="" class="collapseAudit collapse {{ strpos(url()->current(), 'Forms/') !== false ? 'show' : '' }}"
                                aria-labelledby="headingAssessment" data-parent="#accordionSidebar">
                                @if (in_array('Completed Audits', $data))
                                    <div class="bg-white py-2 collapse-inner rounded">
                                        <a class="collapse-item <?php if (Request::segment(2) == 'CompletedFormsList') { echo 'active';} ?>" href="{{ url('Forms/CompletedFormsList') }}">{{ __('Completed Audits') }}</a>
                                    </div>
                                @endif
                            </div>
                        @endif
                        <div id="" class="collapseAudit collapse {{ strpos(url()->current(), 'Forms/') !== false ? 'show' : '' }}" aria-labelledby="headingAssessment" data-parent="#accordionSidebar">
                            @if (in_array('Generated Audits', $data))
                                <div class="bg-white py-2 collapse-inner rounded">
                                    <a class="collapse-item <?php if (Request::segment(2) == 'All_Generated_Forms') {echo 'active'; } ?>" href="{{ route('client_site_all_generated_forms') }}">  {{ __('Generated Audits') }}</a>
                                </div>
                            @endif
                        </div>
                    </li>
                @endif


                <!-- under_dev -->

                @if (Auth::user()->role == 2 || Auth::user()->user_type == 1 || Auth::user()->role == 3)
                    @if ((isset($SAR_company_subform) && !empty($SAR_company_subform)) || Auth::user()->role == 3)
                        @if (in_array('SAR Forms', $data) || in_array('SAR Forms Submitted', $data) || in_array('SAR Forms pending', $data))
                            <li class="nav-item {{ strpos(url()->current(), 'SAR/') !== false ? 'active' : '' }}">
                                @if (isset($SAR_company_subform) && !empty($SAR_company_subform))
                                    @if (Auth::user()->role == 2 || Auth::user()->user_type == 1 || Auth::user()->role == 3)    
                                        <div id="collapseTwo" class="collapse {{ strpos(url()->current(), 'SAR/') !== false ? 'show' : '' }}" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                                            <div class="bg-white py-2 collapse-inner rounded">
                                                @if (in_array('SAR Forms', $data))
                                                    <a class="collapse-item <?php if (Request::segment(2) == 'ShowSARAssignees') { echo 'active'; } ?>" href="{{ url('SAR/ShowSARAssignees/' . $SAR_company_subform->parent_form_id) }}">{{ __('SAR Forms') }}</a>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    <div id="collapseTwo" class="collapse {{ strpos(url()->current(), 'SAR/') !== false ? 'show' : '' }}" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                                        <div class="bg-white py-2 collapse-inner rounded">
                                            @if (in_array('SAR Forms Submitted', $data))
                                                <a class="collapse-item <?php if (Request::segment(2) == 'SARCompletedFormsList') { echo 'active';} ?>" href="{{ url('SAR/SARCompletedFormsList') }}">{{ __('SAR Forms Submitted') }}</a>
                                            @endif
                                        </div>
                                    </div>
                                    <div id="collapseTwo" class="collapse {{ strpos(url()->current(), 'SAR/') !== false ? 'show' : '' }}" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                                        <div class="bg-white py-2 collapse-inner rounded">
                                            @if (in_array('SAR Forms pending', $data))
                                                <a class="collapse-item <?php if (Request::segment(2) == 'SARInCompletedFormsList') { echo 'active';} ?>" href="{{ url('SAR/SARInCompletedFormsList') }}">{{ __('SAR Forms pending') }}</a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </li>
                        @endif
                    @endif
                @endif
                
                <!-- Nav Item -  -->
                @if (Auth::user()->role == 2)
                    <li class="nav-item  {{ Request::segment(1) == 'users_management' || Request::segment(1) == 'add_user' ? 'active' : '' }}">
                        @if (in_array('Users Management', $data))
                            <a class="nav-link collapsed" href="{{ url('users_management') }}">
                                <i class="fas fa-fw fa-users"></i>
                                <span>{{ __('Users Management') }} </span>
                            </a>
                        @endif
                    </li>
                @endif

                <!-- Divider -->
                
                <!-- Nav Item -  -->
                @if (Auth::user()->role == 2 || Auth::user()->user_type == 1 || Auth::user()->role == 3)
                    <?php
                        $collapse = 'collapsed';
                        $show = '';
                        $expand = 'false';
                        if (strpos(url()->current(), 'AssetsReportsReg') !== false) {
                            $collapse = '';
                            $show = 'show';
                            $expand = 'true';
                        }
                    ?>
                    <?php
                        $collapse = 'collapsed';
                        $show = '';
                        $expand = 'false';
                        if (strpos(url()->current(), 'AssetsReportsEx') !== false) {
                            $collapse = '';
                            $show = 'show';
                            $expand = 'true';
                        }
                    ?>
                    @if (Auth::user()->role == 2 || Auth::user()->user_type == 1 || Auth::user()->role == 3)
                        @if (in_array('Global Data Inventory', $data) || in_array('Detailed Data Inventory', $data))
                            <li class="nav-item {{ strpos(url()->current(), 'Reports') !== false ? 'active' : '' }}">
                                <a class="nav-link  {{ strpos(url()->current(), 'Reports') !== false ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseSettingss" aria-expanded="true" aria-controls="collapseSettings">
                                    <i class="fa fa-file"></i>
                                    <span>{{ __('Data Inventory') }}</span>
                                </a>
                                <div id="collapseSettingss" class="collapse  {{ strpos(url()->current(), 'Reports') !== false ? 'show' : '' }}" aria-labelledby="headingSetting" data-parent="#accordionSidebar">
                                    <div class="bg-white py-2 collapse-inner rounded">
                                        @if (in_array('Global Data Inventory', $data))
                                            <a class="collapse-item <?php if (Request::segment(2) == 'GlobalDataInventory') { echo 'active'; } ?>" href="{{ url('Reports/GlobalDataInventory') }}">{{ __('Global Data Inventory') }}</a>
                                        @endif
                                    </div>
                                </div>
                                <div id="collapseSettingss" class="collapse {{ strpos(url()->current(), 'Reports') !== false ? 'show' : '' }}" aria-labelledby="headingSetting" data-parent="#accordionSidebar">
                                    <div class="bg-white py-2 collapse-inner rounded"> 
                                        @if (in_array('Detailed Data Inventory', $data))
                                            <a class="collapse-item <?php if (Request::segment(2) == 'DetailedDataInventory') {echo 'active'; } ?>" href="{{ url('Reports/DetailedDataInventory') }}">{{ __('Detailed Data Inventory') }}</a>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endif
                    @endif

                    <li class="nav-item {{ Request::segment(1) == 'assets' ? 'active' : '' }}">
                        @if (in_array('Assets List', $data))
                            <a class="nav-link collapsed" href="{{ route('asset_list') }}">
                                <i class='bx bx-list-ul'></i>
                                <span>{{ __('Assets Register') }}</span>
                        @endif
                        </a>
                    </li>

                    <li class="nav-item {{ Request::segment(1) == 'activities' ? 'active' : '' }}">

                        @if (in_array('Activities List', $data))
                            <a class="nav-link collapsed" href="{{ route('activity_list') }}">

                                <i class='bx bx-list-check'></i>

                                <span>{{ __('Activities List') }}</span>

                            </a>
                        @endif


                    </li>

                @endif

                <li class="nav-item {{ Request::segment(1) == 'incident' || Request::segment(1) == 'add_inccident' ? 'active' : '' }}"
                    style="border-bottom: 0;">
                    @if (in_array('Incident Register', $data))
                    <a class="nav-link collapsed" href="{{ url('incident') }}">
                        <i class='bx bx-id-card'></i>
                        <span>{{ __('Incident Register') }}</span>
                    </a>
                    @endif

                </li>

                @if (Auth::user()->role == 2 || Auth::user()->user_type == 1)
                    @if (in_array('Sub Forms Expiry Settings', $data) || in_array('SAR Expiry Settings', $data))
                        <li class="nav-item add_custom_margin_on_setting {{ strpos(url()->current(), 'FormSettings') !== false ? 'active' : '' }}">
                            <a class="nav-link  {{ strpos(url()->current(), 'FormSettings') !== false ? '' : 'collapsed' }}"
                                href="#" data-toggle="collapse" data-target="#collapseSettings" aria-expanded="true"
                                aria-controls="collapseSettings">
                                <i class="fas fa-cogs"></i>
                                <span>{{ __('Settings') }}</span>
                            </a>
                            <div id="collapseSettings"
                                class="collapse  {{ strpos(url()->current(), 'FormSettings') !== false ? 'show' : '' }}"
                                aria-labelledby="headingSetting" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    @if (in_array('Sub Forms Expiry Settings', $data))
                                    <a class="collapse-item <?php if (Request::segment(2) == 'SubFormsExpirySettings') {
                                                    echo 'active';
                                                } ?>"
                                        href="{{ url('FormSettings/SubFormsExpirySettings') }}">{{ __('Sub Forms Expiry') }}</a>
                                    @endif
                                </div>
                            </div>
                            <div id="collapseSettings"
                                class="collapse {{ strpos(url()->current(), 'FormSettings') !== false ? 'show' : '' }}"
                                aria-labelledby="headingSetting" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    @if (in_array('SAR Expiry Settings', $data))
                                    <a class="collapse-item <?php if (Request::segment(2) == 'SARExpirySettings') {
                                                    echo 'active';
                                                } ?>" href="{{ url('FormSettings/SARExpirySettings') }}">{{ __('SAR Expiry') }}</a>
                                    @endif
                                </div>
                            </div>
                            <div id="collapseSettings"
                                class="collapse {{ strpos(url()->current(), 'FormSettings') !== false ? 'show' : '' }}"
                                aria-labelledby="headingSetting" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    @if (in_array('Evaluation Rating', $data))
                                    <a class="collapse-item <?php if (Request::segment(2) == 'evaluation_rat') {
                                                    echo 'active';
                                                } ?>"
                                        href="{{ route('evaluation_rat') }}"><span>{{ __('Evaluation Rating') }}</span></a>
                                    @endif
                                </div>
                            </div>
                            <div id="collapseSettings"
                                class="collapse {{ strpos(url()->current(), 'FormSettings') !== false ? 'show' : '' }}"
                                aria-labelledby="headingSetting" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    @if (in_array('Data Elements', $data))
                                    <a class="collapse-item <?php if (Request::segment(2) == 'assets_data_elements') {
                                                    echo 'active';
                                                } ?>"
                                        href="{{ route('asset_data_elements') }}"><span>{{ __('Data Elements') }}</span></a>
                                    @endif
                                </div>
                            </div>
                            <div id="collapseSettings"
                                class="collapse {{ strpos(url()->current(), 'FormSettings') !== false ? 'show' : '' }}"
                                aria-labelledby="headingSetting" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    @if (in_array('Data Classification', $data))
                                    <a class="collapse-item <?php if (Request::segment(2) == 'front') {
                                                    echo 'active';
                                                } ?>"
                                        href="{{ url('front/data-classification') }}"><span>{{ __('Data Classification') }}</span></a>
                                    @endif
                                </div>
                            </div>
                            <div id="collapseSettings"
                                class="collapse {{ strpos(url()->current(), 'FormSettings') !== false ? 'show' : '' }}"
                                aria-labelledby="headingSetting" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    @if (in_array('Remediation Plans', $data))
                                    <a class="collapse-item {{ Request::segment(2) == 'remediation-plans' ? 'active' : '' }}"
                                        href="{{ url('remediation-plans') }}"><span>{{ __('Remediation Plans') }}</span></a>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endif
                @endif

                <li class="nav-item">
                    <a class="nav-link collapsed" href="{{ url('logout') }}">
                        <i class='bx bx-log-out-circle bx-rotate-180'></i>
                        <span>{{ __('Logout') }}</span>
                    </a>
                </li>

                <!-- Divider -->

                <hr class="sidebar-divider">
                <!-- Heading -->

            </ul>

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
                        <div class="beadcrumbs_topbar">
                            <h3 style="font-size: 18px">
                                @if (View::hasSection('page_title'))
                                @yield('page_title')
                                @else
                                @endif

                            </h3>
                        </div>
                        <!-- Topbar Search -->
                        @if (Session::has('top_bar_message'))
                        <div class="alert alert-danger d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search "
                            role="alert">
                            {{ Session::get('top_bar_message') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
                        <!-- Topbar Navbar -->

                        <ul class="navbar-nav ml-auto">
                            <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                            <li class="nav-item dropdown no-arrow d-sm-none">

                                <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                    <i class="fas fa-search fa-fw"></i>

                                </a>

                                <!-- Dropdown - Messages -->
                                <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                    aria-labelledby="searchDropdown">
                                    <form class="form-inline mr-auto w-100 navbar-search">
                                        <div class="input-group">
                                            <input type="text" class="form-control bg-light border-0 small"
                                                placeholder="Search for..." aria-label="Search"
                                                aria-describedby="basic-addon2">
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
                            {{-- <li class="nav-item dropdown no-arrow"> <a class="nav-link dropdown-toggle p-0" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="fa fa-bell-o fa-fw"></i><!-- Counter - Alerts --><span class="badge badge-danger badge-counter">3</span></a><!-- Dropdown - Alerts -->
                                <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">

                                    <h6 class="dropdown-header">{{ __('Alerts Center') }}</h6>

                                    <a class="dropdown-item d-flex align-items-center" href="#">

                                        <div class="mr-3">

                                            <div class="icon-circle bg-primary">

                                                <i class="fas fa-file-alt text-white"></i>

                                            </div>

                                        </div>

                                        <div>

                                            <div class="small text-gray-500">December 12, 2019</div>

                                            <span
                                                class="font-weight-bold">{{ __('A new monthly report is ready to download!') }}</span>

                                        </div>

                                    </a>

                                    <a class="dropdown-item d-flex align-items-center" href="#">
                                        <div class="mr-3">
                                            <div class="icon-circle bg-success">
                                                <i class="fas fa-donate text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="small text-gray-500">December 7, 2019</div> $290.29 has been deposited into your account!
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
                                            {{ __('Spending Alert: We have noticed unusually high spending for your account.') }}
                                        </div>
                                    </a>

                                    <a class="dropdown-item text-center small text-gray-500" href="#">{{ __('Show All Alerts') }}</a>
                                </div>
                            </li> --}}
                            <li class="nav-item dropdown no-arrow">
                                @if (session('locale') == 'fr')
                                    <a class="btn btn-default btn-sm" style=" display:flex; align-items:center;" href="{{ url('language/en') }}"><img src="{{ url('img/eng.png') }}" style=" width: 23px; margin-right: 5px;">{{ __('English') }}</a>
                                @elseif(session('locale') == 'en')
                                    <a class="btn btn-default btn-sm" style=" display:flex; align-items:center;" href="{{ url('language/fr') }}"><img src="{{ url('img/fr.png') }}" style=" width: 23px; margin-right: 5px;">{{ __('Français') }}</a>
                                @endif
                            </li>
                            <!-- </a> -->
                            <!-- Nav Item - Messages -->
                            <div class="topbar-divider d-none d-sm-block"></div>
                            <li class="nav-item dropdown make_my_arrow">
                                <?php
                                            $d_image = '_admin.png';
                                            $path_img = '/img';
                                            if (Auth::user()->role == 2) {
                                                $d_image = '_admin.png';
                                                $path_img = '/img';
                                            }
                                            if (Auth::user()->role == 3) {
                                                $d_image = 'dummy.jpg';
                                                $path_img = 'public/img2';
                                            }
                                            
                                ?>
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    @if (Auth::user()->image_name == '')
                                        <img class="img-profile rounded-circle" src="{{ URL::to('/' . $d_image) }}">
                                    @else
                                        <img class="img-profile rounded-circle" src="{{ URL::to($path_img . '/' . Auth::user()->image_name) }}">
                                    @endif
                                    <span class="ml-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
                                </a>

                                <!-- Dropdown - User Information -->
                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                    <a class="dropdown-item" href="{{ url('/profile/' . Auth::user()->id) }}">
                                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                        {{ __('Profile') }}
                                    </a>
                                    <!--  <div class="dropdown-divider"></div> -->
                                    <a class="dropdown-item" href="{{ route('logout') }}">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        {{ __('Logout') }}
                                    </a>

                                </div>

                            </li>
                        </ul>
                    </nav>
                    <!-- End of Topbar -->
                    <!-- Begin Page Content -->
                    <!-- Bootstrap core JavaScript-->
                    @yield('content')
                    <!-- /.container-fluid -->
                </div>

                <!-- Footer -->
                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            @if (Request::segment(2) == 'ShowSARAssignees')
                                <span>{{ __('Copyright') }} &copy; {{ date('Y') }}</span>
                            @else
                                <span>{{ __('Copyright') }} © Consentio | {{ __('We Manage Compliance') }}</span>
                            @endif
                        </div>
                    </div>
                </footer>
            </div>

        </div>

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
        <!-- Core plugin JavaScript-->
        <script src="{{ url('frontend/js/jquery.min.js') }}"></script>
        <script src="{{ url('frontend/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ url('frontend/js/jquery.easing.min.js') }}"></script>
        <script src="{{ url('frontend/js/sb-admin-2.min.js') }}"></script>
        <script src="{{ url('frontend/js/Chart.min.js') }}"></script>
        <script src="{{ url('frontend/js/chart-area-demo.js') }}"></script>
        <script src="{{ url('frontend/js/chart-pie-demo.js') }}"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/datatables.min.js"></script>
        <script>
            window.addEventListener("load", function() {
                var imgs = document.querySelectorAll("img");
                for (var a = 0; a < imgs.length; a++) {
                    var src = imgs[a].getAttribute("src");
                    imgs[a].setAttribute("onerror", src);
                    imgs[a].setAttribute("src", imgs[a].getAttribute("src").replace("/img/", "/public/img/"));
                }
            });

            $(document).ready(function() {
                $('.overlay_sidebar').click(function() {
                    $('.sidebar').removeClass('toggled');
                    $('body').removeClass('sidebar-toggled');
                });
            });
        </script>
    </body>
</html>