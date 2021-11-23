<?php
/* ****************************    tsml_ajax_feedback Override    **************************************** */
remove_action( "wp_ajax_tsml_feedback", "tsml_ajax_feedback");
remove_action( "wp_ajax_nopriv_tsml_feedback", "tsml_ajax_feedback");
add_action("wp_ajax_tsml_feedback", "tsmlfe_ajax_feedback");
add_action("wp_ajax_nopriv_tsml_feedback", "tsmlfe_ajax_feedback");
if (!function_exists('tsmlfe_ajax_feedback')) {
	function tsmlfe_ajax_feedback() {
		global $tsml_feedback_addresses, $tsml_nonce, $tsml_programs, $tsml_program, $tsml_region;
		
		$IsNew = false;
		$IsChange = false; 
		$IsRemove = false; 
		$IsFeedback = true;  // Is Default
		$RequestType = "feedback";

		// Determine Request Type 
		if ( isset( $_POST['submit'] ) ) {	$RequestType = sanitize_text_field($_POST['submit']); } 

		if ( isset( $_POST['submit'] ) && ( $_POST['submit'] === 'change') ) {
				$IsChange = true; // prove in the Change Processing that there really is a change
				$IsFeedback = false;
		} 
		elseif ( isset( $_POST['submit'] ) && ( $_POST['submit'] === 'new') ) {
			$IsNew = true;
			$IsFeedback = false;
		}
		elseif ( isset( $_POST['submit'] ) && ( $_POST['submit'] === 'remove') ) {
			$IsRemove = true;
			$IsFeedback = false;
		}
		else {
			$IsFeedback = true;
		}
	
		$name = stripslashes(sanitize_text_field($_POST['tsml_name']));
		$email = sanitize_email($_POST['tsml_email']);
		$myTypesArray = $tsml_programs[$tsml_program]['types'];

		
		//------------------ Start HTML Layout ---------------------- 
		$message = '<p style="padding-bottom: 20px; border-bottom: 2px dashed #ccc; margin-bottom: 20px;">' . nl2br(implode( "\n", array_map( 'sanitize_text_field', explode( "\n", stripslashes( $_POST['tsml_message'] ) ) ) ) . '</p>');
		$message .= "<table border='1' style='width:600px;'><tbody>";

		if ( $RequestType === 'new') {
			//break;
		}
		else 
		{
			if ( is_numeric( $_POST ['meeting_id'] ) ) { $meeting_id = (int)sanitize_text_field($_POST['meeting_id']); } else { $meeting_id = 0; }
			$meeting  = tsml_get_meeting ( intval( $meeting_id ) );
			$permalink = get_permalink($meeting->ID);
			$types_string = implode(', ', tsml_sanitize_array($meeting->types));
			$post_title = sanitize_text_field($meeting->post_title);

			$daytime = tsml_format_day_and_time( $meeting->day, $meeting->time);

			$typesDescStr = '';
			$typesDescArray = tsml_sanitize_array($meeting->types);
			foreach ($typesDescArray as $mtg_key) {
				$mtg_description = $myTypesArray[$mtg_key];
				$typesDescStr .= $mtg_description.'<br>';
				$typesDescArray[$mtg_key] = $mtg_description;
			}

			$message_lines = array(
				__('Requestor', '12-step-meeting-list-feedback-enhancement') =>  "<tr><td>Requestor</td><td>$name <a href='mailto:' $email > $email </a></td></tr>",
				__('Meeting', '12-step-meeting-list-feedback-enhancement') => "<tr><td>Meeting</td><td><a href='$permalink'> $post_title </a></td></tr>",
				__('Meeting Id', '12-step-meeting-list-feedback-enhancement') =>  "<tr><td>Meeting Id</td><td>$meeting_id</td></tr>",
				__('When', '12-step-meeting-list-feedback-enhancement') => "<tr><td>When</td><td>$daytime</td></tr>",
			);

			if (!empty($meeting->types)) {
				$message_lines[__('Types', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Types</td><td>$typesDescStr</td></tr>";
			}

			if (!empty($meeting->notes)) {
				$message_lines[__('Notes', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Notes</td><td>$meeting->notes</td></tr>";  
			}

			if (!empty($meeting->conference_url)) {
				$message_lines[__('Conference URL', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Conference URL</td><td>$meeting->conference_url</td></tr>";  
			}

			if (!empty($meeting->conference_url_notes)) {
				$message_lines[__('Conference URL Notes', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Conference URL Notes</td><td>$meeting->conference_url_notes</td></tr>";  
			}

			if (!empty($meeting->conference_phone)) {
				$message_lines[__('Conference Phone', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Conference Phone</td><td>$meeting->conference_phone</td></tr>";  
			}

			if (!empty($meeting->conference_phone_notes)) {
				$message_lines[__('Conference Phone Notes', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Conference Phone Notes</td><td>$meeting->conference_phone_notes</td></tr>";  
			}

			if (!empty($meeting->location)) {
				$message_lines[__('Location', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Location</td><td>$meeting->location</td></tr>";  
			}

			if (!empty($meeting->formatted_address)) {
				$message_lines[__('Address', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Address</td><td>$meeting->formatted_address</td></tr>";  
			}

			if (!empty($meeting->region)) {
				$message_lines[__('Region', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Region</td><td>$meeting->region</td></tr>";  
			}
			
			if (!empty($meeting->sub_region)) {
				$message_lines[__('Sub Region', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Region</td><td>$meeting->sub_regioin</td></tr>";  
			}

			if (!empty($meeting->location_notes)) {
				$message_lines[__('Location Notes', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Location Notes</td><td>$meeting->location_notes</td></tr>";  
			}

			/* Addition Group Information */

			if (!empty($meeting->group)) {
				$message_lines[__('Group Name', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Group Name</td><td>$meeting->group</td></tr>";  
			}

			if ( $meeting->group_id ) {

				if (!empty($meeting->district) && strlen($meeting->district) > 0 ) {
					$message_lines[__('District', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>District</td><td>$meeting->district</td></tr>";  
				}

				if (!empty($meeting->sub_district)) {
					$message_lines[__('Sub District', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Sub District</td><td>$meeting->sub_district</td></tr>";  
				}

				if (!empty($meeting->website)) {
					$message_lines[__('Website', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Website</td><td>$meeting->website</td></tr>";  
				}

				if (!empty($meeting->website_2)) {
					$message_lines[__('Website 2', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Website 2</td><td>$meeting->website_2</td></tr>";  
				}
			
				if (!empty($meeting->mailing_address)) {
					$message_lines[__('Mailing Address', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Mailing Address</td><td>$meeting->mailing_address</td></tr>";  
				}

				if (!empty($meeting->email)) {
					$message_lines[__('Email', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Email</td><td>$meeting->email</td></tr>";  
				}

				if (!empty($meeting->phone)) {
					$message_lines[__('Phone', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Phone</td><td>$meeting->phone</td></tr>";  
				}

				if (!empty($meeting->email)) {
					$message_lines[__('Email', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Email</td><td>$meeting->email</td></tr>";  
				}

				if (!empty($meeting->group_notes)) {
					$message_lines[__('Group Notes', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Group Notes</td><td>$meeting->group_notes</td></tr>";  
				}

				if (!empty($meeting->venmo)) {
					$message_lines[__('Venmo', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Venmo</td><td>$meeting->venmo</td></tr>";  
				}

				if (!empty($meeting->square)) {
					$message_lines[__('Square', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Square</td><td>$meeting->square</td></tr>";  
				}

				if (!empty($meeting->paypal)) {
					$message_lines[__('Paypal', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Paypal</td><td>$meeting->paypal</td></tr>";  
				}

				if (!empty($meeting->contact_1_name)) {
					$message_lines[__('Contact 1 Name', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 1 Name</td><td>$meeting->contact_1_name</td></tr>";  
				}

				if (!empty($meeting->contact_1_email)) {
					$message_lines[__('Contact 1 Email', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 1 Email</td><td>$meeting->contact_1_email</td></tr>";  
				}

				if (!empty($meeting->contact_1_phone)) {
					$message_lines[__('Contact 1 Phone', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 1 Phone</td><td>$meeting->contact_1_phone</td></tr>";  
				}

				if (!empty($meeting->contact_2_name)) {
					$message_lines[__('Contact 2 Name', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 2 Name</td><td>$meeting->contact_2_name</td></tr>";  
				}

				if (!empty($meeting->contact_2_email)) {
					$message_lines[__('Contact 2 Email', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 2 Email</td><td>$meeting->contact_2_email</td></tr>";  
				}

				if (!empty($meeting->contact_2_phone)) {
					$message_lines[__('Contact 2 Phone', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 2 Phone</td><td>$meeting->contact_2_phone</td></tr>";  
				}

				if (!empty($meeting->contact_3_name)) {
					$message_lines[__('Contact 3 Name', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 3 Name</td><td>$meeting->contact_3_name</td></tr>";  
				}

				if (!empty($meeting->contact_3_email)) {
					$message_lines[__('Contact 3 Email', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 3 Email</td><td>$meeting->contact_3_email</td></tr>";  
				}

				if (!empty($meeting->contact_3_phone)) {
					$message_lines[__('Contact 3 Phone', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 3 Phone'</td><td>$meeting->contact_3_phone</td></tr>";  
				}
			}

			//---------------   Change Processing - skip for adds, removals, & feedback  --------------------

			if ( $RequestType === 'change' ) {

				$IsChange = false; // must prove to be real change

				$chg_name = stripslashes(sanitize_text_field($_POST['name']) );
				$chg_day = sanitize_text_field($_POST['day']);
				$chg_time = sanitize_text_field($_POST['start_time']);
				$chg_end_time = sanitize_text_field($_POST['end_time']);
				$chg_types_string = sanitize_text_field(implode(', ', array_filter( $_POST['types'] ) ) );
				$chg_notes = sanitize_text_field($_POST['content']);
				$chg_conference_url = sanitize_text_field($_POST['conference_url']);
				$chg_conference_url_notes = sanitize_text_field($_POST['conference_url_notes']);
				$chg_conference_phone = sanitize_text_field($_POST['conference_phone']);
				$chg_conference_phone_notes = sanitize_text_field($_POST['conference_phone_notes']);
				$chg_location = stripslashes(sanitize_text_field($_POST['location']));
				$chg_address = stripslashes( sanitize_text_field( $_POST['formatted_address'] ) );
				$chg_region_id = sanitize_text_field($_POST['region']);
				$chg_sub_region = sanitize_text_field($_POST['sub_region']);
				$chg_location_notes = sanitize_text_field($_POST['location_notes'] );
				$chg_group = stripslashes(sanitize_text_field($_POST['group']));
				$chg_district_id = sanitize_text_field($_POST['district']);
				$chg_sub_district = sanitize_text_field($_POST['sub_district'] );
				$chg_group_notes = sanitize_text_field($_POST['group_notes'] );
				$chg_website = sanitize_text_field($_POST['website_1']);
				$chg_website_2 = sanitize_text_field($_POST['website_2']);
				$chg_email = sanitize_text_field($_POST['email']);
				$chg_phone = preg_replace('/[^[:digit:]]/', '', sanitize_text_field($_POST['phone']));
				$chg_mailing_address = stripslashes(sanitize_text_field($_POST['mailing_address']));
				$chg_venmo = sanitize_text_field($_POST['venmo']);
				$chg_square = sanitize_text_field($_POST['square']);
				$chg_paypal = sanitize_text_field($_POST['paypal']);
				$chg_contact_1_name = sanitize_text_field($_POST['contact_1_name']);
				$chg_contact_1_email = sanitize_text_field($_POST['contact_1_email']);
				$chg_contact_1_phone = preg_replace('/[^[:digit:]]/', '', sanitize_text_field($_POST['contact_1_phone']));
				$chg_contact_2_name = sanitize_text_field($_POST['contact_2_name']);
				$chg_contact_2_email = sanitize_text_field($_POST['contact_2_email']);
				$chg_contact_2_phone = preg_replace('/[^[:digit:]]/', '', sanitize_text_field($_POST['contact_2_phone']));
				$chg_contact_3_name = sanitize_text_field($_POST['contact_3_name']);
				$chg_contact_3_email = sanitize_text_field($_POST['contact_3_email']);
				$chg_contact_3_phone = preg_replace('/[^[:digit:]]/', '', sanitize_text_field($_POST['contact_3_phone']));

				$m_name = str_replace("\'s", "", sanitize_text_field($meeting->post_title ));
				$c_name = str_replace("\'s", "", sanitize_text_field($_POST['name']));
				if ( ( strcmp( $m_name, $c_name ) !== 0) ) {
					$message_lines[__('Meeting', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Meeting</td><td style='color:red;'>$c_name</td></tr>";  
					$IsChange = true;
				}

				$chg_daytime = tsml_format_day_and_time($chg_day, $chg_time);

				if ($chg_daytime !== $daytime) {
					$message_lines[__('When', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>When</td><td style='color:red;'>$chg_daytime</td></tr>";  
					$IsChange = true;
				}

				$chg_typesDescStr = '';
				$chg_typesDescArray = tsml_sanitize_array($_POST['types']);
				$typesArrayHasChanged = false;
				if (!empty($_POST['types'])){
					//if a meeting is both open and closed, make it closed
					if (in_array('C', $chg_typesDescArray) && in_array('O', $chg_typesDescArray)) {
						$chg_typesDescArray = array_diff($chg_typesDescArray, array('O'));
					}
					foreach ($chg_typesDescArray as $mtg_key) {
						$mtg_description = $myTypesArray[$mtg_key];
						$chg_typesDescStr .= $mtg_description.'<br>';
						$chg_typesDescArray[$mtg_key] = $mtg_description;
						if (!in_array($mtg_key, $typesDescArray)) { $typesArrayHasChanged = true; }
					}
				}
				else {
					$chg_typesDescStr = 'No Types Selected';
				}

				if ( $typesArrayHasChanged === true )  {
					$message_lines[__('Types', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Types</td><td style='color:red;'>$chg_typesDescStr</td></tr>";
					$IsChange = true;
				}

				$old = explode (' ', $meeting->notes);
				$new = explode (' ', $chg_notes);
				if ( $old !==  $new )  {
					// Try a 2nd comparison with white space removed
					$m_notes = str_replace(' ', '', $meeting->notes);
					$c_notes = str_replace(' ', '', $chg_notes);
					if ( ( strcmp( $m_notes, $c_notes ) !== 0) ) {
						$message_lines[__('Notes', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Notes</td><td style='color:red;'>$chg_notes</td></tr>";  
						$IsChange = true;
					}
				}

				if ( $chg_conference_url !== $meeting->conference_url ) {
					$message_lines[__('Conference URL', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Conference URL</td><td style='color:red;'>$chg_conference_url</td></tr>";  
					$IsChange = true;
				}

				$m_notes = str_replace(' ', '', $meeting->conference_url_notes);
				$c_notes = str_replace(' ', '', $chg_conference_url_notes);
				if ( ( strcmp( $m_notes, $c_notes ) !== 0) ) {
					$message_lines[__('Conference URL Notes', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Conference URL Notes</td><td style='color:red;'>$chg_conference_url_notes</td></tr>";  
					$IsChange = true;
				}

				if (  $chg_conference_phone !== $meeting->conference_phone )  {
					$message_lines[__('Conference Phone', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Conference Phone</td><td style='color:red;'>$chg_conference_phone</td></tr>";  
					$IsChange = true;
				}

				$m_notes = str_replace(' ', '', $meeting->conference_phone_notes);
				$c_notes = str_replace(' ', '', $chg_conference_phone_notes);
				if ( ( strcmp( $m_notes, $c_notes ) !== 0) ) {
					$message_lines[__('Conference Phone Notes', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Conference Phone Notes</td><td style='color:red;'>$chg_conference_phone_notes</td></tr>";  
					$IsChange = true;
				}

				if (  $chg_location !== $meeting->location )  {
					$message_lines[__('Location', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Location</td><td style='color:red;'>$chg_location</td></tr>";  
					$IsChange = true;
				}

				if (strcmp(rtrim($meeting->formatted_address), rtrim($_POST['formatted_address'])) !== 0) {
					$message_lines[__('Address', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Address</td><td style='color:red;'>$chg_address</td></tr>";  
					$IsChange = true;
				}

				if ( $chg_region_id != $meeting->region_id) {
					$chg_region = '';
					$chg_region = get_the_category_by_ID($chg_region_id);
					$message_lines[__('Region', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Region</td><td style='color:red;'>$chg_region</td></tr>"; 
					$IsChange = true;
				}

				if ( $chg_sub_region !== $meeting->sub_region ) {
					$message_lines[__('Sub Region', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Sub Region</td><td style='color:red;'>$chg_sub_region</td></tr>";  
					$IsChange = true;
				}

				if ( $chg_location_notes !== $meeting->location_notes ) {
					$message_lines[__('Location Notes', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Location Notes</td><td style='color:red;'>$chg_location_notes</td></tr>";  
					$IsChange = true;
				}

				/* Addition Group Information - when meeting is registered group with an id */
				if ( ( strcmp( $meeting->group, $chg_group ) !== 0) ) {
					$message_lines[__('Group Name', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Group Name</td><td style='color:red;'>$chg_group</td></tr>";  
					$IsChange = true;
				}

				if ( !empty( $_POST['district'] ) ) {
					$chg_district = get_the_category_by_ID($chg_district_id);
					if ( strlen($chg_district) > 0  && $meeting->district_id != $chg_district_id ) {					
						$message_lines[__('District', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>District</td><td style='color:red;'>$chg_district</td></tr>"; 
						$IsChange = true;
					}
				}

				if ($meeting->sub_district != $chg_sub_district) {
					$message_lines[__('Sub District', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Sub District</td><td style='color:red;'>$chg_sub_district</td></tr>";  
					$IsChange = true;
				}

				if ( $meeting->group_notes != $chg_group_notes) {
					$message_lines[__('Group Notes', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Group Notes</td><td style='color:red;'>$chg_group_notes</td></tr>";  
					$IsChange = true;
				}

				if ( $meeting->website != $chg_website ) {
					$message_lines[__('Website', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Website</td><td style='color:red;'>$chg_website</td></tr>";  
					$IsChange = true;
				}

				if ( $meeting->website_2 != $chg_website_2 ) {
					$message_lines[__('Website 2', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Website 2</td><td style='color:red;'>$chg_website_2</td></tr>";  
					$IsChange = true;
				}
			
				if ( $meeting->email != $chg_email ) {
					$message_lines[__('Email', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Email</td><td style='color:red;'>$chg_email</td></tr>";  
					$IsChange = true;
				}

				if ( $meeting->phone != $chg_phone ) {
					$message_lines[__('Phone', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Phone</td><td style='color:red;'>$chg_phone</td></tr>";  
					$IsChange = true;
				}

				if ( $meeting->mailing_address != $chg_mailing_address ) {
					$message_lines[__('Mailing Address', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Mailing Address</td><td style='color:red;'>$chg_mailing_address</td></tr>";  
					$IsChange = true;
				}

				if ( $meeting->venmo != $chg_venmo) {
					$message_lines[__('Venmo', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Venmo</td><td >$chg_venmo</td></tr>";  
					$IsChange = true;
				}

				if ( $meeting->square != $chg_square) {
					$message_lines[__('Square', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Square</td><td style='color:red;'>$chg_square</td></tr>";  
					$IsChange = true;
				}

				if ( $meeting->paypal != $chg_paypal) {
					$message_lines[__('Paypal', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Paypal</td><td style='color:red;'>$chg_paypal</td></tr>";  
					$IsChange = true;
				}

				if ( $meeting->contact_1_name != $chg_contact_1_name ) {
					$message_lines[__('Contact 1 Name', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 1 Name</td><td style='color:red;'>$chg_contact_1_name</td></tr>";  
					$IsChange = true;
				}

				if ( $meeting->contact_1_email != $chg_contact_1_email ) {
					$message_lines[__('Contact 1 Email', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 1 Email</td><td style='color:red;'>$chg_contact_1_email</td></tr>";  
					$IsChange = true;
				}

				if ( $meeting->contact_1_phone != $chg_contact_1_phone ) {
					$message_lines[__('Contact 1 Phone', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 1 Phone</td><td style='color:red;'>$chg_contact_1_phone</td></tr>";  
					$IsChange = true;
				}

				if ( $meeting->contact_2_name != $chg_contact_2_name ) {
					$message_lines[__('Contact 2 Name', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 2 Name</td><td style='color:red;'>$chg_contact_2_name</td></tr>";  
					$IsChange = true;
				}

				if ( $meeting->contact_2_email != $chg_contact_2_email ) {
					$message_lines[__('Contact 2 Email', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 2 Email</td><td style='color:red;'>$chg_contact_2_email</td></tr>";  
					$IsChange = true;
				}

				if ( $meeting->contact_2_phone != $chg_contact_2_phone ) {
					$message_lines[__('Contact 2 Phone', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 2 Phone</td><td >$chg_contact_2_phone</td></tr>";  
					$IsChange = true;
				}

				if ( $meeting->contact_3_name != $chg_contact_3_name ) {
					$message_lines[__('Contact 3 Name', '12-step-meeting-list-feedback-enhancement')] = "<tr><td<td style='color:red;'>Contact 3 Name</td><td style='color:red;'>$chg_contact_3_name</td></tr>";  
					$IsChange = true;
				}

				if ( $meeting->contact_3_email != $chg_contact_3_email ) {
					$message_lines[__('Contact 3 Email', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 3 Email</td><td style='color:red;'>$chg_contact_3_email</td></tr>";  
					$IsChange = true;
				}

				if ( $meeting->contact_3_phone != $chg_contact_3_phone ) {
					$message_lines[__('Contact 3 Phone', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 3 Phone</td><td style='color:red;'>$chg_contact_3_phone</td></tr>";  
					$IsChange = true;
				}

				if ( !$IsChange ) {
					$IsFeedback = true;
				}
			}
		}

		//---------------   New Meeting Processing - skip for removals  --------------------

		if ( $RequestType === 'new') {

			$message = '<p style="padding-bottom: 20px; border-bottom: 2px dashed #ccc; margin-bottom: 20px;">' . nl2br(implode( "\n", array_map( 'sanitize_text_field', explode( "\n", stripslashes( $_POST['tsml_message'] ) ) ) ) . '</p>');
			$message .= "<table border='1' style='width:600px;'><tbody>";

			$new_name = stripslashes(sanitize_text_field($_POST['new_name']));
			$meeting  = tsml_get_meeting();
			$permalink = get_permalink($meeting->ID);
			$new_day = sanitize_text_field($_POST['new_day']);
			$new_time = sanitize_text_field($_POST['new_time']);
			$new_daytime = tsml_format_day_and_time( $new_day, $new_time );

			//------------------ Continue with HTML table construction ----------------------
			$message_lines = array(
				__('Requestor', '12-step-meeting-list-feedback-enhancement') =>  "<tr><td>Requestor</td><td>$name <a href='mailto:' $email > $email </a>;</td></tr>",
				__('Meeting', '12-step-meeting-list-feedback-enhancement') => "<tr><td>Meeting</td><td style='color:blue;' >$new_name</td></tr>'",
				__('When', '12-step-meeting-list-feedback-enhancement') => "<tr><td>When</td><td style='color:blue;' >$new_daytime</td></tr>",
			);

			$new_end_time = sanitize_text_field($_POST['new_end_time']);
			$new_notes = sanitize_text_field($_POST['new_content']);
			$new_conference_url = sanitize_text_field($_POST['new_conference_url']);
			$new_conference_url_notes = sanitize_text_field($_POST['new_conference_url_notes']);
			$new_conference_phone = sanitize_text_field($_POST['new_conference_phone']);
			$new_location = stripslashes( sanitize_text_field($_POST['new_location']));
			$new_address = stripslashes( sanitize_text_field($_POST['new_formatted_address']));
			$new_region_id = sanitize_text_field($_POST['new_region']);
			$new_region = get_the_category_by_ID($new_region_id);
			$new_sub_region = sanitize_text_field($_POST['new_sub_region']);
			$new_location_notes = sanitize_text_field($_POST['new_location_notes'] );
			$new_group = sanitize_text_field($_POST['new_group']);

			$new_typesDescArray = tsml_sanitize_array($_POST['new_types']);
			// If Conference URL, validate; or if phone, force 'ONL' type, else remove 'ONL'
			if (!empty( $new_conference_url ) ) {
				$url = esc_url_raw($new_conference_url, array('http', 'https'));
				if (tsml_conference_provider($url)) {
					$new_conference_url = $url;
					$new_typesDescArray = array_values(array_diff(tsml_sanitize_array($_POST['new_types']), array('ONL')));
				} else {
					$new_conference_url = null;
					$new_conference_url_notes = null;
					$new_conference_phone = null;
				}
			} 

			// '=====================  New MTG Array   =====================================';
			$new_typesDescStr = '';

			if ( ( !empty( $new_typesDescArray ) ) && ( is_array( $new_typesDescArray ) ) ) {

				//if a meeting is both open and closed, make it closed
				if (in_array('C', $new_typesDescArray) && in_array('O', $new_typesDescArray)) {
					$new_typesDescArray = array_diff($new_typesDescArray, array('O'));
				}

				foreach ( $new_typesDescArray as $mtg_key) {
					$mtg_description = $myTypesArray[$mtg_key];
					$new_typesDescStr .= $mtg_description.'<br>';
				}
			}
			else {
				$new_typesDescStr = 'No Types Selected';
			}

			if ( !empty($new_end_time) ) {
				$message_lines[__('End Time', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>End Time</td><td style='color:blue;'>$new_end_time</td></tr>";  
			}
			
			if ( !empty($new_typesDescStr) ) {
				$message_lines[__('Types', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Types</td><td style='color:blue;'>$new_typesDescStr</td></tr>";
			}

			if ( !empty($new_notes) ) {
				$message_lines[__('Notes', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Notes</td><td style='color:blue;'>$new_notes</td></tr>";  
			}

			if ( !empty($new_conference_url) ) {
				$message_lines[__('URL', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>URL</td><td style='color:blue;'>$new_conference_url</td></tr>";  
			}

			if ( !empty($new_conference_url_notes) ) {
				$message_lines[__('Conference URL Notes', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Conference URL Notes</td><td style='color:blue;'>$new_conference_url_notes</td></tr>";  
			}

			if ( !empty($new_conference_phone) ) {
				$message_lines[__('Conference Phone', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Conference Phone</td><td style='color:blue;'>$new_conference_phone</td></tr>";  
			}

			if (!empty($new_conference_phone_notes)) {
				$message_lines[__('Conference Phone Notes', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Conference Phone Notes</td><td style='color:blue;'>$meeting->new_conference_phone_notes</td></tr>";  
			}

			if ( !empty($new_location) ) {
				$message_lines[__('Location', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Location</td><td style='color:blue;'>$new_location</td></tr>";  
			}

			if ( !empty($new_address) ) {
				$message_lines[__('Address', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Address</td><td style='color:blue;'>$new_address</td></tr>";  
			}

			if ( !empty($new_region_id) ) {
				$new_region = '';
				$new_region = get_the_category_by_ID($new_region_id);
				$message_lines[__('Region', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Region</td><td style='color:blue;'>$new_region</td></tr>";  
			}

			if ( !empty($new_sub_region) ) {
				$message_lines[__('Sub Region', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Sub Region</td><td style='color:blue;'>$new_sub_region</td></tr>";  
			}

			if ( !empty($new_location_notes) ) {
				$message_lines[__('Location Notes', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Location Notes</td><td style='color:blue;'>$new_location_notes</td></tr>";  
			}

			//--------------- Do Additional Processing for a New Meeting --------------------

			if ( 1 == 1 )  {

				$new_district_id = sanitize_text_field($_POST['new_district_id']);
				$new_sub_district = sanitize_text_field($_POST['new_sub_district'] );
				$new_group_notes = sanitize_text_field($_POST['new_group_notes'] );
				$new_website = sanitize_text_field($_POST['new_website']);
				$new_website_2 = sanitize_text_field($_POST['new_website_2']);
				$new_email = sanitize_text_field($_POST['new_email']);
				$new_phone = preg_replace('/[^[:digit:]]/', '', sanitize_text_field($_POST['new_phone']));
				$new_mailing_address = stripslashes(sanitize_text_field($_POST['new_mailing_address']));
				$new_venmo = sanitize_text_field($_POST['new_venmo']);
				$new_square = sanitize_text_field($_POST['new_square']);
				$new_paypal = sanitize_text_field($_POST['new_paypal']);
				$new_contact_1_name = sanitize_text_field($_POST['new_contact_1_name']);
				$new_contact_1_email = sanitize_text_field($_POST['new_contact_1_email']);
				$chg_contact_1_phone = preg_replace('/[^[:digit:]]/', '', sanitize_text_field($_POST['contact_1_phone']));
				$new_contact_2_name = sanitize_text_field($_POST['new_contact_2_name']);
				$new_contact_2_email = sanitize_text_field($_POST['new_contact_2_email']);
				$chg_contact_2_phone = preg_replace('/[^[:digit:]]/', '', sanitize_text_field($_POST['contact_2_phone']));
				$new_contact_3_name = sanitize_text_field($_POST['new_contact_3_name']);
				$new_contact_3_email = sanitize_text_field($_POST['new_contact_3_email']);
				$chg_contact_3_phone = preg_replace('/[^[:digit:]]/', '', sanitize_text_field($_POST['contact_3_phone']));

				if ( !empty($new_district_id) ) {
					$new_district_name = '';
					$new_district_name = get_the_category_by_ID($new_district_id);
					$message_lines[__('District', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>District</td><td style='color:blue' >$new_district_name</td></tr>"; 
				}

				if (!empty($new_district)) {
					$message_lines[__('District', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>District</td><td style='color:blue' >$new_district</td></tr>";  
				}

				if (!empty($new_sub_district)) {
					$message_lines[__('Sub District', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Sub District</td><td style='color:blue' >$new_sub_district</td></tr>";  
				}

				if (!empty($new_group_notes)) {
					$message_lines[__('Group Notes', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Group Notes</td><td style='color:blue' >$new_group_notes</td></tr>";  
				}

				if (!empty($new_website)) {
					$message_lines[__('Website', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Website</td><td style='color:blue' >$new_website</td></tr>";  
				}

				if (!empty($new_website_2)) {
					$message_lines[__('Website 2', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Website 2</td><td style='color:blue' >$new_website_2</td></tr>";  
				}
			
				if (!empty($new_phone)) {
					$message_lines[__('Phone', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Phone</td><td style='color:blue' >$new_phone</td></tr>";  
				}

				if (!empty($new_mailing_address)) {
					$message_lines[__('Mailing Address', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Mailing Address</td><td style='color:blue' >$new_mailing_address</td></tr>";  
				}

				if (!empty($new_email)) {
					$message_lines[__('Email', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Email</td><td style='color:blue' >$new_email</td></tr>";  
				}

				if (!empty($new_email)) {
					$message_lines[__('Email', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Email</td><td style='color:blue' >$new_email</td></tr>";  
				}

				if (!empty($new_venmo)) {
					$message_lines[__('Venmo', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Venmo</td><td style='color:blue' >$new_venmo</td></tr>";  
				}

				if (!empty($new_square)) {
					$message_lines[__('Square', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Square</td><td style='color:blue' >$new_square</td></tr>";  
				}

				if (!empty($new_paypal)) {
					$message_lines[__('Paypal', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Paypal</td><td style='color:blue' >$new_paypal</td></tr>";  
				}

				if (!empty($new_contact_1_name)) {
					$message_lines[__('Contact 1 Name', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 1 Name</td><td style='color:blue' >$new_contact_1_name</td></tr>";  
				}

				if (!empty($new_contact_1_email)) {
					$message_lines[__('Contact 1 Email', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 1 Email</td><td style='color:blue' >$new_contact_1_email</td></tr>";  
				}

				if (!empty($new_contact_1_phone)) {
					$message_lines[__('Contact 1 Phone', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 1 Phone'</td><td style='color:blue' >$new_contact_1_phone</td></tr>";  
				}

				if (!empty($new_contact_2_name)) {
					$message_lines[__('Contact 2 Name', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 2 Name</td><td style='color:blue' >$new_contact_2_name</td></tr>";  
				}

				if (!empty($new_contact_2_email)) {
					$message_lines[__('Contact 2 Email', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 2 Email</td><td style='color:blue' >$new_contact_2_email</td></tr>";  
				}

				if (!empty($new_contact_2_phone)) {
					$message_lines[__('Contact 2 Phone', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 2 Phone'</td><td style='color:blue' >$new_contact_2_phone</td></tr>";  
				}

				if (!empty($new_contact_3_name)) {
					$message_lines[__('Contact 3 Name', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 3 Name</td><td style='color:blue' >$new_contact_3_name</td></tr>";  
				}

				if (!empty($new_contact_3_email)) {
					$message_lines[__('Contact 3 Email', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 3 Email</td><td style='color:blue' >$new_contact_3_email</td></tr>";  
				}

				if (!empty($new_contact_3_phone)) {
					$message_lines[__('Contact 3 Phone', '12-step-meeting-list-feedback-enhancement')] = "<tr><td>Contact 3 Phone</td><td style='color:blue' >$new_contact_3_phone</td></tr>";  
				}
			}
		}

		//--------------- Apply concatenated lines and close up the message --------------------
		foreach	($message_lines as $key => $value) {
			$message .= $value;
		}
		$message .= "</tbody></table>";
		/************************************ Send Email ****************************************/

		//email vars
		if (!isset($_POST['tsml_nonce']) || !wp_verify_nonce($_POST['tsml_nonce'], $tsml_nonce)) {
			_e("<div class='bg-danger text-dark'> Error: nonce value not set correctly. Email was not sent.</div>", '12-step-meeting-list-feedback-enhancement');
		}
		elseif (empty($tsml_feedback_addresses) || empty($name) || !is_email($email) || empty($message_lines) ) {
			_e("<div class='bg-danger text-dark'> Error: required form values missing. Email was not sent.<br></div>", '12-step-meeting-list-feedback-enhancement');
				echo esc_html("Missing Form Input Error...<br>");
		}
		elseif (empty($_POST['tsml_message']) && $IsFeedback ) {
			_e("<div class='bg-danger text-dark'> Error: required Message from feedback form missing. Email was not sent.<br></div>", '12-step-meeting-list-feedback-enhancement');
		}
		elseif ( $IsRemove ) {
			//send Removal Request HTML email
			$subject = __('Meeting Removal Request', '12-step-meeting-list-feedback-enhancement') . ': ' . $post_title;
			if (tsml_email($tsml_feedback_addresses, str_replace("'s", "s", $subject), $message, $name . ' <' . $email . '>')) {
				_e("<div class='bg-secondary text-white'> Thank you $name for helping keep our meeting list up-to-date. <br><br> You will receive a response email as soon as this request is processed by the site administrator.<br></div>", '12-step-meeting-list-feedback-enhancement');
			} 
			else {
				global $phpmailer;
				if (!empty($phpmailer->ErrorInfo)) {
					printf(__('Error: %s', '12-step-meeting-list-feedback-enhancement'), $phpmailer->ErrorInfo);
				} 
				else {
					_e("<div class='bg-warning text-dark'>An error occurred while sending email!<br></div>", '12-step-meeting-list-feedback-enhancement');
				}
			}
			remove_filter('wp_mail_content_type', 'tsml_email_content_type_html');
		}
		elseif ( $IsNew ) {
			//send New Request HTML email
			$subject = __('New Meeting Request', '12-step-meeting-list-feedback-enhancement') . ': ' . $new_name;
			if (tsml_email($tsml_feedback_addresses, str_replace("'s", "s", $subject), $message, $name . ' <' . $email . '>')) {
				_e("<div class='bg-success text-white'> Thank you $name for helping keep our meeting list current with your new listing. <br><br> You will receive a response email as soon as this request is processed by the site administrator.<br></div>", '12-step-meeting-list-feedback-enhancement');
			} 
			else {
				global $phpmailer;
				if (!empty($phpmailer->ErrorInfo)) {
					printf(__('Error: %s', '12-step-meeting-list-feedback-enhancement'), $phpmailer->ErrorInfo);
				} 
				else {
					_e("<div class='bg-warning text-dark'>An error occurred while sending email!</div>", '12-step-meeting-list-feedback-enhancement');
				}
			}
			remove_filter('wp_mail_content_type', 'tsml_email_content_type_html');
		}
		elseif ( $IsChange )  {
			//send Change Request HTML email 
			$subject = __('Meeting Change Request', '12-step-meeting-list-feedback-enhancement') . ': ' . $post_title;
			if (tsml_email($tsml_feedback_addresses, str_replace("'s", "s", $subject), $message, $name . ' <' . $email . '>')) {
				_e("<div class='bg-success text-light'> Thank you $name for helping keep our meeting list current with your latest changes.<br><br> You will receive a response email as soon as this request is processed by the site administrator. <br><br> That usually happens within 24 hours or so...<br></div>", '12-step-meeting-list-feedback-enhancement');
			} 
			else {
				global $phpmailer;
				if (!empty($phpmailer->ErrorInfo)) {
					printf(__('Error: %s', '12-step-meeting-list-feedback-enhancement'), $phpmailer->ErrorInfo);
				} 
				else {
					_e("<div class='bg-warning text-dark'>An error occurred while sending email!</div>", '12-step-meeting-list-feedback-enhancement');
				}
			}
			remove_filter('wp_mail_content_type', 'tsml_email_content_type_html');
		}
		else {
			//send Feedback HTML email - without bootstrap in echo
			$subject = __('Meeting Feedback Form', '12-step-meeting-list-feedback-enhancement') . ': ' . $post_title;
			if (tsml_email($tsml_feedback_addresses, $subject, $message, $name . ' <' . $email . '>')) {
				_e('Thank you for your feedback.', '12-step-meeting-list-feedback-enhancement');
			} else {
				global $phpmailer;
				if (!empty($phpmailer->ErrorInfo)) {
					printf(__('Error: %s', '12-step-meeting-list-feedback-enhancement'), $phpmailer->ErrorInfo);
				} else {
					_e('An error occurred while sending email!', '12-step-meeting-list-feedback-enhancement');
				}
			}
			remove_filter('wp_mail_content_type', 'tsml_email_content_type_html');
		}

		/************************************ EXITl ****************************************/
		exit;
	}
}

//function: sanitize passed array
//used:		here
if (!function_exists('tsml_sanitize_array')) {
	function tsml_sanitize_array($array_to_sanitize) {

		$retArray = array();
		if (!empty( $array_to_sanitize ) && is_array( $array_to_sanitize ) ) {
			foreach( $array_to_sanitize as $key => $value ) {
				$retArray[] = sanitize_text_field( $value );
			}
		}
	return $retArray;
	}
}
