<?php if ( !isset( $_GET['app'] ) ) { ?>

<div class="grid-column grid-column-8">

	<div class="content-block content-block-generic">

		<h1>Take The First Step</h1>

		<div class="response-message"></div>

		<form class="inquire-widget-form" method="post" action="">

			<input type="hidden" value="inquire-form-widget" name="form" />
			
			<div>
				<label for="first-name">First Name</label>
				<input type="text" name="first_name" id="first-name" class="first-name" value="" tabindex="1" aria-label="Enter Your First Name" />
				<span class="error-info">Must Enter A Valid First Name</span>
			</div>

			<div>
				<label for="last-name">Last Name</label>
				<input type="text" name="last_name" id="last-name" class="last-name" value="" tabindex="2" aria-label="Enter Your Last Name" />
				<span class="error-info">Must Enter A Valid Last Name</span>
			</div>

			<div>
				<label for="email">Email</label>
				<input type="text" name="email" id="email" class="email" value="" tabindex="3" aria-label="Enter Your Email Address" />
				<span class="error-info">Must Enter A Valid Email</span>
			</div>

			<div>
				<label for="phone">Phone</label>
				<input type="text" name="phone" id="phone" class="phone" value="" tabindex="4" aria-label="Enter Your Phone Number" />
				<span class="error-info">Must Enter A Valid Phone Number</span>
			</div>

			<!-- <div class="select-group">
				<label for="program-of-interest">What is your program of interest?</label>
				<span class="select-dropdown">
					<select name="program" id="program-of-interest" class="program-of-interest" tabindex="5" aria-label="What is your program of interest?">
						<option value="">Program Of Interest</option>
						<option value="Early Childhood Studies (MS)">Early Childhood Studies (MS)</option>
						<option value="Early Childhood Administration, Management, &amp; Leadership (CERT)">Early Childhood Administration, Management, &amp; Leadership (CERT)</option>
					</select>
				</span>
				<span class="error-info">Must Select A Program</span>
			</div> -->

			<div class="form-action">
				<input type="submit" name="signup" id="signup" class="button" value="Submit" tabindex="5" aria-label="Submit Your Info" />
				<input type="hidden" id="lead_source" name="lead_source" value="Marketing Site">
				<input type="hidden" id="program" name="program" value="Early Childhood Studies (MS)">
			</div>

		</form>

		<p class="footnote">By submitting this form, you understand that Walden may contact you via email and or phone regarding programs in which you have expressed interest. You may opt-out at any time. Please view our <a href="/privacy-policy/">Privacy Policy</a> or <a href="/contact-us/">Contact Us</a> for more details.</p>

	</div>

</div>

<?php } ?>

<div class="grid-column grid-column-3 grid-column-offset-1">
	<div class="content-block content-block-generic">
		<div class="content-block-copy">
			<h3>Contact Information</h3>
			<p>Toll Free: 1-855-598-3427<br>
			<a href="mailto:cbl@waldenu.edu">cbl@waldenu.edu</a></p>
			<p>Walden University<br>
			100 Washington Avenue South, #900<br>
			Minneapolis, MN 55401</p>
		</div>
	</div>
</div>