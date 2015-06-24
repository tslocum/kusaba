**Note:  This guide is not yet finished**

# Managing Staff #

  1. Navigate to the Staff section of manage.php
  1. To add a new staff member, use the forms at the top of the page
    1. Username is the name that the staff member will log in with
    1. Password is the password that the staff member will have
    1. Type is the type of staff member they will be:
      * Administrator means they will have full access to every function on the manage page.
      * Moderator is a step down from administrator. Moderators can delete posts and ban IPs and md5 hashes, as well as sticky or lock threads, and modpost.
      * Janitors are a step down from mods. They can delete posts but not ban users.
    1. Once you have added a staff member, you should probably edit them to allow them to moderate boards.
  1. To edit a staff member, click the 'edit' link on the same row as their name in the staff list.
  1. To delete a staff member, click the 'x' link on the same row as their name in the staff list.

# Managing Boards #

  1. To add a board, navigate to the Add Board section of manage.php
    1. Directory is the place that the board will be inside of your kusaba root directory, ie: for "/b/" you would put "b" (without quotes). Do not put slashes or other non-alphanumeric characters in this field
    1. Description is the name of the board, ie: For /b/ you would put "Random" (without quotes).
    1. First post ID is the number that the first post will have.
  1. To delete a board, navigate to the Delete Board section of manage.php.
    1. Select the Directory of the board you want to delete from the drop-down box
    1. Click the delete button.
  1. To edit the options on a particular board, navigate to the Board Options section of manage.php
    1. Select which board you plan to edit from the drop-down box and click the Go button.
    1. After changing options, select the Update button at the bottom of the page

# News #

  1. Navigate to the News section of manage.php
  1. To add a news post, fill out the forms at the top of the page (making sure to add proper html) and click "add"
  1. To edit or delete an existing news post, click the relevant link in the Edit/Delete column in the news list

# Bans and Deletions #

  1. Log in to manage.php
  1. Now, when browsing boards (that you moderate), you will see D & B links next to each post (If you have javascript enabled). Clicking the D will delete the post, while clicking the B will bring you to the ban page to ban the poster. Clicking the & will delete the post and bring you to the ban page to ban the poster.
  1. You can also navigate to the Delete Thread/Post section of manage.php and type in the thread or post ID there.
  1. On the ban page, there are many options:
    1. IP: Put the IP or IP range here.
    1. Allow Read: Whether or not to allow the poster to continue reading the boards while banned. (Your httpd must support .htaccess for no-read bans)
    1. Type: Whether the ban is a single IP or an IP range ban
    1. Boards: Which boards to ban the poster from
    1. Seconds: How many seconds to ban the poster for. You can use the quick links next to the field to quickly fill in preset amounts of time (You must have Javascript enabled).
    1. Reason: The reason you are banning the poster. HTML is not escaped.
    1. Add Ban: Click this when you are done filling out the fields
  1. Also on the ban page is the ban list:
    1. The first 15 bans of each type are always displayed. To display all the bans, select "Display all bans" from the bottom of the list.
    1. To delete a ban, Select the x next to it.
  1. Another way to delete and ban posts, particularly useful in a flood situation is MultiDelBan™
    1. Navigate to a board. (be sure you are logged in)
    1. Select the Checkboxes next to the posts you wish to delete (or delete and ban)
    1. Scroll to the bottom of the page
    1. If you wish to ban the users that posted the selected posts, select the ban checkbox and fill out a reason. Be warned, these will be permanent bans from all boards.
    1. Click the MultiDelBan button to delete/ban all posts/users.

# Stickies and Locked Threads #

Stickies are threads which remain at the top of the first page of a board automatically. They will not expire or be bumped off the page unless a moderator or administrator deletes them. To sticky a thread, you can either modpost in it with the right flags, or you can navigate to the manage stickies section of manage.php:

  1. Navigate to the Manage Stickies section of manage.php.
  1. Select which board you want to sticky the thread from with the drop-down box.
  1. Type in the Thread ID that you want to sticky.
  1. Click Sticky

Locked threads are threads which cannot be replied to except by staff members. To lock a thread you can either modpost in it with the correct flags, or you can navigate to the Manage Locked Threads section of manage.php

  1. To manage locked threads, repeat the above but navigate instead to the Locked Threads section.

# Wordfilters #

Wordfilters are things which filter the messages which are posted on your boards. For instance, you could wordfilter "moo" to "baa", and every instance of moo in posters's messages would be changed to baa.

  1. To manage wordfilters, navigate to the Wordfilter section of manage.php
    1. To add a wordfilter, type the word you want replaced in the Word field
    1. Type the word you want it to be replaced with in the Replaced By field
    1. Select which boards you want this wordfilter to filter messages on.
      * The "Is Regex" Checkbox is an advanced feature to allow for highly customized wordfilters
        * It uses the preg\_replace() function of php, so check php.net for proper syntax
          * Using Regex wordfilters can be dangerous, and may render it impossible to post on your site. Be careful when activating these filters
      * To delete or edit a wordfilter, select the "edit" or "delete" links in the list of wordfilters.

# Misc. Administrative Tools #

  1. Posting Rates
    * This will show you the number of threads, replies, and total posts made by your users on each of your boards in the past hour.
  1. Check for new version
    * This will show your version of kusaba, as well as the most recent version of kusaba available on www.kusaba.org.
  1. Disk space used
    * Currently Broken
  1. View Deleted Thread
    * Currently Broken
  1. Cleanup
    * Deletes images in board directories which are not used
  1. Search Posts
    * Currently Broken
  1. Modlog
    * Allows you to view previous actions done by staff.
  1. Edit Filetypes
    * Allows you to add nonstandard filetypes for users to upload to imageboards.
  1. Edit Sections
    * Allows you to edit the sections used for menu.php (the navigation menu)
  1. SQL Query
    * Allows you to execute a mysql query from the kusaba manage page
  1. Ban proxy list
    * Allows you to upload a list of proxies and automatically bans them
  1. Rebuild all HTML files
    * Rebuilds the cached html piles
    * Useful to use after changing board options or upgrading kusaba to refresh boards
  1. APC
    * Allows you to view the Alternative PHP Cache statistics
  1. Module settings
    * Allows you to change the settings of individual module settings