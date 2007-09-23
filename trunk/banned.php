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
 */
/** 
 * "You are banned" page
 *
 * Users will be redirected to this page when they are banned from posting to or
 * viewing the boards.
 * 
 * @package kusaba  
 */ 

/** 
 * Require the configuration file, functions file, and bans class
 */ 
require 'config.php';
require KU_ROOTDIR . 'inc/functions.php';
require KU_ROOTDIR . 'inc/classes/bans.class.php';

$bans_class = new Bans();

$bans_class->RemoveExpiredBans();

if (isset($_POST['appealmessage']) && KU_APPEAL != '') {
	$results = $tc_db->GetAll("SELECT * FROM `".KU_DBPREFIX."banlist` WHERE `type` = '0' AND `ipmd5` = '" . md5($_SERVER['REMOTE_ADDR']) . "' LIMIT 1");
	if (count($results)>0) {
		foreach($results AS $line) {
			if ($line['appealat'] > 0 && $line['appealat'] < time()) {
				$emails = split(':', KU_APPEAL);
				$expires = ($line['until'] > 0) ? date("F j, Y, g:i a", $line['until']) : 'never';
				foreach ($emails as $email) {
					@mail($email, 'Ban appeal at ' . KU_NAME . ' for ' . $_SERVER['REMOTE_ADDR'], wordwrap(strip_tags($_POST['appealmessage'] . "\n" .
					'Banned for: ' . $line['reason'] . "\n" .
					'On: ' . date("F j, Y, g:i a", $line['at']) . "\n" .
					'Expires: ' . $expires . "\n" .
					'By: ' . $line['by'] . "\n" .
					'Unban: ' . KU_CGIPATH . '/manage_page.php?action=bans&delban=' . $line['id']), 70), 'From: ' . KU_NAME . "\r\n");
				}
					
				$tc_db->Execute("UPDATE `".KU_DBPREFIX."banlist` SET `appealat` = '-1' WHERE `ipmd5` = '" . md5($_SERVER['REMOTE_ADDR']) . "'");
				
				echo 'Your appeal has been sent and is pending review.';
			} else {
				echo 'You may not appeal that ban at this time.';
			}
			
			die();
		}
	}
}

$bans_class->BanCheck($_SERVER['REMOTE_ADDR'], '', true);

?>