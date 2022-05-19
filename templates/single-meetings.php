<?php

tsml_assets();

$meeting = tsml_get_meeting();
$tsml_feedback_method = 'enhanced';

// define local variable and test validity before initializing
$meeting_name = '';
if ((!is_null($meeting->post_title)) | (!empty($meeting->post_title))) {
	$meeting_name = $meeting->post_title;
} 
else {
	$meeting_name = $meeting->group;
}

//define some vars for the map
wp_localize_script('tsml_public', 'tsml_map', [
	'directions' => __('Directions', '12-step-meeting-list-feedback-enhancement'),
	'directions_url' => in_array('TC', $meeting->types) ? null : $meeting->directions,
	'formatted_address' => $meeting->formatted_address,
	'approximate' => $meeting->approximate,
	'latitude' => $meeting->latitude,
	'location' => get_the_title($meeting->post_parent),
	'location_id' => $meeting->post_parent,
	'location_url' => get_permalink($meeting->post_parent),
	'longitude' => $meeting->longitude,
]);

//adding custom body classes
add_filter('body_class', function ($classes) {
	global $meeting;

	$classes[] = 'tsml tsml-detail tsml-meeting';

	if ($type_classes = tsml_to_css_classes($meeting->types, 'tsml-type-')) {
		$classes[] = $type_classes;
	}

	// Add the attendance option class to the body tag
	$classes[] = 'attendance-' . sanitize_title($meeting->attendance_option);
	$classes[] = ($meeting->approximate === 'yes') ? 'address-approximate' : 'address-specific';

	return $classes;
});
function meeting_times_array() {
	return array("06:00" => "6:00 AM", "06:30" => "6:30 AM", "07:00" => "7:00 AM", "07:30" => "7:30 AM", "08:00" => "8:00 AM", "08:30" => "8:30 AM", "09:00" => "9:00 AM", "09:30" => "9:30 AM", "10:00" => "10:00 AM", "10:30" => "10:30 AM", "11:00"  => "11:00 AM", "11:30" => "11:30 AM", "12:00" => "Noon",    "12:30" => "12:30 PM", "13:00"  =>  "1:00 PM", "13:30" =>  "1:30 PM", "14:00" =>  "2:00 PM", "14:30" => "2:30 PM", 
				 "15:00" => "3:00 PM", "15:30" => "3:30 PM", "16:00" => "4:00 PM", "16:30" => "4:30 PM", "17:00" => "5:00 PM", "17:30" => "5:30 PM", "18:00" => "6:00 PM", "18:30" => "6:30 PM", "19:00" =>  "7:00 PM", "19:30" =>  "7:30 PM", "20:00"  =>  "8:00 PM", "20:30" =>  "8:30 PM", "21:00" => "9:00 PM", "21:30" =>  "9:30 PM", "22:00"  => "10:00 PM", "22:30" => "10:30 PM", "23:00" => "11:00 PM", "23:30" => "11:30 PM", "23:59" => "Midnight" );
  }

get_header();
?>

<div id="tsml">
	<div id="meeting" class="container">
		<div class="row">
			<div class="col-md-10 col-md-offset-1 main">

				<div class="page-header">
					<h1><?php echo esc_html($meeting->post_title); ?></h1>
					<?php
					$meeting_types = tsml_format_types($meeting->types);
					if (!empty($meeting_types)) {
						echo '<small><span class="meeting_types">(' . esc_attr($meeting_types) . ')</span></small>';
					}
					?>
					<div class="attendance-option">
						<?php esc_attr_e($tsml_meeting_attendance_options[$meeting->attendance_option], "12-step-meeting-list-feedback-enhancement") ?>
					</div>
					<?php echo tsml_link(get_post_type_archive_link('tsml_meeting'), '<i class="glyphicon glyphicon-chevron-right"></i> ' . esc_attr__('Back to Meetings', '12-step-meeting-list-feedback-enhancement'), 'tsml_meeting') ?>
				</div>

				<div class="row">
					<div id="div_left_col" class="col-md-4">

						<?php if (!in_array('TC', $meeting->types) && ($meeting->approximate !== 'yes')) { ?>
							<div class="panel panel-default">
								<a class="panel-heading tsml-directions" href="#" data-latitude="<?php echo esc_html($meeting->latitude) ?>" data-longitude="<?php echo esc_html($meeting->longitude) ?>" data-location="<?php echo esc_html($meeting->location) ?>">
									<h3 class="panel-title">
										<?php esc_attr_e('Get Directions', '12-step-meeting-list-feedback-enhancement') ?>
										<span class="panel-title-buttons">
											<svg width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" d="M9.896 2.396a.5.5 0 0 0 0 .708l2.647 2.646-2.647 2.646a.5.5 0 1 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708 0z" />
												<path fill-rule="evenodd" d="M13.25 5.75a.5.5 0 0 0-.5-.5h-6.5a2.5 2.5 0 0 0-2.5 2.5v5.5a.5.5 0 0 0 1 0v-5.5a1.5 1.5 0 0 1 1.5-1.5h6.5a.5.5 0 0 0 .5-.5z" />
											</svg>
										</span>
									</h3>
								</a>
							</div>
						<?php } ?>

						<div class="panel panel-default">
							<ul class="list-group">
								<li class="list-group-item meeting-info">
									<h3 class="list-group-item-heading"><?php esc_attr_e('Meeting Information', '12-step-meeting-list-feedback-enhancement') ?></h3>
									<p class="meeting-time">
										<?php
										echo tsml_format_day_and_time($meeting->day, $meeting->time);
										if (!empty($meeting->end_time)) {
											/* translators: until */
											echo esc_attr__(' to ', '12-step-meeting-list-feedback-enhancement'), tsml_format_time($meeting->end_time);
										}
										?>
									</p>
									<ul class="meeting-types">
										<?php
										$li_marker = ('<svg class="icon" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
													<path fill-rule="evenodd" d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z"/>
												</svg>');
										switch ($meeting->attendance_option) {
											case 'in_person':
												echo '<li>' . $li_marker . esc_attr__('In person', '12-step-meeting-list-feedback-enhancement') . '</li>' . PHP_EOL;
												break;
											case 'hybrid':
												echo '<li>' . $li_marker . esc_attr__('In person', '12-step-meeting-list-feedback-enhancement') . '</li>' . PHP_EOL;
												echo '<li>' . $li_marker . esc_attr__('Online', '12-step-meeting-list-feedback-enhancement') . '</li>' . PHP_EOL;
												break;
											case 'online':
												echo '<li>' . $li_marker . esc_attr__('Online', '12-step-meeting-list-feedback-enhancement') . '</li>' . PHP_EOL;
												break;
											case 'inactive':
												echo '<li>' . $li_marker . esc_attr__('Temporarily Inactive', '12-step-meeting-list-feedback-enhancement') . '</li>' . PHP_EOL;
												break;
											default:
												break;
										}
										?>
										<li>
											<hr style="margin:10px 0;" />
										</li>
										<?php foreach ($meeting->types_expanded as $type) { ?>
											<?php echo '<li>' . $li_marker . esc_attr__($type) . '</li>' . PHP_EOL; ?>
										<?php } ?>
									</ul>
									<?php if (!empty($meeting->type_description)) { ?>
										<p class="meeting-type-description"><?php esc_attr_e($meeting->type_description) ?></p>
									<?php }

									if (!empty($meeting->notes)) { ?>
										<section class="meeting-notes"><?php echo wpautop(esc_html($meeting->notes)) ?></section>
									<?php } ?>
								</li>
								<?php if (!empty($meeting->conference_url) || !empty($meeting->conference_phone)) { ?>
									<li class="list-group-item" style="padding-bottom: 0">
										<h3 class="list-group-item-heading">
											<?php esc_attr_e('Online Meeting', '12-step-meeting-list-feedback-enhancement') ?>
										</h3>
										<?php
										if (!empty($meeting->conference_url) && $provider = tsml_conference_provider($meeting->conference_url)) { ?>
											<a class="btn btn-default btn-block" href="<?php echo esc_url($meeting->conference_url) ?>" target="_blank">
												<svg class="icon" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
													<path fill-rule="evenodd" d="M2.667 3.5c-.645 0-1.167.522-1.167 1.167v6.666c0 .645.522 1.167 1.167 1.167h6.666c.645 0 1.167-.522 1.167-1.167V4.667c0-.645-.522-1.167-1.167-1.167H2.667zM.5 4.667C.5 3.47 1.47 2.5 2.667 2.5h6.666c1.197 0 2.167.97 2.167 2.167v6.666c0 1.197-.97 2.167-2.167 2.167H2.667A2.167 2.167 0 0 1 .5 11.333V4.667z" />
													<path fill-rule="evenodd" d="M11.25 5.65l2.768-1.605a.318.318 0 0 1 .482.263v7.384c0 .228-.26.393-.482.264l-2.767-1.605-.502.865 2.767 1.605c.859.498 1.984-.095 1.984-1.129V4.308c0-1.033-1.125-1.626-1.984-1.128L10.75 4.785l.502.865z" />
												</svg>
												<?php echo $provider === true ? $meeting->conference_url : sprintf(esc_attr__('Join with %s', '12-step-meeting-list-feedback-enhancement'), $provider) ?>
											</a>
											<?php if ($meeting->conference_url_notes) { ?>
												<p style="margin: 7.5px 0 15px; color: #777; font-size: 90%;"><?php echo nl2br(esc_html($meeting->conference_url_notes)) ?></p>
											<?php } ?>
										<?php }
										if (!empty($meeting->conference_phone)) { ?>
											<a class="btn btn-default btn-block" href="tel:<?php echo esc_html($meeting->conference_phone) ?>">
												<svg class="icon" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
													<path fill-rule="evenodd" d="M3.925 1.745a.636.636 0 0 0-.951-.059l-.97.97c-.453.453-.62 1.095-.421 1.658A16.47 16.47 0 0 0 5.49 10.51a16.471 16.471 0 0 0 6.196 3.907c.563.198 1.205.032 1.658-.421l.97-.97a.636.636 0 0 0-.06-.951l-2.162-1.682a.636.636 0 0 0-.544-.115l-2.052.513a1.636 1.636 0 0 1-1.554-.43L5.64 8.058a1.636 1.636 0 0 1-.43-1.554l.513-2.052a.636.636 0 0 0-.115-.544L3.925 1.745zM2.267.98a1.636 1.636 0 0 1 2.448.153l1.681 2.162c.309.396.418.913.296 1.4l-.513 2.053a.636.636 0 0 0 .167.604L8.65 9.654a.636.636 0 0 0 .604.167l2.052-.513a1.636 1.636 0 0 1 1.401.296l2.162 1.681c.777.604.849 1.753.153 2.448l-.97.97c-.693.693-1.73.998-2.697.658a17.47 17.47 0 0 1-6.571-4.144A17.47 17.47 0 0 1 .639 4.646c-.34-.967-.035-2.004.658-2.698l.97-.969z" />
												</svg>
												<?php esc_attr_e('Join by Phone', '12-step-meeting-list-feedback-enhancement') ?>
											</a>
											<?php if ($meeting->conference_phone_notes) { ?>
												<p style="margin: 7.5px 0 15px; color: #777; font-size: 90%;"><?php echo nl2br(esc_html($meeting->conference_phone_notes)) ?></p>
											<?php } ?>
										<?php } ?>
									</li>
								<?php }

								$services = [
									'venmo' => [
										'name' => 'Venmo',
										'url' => esc_url('https://venmo.com/'),
										'substr' => 1,
									],
									'square' => [
										'name' => 'Cash App',
										'url' => esc_url('https://cash.app/'),
										'substr' => 0,
									],
									'paypal' => [
										'name' => 'PayPal',
										'url' => esc_url('https://www.paypal.me/'),
										'substr' => 0,
									],
								];
								$active_services = array_filter(array_keys($services), function ($service) use ($meeting) {
									return !empty($meeting->{$service});
								});
								if (count($active_services)) { ?>
									<li class="list-group-item list-group-item-group">
										<h3 class="list-group-item-heading"><?php esc_attr_e('7th Tradition', '12-step-meeting-list-feedback-enhancement') ?></h3>
										<?php
										foreach ($active_services as $field) {
											$service = $services[$field];
											if (!empty($meeting->{$field})) { ?>
												<a id="<?php echo esc_html($field) ?>-link" class="btn btn-default btn-block" href="<?php echo $service['url'] . substr($meeting->{$field}, $service['substr']) ?>" target="_blank">
													<svg class="icon" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
														<path d="M14 3H1a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1h-1z" />
														<path fill-rule="evenodd" d="M15 5H1v8h14V5zM1 4a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H1z" />
														<path d="M13 5a2 2 0 0 0 2 2V5h-2zM3 5a2 2 0 0 1-2 2V5h2zm10 8a2 2 0 0 1 2-2v2h-2zM3 13a2 2 0 0 0-2-2v2h2zm7-4a2 2 0 1 1-4 0 2 2 0 0 1 4 0z" />
													</svg>
													<?php echo sprintf(esc_attr__('Contribute with %s', '12-step-meeting-list-feedback-enhancement'), $service['name']) ?>
												</a>
										<?php }
										}
										?>
									</li>
								<?php }

								if (!empty($meeting->location_id)) {
									$location_info = '
										<h3 class="list-group-item-heading">' . $meeting->location . '</h3>';

									if ($other_meetings = count($meeting->location_meetings) - 1) {
										$location_info .= '<p class="location-other-meetings">' . sprintf(_n('%d other meeting at this location', '%d other meetings at this location', $other_meetings, '12-step-meeting-list-feedback-enhancement'), $other_meetings) . '</p>';
									}

									$location_info .= '<p class="location-address">' . tsml_format_address($meeting->formatted_address) . '</p>';

									if (!empty($meeting->location_notes)) {
										$location_info .= '<section class="location-notes">' . wpautop($meeting->location_notes) . '</section>';
									}

									if (!empty($meeting->region) && !strpos($meeting->formatted_address, $meeting->region)) {
										$location_info .= '<p class="location-region">' . $meeting->region . '</p>';
									}

									echo tsml_link(
										get_permalink($meeting->post_parent),
										$location_info,
										'tsml_meeting',
										'list-group-item list-group-item-location'
									);
								}

								//whether this meeting has public contact info to show
								$hasContactInformation = (($tsml_contact_display == 'public') && (!empty($meeting->contact_1_name) || !empty($meeting->contact_1_email) || !empty($meeting->contact_1_phone) ||
									!empty($meeting->contact_2_name) || !empty($meeting->contact_2_email) || !empty($meeting->contact_2_phone) ||
									!empty($meeting->contact_3_name) || !empty($meeting->contact_3_email) || !empty($meeting->contact_3_phone)));

								if (!empty($meeting->group) || !empty($meeting->website) || !empty($meeting->website_2) || !empty($meeting->email) || !empty($meeting->phone) || $hasContactInformation) { ?>
									<li class="list-group-item list-group-item-group">
										<h3 class="list-group-item-heading"><?php echo esc_html( empty($meeting->group) ? esc_attr__('Contact Information', '12-step-meeting-list-feedback-enhancement') : $meeting->group) ?></h3>
										<?php
										if (!empty($meeting->group_notes)) { ?>
											<section class="group-notes"><?php echo wpautop(esc_html($meeting->group_notes)) ?></section>
										<?php }
										if (!empty($meeting->district)) { ?>
											<section class="group-district"><?php echo esc_html($meeting->district) ?></section>
										<?php }
										if (!empty($meeting->website)) { ?>
											<a href="<?php echo  esc_html($meeting->website) ?>" class="btn btn-default btn-block group-website" target="_blank">
												<svg class="icon" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
													<path d="M4.715 6.542L3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1.001 1.001 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4.018 4.018 0 0 1-.128-1.287z" />
													<path d="M5.712 6.96l.167-.167a1.99 1.99 0 0 1 .896-.518 1.99 1.99 0 0 1 .518-.896l.167-.167A3.004 3.004 0 0 0 6 5.499c-.22.46-.316.963-.288 1.46z" />
													<path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 0 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 0 0-4.243-4.243L6.586 4.672z" />
													<path d="M10 9.5a2.99 2.99 0 0 0 .288-1.46l-.167.167a1.99 1.99 0 0 1-.896.518 1.99 1.99 0 0 1-.518.896l-.167.167A3.004 3.004 0 0 0 10 9.501z" />
												</svg>
												<?php echo substr(esc_html($meeting->website), strpos( esc_html($meeting->website), '//') + 2) ?>
											</a>
										<?php }
										if (!empty($meeting->website_2)) { ?>
											<a href="<?php echo  esc_html($meeting->website_2) ?>" class="btn btn-default btn-block group-website_2" target="_blank">
												<svg class="icon" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
													<path d="M4.715 6.542L3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1.001 1.001 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4.018 4.018 0 0 1-.128-1.287z" />
													<path d="M5.712 6.96l.167-.167a1.99 1.99 0 0 1 .896-.518 1.99 1.99 0 0 1 .518-.896l.167-.167A3.004 3.004 0 0 0 6 5.499c-.22.46-.316.963-.288 1.46z" />
													<path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 0 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 0 0-4.243-4.243L6.586 4.672z" />
													<path d="M10 9.5a2.99 2.99 0 0 0 .288-1.46l-.167.167a1.99 1.99 0 0 1-.896.518 1.99 1.99 0 0 1-.518.896l-.167.167A3.004 3.004 0 0 0 10 9.501z" />
												</svg>
												<?php echo substr( esc_html($meeting->website_2), strpos(esc_html($meeting->website_2), '//') + 2) ?>
											</a>
										<?php }
										if (!empty($meeting->email)) { ?>
											<a href="mailto:<?php echo $meeting->email ?>" class="btn btn-default btn-block group-email">
												<svg class="icon" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
													<path fill-rule="evenodd" d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383l-4.758 2.855L15 11.114v-5.73zm-.034 6.878L9.271 8.82 8 9.583 6.728 8.82l-5.694 3.44A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.739zM1 11.114l4.758-2.876L1 5.383v5.73z" />
												</svg>
												<?php esc_attr_e('Group Email', '12-step-meeting-list-feedback-enhancement') ?>
											</a>
										<?php }
										if (!empty($meeting->phone)) { ?>
											<a href="tel:<?php echo $meeting->phone ?>" class="btn btn-default btn-block group-phone">
												<svg class="icon" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
													<path fill-rule="evenodd" d="M3.925 1.745a.636.636 0 0 0-.951-.059l-.97.97c-.453.453-.62 1.095-.421 1.658A16.47 16.47 0 0 0 5.49 10.51a16.471 16.471 0 0 0 6.196 3.907c.563.198 1.205.032 1.658-.421l.97-.97a.636.636 0 0 0-.06-.951l-2.162-1.682a.636.636 0 0 0-.544-.115l-2.052.513a1.636 1.636 0 0 1-1.554-.43L5.64 8.058a1.636 1.636 0 0 1-.43-1.554l.513-2.052a.636.636 0 0 0-.115-.544L3.925 1.745zM2.267.98a1.636 1.636 0 0 1 2.448.153l1.681 2.162c.309.396.418.913.296 1.4l-.513 2.053a.636.636 0 0 0 .167.604L8.65 9.654a.636.636 0 0 0 .604.167l2.052-.513a1.636 1.636 0 0 1 1.401.296l2.162 1.681c.777.604.849 1.753.153 2.448l-.97.97c-.693.693-1.73.998-2.697.658a17.47 17.47 0 0 1-6.571-4.144A17.47 17.47 0 0 1 .639 4.646c-.34-.967-.035-2.004.658-2.698l.97-.969z" />
												</svg>
												<?php esc_attr_e('Group Phone', '12-step-meeting-list-feedback-enhancement') ?>
											</a>
											<?php }
										if ($hasContactInformation) {
											for ($i = 1; $i <= TSML_GROUP_CONTACT_COUNT; $i++) {
												$name = empty($meeting->{'contact_' . $i . '_name'}) ? sprintf(esc_attr__('Contact %s', '12-step-meeting-list-feedback-enhancement'), $i) : $meeting->{'contact_' . $i . '_name'};
												if (!empty($meeting->{'contact_' . $i . '_email'})) { ?>
													<a href="mailto:<?php echo $meeting->{'contact_' . $i . '_email'} ?>" class="btn btn-default btn-block contact-email">
														<svg class="icon" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
															<path fill-rule="evenodd" d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383l-4.758 2.855L15 11.114v-5.73zm-.034 6.878L9.271 8.82 8 9.583 6.728 8.82l-5.694 3.44A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.739zM1 11.114l4.758-2.876L1 5.383v5.73z" />
														</svg>
														<?php echo sprintf(esc_attr__('%s’s Email'), $name) ?>
													</a>
												<?php
												}
												if (!empty($meeting->{'contact_' . $i . '_phone'})) { ?>
													<a href="tel:<?php echo  esc_html($meeting->{'contact_' . $i . '_phone'}) ?>" class="btn btn-default btn-block contact-phone">
														<svg class="icon" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
															<path fill-rule="evenodd" d="M3.925 1.745a.636.636 0 0 0-.951-.059l-.97.97c-.453.453-.62 1.095-.421 1.658A16.47 16.47 0 0 0 5.49 10.51a16.471 16.471 0 0 0 6.196 3.907c.563.198 1.205.032 1.658-.421l.97-.97a.636.636 0 0 0-.06-.951l-2.162-1.682a.636.636 0 0 0-.544-.115l-2.052.513a1.636 1.636 0 0 1-1.554-.43L5.64 8.058a1.636 1.636 0 0 1-.43-1.554l.513-2.052a.636.636 0 0 0-.115-.544L3.925 1.745zM2.267.98a1.636 1.636 0 0 1 2.448.153l1.681 2.162c.309.396.418.913.296 1.4l-.513 2.053a.636.636 0 0 0 .167.604L8.65 9.654a.636.636 0 0 0 .604.167l2.052-.513a1.636 1.636 0 0 1 1.401.296l2.162 1.681c.777.604.849 1.753.153 2.448l-.97.97c-.693.693-1.73.998-2.697.658a17.47 17.47 0 0 1-6.571-4.144A17.47 17.47 0 0 1 .639 4.646c-.34-.967-.035-2.004.658-2.698l.97-.969z" />
														</svg>
														<?php echo sprintf(esc_attr__('%s’s Phone'), $name) ?>
													</a>
										<?php }
											}
										}
										?>
									</li>
								<?php
								} ?>
								<li class="list-group-item list-group-item-updated">
									<?php esc_attr_e('Updated', '12-step-meeting-list-feedback-enhancement') ?>
									<?php the_modified_date() ?>
								</li>
							</ul>
						</div>
					</div>
					
					<!--  *** *** *** *** *** *** *** *** *** TSML Feedback Enhancement starts here *** *** *** *** *** *** *** ***  -->
					<div id="div_right_col" class="col-md-8">
						<!-- Make toggle button & map hideable -->
						<div class="" style="margin:auto; align-content: right;">
							<input id="btnToggleMap" class="btn-block btn-primary btn-sm" role="button" <?php echo $tsml_feedback_method == "enhanced" ? 'show' : 'hidden';?>" type="button" onclick="switchVisible();" value="<?php esc_attr_e('Request a change to this listing', '12-step-meeting-list-feedback-enhancement')?>" style="display:block" > 
						</div>
						<?php if (!empty($tsml_mapbox_key) || !empty($tsml_google_maps_key)) {?>
							<div id="map" class="panel panel-default" ></div>
						<?php }?>
						<!-- Visibility of Request Forms set with style & js -->
						<div id="requests" style="float:left; width:100%; display:none;" >
						<?php
						if ( !empty($tsml_feedback_addresses) && $tsml_feedback_method == 'enhanced') {?>
							<form id="feedback">
								<input type="hidden" name="action" value="tsml_feedback">
								<input type="hidden" name="meeting_id" value="<?php echo preg_replace('/[^0-9]+/', '', $meeting->ID); ?>">
								<?php wp_nonce_field($tsml_nonce, 'tsml_nonce', false)?>

									<!-- ************************** Request Form Header starts here ************************** -->
									<ul class="list-group " style="margin: 0; list-style-type: none;" >
										<li class="list-group-item list-group-item-form list-group-item-warning " >
											<center><h3 id="mcrf_title">Meeting Change Request</h3></center>
											<center><h3 id="mrrf_title" style="display:none;" >Remove Meeting Request</h3></center>
											<center><h3 id="anmrf_title" style="display:none;" >Add New Meeting Request</h3></center>
										</li>
										<li class="well well-sm list-group-item list-group-item-form text-left" style="padding:20px;" >
											<?php _e(wp_kses_post('Use this form to send your meeting information to our website administrator. Toggle the Change, New, or Remove buttons to generate a specific type of update request to suit your needs. Groups may register with this website by providing a phone number, email or mailing address for us to contact in the <b>Additional Group Information</b> section.<br><br>Signature Information must be filled in before a request can be submitted.'), '12-step-meeting-list-feedback-enhancement')?><br><br>
										</li>
										<li class="list-group-item list-group-item-form text-center" >
											<div id="request-btn-group" class="btn-group btn-group-toggle "data-toggle="buttons">
												<label class="btn btn-primary checked">
												<input type="checkbox"name="change" checked autocomplete="off" value="change" onclick="setRequestHeaderDisplay('mcrf_title')" > Change
												</label>
												<label class="btn btn-primary">
												<input type="checkbox" name="new" autocomplete="off" value="new" onclick="setRequestHeaderDisplay('anmrf_title')" > New
												</label>
												<label class="btn btn-primary">
												<input type="checkbox" name="remove" autocomplete="off" onclick="setRequestHeaderDisplay('mrrf_title')" > Remove
												</label>	
											</div>
										</li> 
										<li class="list-group-item list-group-item-form " >
											<!-- ***************************** Change Request Form starts here*** ********************************* --> 
											<div id="change_request" class="panel-header panel-default" style="display:block;" >
												<div id="divChange" class="" style="display:block;" >
									
													<div id="changedetail">
														<div class="meta_form_separator row">
															<div class="col-md-8">
																<h4><?php _e('Meeting Details', '12-step-meeting-list-feedback-enhancement')?></h4>
																<p><?php echo '' ?></p>
															</div>
														</div>
														<div class="meta_form_row row">
															<div class="well well-sm col-md-10 col-md-offset-1 ">
																<label for="name"><?php esc_attr_e('Meeting Name', '12-step-meeting-list-feedback-enhancement')?></label>
																<input type="text" class="required"  name="name" id="name" style="width:100%; display:block;" placeholder="<?php esc_attr_e('Enter meeting short name (ie. without the words Group or Meeting...)', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo esc_html($meeting_name); ?>" >
															</div>
														</div><br>
														<div class="meta_form_row row">
															<div class="well well-sm col-md-10 col-md-offset-1 ">
																<div class="col-md-6">
																	<label for="day"><?php esc_attr_e('Day', '12-step-meeting-list-feedback-enhancement')?></label><br />
																	<select name="day" id="day">
																		<?php foreach ($tsml_days as $key => $day) {?>
																		<option value="<?php echo esc_html($key) ?>"<?php selected(strcmp(@$meeting->day, $key) == 0)?> ><?php echo esc_html($day) ?></option>
																		<?php }?>
																	</select>
																</div>
																<div class="col-md-3 form-group" style="display:block;" >
																	<label for="start_time"><?php esc_attr_e('Start Time', '12-step-meeting-list-feedback-enhancement')?></label><br />
																	<select name="start_time" id="start_time">
																		<?php $options = meeting_times_array();
																		foreach ( $options as $key => $val ) {?>
																		<option value="<?php echo esc_html($key) ?>"<?php selected(strcmp(@$meeting->time, $key) == 0)?> ><?php echo esc_html($val) ?></option>
																		<?php }?>
																	</select>
																</div>
																<div class="col-md-3" style="display:none;" >
																	<label for="end_time"><?php esc_attr_e('End Time', '12-step-meeting-list-feedback-enhancement') ?></label>
																	<select name="end_time" id="end_time">
																		<?php $options = meeting_times_array();
																		foreach ( $options as $key => $val ) {?>
																		<option value="<?php echo esc_html($key) ?>"<?php selected(strcmp(@$meeting->time, $key) == 0)?> ><?php echo esc_html($val) ?></option>
																		<?php }?>
																	</select>
																	<input type="time" class="text-center" name="end_time" id="end_time" list="meeeting_times" min="05:00" max="23:59" placeholder="00:00"  value="<?php echo esc_html($meeting->end_time) ?>" >
																</div>
															</div>
														</div>
														<div class="meta_form_row row">
															<div class="well well-sm col-md-10 col-md-offset-1 ">
																<?php if (tsml_program_has_types())	{?>

																<label for="types"><?php esc_attr_e('Types', '12-step-meeting-list-feedback-enhancement') ?> </label>
																<div class="checkboxes">
																	<?php
																	foreach ($tsml_programs[$tsml_program]['types'] as $key => $type) {
																		if (!in_array($key, $tsml_types_in_use)) continue; //hide TYPES not used 
																		?>
																		<div class="checkbox col-md-6" >
																			<label>
																				<input type="checkbox" name="types[]" value="<?php echo esc_html($key) ?>" <?php checked(in_array($key, @$meeting->types)) ?> >
																				<?php echo esc_html($type) ?>
																			</label>
																		</div>
																	<?php } ?>
																</div>
																<?php } ?>
															</div>
														</div>
														<div class="meta_form_row row">
															<div class="well well-sm col-md-10 col-md-offset-1 ">
																<label for="notes"><?php esc_attr_e('Notes', '12-step-meeting-list-feedback-enhancement')?></label>
																<textarea name="content" id="content" rows="7" style="width:100%;" placeholder="<?php esc_textarea('notes are specific to this meeting. For example: Birthday speaker meeting last Saturday of the month.', '12-step-meeting-list-feedback-enhancement')?>"><?php echo $meeting->post_content ?></textarea>
															</div>
														</div>
														<div class="meta_form_row row">
															<div class="well well-sm col-md-10 col-md-offset-1 ">
															    <h4><?php esc_attr_e('Online Meeting Details', '12-step-meeting-list-feedback-enhancement') ?></h4>
																<p><?php echo sprintf(__(wp_kses_post("If this meeting has videoconference information, please enter the full valid URL here. Currently supported providers: %s. If other details are required, such as a password, they can be included in the Notes field above, but a ‘one tap’ experience is ideal. Passwords can be appended to phone numbers using this format <code>+12125551212,,123456789#,,#,,444444#</code>"), '12-step-meeting-list-feedback-enhancement'), implode(', ', tsml_conference_providers()))?></p>
															</div>
														</div>
														<div class="meta_form_row row">
															<div class="well well-sm col-md-10 col-md-offset-1 ">
																<label for="conference_url"><?php esc_attr_e('Conference URL', '12-step-meeting-list-feedback-enhancement')?></label><br>
																<input type="Url" name="conference_url" id="conference_url" style="width:100%;" placeholder="https://zoom.us/j/9999999999?pwd=1223456" value="<?php echo $meeting->conference_url ?>">
															</div>
														</div>
														<div class="meta_form_row row" > 
															<div class="well well-sm col-md-10 col-md-offset-1 ">
																<label for="conference_url_notes"><?php esc_attr_e('Conference URL Notes', '12-step-meeting-list-feedback-enhancement')?></label><br>
																<input type="text" name="conference_url_notes" id="conference_url_notes" style="width:100%;" placeholder="<?php esc_attr_e('Password if needed or other info related to joining an online meeting...', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo $meeting->conference_url_notes ?>">
															</div>
														</div>
														<div class="meta_form_row row">
															<div class="well well-sm col-md-10 col-md-offset-1 ">
																<label for="conference_phone"><?php esc_attr_e('Conference Phone #', '12-step-meeting-list-feedback-enhancement')?></label><br>
																<input type="text" name="conference_phone" id="conference_phone" style="width:100%;" placeholder="<?php esc_attr_e('Phone Number for your Online meeting Provider', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo $meeting->conference_phone ?>">
															</div>
														</div>
														<div class="meta_form_row row"> 
															<div class="well well-sm col-md-10 col-md-offset-1 "><br>
																<label for="conference_phone_notes"><?php esc_attr_e('Conference Phone Notes', '12-step-meeting-list-feedback-enhancement')?></label><br> 
																<input type="text" name="conference_phone_notes" id="conference_phone_notes" style="width:100%;" placeholder="<?php esc_attr_e('Info related to joining an online meeting via phone...', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo $meeting->conference_phone_notes ?>">
															</div>
														</div>
														<div class="meta_form_row row">
															<div class="col-md-12">
																<h4><?php esc_attr_e('Location Details', '12-step-meeting-list-feedback-enhancement')?></h4>
																<p><?php echo '' ?></p>
															</div>
														</div>
														<div class="meta_form_row row">
															<div class="well well-sm col-md-10 col-md-offset-1 ">
																<label for="location"><?php esc_attr_e('Location', '12-step-meeting-list-feedback-enhancement')?></label><br>
																<input type="text" name="location" id="location" style="width:100%;" placeholder="<?php esc_attr_e('building name (i.e. St John Baptist Church)', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo esc_attr($meeting->location)?>">
															</div>
														</div>
														<div class="meta_form_row row">
															<div class="well well-sm col-md-10 col-md-offset-1 ">
																<label for="formatted_address"><?php esc_attr_e('Address', '12-step-meeting-list-feedback-enhancement')?></label><br>
																<input type="text" name="formatted_address" id="formatted_address" style="width:100%;" placeholder="123 Any Street, Someplace, OK, USA, 98765" value="<?php echo $meeting->formatted_address ?>">
															</div>
														</div>
														<?php if (wp_count_terms('tsml_region')) {?>
														<div class="meta_form_row row">
															<div class="well well-sm col-md-5 col-md-offset-1">
																
																<label for="region"><?php esc_attr_e('Region', '12-step-meeting-list-feedback-enhancement')?></label><br />
																<?php wp_dropdown_categories(array(
																	'name' => 'region', 'taxonomy' => 'tsml_region','hierarchical' => true, 'id' => 'region_id',
																	'hide_empty' => false, 'orderby' => 'name', 'selected' => empty($meeting->region_id) ? null : $meeting->region_id,
																	'show_option_none' => esc_attr__(' ', '12-step-meeting-list-feedback-enhancement'), 
																))?>
															</div>		
															<div class="well well-sm col-md-5">		
																<label for="sub_region"><?php esc_attr_e('Sub Region', '12-step-meeting-list-feedback-enhancement')?></label>
																<input type="text" name="sub_region" id="sub_region" placeholder="<?php esc_attr_e('related to Region...', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo 	$meeting->sub_region ?>">
															</div>
														</div>
														<?php }?>
														<div class="meta_form_row row">
															<div class="well well-sm col-md-10 col-md-offset-1 " style="display:block;" > 
																<label for="location_notes"><?php esc_attr_e('Location Notes', '12-step-meeting-list-feedback-enhancement')?></label><br>
																<input type="text" name="location_notes" id="location_notes" style="width:100%;" placeholder="<?php esc_attr_e('common information that will apply to every meeting at this site', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo $meeting->location_notes ?>">
															</div>
														</div>

														<div class="meta_form_row row">
															<div class="col-md-12 radio">
																<h4>
																	<label><input type="radio" name="group_status" onclick="toggleAdditionalInfoDisplay('divAdditionalInfo', 'hide')"  value="meeting"<?php checked(empty(''))?> > <?php esc_attr_e('Hide', '12-step-meeting-list-feedback-enhancement')?></label>
																	<label><input type="radio" name="group_status" onclick="toggleAdditionalInfoDisplay('divAdditionalInfo', 'show')"  value="group"<?php checked(!empty(''))?> > <?php esc_attr_e('Show', '12-step-meeting-list-feedback-enhancement')?></label>
																	<?php _e('Additional Group Information', '12-step-meeting-list-feedback-enhancement')?>
																</h4>
															</div>
														</div>

														<!-- ---------------------------------Additional Information starts here ----------------------------------------- -->

														<div id="divAdditionalInfo" style="display:none">

															<div class="meta_form_row row">
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="group"><?php esc_attr_e('Group Name', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="group" id="group" style="width:100%;" placeholder="<?php esc_attr_e('full registered name...', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo $meeting->group ?>">
																</div>
															</div>
															<?php if (wp_count_terms('tsml_district')) {?>
															<div class="meta_form_row row">
																<div class="well well-sm col-md-4 col-md-offset-1">																
																	<label for="district"><?php esc_attr_e('District', '12-step-meeting-list-feedback-enhancement')?></label><br />
																	<?php wp_dropdown_categories(array(
																		'name' => 'district', 'taxonomy' => 'tsml_district','hierarchical' => true,  'id' => 'district_id',
																		'hide_empty' => false, 'orderby' => 'name', 'selected' => empty($meeting->district_id) ? null : $meeting->district_id,
																		'show_option_none' => esc_attr__(' ', '12-step-meeting-list-feedback-enhancement'), 
																	))?>
																</div>		
																<div class="well well-sm col-md-6 ">		
																	<label for="sub_district"><?php esc_attr_e('Sub District', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="sub_district" id="sub_district" style="width:100%;" placeholder="<?php esc_attr_e('related to District...', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo 	$meeting->sub_district ?>">
																</div>
															</div>
															<?php }?>
															<div class="meta_form_row row">
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="group_notes"><?php esc_attr_e('Group Notes', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="group_notes" id="group_notes" style="width:100%;" placeholder="<?php esc_attr_e('for stuff like when the business meeting takes place...', '12-step-meeting-list-feedback-enhancement')?>"  value="<?php echo $meeting->group_notes ?>">
																</div>
															</div>
															<div class="meta_form_row row">
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="website_1"><?php esc_attr_e('Website', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="Url" name="website_1" id="website_1" style="width:100%;" placeholder="<?php esc_attr_e('primary URL of org where group posts its meeting info', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo $meeting->website ?>">
																</div>
															</div>
															<div class="meta_form_row row">
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="website_2"><?php esc_attr_e('Website 2', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="Url" name="website_2" id="website_2" style="width:100%;" placeholder="<?php esc_attr_e('secondary URL of org where group posts its meeting info', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo $meeting->website_2 ?>">
																</div>
															</div>
															<div class="meta_form_row row" >
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="email"><?php esc_attr_e('Email', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="email" name="email" id="email" style="width:100%;" placeholder="<?php esc_attr_e('non personal email (i.e. groupName@gmail.com)', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo (empty($meeting->email) ) ? '' : substr($meeting->email, 0) ?>" >
																</div>
															</div>
															<div class="meta_form_row row">
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="phone"><?php esc_attr_e('Phone', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="phone" id="phone" style="width:100%;" placeholder="<?php esc_attr_e('10 digit public number for contacting the group', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo $meeting->phone ?>" >
																</div>
															</div>
															<div class="meta_form_row row">
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="mailing_address"><?php esc_attr_e('Mailing Address', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="mailing_address" id="mailing_address" style="width:100%;" placeholder="<?php esc_attr_e('postal address which receives correspondence for the group', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo (empty($meeting->mailing_address) ) ? '' : substr($meeting->mailing_address, 0) ?>"> 
																</div>
															</div>
															<div class="meta_form_row row">
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="venmo"><?php esc_attr_e('Venmo', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="venmo" id="venmo" style="width:100%;" placeholder="<?php esc_attr_e('@VenmoHandle - handle for 7th Tradition contributions', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo $meeting->venmo ?>">
																</div>
															</div>
															<div class="meta_form_row row">
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="square"><?php esc_attr_e('Square', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="website" id="square" style="width:100%;" placeholder="<?php esc_attr_e('$Cashtag - handle for 7th Tradition contributions', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo $meeting->square ?>">
																</div>
															</div>
															<div class="meta_form_row row">
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="paypal"><?php esc_attr_e('PayPal', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="paypal" id="paypal" style="width:100%;" placeholder="<?php esc_attr_e('PayPalUsername - handle for 7th Tradition contributions', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo $meeting->paypal ?>">
																</div>
															</div>

															<div class="meta_form_row row" >
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="contact_1_name"><?php esc_attr_e('Contact 1 Name', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="contact_1_name" id="contact_1_name" style="width:100%;" placeholder="<?php esc_attr_e('First Name & Last Initial', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo $meeting->contact_1_name ?>" >
																</div>
															</div>
															<div class="meta_form_row row" >
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="contact_1_email"><?php esc_attr_e('Contact 1 Email', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="contact_1_email" id="contact_1_email" style="width:100%;" placeholder="<?php esc_attr_e('No personally identifying email address...', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo $meeting->contact_1_email ?>" >
																</div>
															</div>
															<div class="meta_form_row row" >
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="contact_1_phone"><?php esc_attr_e('Contact 1 Phone', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="contact_1_phone" id="contact_1_phone" style="width:100%;" placeholder="<?php esc_attr_e('10 digit number for a group contact', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo esc_attr($meeting->contact_1_phone) ?>" >
																</div>
															</div>
															<div class="meta_form_row row" >
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="contact_2_name"><?php esc_attr_e('Contact 2 Name', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="contact_2_name" id="contact_2_name" style="width:100%;" placeholder="<?php esc_attr_e('First Name & Last Initial', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo $meeting->contact_2_name ?>" >
																</div>
															</div>
															<div class="meta_form_row row" >
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="contact_2_email"><?php esc_attr_e('Contact 2 Email', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="contact_2_email" id="contact_2_email" style="width:100%;" placeholder="<?php esc_attr_e('No personally identifying email address...', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo $meeting->contact_2_email ?>" >
																</div>
															</div>
															<div class="meta_form_row row" >
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="contact_2_phone"><?php esc_attr_e('Contact 2 Phone', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="contact_2_phone" id="contact_2_phone" style="width:100%;" placeholder="<?php esc_attr_e('10 digit number for a group contact', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo esc_attr($meeting->contact_2_phone) ?>" >
																</div>
															</div>
															<div class="meta_form_row row" >
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="contact_3_name"><?php esc_attr_e('Contact 3 Name', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="contact_3_name" id="contact_3_name" style="width:100%;" placeholder="<?php esc_attr_e('First Name & Last Initial', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo $meeting->contact_3_name ?>" >
																</div>
															</div>
															<div class="meta_form_row row" >
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="contact_3_email"><?php esc_attr_e('Contact 3 Email', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="contact_3_email" id="contact_3_email" style="width:100%;" placeholder="<?php esc_attr_e('No personally identifying email address...', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo $meeting->contact_3_email ?>" >
																</div>
															</div>
															<div class="meta_form_row row" >
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="contact_3_phone"><?php esc_attr_e('Contact 3 Phone', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="contact_3_phone" id="contact_3_phone" style="width:100%;" placeholder="<?php esc_attr_e('10 digit number for a group contact', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo esc_attr($meeting->contact_3_phone) ?>" >
																</div>
															</div>
														</div>
														<!-- ---------------------------- Group Information Ends ------------------------------------------- -->
													</div>
												</div>
											</div>
											<div class="clearfix " ></div>
											<!-- ************************** Add New Request Form starts here ********************************* --> 
											<div id="add_new_request" class="" style="display:none;" >
												<!-- ************************** Add New Request Details starts here ************************** -->
												<div id="divAddNew" class="panel-header panel-default" >	
													<div id="addnewdetail">
														<div class="meta_form_separator row">
															<div class="col-md-8">
																<h4><?php esc_attr_e('Meeting Details', '12-step-meeting-list-feedback-enhancement')?></h4>
															</div>
														</div>
														<div class="meta_form_row row">
															<div class="well well-sm col-md-10 col-md-offset-1 ">
																<label for="new_name"><?php esc_attr_e('Meeting Name', '12-step-meeting-list-feedback-enhancement')?></label><br>
																<input type="text" class="required"  name="new_name" id="new_name" style="width:100%; display:block;" placeholder="<?php esc_attr_e('Enter meeting short name (ie. without the words Group or Meeting...)', '12-step-meeting-list-feedback-enhancement')?>"  >
															</div>
														</div><br> 
														<div class="meta_form_row row">
															<div class="well well-sm col-md-10 col-md-offset-1 ">
																<div class="col-md-6">
																	<label for="new_day"><?php esc_attr_e('Day', '12-step-meeting-list-feedback-enhancement')?></label><br />
																	<select name="new_day" id="new_day">
																		<?php foreach ($tsml_days as $key => $day) { ?>
																		<option value="<?php echo esc_html( $key ) ?>"<?php selected(0) ?> ><?php echo esc_html( $day ) ?></option>
																		<?php } ?>
																	</select>
																</div>
																<div class="col-md-3 text-center" >
																	<label for="new_time"><?php esc_attr_e('Start Time', '12-step-meeting-list-feedback-enhancement')?></label><br />
																	<select name="new_time" id="new_time">
																		<?php $options = meeting_times_array();
																		foreach ( $options as $key => $val ) {?>
																		<option value="<?php echo esc_html( $key ) ?>"<?php selected(strcmp(@$meeting->time, $key) == 0)?> ><?php echo esc_html( $val ) ?></option>
																		<?php }?>
																	</select>
																</div>
																<div class="col-md-3 text-center"  style="display:none;" > 
																	<label for="new_end_time"><?php esc_attr_e('Start Time', '12-step-meeting-list-feedback-enhancement')?></label><br />
																	<select name="new_end_time" id="new_end_time">
																		<?php $options = meeting_times_array();
																		foreach ( $options as $key => $val ) {?>
																		<option value="<?php echo esc_html( $key ) ?>"<?php selected(strcmp(@$meeting->time, $key) == 0)?> ><?php echo esc_html( $val ) ?></option>
																		<?php }?>
																	</select>
																</div>
															</div>
														</div>
														<div class="meta_form_row row">
															<div class="well well-sm col-md-10 col-md-offset-1 ">
																<?php if (tsml_program_has_types())	{?>

																<label for="new_types"><?php esc_attr_e('Types', '12-step-meeting-list-feedback-enhancement') ?> </label>
																<div class="checkboxes">
																	<?php
																	$default_checkbox = array('C', 'O', 'ONL');
																	foreach ($tsml_programs[$tsml_program]['types'] as $key => $type) {
																		if (!in_array($key, $tsml_types_in_use)) continue; //hide TYPES not used 
																		if ($key == 'ONL') continue; //hide "Online Meeting" since it's not manually settable
																		?>
																		<div class="checkbox col-md-6" >
																			<label>
																				<input type="checkbox" name="new_types[]" id="$key" value="<?php echo esc_html( $key ) ?>" <?php checked(in_array($key, $default_checkbox))?> >
																				<?php echo esc_html( $type ) ?>
																			</label>
																		</div>
																	<?php } ?>
																</div>
																<?php } ?>
															</div>
														</div>
														<div class="meta_form_row row">
															<div class="well well-sm col-md-10 col-md-offset-1 ">
																<label for="new_notes"><?php esc_attr_e('Notes', '12-step-meeting-list-feedback-enhancement')?></label>
																<textarea name="new_content" id="new_content" rows="7" style="width:100%;" placeholder="<?php esc_attr_e('notes are specific to this meeting. For example: Birthday speaker meeting last Saturday of the month.', '12-step-meeting-list-feedback-enhancement')?>" ></textarea>
															</div>
														</div>
														<div class="meta_form_row row">
															<div class="well well-sm col-md-10 col-md-offset-1 ">
																<h4><?php esc_attr_e('Online Meeting Details', '12-step-meeting-list-feedback-enhancement')?></h4>
																<p><?php echo sprintf(esc_attr__('If this meeting has videoconference information, please enter the full valid URL here. Currently supported providers: %s. If other details are required, such as a password, they can be included in the Notes field above, but a ‘one tap’ experience is ideal. Passwords can be appended to phone numbers using this format <code>+12125551212,,123456789#,,#,,444444#</code>', '12-step-meeting-list-feedback-enhancement'), implode(', ', tsml_conference_providers()))?></p>
															</div>
														</div>
														<div class="meta_form_row row">
															<div class="well well-sm col-md-10 col-md-offset-1 ">
																<label for="new_conference_url"><?php esc_attr_e('Conference URL', '12-step-meeting-list-feedback-enhancement')?></label><br>
																<input type="Url" name="new_conference_url" id="new_conference_url" style="width:100%;" placeholder="https://zoom.us/j/9999999999?pwd=1223456" value="<?php echo "" ?>">
															</div>
														</div>
														<div class="meta_form_row row" > 
															<div class="well well-sm col-md-10 col-md-offset-1 ">
																<label for="new_conference_url_notes"><?php esc_attr_e('Conference URL Notes', '12-step-meeting-list-feedback-enhancement')?></label><br>
																<input type="text" name="new_conference_url_notes" id="new_conference_url_notes" style="width:100%;" placeholder="<?php esc_attr_e('Password if needed or other info related to joining an online meeting...', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo "" ?>">
															</div>
														</div>
														<div class="meta_form_row row">
															<div class="well well-sm col-md-10 col-md-offset-1 ">
																<label for="new_conference_phone"><?php esc_attr_e('Conference Phone #', '12-step-meeting-list-feedback-enhancement')?></label><br>
																<input type="text" name="new_conference_phone" id="new_conference_phone" style="width:100%;" placeholder="<?php esc_attr_e('Phone Number for your Online meeting Provider', '12-step-meeting-list-feedback-enhancement')?>" >
															</div>
														</div>
														<div class="meta_form_row row" > 
															<div class="well well-sm col-md-10 col-md-offset-1 ">
																<label for="new_conference_phone_notes"><?php esc_attr_e('Conference Phone Notes', '12-step-meeting-list-feedback-enhancement')?></label> <br>
																<input type="text" name="new_conference_phone_notes" id="new_conference_phone_notes" style="width:100%;" placeholder="<?php esc_attr_e('Info related to joining an online meeting via phone...', '12-step-meeting-list-feedback-enhancement')?>" >
															</div>
														</div>
														<div class="meta_form_row row">
															<div class="col-md-12">
																<h4><?php esc_attr_e('Location Details', '12-step-meeting-list-feedback-enhancement')?></h4>
																<p><?php echo '' ?></p>
															</div>
														</div>
														<div class="meta_form_row row">
															<div class="well well-sm col-md-10 col-md-offset-1 ">
																<label for="new_location"><?php esc_attr_e('Location', '12-step-meeting-list-feedback-enhancement')?></label><br>
																<input type="text" name="new_location" id="new_location" style="width:100%;" placeholder="<?php esc_attr_e('building name', '12-step-meeting-list-feedback-enhancement')?>" >
															</div>
														</div>
														<div class="meta_form_row row">
															<div class="well well-sm col-md-10 col-md-offset-1 ">
																<label for="new_formatted_address"><?php esc_attr_e('Address', '12-step-meeting-list-feedback-enhancement')?></label><br>
																<input type="text" name="new_formatted_address" id="new_formatted_address" style="width:100%;" placeholder="<?php esc_attr_e('Combination of address, city, state/prov, postal_code, and country required', '12-step-meeting-list-feedback-enhancement')?>" >
															</div>
														</div>
														<?php if (wp_count_terms('tsml_region')) {?>
														<div class="meta_form_row row">
															<div class="well well-sm col-md-5 col-md-offset-1">
																
																<label for="new_region"><?php esc_attr_e('Region', '12-step-meeting-list-feedback-enhancement')?></label><br />
																<?php wp_dropdown_categories(array(
																	'name' => 'new_region', 'taxonomy' => 'tsml_region','hierarchical' => true, 'id' => 'new_region_id',
																	'hide_empty' => false, 'orderby' => 'name', 'selected' =>  null,
																	'show_option_none' => esc_attr__(' ', '12-step-meeting-list-feedback-enhancement'), 
																))?>
															</div>		
															<div class="well well-sm col-md-5 ">		
																<label for="new_sub_region"><?php esc_attr_e('Sub Region', '12-step-meeting-list-feedback-enhancement')?></label><br>
																<input type="text" name="new_sub_region" id="new_sub_region" placeholder="<?php esc_attr_e('related to Region...', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo '' ?>">
															</div>
														</div>
														<?php }?>
														<div class="meta_form_row row" > 
															<div class="well well-sm col-md-10 col-md-offset-1 ">
																<label for="new_location_notes"><?php esc_attr_e('Location Notes', '12-step-meeting-list-feedback-enhancement')?></label><br>
																<input type="text" name="new_location_notes" id="new_location_notes" style="width:100%;" placeholder="<?php esc_attr_e('common information that will apply to all meetings at this location', '12-step-meeting-list-feedback-enhancement')?>" >
															</div>
														</div>
														<div class="meta_form_row row">
															<div class="col-md-12">
																<h4>
																	<label><input type="radio" name="new_group_status" onclick="toggleAdditionalInfoDisplay('divAdditionalNewGroupInfo', 'hide')"  value="meeting"<?php checked(empty(''))?> > <?php esc_attr_e('Hide', '12-step-meeting-list-feedback-enhancement')?></label>
																	<label><input type="radio" name="new_group_status" onclick="toggleAdditionalInfoDisplay('divAdditionalNewGroupInfo', 'show')"  value="group"<?php checked(!empty(''))?> > <?php esc_attr_e('Show', '12-step-meeting-list-feedback-enhancement')?></label>
																	<?php esc_attr_e('Additional Group Information', '12-step-meeting-list-feedback-enhancement')?>
																</h4>
															</div>
														</div>
														<div class="meta_form_row row ">
															<div class="col-md-11 col-md-offset-1 radio">
															</div>
														</div>

														<!-- --------------------------------- Additional Group Information Starts ----------------------------------------- -->

														<div id="divAdditionalNewGroupInfo" style="display:none;" >

															<div class="meta_form_row row">
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="new_group"><?php esc_attr_e('Group Name', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="new_group" id="new_group" style="width:100%;" placeholder="<?php esc_attr_e('full registered name...', '12-step-meeting-list-feedback-enhancement')?>" >
																</div>
															</div>
															<?php if (wp_count_terms('tsml_district')) {?>
															<div class="meta_form_row row">
																<div class="well well-sm col-md-4 col-md-offset-1 ">
																	<label for="new_district"><?php esc_attr_e('District', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<?php wp_dropdown_categories(array(
																		'name' => 'new_district', 'taxonomy' => 'tsml_district','hierarchical' => true, 
																		'hide_empty' => false, 'orderby' => 'name', 'selected' => empty($meeting->new_district_id) ? null : $meeting->new_district_id,
																		'show_option_none' => esc_attr__(' ', '12-step-meeting-list-feedback-enhancement'), 
																	))?>
																</div>
																<div class="well well-sm col-md-6  ">
																	<label for="new_sub_district"><?php esc_attr_e('Sub District', '12-step-meeting-list-feedback-enhancement')?></label><br> 
																	<input type="text" name="new_sub_district" id="new_sub_district" style="width:100%;" placeholder="<?php esc_attr_e('related to district...', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo '' ?>">
																</div>
															</div>
															<?php }?>
															<div class="meta_form_row row">
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="new_group_notes"><?php esc_attr_e('Group Notes', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="new_group_notes" id="new_group_notes" style="width:100%;" placeholder="<?php esc_attr_e('eg. when the business meeting takes place, etc.', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo '' ?>">
																</div>
															</div>
															<div class="meta_form_row row">
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="new_website"><?php esc_attr_e('Website', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="new_website" id="new_website" style="width:100%;" placeholder="<?php esc_attr_e('https:// primary URL of org where group posts its meeting info', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo '' ?>">
																</div>
															</div>
															<div class="meta_form_row row">
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="new_website_2"><?php esc_attr_e('Website 2', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="new_website_2" id="new_website_2" style="width:100%;" placeholder="<?php esc_attr_e('https:// secondary URL of org where group posts its meeting info', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo '' ?>">
																</div>
															</div>
															<div class="meta_form_row row" style="display:block;" >
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="new_email"><?php esc_attr_e('Email', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="new_email" id="new_email" style="width:100%;" placeholder="<?php esc_attr_e('non personal email (i.e. groupName@gmail.com)', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo '' ?>">
																</div>
															</div>
															<div class="meta_form_row row">
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="new_phone"><?php esc_attr_e('Phone', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="new_phone" id="new_phone" style="width:100%;" placeholder="<?php esc_attr("group contact number: +18005551212")?>" value="<?php echo '' ?>">
																</div>
															</div>
															<div class="meta_form_row row">
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="mailing_address"><?php esc_attr_e('Mailing Address', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="new_mailing_address" id="new_mailing_address"  style="width:100%;" placeholder="<?php esc_attr_e('postal address which receives correspondence for the group', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo '' ?>">
																</div>
															</div>
															<div class="meta_form_row row">
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="new_venmo"><?php esc_attr_e('Venmo', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="new_venmo" id="new_venmo" style="width:100%;" placeholder="<?php esc_attr_e('@VenmoHandle - handle for 7th Tradition contributions', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo $meeting->venmo ?>">
																</div>
															</div>
															<div class="meta_form_row row">
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="new_square"><?php esc_attr_e('Square', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="new_square" id="new_square" style="width:100%;" placeholder="<?php esc_attr_e('$Cashtag - handle for 7th Tradition contributions', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo $meeting->square ?>">
																</div>
															</div>
															<div class="meta_form_row row">
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="new_paypal"><?php esc_attr_e('PayPal', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="new_paypal" id="new_paypal" style="width:100%;" placeholder="<?php esc_attr_e('PayPalUsername - handle for 7th Tradition contributions', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo $meeting->paypal ?>">
																</div>
															</div>
															<div class="meta_form_row row">
																<div class="col-md-12">
																	<h4><?php esc_attr_e('Contact Information', '12-step-meeting-list-feedback-enhancement')?></h4>
																	<p><?php echo '' ?></p>
																</div>
															</div>
															<div class="meta_form_row row" >
																<div class="well well-sm col-md-10 col-md-offset-1" >
																	<label for="new_contact_1_name"><?php esc_attr_e('Contact 1 Name', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="new_contact_1_name" id="new_contact_1_name" style="width:100%;" placeholder="<?php esc_attr_e('First Name & Last Initial', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo '' ?>">
																</div>
															</div>
															<div class="meta_form_row row"  >
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="new_contact_1_email"><?php esc_attr_e('Contact 1 Email', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="new_contact_1_email" id="new_contact_1_email"  style="width:100%;" placeholder="<?php esc_attr_e('No personally identifying email address...', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo '' ?>">
																</div>
															</div>
															<div class="meta_form_row row" >
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="new_contact_1_phone"><?php esc_attr_e('Contact 1 Phone', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="new_contact_1_phone" id="new_contact_1_phone" style="width:100%;" placeholder="<?php esc_attr_e('10 digit number for a group contact', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo '' ?>">
																</div>
															</div>
															<div class="meta_form_row row" >
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="new_contact_2_name"><?php esc_attr_e('Contact 2 Name', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="new_contact_2_name" id="new_contact_2_name" style="width:100%;" placeholder="<?php esc_attr_e('First Name & Last Initial', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo '' ?>">
																</div>
															</div>
															<div class="meta_form_row row" >
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="new_contact_2_email"><?php esc_attr_e('Contact 2 Email', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="new_contact_2_email" id="new_contact_2_email" style="width:100%;" placeholder="<?php esc_attr_e('No personally identifying email address...', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo '' ?>">
																</div>
															</div>
															<div class="meta_form_row row" >
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="new_contact_2_phone"><?php esc_attr_e('Contact 2 Phone', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="new_contact_2_phone" id="new_contact_2_phone" style="width:100%;" placeholder="<?php esc_attr_e('10 digit number for a group contact', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo '' ?>">
																</div>
															</div>
															<div class="meta_form_row row" >
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="new_contact_3_name"><?php esc_attr_e('Contact 3 Name', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="new_contact_3_name" id="new_contact_3_name" style="width:100%;" placeholder="<?php esc_attr_e('First Name & Last Initial', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo '' ?>">
																</div>
															</div>
															<div class="meta_form_row row"  >
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="new_contact_3_email"><?php esc_attr_e('Contact 3 Email', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="new_contact_3_email" id="new_contact_3_email" style="width:100%;" placeholder="<?php esc_attr_e('No personally identifying email address...', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo '' ?>">
																</div>
															</div>
															<div class="meta_form_row row" >
																<div class="well well-sm col-md-10 col-md-offset-1 ">
																	<label for="new_contact_3_phone"><?php esc_attr_e('Contact 3 Phone', '12-step-meeting-list-feedback-enhancement')?></label><br>
																	<input type="text" name="new_contact_3_phone" id="new_contact_3_phone" style="width:100%;" placeholder="<?php esc_attr_e('10 digit number for a group contact', '12-step-meeting-list-feedback-enhancement')?>" value="<?php echo '' ?>">
																</div>
															</div>
														</div>

														<!-- -------------------------- New Group Information Ends ------------------------------------------- -->
													</div> 
												</div>
											</div> 
											<div class="clearfix " ></div>
										</li> 
										<li class="list-group list-group-item-form " >											
											<!-- ************************** Signature Information starts here ************************** -->
											<div id="divSignature" style="float:left; width:100%; margin: 0; padding: 0;" >
												<ul class="list-group-item-form" style="margin: 0; padding: 0;" >
													<li class="list-group-item list-group-item-warning">
														<h4><?php esc_attr_e('Signature Information', '12-step-meeting-list-feedback-enhancement')?></h4>
													</li>
													<li class="list-group-item list-group-item-information">
														<input type="text" id="tsml_name" name="tsml_name" style="width:100%; display:block;" placeholder="<?php esc_attr_e('Your Name', '12-step-meeting-list-feedback-enhancement')?>" class="required">
														<input type="email" id="tsml_email" name="tsml_email" style="width:100%; display:block;" placeholder="<?php esc_attr_e('Email Address', '12-step-meeting-list-feedback-enhancement')?>" class="required email">
														<textarea id="tsml_message" name="tsml_message" rows="7" style="width:100%;" placeholder="<?php esc_attr_e('Your Message', '12-step-meeting-list-feedback-enhancement')?>" ></textarea><br><br>
														<center><button type="submit" class="btn btn-primary" id="submit_change" name="submit" onclick="return confirm('Are you ready to submit your changes for this meeting?');" style="display:block; " value="change" ><?php esc_attr_e('Submit', '12-step-meeting-list-feedback-enhancement')?></button></center>
														<center><button type="submit" class="btn btn-primary" id="submit_new" name="submit" onclick="return confirm('Are you ready to send us your new meeting information?');" style="display:none; " value="new" ><?php esc_attr_e('Submit', '12-step-meeting-list-feedback-enhancement')?></button></center>
														<center><button type="submit" class="btn btn-primary" id="submit_remove" name="submit" onclick="return confirm('Are you really sure you want to have this meeting permanently removed from our listing?  There is an option to have the Location marked as Temporarily Closed! Just cancel and then on the Change screen mark the meeting type checkbox for Location Temporily Closed.');" style="display:none;" value="remove" ><?php esc_attr_e('Submit', '12-step-meeting-list-feedback-enhancement')?></button></center>
													</li>
												</ul>
											</div>
										</li>
									</ul>
									<!--</div>-->
									<div class="clearfix " ></div>
								</div>
							</form>
						<?php }?>
					</div>
				</div>
			</div>
		</div>

		<?php if (is_active_sidebar('tsml_meeting_bottom')) { ?>
			<div class="widgets meeting-widgets meeting-widgets-bottom" role="complementary">
				<?php dynamic_sidebar('tsml_meeting_bottom') ?>
			</div>
		<?php } ?>

	</div>
</div>
<?php
get_footer();
