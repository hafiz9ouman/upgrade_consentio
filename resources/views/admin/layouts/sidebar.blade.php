<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
  <?php $test = Auth::user()->image_name; ?>
  @if($test =="")
  <div class="app-sidebar__user"><img class="app-sidebar__user-avatar" src="{{ url('_admin.png')}}" style="    background: #f8f036; max-height: 100px; max-width: 100px;" alt="User Image">      
  @else
  <div class="app-sidebar__user"><img class="app-sidebar__user-avatar" style="max-height: 100px; max-width: 100px;" src="<?php echo url("img/$test");?>" alt="User Image">
  @endif
    <p class="app-sidebar__user-name">{{Auth::user()->name}}</p>
    <p class="app-sidebar__user-designation">{{Auth::user()->email}}</p>
  </div>
  <ul class="app-menu">
    <li><a class="app-menu__item <?php if(Request::segment(1) == "site_admins") echo "active"; ?>" href="{{url('/site_admins')}}"><i class="app-menu__icon fa fa-user-tie"></i><span class="app-menu__label">Site Admins</span></a></li>
    <li><a class="app-menu__item <?php if(Request::segment(1) == "admin") echo "active"; ?>" href="{{url('/admin')}}"><i class="app-menu__icon fa fa-users"></i><span class="app-menu__label">Organization Admins</span></a></li>
    <li><a class="app-menu__item <?php if(Request::segment(1) == "company") echo "active"; ?>" href="{{url('/company')}}"><i class="app-menu__icon fas fa-copyright"></i><span class="app-menu__label">{{ __('Organizations')}}</span></a></li>
    <li><a class="app-menu__item <?php if(Request::segment(1) == "evaluation_rating") echo "active"; ?>" href="{{route('evaluation_rating')}}"><i class="app-menu__icon fa fa-tasks"></i><span class="app-menu__label">{{ __('Evaluation Rating')}}</span></a></li>
    <li><a class="app-menu__item {{ Request::is('Forms/AdminFormsList') ? 'active' : '' }}" href="{{route('admin_forms_list')}}"><i class="app-menu__icon fa fa-tasks"></i><span class="app-menu__label">{{ __('Manage Assessment Form')}}</span></a></li>

    <li><a class="app-menu__item {{ Request::is('Forms/AdminFormsList/audit') ? 'active' : '' }}" href="{{route('admin_forms_list', 'audit')}}"><i class="app-menu__icon fa fa-tasks"></i><span class="app-menu__label">{{ __('Manage Audit Form')}}</span></a></li>

    <li><a class="app-menu__item <?php if(Request::segment(1) == "group") echo "active"; ?>" href="{{route('groups_list')}}"><i class="app-menu__icon fa fa-tasks"></i><span class="app-menu__label">{{ __('Manage Question Groups')}}</span></a></li>

    <li><a class="app-menu__item <?php if(Request::segment(1) == "data_element") echo "active"; ?>" href="{{url('/data_element')}}"><i class="app-menu__icon fa fa-list"></i><span class="app-menu__label">{{ __('Data Element')}}</span></a></li>
    <li><a class="app-menu__item <?php if(Request::segment(1) == "login_img_settings") echo "active"; ?>" href="{{url('/login_img_settings')}}"><i class="app-menu__icon fa fa-list"></i><span class="app-menu__label">{{ __('Logo Settings')}}</span></a></li>
    <li><a class="app-menu__item <?php if(Request::segment(1) == "data-classification") echo "active"; ?>" href="{{url('/data-classification')}}"><i class="app-menu__icon fa fa-list"></i><span class="app-menu__label">{{ __('Data Classification')}}</span></a></li>
    <li><a class="app-menu__item <?php if(Request::segment(1) == "impact") echo "active"; ?>" href="{{url('/impact')}}"><i class="app-menu__icon fa fa-list"></i><span class="app-menu__label">{{ __('Impact')}}</span></a></li>
    <?php 
      $expanded = '';
      if (strpos(url()->current(), 'AssetsReports') !== false || strpos(url()->current(), 'DataInvReports') !== false){
        $expanded = 'is-expanded';	
      }
    ?>
    <li><a class="app-menu__item" href="{{url('logout')}}"><i class="app-menu__icon fa fa-sign-out"></i><span class="app-menu__label">Logout</span></a></li>
  </ul>
</aside>