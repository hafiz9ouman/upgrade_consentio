@extends( 'admin.layouts.admin_app' )
@section('content')
<style>
  #sub-form-area {
      /*width:200px;*/
    /*border: 1px solid;*/
    padding: 14px;
    margin-right: 30px;
        border-radius: 3px;
    background: #fff;
    margin-top: 20px;
  }
  .sub-form {
    margin-top:20px;
  }
  .right_sec {
      border-right: 2px solid;
          height: 300px;
  }
  #size {
    font-size: 18px;
    /*width: 174px;*/
    /* padding: 3px; */
    padding: 5px 4px;
}
.custom-file {
    margin-top: 6.5rem;
}
.img_dlt  {
        margin-top: 7rem;
}
.sbmit_btn button {
    margin-top: 15px;
    background: #3094d1;
    border: none;
    padding: 5px 16px;
    border-radius: 4px;
    color: #fff;
}
</style>
<div class="Cust_page" style="margin-left:30px;">
<h3 class="tile-title text-center">Update image for Login Page</h3>
<div id="sub-form-area">
    @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
   <div class="row">
        <div class="col-md-6">
           <div class="right_sec" style="background: #a0c8d1;">
               <div class="form-group">	
				<label class="form-control-label" style="display: flex;justify-content: center;">Current Image</label>
				<div class="img_dlt text-center">
                    <img id="blah" class="img-fluid" src="{{url('public/image')}}/{{$responce->image}}" name="image" 
                    {{-- style="padding: 5px;height: 41px;width: 300px; border-style: dashed;" --}}
                    style="padding: 5px;    height: 85px;    width: 350px;    border-style: dashed;">
					<br>
					<div id="size" style="font-size:14px;">Max Resolution 300 x 41</div>
					<div id="size" style="font-size:14px;">Max Size 1 MB</div>
				</div>
			</div>
        </div>
        </div>
        <div class="col-md-6">
           <div class="left_sec">
               <label style="width: 100%;text-align: center;">Update Image</label>
               <form method="POST" action="{{ url('/update_login_img') }}" enctype="multipart/form-data">
                   {{ csrf_field() }}
                  <div class="custom-file">
                      <label class="custom-file-label" for="customFile">Choose File</label>
                      <input type="file" class="custom-file-input" id="customFile" name="image" required>
                  </div>
                  <div class="sbmit_btn text-right">
                  <button style="cursor:pointer;"type="submit">Update</button>
                  </div>
                </form>
           </div>
        </div>
   </div>
</div>
</div>

        <script>
        $(".custom-file-input").on("change", function() {
          var fileName = $(this).val().split("\\").pop();
          $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });
        </script>
        


@endsection
