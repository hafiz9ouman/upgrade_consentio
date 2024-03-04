<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Incident;
use App\Notification;
use App\Friend;
use App\Who;
use App\Where;
use App\Airport;
use App\Message;
use App\Plan;
use App\How;
use App\PasswordSecurity;
use Auth;
use Lang;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use DB;
class IncidentRegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
         $user = Auth::user()->id;
            $assigned_permissions =array();
            $data = DB::table('module_permissions_users')->where('user_id' , $user)->pluck('allowed_module');

            if($data != null){
                 foreach ($data as $value) {
                $assigned_permissions = explode(',',$value);
                 
            }
            }
            // if(Auth::user()->role != 3 ){
            if(!in_array('Incident Register', $assigned_permissions)){
                return redirect('dashboard');
            }
        // }

    	$incident_type = DB::table('incident_type')->get();
    	$organization  = User::where('role',4)->get();
    	$user_type = Auth::user()->role;
    	$currentuserid = Auth::user()->id;
    	if ($user_type == 2 || Auth::user()->user_type == 1 || Auth::user()->role == 3){
    		$incident_front = Incident::where('organization_id',Auth::user()->client_id)
    		->orderBy('date_discovered', 'DESC')
    		->get();
    	}
    	else {
    		$incident_front = Incident::where('created_by',$currentuserid)
    		->orderBy('date_discovered', 'DESC')
    		->get();

    	}
    	$incident_register = Incident::orderBy('date_discovered', 'DESC')->get();
    	// dd($incident_front);

    	return view('admin.incident.index',compact('incident_register','user_type','incident_front'));
    }

    public function create()
	{
		// dd('walla');
		$incident_type = DB::table('incident_type')->get();
		$organization  = User::where('role',4)->get();
		$user_type = Auth::user()->role;
		return view('admin.incident.add',compact('incident_type', 'organization','user_type'));
	}

	public function add(Request $request){

		$request->validate([
            'incident_type' => 'required',
            'name' => 'required',
            'assignee' => 'required',
			// 'description' => 'required',
			'date_occurred' => 'required',
			'date_discovered' => 'required',
			'deadline_date' => 'required',
			'root_cause' => 'required',
			// 'resolution' => 'required',
			'incident_status' => 'required',
			'incident_severity' => 'required',
        ],
        [
            'incident_type.required' => __('Please provide Incident Type to proceed.'),
            'name.required' => __('Please provide proper Name to proceed.'),
            'assignee.required' => __('Please provide Assignee to proceed.'),
            // 'description.required' => 'Please provide Description to proceed.',
            'date_occurred.required' => __('Please provide Date Occurred to proceed.'),
            'date_discovered.required' => __('Please provide Date Discovred to proceed.'),
            'deadline_date.required' => __('Please provide Deadline Date to proceed.'),
            'root_cause.required' => __('Please provide Root Cause to proceed.'),
            // 'resolution.required' => 'Please provide Resolution to proceed.',
            'incident_status.required' => __('Please provide Incident Status to proceed.'),
            'incident_severity.required' => __('Please provide Incident Severity to proceed.'),
        ],
    );

		    // dd($request);
        $update = 0;
        if($request->id){
    		$incident = Incident::find($request->id);
			$update  = 1;			
    	}else{
    		 $incident = new Incident;
    	}
    	$currentuserid = Auth::user()->id;
		try{
			$incident->incident_type = $request->incident_type;
			$incident->organization_id = $request->organization_id;
			$incident->name = $request->name;
			$incident->assignee = $request->assignee;
			$incident->description = $request->description;
			$incident->date_occurred = date('Y-m-d', strtotime($request->date_occurred));
			$incident->time_occured = $request->time_occured;
			$incident->date_discovered = date('Y-m-d', strtotime($request->date_discovered));
			$incident->time_discovered = $request->time_discovered;
			$incident->deadline_date = date('Y-m-d', strtotime($request->deadline_date));
			$incident->time_deadline = $request->time_deadline;
			$incident->root_cause = $request->root_cause;
            $incident->resolution = $request->resolution;
            $incident->incident_status = $request->incident_status;
            $incident->incident_severity = $request->incident_severity;
			if($update  == 1){  
               $incident->updated_by = $currentuserid;
			} else{

			$incident->created_by = $currentuserid; }
			
			$success = $incident->save();
			if ($success) {
				if($update  == 1){
				\Session::flash('success', __('Your Incident Update Successfully'));
			   }
			   else {
                   \Session::flash('success', __('Your Incident Save Successfully'));
			   }
				return redirect('incident');
			}
			else{
				\Session::flash('error', __('Your Incident Cannot added Please tryagain later'));
				return redirect('add_inccident');
				
			}

		}catch (\Exception $e) {
			// echo $e->getMessage();
			// exit();
			return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
		}
	}


	 public function edit_incident($id){
	 	$data = Incident::find($id);
	 	$incident_type = DB::table('incident_type')->get();
		$organization  = User::where('role',4)->get();
		$user_type = Auth::user()->role;
		return view('admin.incident.edit',compact('incident_type', 'organization','data','user_type'));

	 }


	 public function destroy(Request $request) 
    { 
        $id = $request->input("id");
      
        
        Incident::where("id", $id)->delete();
       
    }

	

}