# Don't use kusaba, use TinyIB #
**kusaba is outdated and unsupported.  Visit [https://github.com/tslocum/TinyIB](https://github.com/tslocum/TinyIB) to learn about TinyIB, a better imageboard software.**




# kusaba Requirements #
To run kusaba, you should have a webserver with at least the following:

  * An apache or apache2 installation
  * mod\_php version 4 or better, with GD and MBStrings support
  * A mysql server with the MyISAM engine

# Extracting kusaba #

  1. Download the most recent kusaba release from www.kusaba.org
  1. Extract it to a directory that your apache installation has access to
  1. Make sure the permissions for everything are readable and writable by your web server
  1. Edit config.php

# Editing config.php #

To enable a feature, change the value to true:
> define('TC\_INSTANTREDIRECT', true);
To disable a feature, change the value to false:
> define('TC\_INSTANTREDIRECT', false);

To change the text value of a configuration, edit the text in the single quotes:
> define('TC\_NAME', 'kusaba');
Becomes:
> define('TC\_NAME', 'Mychan');
Warning: Do not insert single quotes in the value yourself, or else you will cause problems. To overcome this, you use what is called escaping, which is the process of adding a backslash before the single quote, to show it is part of the string:
> define('TC\_NAME', 'Jason\'s chan');

The postbox is where you mix dynamic values with your own text. The text from what you enter is then parsed and will be displayed under the postbox on each board page and thread page:
> define('TC\_POSTBOX', '<ul><li>Supported file types are: <!tc_filetypes /></li><li>Maximum file size allowed is <!tc_maximagekb /> KB.</li><li>Images greater than <!tc_maxthumbwidth />x<!tc_maxthumbheight /> pixels will be thumbnailed.</li><li>Currently <!tc_uniqueposts /> unique user posts.</li></ul>');
Will become (if you had my settings):
  * Supported file types are: GIF, JPG, PNG
  * Maximum file size allowed is 1000 KB.
  * Images greater than 200x200 pixels will be thumbnailed.
  * Currently 221 unique user posts.

Possible values you may use:
  * <!tc\_filetypes />
  * <!tc\_maximagekb />
  * <!tc\_maxthumbwidth />
  * <!tc\_maxthumbheight />
  * <!tc\_uniqueposts />

# Setting kusaba Up #

  1. Move the files install.php, install-mysql.php, and kusaba\_freshinstall.sql from .OTHER to your kusaba root directory
  1. Visit http://yoursite/yourkusabadir/install-mysql.php
  1. Visit http://yoursite/yourkusabadir/install.php
  1. Log into http://yoursite/yourkusabadir/manage.php and make a new administrator account. From your new administrator account, delete the old administrator account.
  1. Remove .OTHER, install.php, install-mysql.php, and kusaba\_freshinstall.sql from your kusaba root directory