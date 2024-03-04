
@if (Auth::check()) 
               
<script type="text/javascript">
var html='<tr id="msg"><td colspan="5" class="text-center" ><div class="alert alert-danger">Your cart is empty</div></td></tr>';
$(document).on('click','button.my_tickets',function(){
  var ticket_id=$(this).data('id');
  var user_id={{ Auth::id() }};
  var package_id={{ isset($package->id) ? $package->id:0  }};
  $.ajax({
    type:'post',
    url: '{{ url("ticket/add_to_cart")}}',
    data:{ ticket_id:ticket_id, user_id:user_id, package_id:package_id,"_token": "{{ csrf_token() }}" },
    context:this,
    success: function(ticket){
      if(ticket.success){
        
        if(ticket.cart){
          $('#cart-data').html(ticket.cart);
          $('#msg').hide();
        }else{
          $('#cart-data').html('html');
        }
          $('#similar_products').html(ticket.similar_items);
          if(ticket.similar_items){
        $('#cont').show();  
        }else{
          $('#cont').hide();
        }
        
        $(this).removeClass('red-btn my_tickets').addClass('yellow-btn delet_cart_item');
        $('.cart-counter').text(ticket.total_tickets);
        $('.total').text('$'+ticket.total);
        if(ticket.total==0){
          $('.cust_cart_check').hide();
        }else{
          $('.cust_cart_check').show();
        }
      }else{
        alert(ticket.error_messages);
      }

    },
    error: function(){},
  });
});
$(document).on('click','.delet_cart_item',function(){
  var ticket_id=$(this).data('id');
  var user_id={{ Auth::id() }};
  $.ajax({
    type:'post',
    url: '{{ url("ticket/remove_from_cart")}}',
    data:{ ticket_id:ticket_id, user_id:user_id,"_token": "{{ csrf_token() }}" },
    context:this,
    success: function(ticket){
      if(ticket.success){
        $(this).parents('tr.cart-items').remove();
        if($('tr.cart-items').length <=0) $('#cart-data').html(html); else $('#msg').hide();

        $('.cart-counter').text(ticket.total_tickets);
        $('.total').text('$'+ticket.total);
        $('#similar_products').html(ticket.similar_items);
        if(ticket.similar_items){
        $('#cont').show();  
        }else{
          $('#cont').hide();
        }
        $(document).find('a.delet_cart_item[data-id="'+ ticket_id +'"]').parents('tr.cart-items').remove();
        $(document).find('button.delet_cart_item[data-id="'+ ticket_id +'"]').removeClass('yellow-btn delet_cart_item').addClass('red-btn my_tickets');
        if(ticket.total==0){
          $('.cust_cart_check').hide();
        }else{
          $('.cust_cart_check').show();
        }
      }else{ 
        alert(ticket.error_messages);
      }

    },
    error: function(){

    },
  });
});

</script>

<style type="text/css">

#cont {
    user-select: none;
    margin: 0;
    padding: 13px 20px 0px;
    background: none;
    height: auto;
    width: 100%;
    overflow: hidden;
    position: relative;
    border-radius: 6px;
}
#cont:active{
  cursor:e-resize;
}

#slider-container {
  width:300%;
  margin:0 auto;
}

#right-btn, #left-btn {
    font-size: 3em;
    color: #2ecdff !important;
    position: absolute;
    top: 8px;
    transition: all .4s ease-out;
    cursor: pointer;
    border-radius: 50%;
    height: 40px;
    line-height: 0.9em;
    box-shadow: none !important;
    z-index: 99;
}

#right-btn {
  right: 4%;
    top: 44%;
}
#right-btn:hover {
  color: #2ecdff;
}
#left-btn {
    left: 4%;
    top: 44%;
}
#left-btn:hover {
  color: #2ecdff;
}

.item-container {
    padding-top: 6px;
    border-radius: 0;
    margin: 0 4px;
    height: 269px;
    width: 8%;
    display: inline-block;
    background: #006dbd;
    transition: all .2s ease-out;
    cursor: pointer;
    box-shadow: none;
    float: left;
}
.item-container:hover {
  /*box-shadow:0px 6px 20px rgba(0,0,0,0.5);*/
}

.item-image-wrapper {
  height: auto;
  width:96%;
  position:relative;
  overflow:hidden;
  /*box-shadow:0px 2px 10px black;*/
  border-radius: 2px;
  margin:auto;
  background:blue;
}
.item-image-wrapper:before {
  content:"";
  position:absolute;
  background-color:rgba(250,250,250,0.4);
  width:380px;
  height:100px;
  transform: translate(106px,-116px) rotateZ(30deg);
}

.item-image-wrapper:hover:before{
  transform: translate(-180px,198px) rotateZ(30deg);
  transition: .4s ease-in-out;
  z-index:99;
 }
.item-image-wrapper img {
    width: 100%;
    height: 135px;
    transition: all .4s ease-out;
}
.item-image-wrapper img:hover {
  transform: scale(1.2) rotateZ(-6deg);
}

.item-title {
    /* color: #0107e1; */
    /* font-family: arial; */
    /* font-size: 1.8em; */
    /* margin: 20px 10px; */
    text-align: center;
    color: white !important;
    margin: 0 !important;
    padding: 3px 0 !important;
}
.item-desc {
  margin:0 10px;
  color:#f1f0f0;
  font-weight:bold;
  font-family: calibri;
}

.item-stars {
  float:left;
  margin:24px 14px;
  color:orange;
}

.item-link {
  font-size:1.2em;
  font-weight:bold;
  color:#fff;
  background-color:#0a4977;
  float:right;
  margin: 10px;
  padding:10px 16px;
  border-radius:6px;
}

.item-link:hover {
  color:#eee;
  background-color:#01355a;
}
</style>
 <script type="text/javascript">
   var nextButton = $("#right-btn");
var backButton = $("#left-btn");
var con = $("#cont");
var sliderCont = $("#slider-container");

var sldElm = $(".item-image-wrapper img");
var i = 0;
while (i<sldElm.length) {
  sldElm[i].setAttribute("draggable", false);
  i++
}

var mL = 0, maxX = 200, diff = 0 ;

function slide() {
   mL-=100;
  if( mL < -maxX ){ mL = 0 ;}
  sliderCont.animate({"margin-left" : mL + "%"}, 800);
}

function slideBack() {
  mL += 100;
  if ( mL > 0 ) { mL = -200 ; }
  sliderCont.animate({"margin-left" : mL + "%"}, 800);
}

nextButton.click(slide);
backButton.click(slideBack);

$(document).on("mousedown touchstart", con, function(e) {
  
  var startX = e.pageX || e.originalEvent.touches[0].pageX;
  diff = 0;

  $(document).on("mousemove touchmove", function(e) {
    
      var xt = e.pageX || e.originalEvent.touches[0].pageX;
      diff = (xt - startX) * 100 / window.innerWidth;
    if( mL == 0 && diff > 10 ) { 
      event.preventDefault() ;
    } else if (  mL == -maxX && diff < -10 ) {
       event.preventDefault();   
    } else {
      sliderCont.css("margin-left", mL + diff + "%");
    }
  });
});

$(document).on("mouseup touchend", function(e) {
  $(document).off("mousemove touchmove");
  if(  mL == 0 && diff > 4 ) { 
      sliderCont.animate({"margin-left" :  0 + "%"},100);
   } else if (  mL == -maxX  && diff < 4 ){
       sliderCont.animate({"margin-left" : -maxX  + "%"},100);  
   } else {
      if (diff < -10) {
        slide();
      } else if (diff > 10) {
        slideBack();
      } else {
        sliderCont.animate({"margin-left" :  mL + "%"},300);
      }
  }
});

 </script>	
 @endif
    <footer>
			<div class="footer">
				<div class="term_policy">
					<ul>
						<li><a href="{{url('terms')}}">terms of play</a></li>
						<li><a href="{{url('privacy')}}">privacy & policy</a></li>
						<li><a href="{{url('subscriber')}}">subscriber</a></li>
						<li><a href="{{url('contact')}}">contact</a></li>
					</ul>
				</div>
				<div class="address">
					<p>New Road Jinah xyz. Reg. Office: 2, Plato Place, 72-74 St.Reg. No: 3755182</p>
					<ul>
						<li><a href="#">info@ijustwon.com</a></li>
						<li><a href="#">+9245875124 45 5  ijustwon.com</a></li>
						<li><a href="#">&copy; Copyright 1999-2019</a></li>
						<li><a href="#">ijustwon.com</a></li>
					</ul>
				</div>
				<div class="social_links">
					<ul>
						<li><a href="#"><img src="{{ url('frontend/images/fb.png') }}"></a></li>
						<li><a href="#"><img src="{{ url('frontend/images/tiwitter.png') }}"></a></li>
						<li><a href="#"><img src="{{ url('frontend/images/youtube.png') }}"></a></li>
						<li><a href="#"><img src="{{ url('frontend/images/insta.png') }}"></a></li>
					</ul>
				</div>
			</div>
		</footer>
		
    <script type="text/javascript" src="{{ url('frontend/js/bootstrap.min.js') }}"></script>
      <script type="text/javascript" src="{{ url('frontend/js/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('frontend/js/jquery.lineProgressbar.js') }}"></script>
    <script type="text/javascript" src="{{ url('frontend/js/slick.min.js') }}"></script>
     <script type="text/javascript" src="{{ url('frontend/js/cust.js') }}"></script>

     <!-- Page specific javascripts-->
     

