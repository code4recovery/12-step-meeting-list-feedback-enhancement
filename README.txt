=== 12 Step Meeting List Feedback Enhancement ===
Contributors: code4recovery
Donate link: https://code4recovery.org/donate
Requires at least: 3.2
Requires PHP: 5.6
Requires 12 Step Meeting List Version: 3.12
Tested up to: 6.5
Stable tag: 1.0.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin is designed to extend and enhance the feedback feature of the legacy 12 Step Meeting List plugin. 

== Description ==

This plugin is designed to enhance the feedback feature of the legacy 12-step-meeting-list plugin found on its Meeting Detail screen. It provides a formatted solution to guide user feedback input, giving a consistent, auditable, and accurate view of what the feedback submitter is wanting added, changed, or removed in the 12 Step Meeting List.

== Installation ==

* Upload the plugin files to the /wp-content/plugins directory, or install the plugin through the WordPress plugins screen directly.
* Activate the plugin through the ‘Plugins’ screen in WordPress.

== Frequently Asked Questions ==

= No 'Request a change to this listing' button found? =

Like the original feedback feature, this enhanced version requires a website administrator email address be entered in the "User Feedback Emails" field on the Meetings/Import & Settings page before the feedback system will display.

= What version of the 12 Step Meeting List plugin is required? =

Version 3.12 or later.

= Is there a way to un-hide the Contact Information fields on the Additional Group Information portion of the form? =

Add this to your (child) theme’s functions.php. 

		$tsml_hide_contact_information = false;


== Screenshots ==

1. screenshot-1.png. Meeting detail page normal view with 'Request a change to this listing' button visible.
1. screenshot-2.png. Meeting detail page with enhanced 'Meeting Change Request' visible.
1. screenshot-3.png. Meeting detail page with green 'Request Submission Success' message visible.
1. screenshot-4.png. Example of an enhanced 'Meeting Change Request' feedback email.

== Changelog ==

= 1.0.8 = 
* Synchronize single-meetings with corresponding file in TSML 3.15 version to fix block theme header/footer bug.
* Connect Settings page 'Contact Visibility' option to the Contact fields override variable. 

= 1.0.7 = 
* Synchronize and update single-meetings.php with corresponding file in TSML 3.14.15 version.
* Set display of Additional Information Contact fields default to hidden. Change FAQ to reflect this change. https://github.com/code4recovery/12-step-meeting-list-feedback-enhancement/issues/26
* Disable link to 'Meetings at this Location' feature. https://github.com/code4recovery/12-step-meeting-list-feedback-enhancement/issues/23
* Disable double click on Change Request button for approximate locations. Fix bug displaying empty div below map. https://github.com/code4recovery/12-step-meeting-list-feedback-enhancement/issues/19

= 1.0.6 = 
* Synchronize and update single-meetings with features in TSML 3.14.5 version, including attendance option.
* Add radio button control and info graphic to manage in-person/online/hybrid/inactive attendance options. 
* Remove sub region text field. 
* Change free form input text boxes to receive multi-line input.
* Move Open, Close, Men-Only, Women-Only from Types checkbox list to mutually exclusive radio buttons.
* Add TC and ONL types to list of types excluded from Types checkbox list. 
* Add validation for all URL type input fields.
* Add bootstrap visibility error classes for required and validated information fields.
* Add Meeting End Time dropdown field.
* Move "Request for Change" button back to original position near bottom left column.
* Make display of Additional Information Contact fields optional. Add FAQ for same here.

= 1.0.5 = 
* Add uninstall function.

= 1.0.4 = 
* Enqueued java script in footer with jquery dependency to resolve bootstrap button issue. Added code for deactivate function to fix deactivation error.

= 1.0.3 = 
* Updated CSS and internal styles to display correctly with most themes.

= 1.0.2 = 
* Removed periods from header which caused invalid header warning on activation when debug mode is on.

= 1.0.1 = 
* Added version check of '12 Step Meeting List' plugin during Activation. If version number is less than 3.12 Activation will fail.

= 1.0 = 
* Initial release to WordPress SVN repository. 

== Upgrade Notice ==

= 1.0 =
* Enhances the '12 Step Meeting List' plugin feedback feature found on the meeting details page.  
