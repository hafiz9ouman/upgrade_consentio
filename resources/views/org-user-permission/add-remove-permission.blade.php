<?php /*echo "<pre>";print_r($permissions);die;*/ ?>
@extends('admin.client.client_app')
@section( 'content' )

<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

#size{
    margin-left: 58px;
    width: 168px;
    padding: 3px;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #222d32;
  -webkit-transition: .4s;
  transition: .4s;
}
input:checked + .slider {
    background-color: #1cc88a !important;
}
.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
.max-spec {
    font-size:14px;
}
.card-body-form {
    width: 100%;
}
.form-group {
     display: block; 
     flex-direction: column; 
}
.form-btn a , .form-btn button {
  font-size: 18px;

}
.add_color {
      background: #0f75bd;
    font-size: 19px;
    color: #fff;
}
.left_radius {
  border-top-left-radius: 10px;
  border-bottom-left-radius: 10px;
  border: none !important;
}
.right_radius {
  border-top-right-radius: 10px;
  border-bottom-right-radius: 10px;
  border: none !important;
}
</style>

<!-- <div class="app-title">
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i>
		</li>
		<li class="breadcrumb-item"><a href="{{url('/admin')}}">Organization Admins </a>
		</li>
		<li class="breadcrumb-item"><a href="{{url('/users/add')}}">Add Organization Administrator</a>
		</li>		
	</ul>
</div> -->

@if (session('alert'))
    <div class="alert alert-danger">
        {{ session('alert') }}
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@section('page_title')
    {{ __('CHANGE USER PERMISSIONS')}}
@endsection

<div class="row">
	<div class="col-lg-12">

	<div class="card" style="border-radius: 30px; margin-left: 30px; margin-right: 30px">
		<div class="card-body" id="org-form">
			<div class="card-body-form">
				<form class="form-horizontal" method="POST" action="{{ url('orgusers/permissions/store') }}" enctype="multipart/form-data">
					{{ csrf_field() }}
                    <table class="table">
  
                        <tbody>
                            <tr>
                              <th class="add_color left_radius">{{ __('Modules')}}</th>
                              <td class="add_color right_radius">{{ __('Permission')}}</td>
                                @foreach($permissions as $total_permissions)
                                    <?php  
                                        if($total_permissions == 'Users Management' || $total_permissions == 'Sub Forms Expiry Settings' ||  $total_permissions =='SAR Expiry Settings'){
                                          continue;
                                        }
                                        $per = $total_permissions;
                                        if(session('locale') == 'fr'){
                                          if ($total_permissions == "Dashboard") {
                                            $per = "Tableau de bord";
                                        } elseif ($total_permissions == "Manage Forms") {
                                            $per = "Gérer les formulaires";
                                        } elseif ($total_permissions == "My Assigned Forms") {
                                            $per = "Mes formulaires assignés";
                                        } elseif ($total_permissions == "Completed Forms") {
                                            $per = "Formulaires complétés";
                                        } elseif ($total_permissions == "Generated Forms") {
                                            $per = "Formulaires générés";
                                        } elseif ($total_permissions == "Manage Audits") {
                                            $per = "Gérer les audits";
                                        } elseif ($total_permissions == "Assigned Audits") {
                                            $per = "Audits assignés";
                                        } elseif ($total_permissions == "Completed Audits") {
                                            $per = "Audits complétés";
                                        } elseif ($total_permissions == "Generated Audits") {
                                            $per = "Audits générés";
                                        } elseif ($total_permissions == "SAR Forms") {
                                            $per = "Formulaires SAR";
                                        } elseif ($total_permissions == "SAR Forms Submitted") {
                                            $per = "Formulaires SAR soumis";
                                        } elseif ($total_permissions == "SAR Forms pending") {
                                            $per = "Formulaires SAR en attente";
                                        } elseif ($total_permissions == "Global Data Inventory") {
                                            $per = "Inventaire global des données";
                                        } elseif ($total_permissions == "Detailed Data Inventory") {
                                            $per = "Inventaire détaillé des données";
                                        } elseif ($total_permissions == "Assets List") {
                                            $per = "Liste des actifs";
                                        } elseif ($total_permissions == "Activities List") {
                                            $per = "Liste des activités";
                                        } elseif ($total_permissions == "Incident Register") {
                                            $per = "Registre des incidents";
                                        } elseif ($total_permissions == "Data Elements") {
                                            $per = "Éléments de données";
                                        } elseif ($total_permissions == "Data Classification") {
                                            $per = "Classification des données";
                                        } elseif ($total_permissions == "Evaluation Rating") {
                                            $per = "Évaluation de la notation";
                                        } elseif ($total_permissions == "Remediation Plans") {
                                            $per = "Plans de remédiation";
                                        }
                                        
                                        }
                                             
                                    ?>
                                    <tr>
                                         <th style="vertical-align: middle;">{{$per}}</th>   
                                        <td style="padding:8px;">
                                            <label class="switch">
                                                <input name="permiss[]" value="{{$total_permissions}}"  @if(in_array($total_permissions, $granted_permissions) == true) checked="true" @endif  type="checkbox">
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                  
                                    </tr>
                                @endforeach
                        </tr>
                        </tbody>
                    </table>
                     <input id="file" type="hidden" class="form-control" name="id" value="{{$id}}">
                     <div class="form-group row form-btn">
						<div class="col-sm-12 text-right">
							<a href="{{url('users_management')}}" class="buton bg-dark" style="padding:9px;"> {{ __('Cancel')}}</a>
							<button type="submit" class="buton ">{{ __('Save')}} </button>
						</div>
					 </div>	
			         
							
				</form>

			</div>


	   </div>
	</div>
</div>



@endsection