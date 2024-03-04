<!DOCTYPE html>
<html>
	<head>
		<title>Email</title>
	</head>
	<body style="background-color: #F1F3F3;">
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
					
                        <?php if(Auth::user()->image_name=="") { ?>
							<td>	<img id="blah" src="{{ url('dummy.jpg')}}" style=" width: 100px;; height: 90px; " /></td>
								<?php }else { ?>
							<td>	<img id="blah" class="img-fluid" src="{{ url('img/'.Auth::user()->image_name) }}" name="profile_image" style=" height: 90px; width: 100px;"> </td>
								<?php } ?>
                        
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td><strong style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;padding: 30px 30px 0;color: #CFCFCF; font-size: 14px;">HELLO THERE,</strong></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td><h3 style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;font-size: 36px; color: #66BECD; letter-spacing: 2px; text-align: center;">Assessment Form</h3></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td><img src="{!! url('divider.png') !!}"></td>
				</tr>
				<tr>
					<td><p style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;letter-spacing: 1px;width: 80%;text-align: justify;color: #555555; margin: 40px auto;">
						<br>
							Please <a href="{{ url('/Forms/'.$user_form.'/'.$form_link_id) }}">click here</a> to fill this form before {{ $expiry_time }}<br>
							Thanks.</p></td>
				</tr>				
				
				<tr style="padding: 14px 0 0;text-align: center;display: flex;flex-direction: row;justify-content: center;">
					<td>Mobile phone <a href="tel:00000000" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;color: #555555; text-decoration: none;">00000000</a></td>
					<td><a href="mailto:D3GForm@info.com" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:17px;margin-left: 20px; color: #555555; text-decoration: none;font-size:14px; font-weight: bold;">D3GForm@info.com</a></td>
					<td style="text-align: right;"><img src="{!! url('image/D3GDPR42.png') !!}" style="width: 40%;margin-bottom: 20px;"></td>
				</tr>
		</table>
	</body>
</html>