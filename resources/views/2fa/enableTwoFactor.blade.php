@extends('layouts.2factorlayout')

@section('content')

<div class="container">
    <div class="row justify-content-md-center">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">2FA Secret Key</div>

                <div class="panel-body">
                    Open up your 2FA mobile app and scan the following QR barcode:
                    <br />
                    <img alt="Image of QR barcode" src="{{ $image }}" />
                    
                    <br />
                    If your 2FA mobile app does not support QR barcodes, 
                    enter in the following number: <code>{{ $secret }}</code>
                    <br /><br />
                    
					
					<form class="form-horizontal" method="POST" action="{{ route('validate') }}">
						{{ csrf_field() }}
						
						<button type="submit" class="btn btn-primary">
							Enable 2FA
						</button>
					</form>
							
							
					<!-- <a href="{{ url('/admin') }}">Go Home</a> -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

