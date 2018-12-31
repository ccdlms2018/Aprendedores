=== Project Panorama ===
Contributors: Ross Johnson
Tags: project, management, project management, basecamp, status, client, admin, intranet
Requires at least: 3.5.0
Tested up to: 4.5.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Keep clients happy by communicating project status and progress in a visual way. Project Panorama is a simple and effective alternative to basecamp and other project management software.

== Description ==

Project Panorama is a WordPress project management plugin designed to keep your clients and team in the loop. Each project can be configured to display overall project status, store documents, identify task and task completion, have phases and phase progress.

= Website =
https://www.projectpanorama.com

= Documentation =
https://www.projectpanorama.com/docs

= Bug Submission and Forum Support =
https://www.projectpanorama.com/support


== Installation ==

1. Upload 'project-panorama' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Click on the new menu item "Projects" and create your first project!
4. You can now visit and share the project with clients
5. Embed projects into pages and posts using the embed code [project_status id="<post_id>"] which can be found on the project listing page

== Upgrading ==

Upgrading from Project Panorama Lite?

1. Disable Project Panorama Lite
2. Upload Project Panorama Pro
3. Activate Project Panorama Pro

All of your projects will exist. You may have to move your documents to the new and improved document manager.

== Credits ==

Project Panorama is powered by Advanced Custom Fields and the Advanced Custom Field Repeater Add-on. Advanced Custom Field Repeater Add-on may not be removed, distributed or sold without purchase from Advanced Custom Fields (http://www.advancedcustomfields.com).

Project Panorama also uses open source code from Mark Root-Wiley (http://mrwweb.com/) and Lopo (http://Lopo.it)

== Changelog ==

= 1.6.1 =
* Fix: Uploader button in admin not working
* Fix: White text in task panel if you've configured your menu colors

= 1.6 =
* Feature: Adds documents to individual tasks
* Feature: Adds discussions to individual tasks
* Improvement: Better live updates of stats on the front end
* Improvement: Completed tasks no longer show up in calendar

= 1.5.8.10 =
* Adds RTL support
* Supports for upcoming task related add-ons
* Updates task graph on project single when tasks are complete
* Only redraw phases chart if auto progression has been set
* Adds support for CC and BCC notifications on emails

= 1.5.8.9 =
* Custom links appear on all submenus in panorama
* Project Managers can now create teams
* Pagination on search resolved

= 1.5.8.6 =
* Added notification for project progress
* Project list widget now supports the collapsed view
* Can now link to /panorama/calendar/ and it will automatically show the logged in users calendar
* Lots of design improvements
* Prep for front end editor improvements

= 1.5.8.3 =
* %current_user% now works for notifications generated from admin view
* No longer require restrict access for notifications to be sent to users

= 1.5.8.2 =
* Updated what is passed through hooks to reduce queries on load
* Changed fire order of JS to prevent project status updates before task update is complete
* Changed CSS behavior for before-milestone and after-milestone shortcodes
* Fixed issue with excerpt view on projects
* Added attribute of collapsed for [project_list] for a more compact view [project_list collapsed="true"]

= 1.5.8.1 =
* Fixed bug with task title notifications
* Redesigned milestone area
* Minor graphic improvements
* Better support for dates in different languages

= 1.5.8 =
* Refined design in several areas
* Adds compatibility for upcoming front end editor update
* Post edit notification now includes users added via team
* Adds option to restrict media gallery within projects to files uploaded to the project
* Added new task notification type of task_assigned
* Workaround for WordPress 4.8.3 escaping SQL issue

= 1.5.7.4 =
* Added slug to project types for more consistency

= 1.5.7.3 =
* Added menu support for Panorama Pages add-on

= 1.5.7.2 =
* Select2 removed

= 1.5.7.1 =
* Better handling of going from zero documents to one document with front end uploader
* Updated repeater collapser
* Bug fix around displaying logos on navigation

= 1.5.7 =
* Added support for publishing documents in phases (make sure to download the latest front end uploader!)
* Addresses issue with showing all projects under admin user lists
* MISC edge case bug fixes
* Design refinements around dashboard
* Better support for using the custom / theme template function, much more usable!
* Resolves issue with commenting on password protected projects
* Adds support for prefixes on permalink structure
* Addresses missing %project_title% variable for task due notifications
* Addresses issues with calendar not displaying dates in dashboard

= 1.5.6.1 =
* Fixed issue with using two psp_project_part shortcodes on the same page
* Added new notification recipient variables %subscribers% %project_owners% %project_managers%

= 1.5.6 =
* Document search only visible if you have more than five documents
* Removed improper hook from project_status_part shortcode
* Resolved the flash of unstyled navigation on page load
* Fixed invisible tasks when entered as links
* Resolved email notification troubles
* Fixed issue where you couldn't scroll after changing document status
* Added lost password link to login page
* Failed logins through panorama will redirect back to the failed login page vs WordPress login page

= 1.5.5 =
* Switched include logo option to select, fixes issue with
* Added setting to change how the dashboard is sorted (project start, end, alphabetical, etc...)
* Added edit link to the masthead of projects if user has permission to edit
* Switched auto-expand comment JS for IE support
* Added option for documents to not have a status (don't need to be approved, etc...)

= 1.5.4 =
* Added ability to upload custom favicon
* Added percentage phase calculation

= 1.5.3 =
* Made entire projects clickable in the dashboard
* Limited comment nesting to 3

= 1.5.2 =
* Fixed date format on project page
* Fixed issue with mini charts incorrectly rendering totals

= 1.5.1 =
* Fixed issue with calculation on phases and task counts

= 1.5 =
* Redesign a refresh of the entire interface
* Added ability to assign priority to projects (only visible to project owners and above)
* Improved responsive breakpoints
* Added client name to task listing in dashboard view
* Linked comments will automatically expand and receive focus
* Added link to expand or collapse all phase discussions
* Better organized project editing interface
* Added more dynamic variables to notifications

= 1.4.4 =
* Updated chart.js and jquery.js for performance
* Normalized tooltip, popover and modal libraries (now using Bootstrap 3)

= 1.4.3.2 =
* BUG: Fixed issue with 404s on page 2 of dashboard
* BUG: Fixed issue with ACF5 and the user select field
* COMPATIBILITY: Switched site_url with home_url to prevent 404s when WordPress core is in a subdirectory

= 1.4.3.1 =
* BUG: Fixed issue with %project_title% variable on user assignment notifications
* COMPATIBILITY: Fixed issue with using %current_user% on date based task notifications
* COMPATIBILITY: Improved compatibility with iCal feeds
* UX: Fixed issues with trying to activate without saving license first
* UX: When filtering projects on the dashboard type and count options are saved

= 1.4.3 =
* UPDATES: iCal feed links to make importing them into calendars easier
* FEATURE: Body class now has user role

= 1.4.2.9 =
* COMPATIBILITY: Supports older versions of PHP

= 1.4.2.8 =
* COMPATIBILITY: Changed license error checking to better support older versions of PHP

= 1.4.2.7 =
* FEATURE: Added two new notifications, tasks due today and task is overdue (sent day after it's due)
* FEATURE: Dynamic notification variables only display when they can be used with the notification type
* COMPATIBILITY: Improved support for plain permalinks
* FEATURED: Added iCal feed to calendars

= 1.4.2.6 =
* BUG: Fixed issue with warnings on dashboard
* BUG: Fixed issue where sometimes task status got saved as an empty character
* FEATURE: Added 5% increments to task update on front end

= 1.4.2.5 =
* BUG: You no longer have to assign a single user to a project if you're also using teams
* FEATURE: Improved modal usability
* FEATURE: Added universal JS libraries for better performance
* FEATURE: Added advanced setting to turn on wp_head and wp_footer for shortcode compatibility
* FEATURE: Added advanced setting for disabling file obfuscation
* FEATURE: Added option to lazy load WYSIWYG fields for performance
* FEATURE: Added upcoming tasks page in dashboard
* COMPATIBILITY: Improved responsive displays
* FEATURE: Project owners and authors only see media they have uploaded to the gallery
* COMPATIBILITY: Better handling of variables in notifications
* FEATURE: Added shortcode [panorama_login] to output a Panorama login form anywhere
* BUG: Fixed issue where task edit links don't appear for project owners assigned via a team
* BUG: Automatically generate and save a comment key for phases if for some reason one doesn't exist

= 1.4.2.2 =
* COMPATIBILITY: Wrapped init routine in an action to prevent clashes with ACF

= 1.4.2.1 =
* FEATURE: Improved menu design, especially at responsive levels
* FEATURE: You can now link to /panorama/calendar/home to automatically pull up the current logged in users calendar
* FEATURE: Added task completion percentage to calendar tasks
* BUG: Fixed issue where tasks were not showing up on calendars

= 1.4.2 =
* BUG: Flushing output before download redirect, compatibility with other plugins.
* FEATURE: Added teams section in dashboard
* FEATURE: Added new sub navigation in dashboard
* FEATURE: Improved design of users assigned to projects
* FEATURE: Made templates more modular for less repeated code
* BUG: Fixed issue with encoded characters in document uploads
* FEATURE: Improved popups and issues with scrolling within pop-ups
* BUG: Fixed issue where you might get 100% completion on the day before deadline
* FEATURE: Added footer to design
* FEATURE: Added ability to replace e-mail variables using $post_id
* FEATURE: Phase colors are now an array so you can easily customize them or add more colors

= 1.4.1.4 =
* FEATURE: Added support for comments on custom templates
* FEATURE: Added support for multi column phases on custom templates
* FEATURE: Fixed issue with non visible tasks on dashboard pages
* FEATURE: Added 'target' attribute for [project_list] shortcode to allow links to open in new windows
* BUG: Fixed issue where some date formats prevented tasks from appearing in the calendar

= 1.4.1.3 =
* FEATURE: Added more dynamic notification variables
* BUG: Fixed some mobile styling bugs

= 1.4.1.2 =
* Fixed issue with white screen

= 1.4.1.1 =
* Made changes for PHP 5.2 backward compatibility
* Fixed some issues with the user notification e-mails

= 1.4.1 =
* Fix PHP warning on saving notifications.
* Added notification events: Add user to project, assigned task to user
* Added ability to specify all project users on a notification
* Added shortcode [psp-upcoming-tasks], shows a logged in users current open tasks sorted by upcoming due dates
* Added [psp_my_calendar] shortcode to output your own calendar
* Fixed issue where comments were getting linked via cloned projects

= 1.4
* Improved e-mail notification system. Set triggers and send e-mails when specific events occur like completing a task from the front end or when a project is completed.
* Updated permissions around Project Creator roles
* Editing elements are now always visible at responsive breakpoints
* Manual flush rewrites

= 1.3.6.3 =
* Menus can now be filtered by 'psp_get_nav_items'
* Custom menus can now have an icon by adding a fontawesome class to the link description
* Fixed bug with errors and user IDs in the user notification window
* Improved responsiveness of menu
* Improved responsiveness of charts, better support for phone rotating
* Dashboard page now paginates via ajax instead of querying all projects
* Added search to dashboard page and ability to filter by project type
* Fixed issues with translations in calendar
* Updated calendar to FullCalendar 2.9
* Added tasks and milestones to calendar

= 1.3.6.2 =
* Fixed issue with notices on restricted projects but no users attached
* Standardized psp-modal
* Totally remove milestones and comments if not in use
* Improved menu design, more compact

= 1.3.6.1 =
* Misc bug fixes

= 1.3.6 =
* New options system for easier integration with add-ons
* Fixes Jquery UI CSS conflict with Divi theme options page
* Changes milestone field format to vertical
* Fixed issues where PMs needed to add themselves to a project to edit
* Cloning a project now takes you into the edit post window
* Clicking the reply link in comments now opens a reply box below the current post
* If comment notifications are turned on all assigned users and the post author will get a notification
* New user level of "Project Creator" who can create and edit projects, but only see projects they've created or are assigned to
* Dashboard task lists can now have the client logo at the top
* Calendar now works with plain permalinks
* Improved performance on all projects view
* Prevents the use of [project_status] and [project_status_part] in Project Panorama WYSIWYG
* Added shortcodes [before-milestone] and [after-milestone] to conditionally display content in milestone descriptions based on if the milestone has been reached or not
* All timelines will appear red when project is behind
* Added dashboard link to menu
* Tasks and milestones now have due / completion dates

= 1.3.5.2 =
* Fixed issue with phases not getting cloned
* Dates will now be output based on WordPress options
* Fixes download issue when permalinks are not enabled

= 1.3.5.1 =
* Added localization support for calendar

= 1.3.5 =
* Added comments to phases (ajax driven!)
* Improved styling of login form
* Login form errors now redirect you back to the same page login form
* Improved styling of document update modal
* Added hooks and filters
* Added psp_enqueue_scripts hook and psp_register_script and psp_register_style to make adding assets easier
* Added download url obfuscation
* Updated charts.js library to most recent stable version
* Updated ACF library to most recent stable version
* Improved milestone styling
* Improved donut chart styling

= 1.3 =
* Added the ability to create teams and assign teams to projects
* Restructured the entire template hierarchy making it much easier to customize Panorama
* Added tons of hooks and filters, making it much easier to customize Panorama
* Improved styling on new milestones and phase sections
* Added check activation button to make license troubleshooting easier
* Improved the access management checks to allow easier integration with outside plugins and APIs
* Improved the styling of the [project_list] shortcode

= 1.2.7 =
* Added ability to add milestones in 5% increments, up to 20 milestones
* Added ability to use variables in messages
* Added option to disable the 'duplicate post' integration
* Added ability to update tasks from all projects dashboard
* Fixed bug where admins can't edit private projects
* Cleaned up front end task update ajax markup and code
* You can now create a menu in the WordPress admin and add it to the panorama single project or dashboard menu
* Scripts and styles can now be added to panorama template using hook psp_enqueue_scripts and functions psp_add_style() and psp_add_script()
* Added filters to field arrays so you can now dynamically add fields to projects, psp_milestone_fields, psp_overview_fields , psp_phase_fields
* Added filters and hooks to the tasks template filters: psp_task_class(), psp_task_assigned(), psp_task_name(). Hooks: psp_before_task(), psp_before_task_name(), psp_after_task()

= 1.2.6.5 =
* Added calendar feature that displays project start and end dates
* Improved dashboard layout
* Improved header design for dashboard / project pages
* Added mobile menu to project pages
* Improved compatibility with using theme template pages
* Added logo field for projects, allowing for uploading of client logos

= 1.2.6.4 =
* Added search box and pagination to document interface
* Subscribers can now update the status of tasks assigned to them
* Fixed issue with ampersands in phase titles
* Fixed issue with translating month names

= 1.2.6.3 =
* Fixed bug where user list doesn't show up if you have ACF5 plugin
* Switched document layout to vertical for better display
* Added options to customize accent colors (phases, timeline, etc...)

= 1.2.6.2 =
* Improved styling for elements in description areas
* Added shortcodes [before-phase] [during-phase] and [after-phase] which display before a phase starts, during an active phase and once a phase is completed
* Date format on backend is determined by user settings
* Added ability to sort by title using the [project_list] shortcode, attribute sort="title"

= 1.2.6.1 =
* Removed password protected projects outside of project lists unless admin

= 1.2.6 =

* Improved dashboard for better user experience
* Added the ability to assign tasks to users
* Added [task-list] shortcode to output tasks assigned to logged in users
* Fixed issue where translating document status is reset on front end update
* Fixed plugin activation notice
* Moved phase calculation settings to main overview to make easier to find
* Fixed slashes in HTML e-mails
* Added Active | Complete links in all projects listing in backend
* Misc bug fixes
* Dropped IE8 and previous support for better modern browser utilization

= 1.2.5.3 =

* Document update notification fixes
* Checked for dates before displaying, fixes notices if date isn't set
* Fixed JS issue in IE for frontend editing
* Switched last modified time to date on [project_list]
* Added pagination on project listing
* Mark project complete when done through the front end
* If there isn't a start or end date, hide the time elapsed bar
* [project_list] shortcode will now display a login form if access is set to user and user isn't logged in
* Fixed bug where special characters in task names would get garbled after updates (like &, etc...)
* Task names now support HTML
* Added logo and home link to project dashboard page

= 1.2.5.2 =
* Fixed bug where sometimes e-mails had a broken link
* Fixed bug where timing could be off when using an embed shortcode
* Added a simple project list / archive page for logging in and seeing your list of projects (i.e. /panorama/project-name the login would be /panorama/)
* Added better support for handling wide height ranges between project phases
* Improved the UI of the project heading area
* Added the ability to sort by start or end date with [project_list]
* Fixed bug where if you had a project password protected and restricted to users you couldn't update tasks from the front end

= 1.2.5.1 =
* Separated jQuery from frontend lib file
* Reworking of how and when admin scripts are enqueued for compatibility
* Added Advanced tab for debugging
* Switched dashboard widget chart to chart.js
* Renamed comments.php to psp-comments.php for compatibility
* Core fixes

= 1.2.5 =
* Added front end updating of tasks
* Added front end updating of documents
* Added notification system for document updates
* New project page interface
* Added time elapsed feature, tracks overall time elapsed compared to project completion
* Improved project listing interface on the backend
* Improved project listing shortcode display
* Split project templates into sub parts for easier customization
* Reworked file structure
* Misc bug fixes and improvements
* Split field loading into individual parts, function to check if field files exist in theme directory for customization
* BETA FEATURE: Load Panorama into your theme templates

= 1.2.2.2 =
* Only enqueue javascript files on pages that need them for compatibility
* Improved formatting of e-mail notifications on smaller screens
* Added password reset link to Panorama login
* Removed dashboard widget for users who are not editor level or higher
* Fixed issue where some users can't set a default e-mail / from name for notifications

= 1.2.2.1 =
* Fixed calculation bug with shortcodes
* Fixed weighting issue with previously completed projects
* Switched method of designated completed projects to custom taxonomy
* Fixed conflicts with ACF5 users and progress bars

= 1.2.2 =
* Added e-mail notifications
* Broke settings into three tabs
* Cleaned up admin interface
* Added ability to expand and collapse phases in admin (Thanks Mark Root-Wiley http://mrwweb.com/)
* Added graph to dashboard widget
* Reworked phase weighting, you can now specify hours instead of percentage
* Phases now have project specific settings rather than each individual phase
* Added setting to expand tasks by default
* Fixed unset variable PHP notice
* You can now specify number of projects to display in the [project_list] shortcode


= 1.2.1.8.2 =
* Added the ability to use your own template, simply create a folder called "panorama" in your theme directory and then copy /wp-content/plugins/panorama/lib/templates/single.php into it. You can then modify the file as you'd like
* Added project listing widget
* You can now use URLs for documents
* Added color customizations and an open css text box to the settings page
* Fixed bug with DISQUS plugins

= 1.2.1.8.1 =
* Minor bug fix

= 1.2.1.8 =
* Adjusted project_list shortcode to only display projects viewing user has access to, this can be overwritten by adding an access="all" attribute
* Added two user roles, 'Project Owner' and 'Project Manager' - More information here http://www.projectpanorama.com/docs/permissions
* Project editing in the admin is now restricted by the access control settings, i.e. authors/editors/project owners can only edit projects assigned to them (admins and project managers can edit all projects)
* Fixed issue where auto-calculation wouldn't work if you only had one task

= 1.2.1.6 =
* Added function to translate ACF fields

= 1.2.1.5 =
* Fixed output of "Fired" on plugin page
* Added [panorama_dashboard] shortcode
* Expanding and collapsing task lists
* Fixed issue where project list wouldn't output completed only projects
* Slightly redesigned interface

= 1.2.1.2 =
* Working translation and textdomain
* Added translations for French and Bulgarian - Thanks Gregory Further and Yassen Yotov!
* Move settings into the Project Panorama menu
* Added hooks into the template for future addons and easier styling adjustments
* Login form no longer trips security on WPEngine
* Fixed some misc bugs
* Adds dashboard widget

= 1.2.1 =
* Better translation and textdomain support
* Reworked shortcode system, now you can embed parts of projects, configure your project output and adjust what projects are listed
* Added "Project Type" custom taxonomy
* Added the ability to alter your project slug (from panorama to anything else)
* Added the ability to brand your projects
* Styling improvements and fixes
* Expanded WYSIWYG tools
* Support for WP 3.9

= 1.2 =
* Swapped out donut charts for Pizza Charts by Zurb (much nicer at all resoultions, better IE support)
* Added password protection
* Added user management / restrictions
* Check for duplicate post plugin before including
* Added option to noindex projects
* Minor styling tweaks
* Only load scripts and styles when a shortcode is used or on a project page

= 1.1.3 =
Small Bug Fixes - Added icons to new shortcode buttons

= 1.0 =
* Initial Release!
