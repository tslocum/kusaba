<?php
/*
 * This file is part of kusaba.
 *
 * kusaba is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * kusaba is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * kusaba; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * +------------------------------------------------------------------------------+
 * kusaba - http://www.kusaba.org/
 * Written by Trevor "tj9991" Slocum
 * http://www.tj9991.com/
 * tslocum@gmail.com
 * +------------------------------------------------------------------------------+
 */
/** 
 * Board operations which available to all users
 *
 * This file serves the purpose of providing functionality for all users of the
 * boards.  This includes: posting, reporting posts, and deleting posts.
 * 
 * @package kusaba  
 */

/** 
 * Start the session
 */ 
session_start();

/** 
 * Require the configuration file, functions file, board and post class, bans class, and posting class
 */ 
require 'config.php';
require KU_ROOTDIR . 'inc/functions.php';
require KU_ROOTDIR . 'inc/classes/board-post.class.php';
require KU_ROOTDIR . 'inc/classes/bans.class.php';
require KU_ROOTDIR . 'inc/classes/posting.class.php';
require KU_ROOTDIR . 'inc/classes/parse.class.php';
		
$bans_class = new Bans();
$parse_class = new Parse();
$posting_class = new Posting();

// {{{ Module loading

modules_load_all();

// }}}
// {{{ Fake email field check

if (isset($_POST['email'])) {
	if ($_POST['email']!= '') {
		exitWithErrorPage('Spam bot detected');
	}
}

// }}}
// {{{ GET/POST board send check

/* In some cases, the board value is sent through post, others get */
$_POST['board'] = (isset($_GET['board'])) ? $_GET['board'] : $_POST['board'];

// }}}

/* Check to see the board supplied exists */
$posting_class->ValidateBoard();

// {{{ Expired ban removal, and then existing ban check on the current user

$bans_class->RemoveExpiredBans();
$bans_class->BanCheck($_SERVER['REMOTE_ADDR'], $board_class->board_dir);

// }}}

$oekaki = $posting_class->CheckOekaki();
if ($oekaki == '') {
	$is_oekaki = false;
} else {
	$is_oekaki = true;
}

/* Ensure that UTF-8 is used on some of the post variables */
$posting_class->UTF8Strings();

/* Check if the user sent a valid post (image for thread, image/message for reply, etc) */
if ($posting_class->CheckValidPost($is_oekaki)) {
	$db->Execute('START TRANSACTION');
	
	$posting_class->CheckReplyTime();
	$posting_class->CheckNewThreadTime();
	$posting_class->CheckMessageLength();
	$posting_class->CheckCaptcha();
	$posting_class->CheckBannedHash();
	$posting_class->CheckBlacklistedText();
	$posting_class->CheckFormatting();
	$posting_class->CheckAuthority();
	$posting_class->SetPostTag();
	
	$post_isreply = $posting_class->CheckIsReply();
	$imagefile_name = isset($_FILES['imagefile']) ? $_FILES['imagefile']['name'] : '';
	
	$posting_class->thread_replies = 0;
	$posting_class->thread_locked  = 0;
	$posting_class->thread_replyto = 0;
	if ($post_isreply) {
		$posting_class->SetThreadInfo($_POST['replythread']);
	} else {
		if ($board_class->board_type != 1 && ($board_class->board_uploadtype == '1' || $board_class->board_uploadtype == '2')) {
			if (isset($_POST['embed'])) {
				if ($_POST['embed'] == '') {
					if (($board_class->board_uploadtype == '1' && $imagefile_name == '') || $board_class->board_uploadtype == '2') {
						exitWithErrorPage('Please enter an embed ID.');
					}
				}
			} else {
				exitWithErrorPage('Please enter an embed ID.');
			}
		}
	}
	
	list($post_name, $post_email, $post_subject) = $posting_class->GetFields();
	$post_password = isset($_POST['postpassword']) ? $_POST['postpassword'] : '';
	
	if ($board_class->board_type_readable == 'text') {
		if ($post_isreply) {
			$post_subject = '';
		} else {
			$posting_class->CheckNotDuplicateSubject($post_subject);
		}
	}
	
	$parse_class->id = $posting_class->GetNextID($board_class->board_dir);
	
	$post_displaystaffstatus = false;
	$file_is_special         = false;
	
	/* If they are just a normal user, or vip... */
	if (isNormalUser($posting_class->authority)) {
		/* If the thread is locked */
		if ($posting_class->thread_locked == 1) {
			/* Don't let the user post */
			exitWithErrorPage(_gettext('Sorry, this thread is locked and can not be replied to.'));
		}
		
		$post_message = $parse_class->ParsePost($_POST['message'], $board_class->board_dir, $board_class->board_type, $posting_class->thread_replyto);
	/* Or, if they are a moderator/administrator... */
	} else {
		/* If they checked the D checkbox, set the variable to tell the script to display their staff status (Admin/Mod) on the post during insertion */
		if (isset($_POST['displaystaffstatus'])) {
			$post_displaystaffstatus = true;
		}
		
		/* If they checked the RH checkbox, set the variable to tell the script to insert the post as-is... */
		if (isset($_POST['rawhtml'])) {
			$post_message = $_POST['message'];
		/* Otherwise, parse it as usual... */
		} else {
			$post_message = $parse_class->ParsePost($_POST['message'], $board_class->board_dir, $board_class->board_type, $posting_class->thread_replyto);
		}
		
		/* If they checked the L checkbox, set the variable to tell the script to lock the post after insertion */
		if (isset($_POST['lockonpost'])) {
			$posting_class->post_autolock = true;
		}
		
		/* If they checked the S checkbox, set the variable to tell the script to sticky the post after insertion */
		if (isset($_POST['stickyonpost'])) {
			$posting_class->post_autosticky = true;
		}
		if (isset($_POST['usestaffname'])) {
			$_POST['name'] = md5_decrypt($_POST['modpassword'], KU_RANDOMSEED);
			$post_name = md5_decrypt($_POST['modpassword'], KU_RANDOMSEED);
		}
	}
	
	$posting_class->CheckBadUnicode($post_name, $post_email, $post_subject, $post_message);

	if ($post_isreply) {
		if ($imagefile_name == '' && !$is_oekaki && $post_message == '') {
			exitWithErrorPage(_gettext('An image, or message, is required for a reply.'));
		}
	} else {
		if ($imagefile_name == '' && !$is_oekaki && ((!isset($_POST['nofile'])&&$board_class->board_enablenofile==1) || $board_class->board_enablenofile==0) && ($board_class->board_type == 0 || $board_class->board_type == 2 || $board_class->board_type == 3)) {
			if (!isset($_POST['embed']) && $board_class->board_uploadtype != 1) {
				exitWithErrorPage(_gettext('A file is required for a new thread.  If embedding is allowed, either a file or embed ID is required.'));
			}
		}
	}
	
	if (isset($_POST['nofile'])&&$board_class->board_enablenofile==1) {
		if ($post_message == '') {
			exitWithErrorPage('A message is required to post without a file.');
		}
	}
	
	if ($board_class->board_type == 1 && !$post_isreply && $post_subject == '') {
		exitWithErrorPage('A subject is required to make a new thread.');
	}
	
	if ($board_class->board_locked == 0 || ($posting_class->authority > 0 && $posting_class->authority != 3)) {
		require_once KU_ROOTDIR . 'inc/classes/upload.class.php';
		$upload_class = new Upload();
		if ($post_isreply) {
			$upload_class->isreply = true;
		}

		if ((!isset($_POST['nofile']) && $board_class->board_enablenofile == 1) || $board_class->board_enablenofile == 0) {
			$upload_class->HandleUpload();
		}
		
		if ($board_class->board_forcedanon == '1') {
			if ($posting_class->authority == 0 || $posting_class->authority == 3) {
				$post_name = '';
			}
		}
		
		$nameandtripcode = calculateNameAndTripcode($post_name);
		if (is_array($nameandtripcode)) {
			$name = $nameandtripcode[0];
			$tripcode = $nameandtripcode[1];
		} else {
			$name = $post_name;
			$tripcode = '';
		}
		
		$filetype_withoutdot = substr($upload_class->file_type, 1);
		$post_passwordmd5 = ($post_password == '') ? '' : md5($post_password);
		$posting_class->CheckAutoSticky();
		$posting_class->CheckAutoLock();
		
		if (!$post_displaystaffstatus && $posting_class->authority > 0 && $posting_class->authority != 3) {
			$posting_class->authority_display = 0;
		} elseif ($posting_class->authority > 0) {
			$posting_class->authority_display = $posting_class->authority;
		} else {
			$posting_class->authority_display = 0;
		}
		
		$post = array();
		
		/* First array is the converted form of the japanese characters meaning sage, second meaning age */
		$ords_email = unistr_to_ords($post_email);
		if (strtolower($_POST['em']) != 'sage' && $ords_email != array(19979, 12370) && strtolower($_POST['em']) != 'age' && $ords_email != array(19978, 12370) && $_POST['em'] != 'return' && $_POST['em'] != 'noko') {
			$post['email_save'] = true;
		} else {
			$post['email_save'] = false;
		}
		
		$post['board']             = $board_class->board_dir;
		$post['name']              = substr($name, 0, 100);
		$post['name_save']         = true;
		$post['tripcode']          = $tripcode;
		$post['email']             = substr($post_email, 0, 100);
		$post['subject']           = substr($post_subject, 0, 100);
		$post['message']           = $post_message;
		$post['filename']          = $upload_class->file_name;
		$post['filename_original'] = $upload_class->original_file_name;
		$post['filetype']          = $filetype_withoutdot;
		$post['filemd5']           = $upload_class->file_md5;
		$post['image_w']           = $upload_class->imgWidth;
		$post['image_h']           = $upload_class->imgHeight;
		$post['filesize']          = $upload_class->file_size;
		$post['thumb_w']           = $upload_class->imgWidth_thumb;
		$post['thumb_h']           = $upload_class->imgHeight_thumb;
		$post['password']          = $post_passwordmd5;
		$post['ip']                = $_SERVER['REMOTE_ADDR'];
		$post['posterauthority']   = $posting_class->authority_display;
		$post['tag']               = $posting_class->post_tag;
		$post['stickied']          = $posting_class->post_autosticky;
		$post['locked']            = $posting_class->post_autolock;
		
		$post = hook_process('posting', $post);
		
		if ($is_oekaki) {
			if (file_exists(KU_BOARDSDIR . $board_class->board_dir . '/src/' . $upload_class->file_name . '.pch')) {
				$post['message'] .= '<br><small><a href="' . KU_CGIPATH . '/animation.php?board=' . $board_class->board_dir . '&id=' . $upload_class->file_name . '">' . _gettext('View animation') . '</a></small>';
			}
		}
		
		if ($posting_class->thread_replyto != '0') {
			if ($post['message'] == '' && KU_NOMESSAGEREPLY != '') {
				$post['message'] = KU_NOMESSAGEREPLY;
			}
		} else {
			if ($post['message'] == '' && KU_NOMESSAGETHREAD != '') {
				$post['message'] = KU_NOMESSAGETHREAD;
			}
		}
		
		$post_class = new Post(0, $board_class->board_dir, true);
		$post_id = $post_class->Insert($post);
		
		if ($posting_class->authority > 0 && $posting_class->authority != 3) {
			$modpost_message = 'Modposted #<a href="' . KU_BOARDSFOLDER . $board_class->board_dir . '/res/';
			if ($post_isreply) {
				$modpost_message .= $posting_class->thread_replyto;
			} else {
				$modpost_message .= $post_id;
			}
			$modpost_message .= '.html#' . $post_id . '">' . $post_id . '</a> in /' . $_POST['board'] . '/ with flags: ' . $posting_class->post_flags . '.';
			management_addlogentry($modpost_message, 1, md5_decrypt($_POST['modpassword'], KU_RANDOMSEED));
		}
		
		if ($post['name_save'] && isset($_POST['name'])) {
			setcookie('name', urldecode($_POST['name']), time() + 31556926, '/', KU_DOMAIN);
		}
		
		if ($post['email_save']) {
			setcookie('email', urldecode($post['email']), time() + 31556926, '/', KU_DOMAIN);
		}
		
		setcookie('postpassword', urldecode($_POST['postpassword']), time() + 31556926, '/');
		
		/* If the user replied to a thread, and they weren't sage-ing it... */
		if ($posting_class->thread_replyto != '0' && strtolower($_POST['em']) != 'sage' && unistr_to_ords($_POST['em']) != array(19979, 12370)) {
			/* And if the number of replies already in the thread are less than the maximum thread replies before perma-sage... */
			if ($posting_class->thread_replies <= $board_class->board_maxreplies) {
				/* Bump the thread */
				$db->Execute('UPDATE `' . KU_DBPREFIX . 'posts_' . $board_class->board_dir . '` SET `lastbumped` = ' . time() . ' WHERE `id` = ' . $posting_class->thread_replyto);
			}
		}
		
		/* If the user replied to a thread he is watching, update it so it doesn't count his reply as unread */
		if (KU_WATCHTHREADS && $posting_class->thread_replyto != '0') {
			$viewing_thread_is_watched = $db->GetOne("SELECT COUNT(*) FROM `" . KU_DBPREFIX . "watchedthreads` WHERE `ip` = '" . $_SERVER['REMOTE_ADDR'] . "' AND `board` = '" . $board_class->board_dir . "' AND `threadid` = '" . $posting_class->thread_replyto . "'");
			if ($viewing_thread_is_watched > 0) {
				$newestreplyid = $db->GetOne('SELECT `id` FROM `' . KU_DBPREFIX . 'posts_' . $board_class->board_dir . '` WHERE `IS_DELETED` = 0 AND `parentid` = ' . $posting_class->thread_replyto . ' ORDER BY `id` DESC LIMIT 1');
				
				$db->Execute('UPDATE `' . KU_DBPREFIX . 'watchedthreads` SET `lastsawreplyid` = ' . $newestreplyid . ' WHERE `ip` = \'' . $_SERVER['REMOTE_ADDR'] . '\' AND `board` = \'' . $board_class->board_dir . '\' AND `threadid` = ' . $posting_class->thread_replyto);
			}
		}
		
		$db->Execute('COMMIT');
		
		/* Trim any threads which have been pushed past the limit, or exceed the maximum age limit */
		$board_class->TrimToPageLimit();
		
		/* Regenerate board pages */
		$board_class->RegeneratePages();
		if ($posting_class->thread_replyto == '0') {
			/* Regenerate the thread */
			$board_class->RegenerateThread($post_id);
		} else {
			/* Regenerate the thread */
			$board_class->RegenerateThread($posting_class->thread_replyto);
		}
	} else {
		exitWithErrorPage(_gettext('Sorry, this board is locked and can not be posted in.'));
	}
} elseif (isset($_POST['delete']) || isset($_POST['reportpost'])) {
	/* Initialize the post class */
	$post_class = new Post(mysql_real_escape_string($_POST['delete']), $board_class->board_dir);
	
	if (isset($_POST['reportpost'])) {
		/* They clicked the Report button */
		if ($board_class->board_enablereporting == 1) {
			$post_reported = $post_class->post_isreported;
			
			if ($post_reported === 'cleared') {
				echo _gettext('That post has been cleared as not requiring any deletion.');
			} elseif ($post_reported) {
				echo _gettext('That post is already in the report list.');
			} else {
				if ($post_class->Report()) {
					echo _gettext('Post successfully reported.');
				} else {
					echo _gettext('Unable to report post.  Please go back and try again.');
				}
			}
		} else {
			echo _gettext('This board does not allow post reporting.');
		}
	} elseif (isset($_POST['postpassword'])) {
		/* They clicked the Delete button */
		
		if ($_POST['postpassword'] != '') {
			if (md5($_POST['postpassword']) == $post_class->post_password) {
				if (isset($_POST['fileonly'])) {
					if ($post_class->post_filename != '' && $post_class->post_filename != 'removed') {
						$post_class->DeleteFile();
						$board_class->RegeneratePages();
						if ($post_class->post_parentid != 0) {
							$board_class->RegenerateThread($post_class->post_parentid);
						}
						echo _gettext('File successfully deleted from your post.');
					} else {
						echo _gettext('Your post already doesn\'t have a file attached to it!');
					}
				} else {
					if ($post_class->Delete()) {
						if ($post_class->post_parentid != '0') {
							$board_class->RegenerateThread($post_class->post_parentid);
						}
						$board_class->RegeneratePages();
						echo _gettext('Post successfully deleted.');
					} else {
						echo _gettext('There was an error in trying to delete your post');
					}
				}
			} else {
				echo _gettext('Incorrect password.');
			}
		} else {
			do_redirect(KU_BOARDSPATH . '/' . $board_class->board_dir . '/');
		}
	}
	
	die();
} elseif (isset($_GET['postoek'])) {
	$board_class->OekakiHeader($_GET['replyto'], $_GET['postoek']);
	
	die();
} else {
	do_redirect(KU_BOARDSPATH . '/' . $board_class->board_dir . '/');
}

if (KU_RSS) {
	require_once KU_ROOTDIR . 'inc/classes/rss.class.php';
	$rss_class = new RSS();
	
	print_page(KU_BOARDSDIR.$_POST['board'].'/rss.xml',$rss_class->GenerateRSS($_POST['board']),$_POST['board']);
}

if ($board_class->board_redirecttothread == 1 || $_POST['em'] == 'return' || $_POST['em'] == 'noko') {
	if ($posting_class->thread_replyto == '0') {
		do_redirect(KU_BOARDSPATH . '/' . $board_class->board_dir . '/res/' . $post_id . '.html', true, $imagefile_name);
	} else {
		do_redirect(KU_BOARDSPATH . '/' . $board_class->board_dir . '/res/' . $posting_class->thread_replyto . '.html', true, $imagefile_name);
	}
} else {
	do_redirect(KU_BOARDSPATH . '/' . $board_class->board_dir . '/', true, $imagefile_name);
}
?>