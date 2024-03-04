@extends( 'admin.layouts.admin_app' )
@section( 'content' )

<div class="app-title">

	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i>
		</li>
		<li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a>
		</li>
	</ul>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="tile">
			
			<body>
		
		<div class="container">
			<div class="form_pia">
				<h1>GDPR PIA FORM</h1>
				<p>GDPR Privacy Impact Assessment form</p>
				<h3>General</h3>q
				<form>
					<div class="form-group">
						<label><h5>What activity are you assessing?</h5></label>
						<input type="text" class="form-control" name="first_field">
					</div>
					<div class="form-group">
						<label><h5>What activity are you assessing?</h5></label>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> New activity
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Existing activity
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Applicable
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
					</div>
					<div class="form-group">
						<label><h5>Who are the key participant groups in this activity?</h5></label>
						<p>These are the groups who will participate in the processing activity</p>
						<p>Select all that apply:</p>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> HR
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Legal
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Sales
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Marketing
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> IT
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> R&D
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Finance/Accounting
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Executive Management
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> External Parties
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Applicable
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Other
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<label><h5>What is the project starting date?</h5></label>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Date Picker Option
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Applicable
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Submit
							</label>
						</div>
						<div class="form-group">
							<label >Begin voorverkoop periode</label>
							<input type="date" name="bday" max="3000-12-31"
							min="1000-01-01" class="form-control">
						</div>
					</div>
					<h3 class="pt-2">Collection</h3><br><br>
					<div class="bullets">
					<h5>What data is involved in the activity?</h5>
					<p>Please select the data elements processed.</p>
					<h5><li>Background Checks</li></h5>
					<div class="form-group radio">
						<label><input type="radio">  Criminal History</label>
						<label><input type="radio">  Criminal Records</label>
						<label><input type="radio">  Driving Citations</label>
						<label><input type="radio">  Drug Test Results</label>
						<label><input type="radio">  Reference or Background checks</label>
					</div>
					<h5><li>Biometric</li></h5>
					<div class="form-group radio">
						<label><input type="radio">  Fingerprint</label>
						<label><input type="radio">  Retina Scan</label>
						<label><input type="radio">  Voice Recognition</label>
						<label><input type="radio">  Facial Recognition</label>
					</div>
					<h5><li>Browsing Information</li></h5>
					<div class="form-group radio">
						<label><input type="radio">  Browsing Time</label>
						<label><input type="radio">  IP Address</label>
						<label><input type="radio">  Website History</label>
						<label><input type="radio">  Cookies</label>
					</div>
					<h5><li>Contact Information</li></h5>
					<div class="form-group radio">
						<label><input type="radio">  Contact details</label>
						<label><input type="radio">  Emergency Contact Details</label>
						<label><input type="radio">  Home Address</label>
						<label><input type="radio">  Personal Email</label>
						<label><input type="radio">  Phone Numbers</label>
						<label><input type="radio">  Previous Residence Address</label>
					</div>
					<h5><li>Education & Skills</li></h5>
					<div class="form-group radio">
						<label><input type="radio">  Academic Transcripts</label>
						<label><input type="radio">  Education & training history</label>
						<label><input type="radio">  Educational Degrees</label>
						<label><input type="radio">  Grade Languages</label>
					</div>
					<h5><li>Contact Information</li></h5>
					<div class="form-group radio">
						<label><input type="radio">  Home Address</label>
						<label><input type="radio">  Work Address</label>
						<label><input type="radio">  Personal Email</label>
						<label><input type="radio">  Phone Numbers</label>
					</div>
					<h5><li>Financial</li></h5>
					<div class="form-group radio">
						<label><input type="radio">  Bank account information</label>
						<label><input type="radio">  Credit card number</label>
						<label><input type="radio">  Credit score</label>
					</div>
					<h5><li>Personal Identification</li></h5>
					<div class="form-group radio">
						<label><input type="radio">  Age</label>
						<label><input type="radio">  Date of Birth</label>
						<label><input type="radio">  First Name </label>
						<label><input type="radio">  Last Name</label>
						<label><input type="radio">  Gender</label>
						<label><input type="radio">  Height</label>
						<label><input type="radio">  Weight</label>
						<label><input type="radio">  Marital Status</label>
						<label><input type="radio">  Nationality</label>
						<label><input type="radio">  Racial or Ethnic Origin</label>
						<label><input type="radio">  Religion / Religious beliefs</label>
						<label><input type="radio">  Sexual Orientation</label>
						<label><input type="radio">  Signature</label>
					</div>
					<h5><li>Social</li></h5>
					<div class="form-group radio">
						<label><input type="radio">  Social Media Account</label>
						<label><input type="radio">  Social Media Contact</label>
						<label><input type="radio">  Social Media History</label>
					</div>
					<h5><li>User Account Information</li></h5>
					<div class="form-group radio">
						<label><input type="radio">  Account Age</label>
						<label><input type="radio">  Account Number</label>
						<label><input type="radio">  Account Password</label>
					</div>
					<h5><li>Contact Information</li></h5>
					<div class="form-group radio">
						<label><input type="radio">  Contact details</label>
						<label><input type="radio">  Emergency Contact Details</label>
						<label><input type="radio">  Home Address</label>
						<label><input type="radio">  Personal Email</label>
						<label><input type="radio">  Phone Numbers</label>
					</div>
					<h5><li>Education & Skills</li></h5>
					<div class="form-group radio">
						<label><input type="radio">  Academic Transcripts</label>
						<label><input type="radio">  Education & training history</label>
						<label><input type="radio">  Educational Degrees</label>
						<label><input type="radio">  Languages</label>
						<label><input type="radio">  Grade</label>
					</div>
					<h5><li>Employment Information</li></h5>
					<div class="form-group radio">
						<label><input type="radio">  Benefits and entitlements data</label>
						<label><input type="radio">  Business unit / division</label>
						<label><input type="radio">  Company / entity</label>
						<label><input type="radio">  Contract type - fixed term / temporary / permanent etc.</label>
						<label><input type="radio">  Corporate Credit or Debit Card Numbers</label>
						<label><input type="radio">  Disciplinary action</label>
						<label><input type="radio">  Disciplinary action</label>
						<label><input type="radio">  Exit interview and comments</label>
						<label><input type="radio">  Grievances and complaints</label>
						<label><input type="radio">  Health & safety related information and reporting</label>
						<label><input type="radio">  Hours of work</label>
						<label><input type="radio">  Job application details (e.g. application form, interview notes, references)</label>
						<label><input type="radio">  Job title / role</label>
						<label><input type="radio">  Line / Reporting manager</label>
						<label><input type="radio">  Office location</label>
						<label><input type="radio">  Performance rating</label>
						<label><input type="radio">  Personnel number</label>
						<label><input type="radio">  Previous work history</label>
						<label><input type="radio">  Record of absence / time tracking / annual leave</label>
						<label><input type="radio">  Salary / wage</label>
						<label><input type="radio">  Salary / wage expectation</label>
						<label><input type="radio">  Start date</label>
						<label><input type="radio">  Workers Compensation Claims</label>
					</div>
					<h5><li>Family Information</li></h5>
					<div class="form-group radio">
						<label><input type="radio">  Children's Name</label>
						<label><input type="radio">  Parent's Names</label>
					</div>
					<h5><li>Financial</li></h5>
					<div class="form-group radio">
						<label><input type="radio">  Bank account information</label>
						<label><input type="radio">  Bank statements</label>
						<label><input type="radio">  Bonus payments</label>
						<label><input type="radio">  Compensation data</label>
					</div>
					<h5><li>Genetic</li></h5>
					<div class="form-group radio">
						<label><input type="radio">  Genetic Sequence</label>
					</div>
					<h5><li>Government Identifiers</li></h5>
					<div class="form-group radio">
						<label><input type="radio">  Driving license number</label>
						<label><input type="radio">  National Identification Number</label>
						<label><input type="radio">  National identity card details</label>
						<label><input type="radio">  Passport number</label>
					</div>
					<h5><li>Professional Experience & Affiliations</li></h5>
					<div class="form-group radio">
						<label><input type="radio">  Professional Memberships</label>
						<label><input type="radio">  Qualifications / certifications</label>
						<label><input type="radio">  Trade union membership</label>
					</div>
					<h5><li>Travel & Expense</li></h5>
					<div class="form-group radio">
						<label><input type="radio">  Expense details</label>
						<label><input type="radio">  Travel History</label>
						<label><input type="radio">  Travel booking details</label>
					</div>
					<h5><li>Workplace Welfare</li></h5>
					<div class="form-group radio">
						<label><input type="radio">  Bullying and harassment details</label>
					</div>
					</div>
					<label><h5>Did the individuals consent to the collection?</h5></label>
					<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Yes
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> No
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Applicable 
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<label><h5>Could any of the data have been collected from minors?</h5></label>
					<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Yes
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> No
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Applicable 
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<label><h5>What are the sources of the data collected?</h5></label><br>
						<label>Select all that apply:</label>
					<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Individual
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Commercial Data Aggregators
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Publicly Available Information
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Other
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<label><h5>How is the data collected?</h5></label><br>
						<label>Select all that apply:</label>
					<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Hard copy forms
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Electronic forms
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Online transactions
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Other
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<label><h5>Location(s) of individuals:</h5></label><br>
						<label>Select all that apply:</label>
					<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> United States
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Europe (EU + EEA)
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Europe (Non EU)
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Canada
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Central America / Caribbean / Mexico
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Asia - Pacific
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Middle East
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Africa
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Other
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<h3 class="pt-2">Notice</h3><br><br>
						<label><h5>Was notice provided to the individuals prior to collection of their data?</h5></label><br>
						<label>Select all that apply:</label>
					<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Yes
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> No
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<label><h5>How was the notice provided?</h5></label><br>
					<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Written
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Verbal
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Other	
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<label><h5>Did the notice include the following?</h5></label><br>
						<label>Select all that apply:</label>
					<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Scope of the notice
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Types of PII collected
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> How the PII is collected
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> How the PII is used	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> How the PII is shared	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Sector or geographic-specific disclosures	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Individual choice mechanisms	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Data security standards or practices followed	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> How revisions and updates to the notice are handled/communicated	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Contact information for requesting additional information or lodging complaints	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> How to lodge complaints	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Other	
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<h3 class="pt-2">Use</h3><br><br>
						<label><h5>What is the data used for?</h5></label><br>
						<label>You may provide additional detail in the notes field below.</label>
						<label>Select all that apply:</label>
					<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Payroll Processing
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Sales
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Market Research
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Finance	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Travel Planning	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Recruiting Activities	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Benefits	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Compensation	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Background Checks	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Customer Engagement complaints	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Directed Marketing	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Customer Service
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Public Health and Safety	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Customer Relationship Management	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Retirement Planning	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Insurance Processing	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Health Related Initiatives
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> New Product Development	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Online Learning Initiatives	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> e-Commerce Activities	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Contractual Obligations	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure	
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Other	
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<label><h5>Is the use of the data limited to only these purposes?</h5></label>
					<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Yes
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> No 
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<label><h5>Is consent obtained for additional uses of the data that are beyond its defined purpose?</h5></label>
					<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Yes
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> No 
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<h3 class="pt-2">Access</h3><br><br>
						<label><h5>Is access to the data limited to a need-to-know basis?</h5></label>
					<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Yes
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> No 
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<label><h5>Is the data accessible by third parties?</h5></label>
					<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Yes
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> No 
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<label><h5>Are access control procedures in place?</h5></label>
					<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Yes
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> No 
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<label><h5>Are controls in place to correct inaccurate data?</h5></label>
					<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Yes
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> No 
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<label><h5>Are individuals able to request that their data be corrected?</h5></label>
					<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Yes
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> No 
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<label><h5>Are individuals able to ask to have their data erased?</h5></label>
					<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Yes
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> No 
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<h3 class="pt-2">Handling/storage</h3><br><br>
						<label><h5>What assets are used to process the data for this activity?</h5></label>
						<label>This includes assets involved in the collection, storage, analysis, sharing, etc. of the data</label>
						<div class="list">
						<ul>
							<li>List available assets to select</li>
							<li>Add option to create a new asset</li>
							<li>Not Sure</li>
							<li>Not Applicable</li>
						</ul>
						</div>
						<label><h5>Is the data encrypted at the storage location?</h5></label>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Yes
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> No 
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<label><h5>Are there backups of the data?</h5></label>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Yes
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> No 
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<h3 class="pt-2">Transfer</h3><br><br>
						<label><h5>Can the data be transmitted in a structured, commonly used and machine-readable format?</h5></label>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Yes
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> No 
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<label><h5>Is the data shared with third-parties?</h5></label>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Yes
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> No 
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<label><h5>Is the data encrypted while in transit?</h5></label>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Yes
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> No 
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<h3 class="pt-2">Retention and destruction</h3><br><br>
						<label><h5>Is there a retention schedule?</h5></label>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Yes
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> No 
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<label><h5>Are there specific events that trigger the deletion of data?</h5></label>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Yes
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> No 
							</label>
						</div>
						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="remember" required> Not Sure
							</label>
						</div>
						<div class="form-group">
							<label>Additional comments below:</label>
							<input type="text" class="form-control" name="first_field">
						</div>
						<h3 class="pt-2">Additional information</h3><br><br>
						<label><h5>Please add any additional notes below</h5></label>
						<div class="form-group">
							<label>Additional Notes:</label>
							<input type="text" class="form-control" name="first_field">
						</div>


						<button type="Submit" class="btn btn-primary">Submit</button>

				</form>
			</div>
		</div>
		
	</body>

		</div>
	</div>
</div>




@endsection