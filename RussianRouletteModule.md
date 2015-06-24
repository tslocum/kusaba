# Usage #
By default, enter #soviet or #ussr in the email field, however this can be changed

# Customization #
You can change the ban time, triggers, and result messages.  This module is able to be used globally, and is not board specific

# russianroulette.php #
```
<?php
/*
 * This file is part of Trevorchan.
 *
 * Trevorchan is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Trevorchan is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Trevorchan; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * +------------------------------------------------------------------------------+
 * Russian Roulette (created by tj9991)
 * +------------------------------------------------------------------------------+
 * Let your posters try their luck.
 * +------------------------------------------------------------------------------+
 */

/* Module initialization */
function russianroulette_init() {
	global $hooks;
	
	$hooks['posting'][] = 'russianroulette';
}

/* Is this module authorized to be used right now? */
function russianroulette_authorized($board) {
	return true;
}

function russianroulette_info() {
	$info = array();
	$info['type']['board-specific'] = false;
	
	return $info;
}

function russianroulette_settings() {
	$settings = array();
	
}

function russianroulette_help() {
	$output = 'russianroulette:  No help available.';
	
	return $output;
}

function russianroulette_process_posting($post) {
	global $bans_class;
	/* EDIT HERE */
	$triggers = array('#soviet', '#ussr');
	$banseconds = 30;
	$message1 = '<em>*click*</em>';
	$message2 = 'BANG! <font color="red"><b>(USER SHOT HIMSELF FOR THIS POST)</b></font>';
	$banmessage = 'Luck was not with you.';
	/* End editing */
	
	if (in_array(strtolower($post['email']), $triggers)) {
		$rand = rand(1,6);
		if ($rand != 6) {
			$post['message'] = '<span style="color: red; background-color: black;">RR</span>: ' . $message1 . '<br><br>' . $post['message'];
		} else {
			$post['message'] = '<span style="color: red; background-color: black;">RR</span>: ' . $message2 . '<br><br>' . $post['message'];
			$bans_class->BanUser($_SERVER['REMOTE_ADDR'], 'SERVER', 1, $banseconds, '', $banmessage, 0, 0);
		}

		$post['email'] = '';
		$post['email_save'] = false;
	}

	return $post;
}

function russianroulette__get_russianroulette() {
	$russianroulettes = explode('|', module_setting_get('russianroulette', 'russianroulettes'));
	$russianroulette_index = rand(0, (count($russianroulettes) - 1));
	$russianroulette = $russianroulettes[$russianroulette_index];
	
	return $russianroulette;
}

?>
```