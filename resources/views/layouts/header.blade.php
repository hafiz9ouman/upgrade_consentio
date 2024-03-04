<?php $data=get_user_cart();
// $cart = ['cart'];
// print_r($data);exit();
?>
      <header>
        <div class="header">
          <div class="container">
            <div class="header-body">
              <div class="logo">
                <a href="{{ url('/') }}">
                <img class="img-fluid" src="{{ url('frontend/images/logo.png')}}">
                </a>
              </div>
              <div class="logIn_sec">
                <ul>
                  @if(!Auth::check())
                  <li onclick="document.getElementById('id01').style.display='block'"><a href="#">login</a></li>
                  <li onclick="document.getElementById('id02').style.display='block'"><a href="#">register</a></li>
                  @else
                  <li><a href="{{ url('logout') }}">Logout</a></li>
                  @endif
                  <li><a data-toggle="modal" data-target="#cartModal" href="#"><i class="fa fa-shopping-cart"></i> <span class="badge  badge-danger cart-counter">{{  $data['total_tickets'] }}</span></a></li>
                </ul>
              </div>
             </div>
           </div>
           <div class="nab">
            <button class="togle_button">
                <i class="fa fa-bars"></i>
            </button>
            <div class="container">
              <ul class="main-nav">
                <li class="active"><a href="{{ url('/') }}">home</a></li>
                <li class="dropdown">
                  <a class="dropdown-toggle" data-toggle="dropdown" href="#">competitions</a>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="#">live draws</a>
                    <a class="dropdown-item" href="{{ url('competition') }}">active competitions</a>
                    <a class="dropdown-item" href="{{ url('winners') }}">winner podium</a>
                  </div>
                </li>
                <li><a href="#">how to play</a></li>
                <li><a href="{{ url('faqs') }}">FAQs</a></li>
                <li><a href="{{ url('blog') }}">Blog</a></li>
                  <div class="dropdown accounts_menu">
                    @if(Auth::check())
                    <li><a class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" href="#" aria-haspopup=" true" aria-expanded="false">
                    {{ Auth::user()->name}}
                  </a></li>
                  @else
                    <li><a class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" href="#" aria-haspopup=" true" aria-expanded="false">
                    accounts
                  </a></li>
                  @endif
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                  @if(!Auth::check())
                      <a class="dropdown-item" onclick="document.getElementById('id01').style.display='block'" href="#">login</a>
                      <a class="dropdown-item" onclick="document.getElementById('id02').style.display='block'" href="#">register</a>
                      <a class="dropdown-item" onclick="document.getElementById('id03').style.display='block'" href="#">forget password</a>
                      @else

                      <a class="dropdown-item"  href="{{ url('/profile/'.<?php Auth::user()->id ?>) }}">Profile</a>
                      <a class="dropdown-item"  href="{{ url('logout') }}">logout</a>
                    @endif
                    </div>
                  </div>
              </ul>
              </div>
            </div>
        </div>
      </header>

<!-- The Modal -->
  <div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content cust_cart">
      <div class="modal-header border-bottom-0">
        <h5 class="modal-title" id="exampleModalLabel">
          Cart Details
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-image">
          <thead>
            <tr>
              <th width="5%">Ticket#</th>
              <th>Image</th>
              <th>Product</th>
              <th>Price</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="cart-data">
            <?php $data['cart'];?>
           
          <tr id="msg" @if($data['cart']==0) style="display:table-row;" @else style="display:none;" @endif>
              <td colspan="5" class="text-center" ><div class="alert alert-danger">Your cart is empty</div></td>
              
            </tr>


          </tbody>
        </table> 
        <div class="d-flex justify-content-end">
          <h5>Total: <span class="price text-success total">${{  $data['total'] }}</span></h5>
        </div>
        <!-------------slider------------------------->

<div id="cont" <?php if($data['similar_items'] ==0) { ?> style="display: none" <?php }else { ?> style="display: block" <?php } ?> >
 <h3 class="text-center">Similar Items</h3>
  <div id="slider-container">
    <span id="right-btn" class="fa fa-arrow-circle-right" aria-hidden="true">
    </span>
    <span id="left-btn" class="fa fa-arrow-circle-left" aria-hidden="true">
    </span>
    <div id="similar_products">
     <?php echo $data['similar_items'];?>
    </div>
  </div>
</div>

      </div>



      <div class="modal-footer border-top-0 d-flex justify-content-between">
        <button type="button" class="btn btn-secondary cust_cart_cls" data-dismiss="modal">Close</button>
        <a href="{{ url('user/tickets')}}" @if($data['cart']==0) style="display:none;" @else style="display:  block;" @endif type="button" class="btn btn-success cust_cart_check">Checkout</a>
      </div>
    </div>
  </div>
</div>

@include('login')
@include('signup')
<script type="text/javascript">
  
//   $(document).ready(function ($) {
//     var url = window.location.href;
//     var activePage = url;
//     $('.main-nav a').each(function () {
//         var linkPage = this.href;

//         if (activePage == linkPage) {
//             $(this).closest("li").addClass("active");
//         }
//     });
// });
</script>
