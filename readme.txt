=== Contact Form With Shortcode ===
Contributors: avimegladon
Donate link: http://aviplugins.com/donate/
Tags: contact form, email, user query, query, user mail, contact, contact widget, custom fields, contact fields, date, time, newsletter, newsletter email, mail, contact fields, newsletter template, wp newsletter, subscription, subscribers, newsletter subscribers, ajax contact, responsive, responsive contact, ajax form, ajax newsletter, contact widget, widget contact, ajax contact widget, file attachment, contact file, contact file upload, file upload
Requires at least: 2.0.2
Tested up to: 4.7.4
Stable tag: 4.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This is a contact form with shortcode plugin. With option to add unlimited dynamic fields to the form. 

== Description ==

This is a contact form plugin. You can use widgets and shortcodes to display contact form in your theme. Unlimited number of dynamic fields can me created for contact froms. Newsletter subscription. Send newsletter emails to subscribers. Forms are responsive.

= Features =
* Enable <strong>AJAX</strong> driven contact form submission.
* Support for SMTP.
* Create unlimited number of contact forms.
* Create Dynamic form fields.
* Option to enable Captcha security in contact forms.
* Store contact from data in database, change query status to ( Processing, Attending, Unresolved, Resolved ) for easy management.
* Easy contact email Setup.
* Option to upload <strong>Files</strong> in contact forms. Uploaded files will be mailed as <strong>Attachments</strong> to selected mail address. This way admin can easily request important docs from users using the contact form. Supported files are jpg, jpeg, png, gif, doc, docx, pdf. <strong>PRO</strong> version supports additional file types as <strong>Email Attachments</strong>.
* Newsletter subscription widget.
* Send bulk newsletter emails to subscribers.
* You can send blog posts as newsletter. PRO version allowes you to send posts from custom post types as newsletter.
  
= Contact Form With Shortcode PRO =

Get the PRO version from <a href="http://aviplugins.com/contact-form-with-shortcode-pro/" target="_blank">aviplugins.com</a> for <strong>USD 3.00</strong>

* Support for <strong>SMTP</strong>
* Sort contact form fields by <strong>Drag & Drop</strong>.
* Option to use custom Captcha security or Google ReCAPTCHA in contact forms.
* Store contact form submitted data to database, option to Export data in XLS. Reply directly to user from admin panel.
* New user type "Query Manager" can be created who will be able to handle query data, Reply to user, Change status of the query from admin panel.
* <strong>AJAX</strong> mode for contact and newsletter subscription forms.
* Only selected file types can be uploaded and send as attachment. File types can be Enabled/ Disabled from settings page.
* Create unlimited newsletter templates.
* Send recent posts in the newsletter email. There are different <strong>options</strong> available to choose from to create your desired newsletter Email. Checkout the options <a href="http://aviplugins.com/contact-form-with-shortcode-pro/" target="_blank">here</a>.
* Send <strong>Posts</strong> from <strong>Custom post types</strong> in the Newsletter Email.
* Choose <strong>Theme</strong> for your newsletter. <strong>FREE</strong> downloadable themes are available to download for Newsletter Templates from <a href="http://aviplugins.com/contact-form-with-shortcode-pro/" target="_blank">here</a>.
* Send bulk Emails to the subscribers.

> Post your plugin related queries at <a href="http://www.aviplugins.com/support.php" target="_blank">http://www.aviplugins.com/support.php</a>

== Installation ==

1. Upload `contact-form-with-shortcode.zip` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Crate a new contact form and setup the form fields.
4. Use widget or shortcodes to display the contact form in frontend.

= Newsletter Shortcode Usage =  
Use these shortcodes in your newsletter template.

* Send latest 10 blog posts in the newsletter.<br>
Example: <strong>[newsletter]</strong>
* Option <strong>cat</strong> is optional. The parameter for this option is your category ID. Category ID can be blog post category. This option can be used when you want to send posts of a specific category in the newsletter email. You can use multiple category IDs here.<br>
Example: <strong> [newsletter cat="4,5,6"]</strong>
* Option <strong>count</strong> is optional. The parameters can be any numaric value 10, 20 etc. count option is for number of posts you want to send in the newsletter. Default is 10.<br>
Example: <strong>[newsletter count="5"]</strong>
* Option <strong>order</strong> is optional. The parameters can be asc or desc. This is to set the order of posts in the newsletter template.<br>
Example: <strong>[newsletter order="asc"]</strong>
* Option <strong>orderby</strong> is optional. The parameters can be date, ID, title. orderby is used in addition with order option.<br>
Example: <strong>[newsletter order="asc" orderby="date"]</strong>
* Option <strong>featuredimage</strong> is optional. The parameters can be yes or no. This is to enable featured image for posts in the newsletter email. Default is yes.<br>
Example: <strong>[newsletter featuredimage="yes"]</strong>
* Option <strong>readmore</strong> is optional. The parameters can be yes or no. This is to enable read more links posts in the newsletter email. Default is yes.<br>
Example: <strong>[newsletter readmore="yes"]</strong>

== Frequently Asked Questions ==

= Support =

1. Please submit your queries here <a href="http://www.aviplugins.com/support.php" target="_blank">http://www.aviplugins.com/support.php</a> or contact at <a href="http://www.aviplugins.com/contact.php" target="_blank">http://www.aviplugins.com/contact.php</a>


== Screenshots ==

1. Contact form setup

1. Contact form setup 2

3. Contact form view 1

4. Contact form view 2

5. Contact form widget

6. SMTP Settings

7. Customizable success message.

== Changelog ==

= 4.0.1 =
* Custom Success message. Plugin Dashboard updated.

= 4.0.0 =
* Contact form query data will be stored in database. Please Deactivate and Reactivare the plugin once after the update, otherwise the new functionalities will not work. 

= 3.2.2 =
* Bug fixed.

= 3.2.1 =
* Now compatible with <a href="https://wordpress.org/plugins/wp-register-profile-with-shortcode/">WP Register Profile With Shortcode</a>

= 3.2.0 =
* This is a major update. Newsletter subscription module is added in the plugin.

= 3.1.0 =
* SMTP support added.

= 3.0.0 =
* Option to send Thank You mail to user. 

= 2.3.2 =
* Reply-to option added in the contact form.

= 2.3.1 =
* Optional Captcha security added in contact forms.

= 2.3.0 =
* Plugin code updated.

= 2.2.4 =
* Code updated with some security modifications.

= 2.2.3 =
* news dashboard widget optimized.

= 2.2.2 =
* aviplugins.com news dashboard widget added. 

= 2.2.1 =
* Addon support added for storing contact form data in database.

= 2.1.1 =
* Notice message bug fixed.

= 2.1.0 =
* File upload error message modified.

= 2.0.0 =
* Files can be uploaded in contact forms.

= 1.0.1 =
* this is the first release.

= 1.1.1 =
* AJAX driven contact form added


== Upgrade Notice ==

= 1.0 =
I will update this plugin when ever it is required.
