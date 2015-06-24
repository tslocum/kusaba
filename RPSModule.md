# About #

Created by jpeg.  #rock #paper or #scissor in email field.


# rockpaperscissor.php #

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
 * Rock, Paper, Scissor (created by jpeg for Crisschan, but you can use it anyway)
 * Crisschan.ath.cx, produit du Québec.
 * +------------------------------------------------------------------------------+
 * So, you think you have nice rock paper scissor skills? Let's give it a try
 * +------------------------------------------------------------------------------+
 */

/* Module initialization */
function rockpaperscissor_init() {
	global $hooks;
	
	$hooks['posting'][] = 'rockpaperscissor';
}

/* Is this module authorized to be used right now? */
function rockpaperscissor_authorized($board) {
	return true;
}

function rockpaperscissor_info() {
	$info = array();
	$info['type']['board-specific'] = false;
	
	return $info;
}

function rockpaperscissor_settings() {
	$settings = array();
	
}

function rockpaperscissor_help() {
	$output = 'Rock Paper scissor:  #rock, #paper or #scissor goes in email field.';
	
	return $output;
}

function rockpaperscissor_process_posting($post) {
	global $bans_class;
	/* EDIT HERE */
	$triggers = array('#rock', '#paper', '#scissor');
	$choices = array('rock', 'paper', 'scissor');
	$banseconds = 30;
	$banmessage = 'Luck was not with you.';
	/* End editing */
	
	if (in_array(strtolower($post['email']), $triggers)) {
		$boardSel = $choices[array_rand($choices)];
		$userSel = trim(strtolower($post['email']), "#");
		$newMsg = 'Me: ' . $boardSel . '<br />You: ' . $userSel . '<br />';
		switch($userSel) {
			case 'rock':
				$win = ($boardSel == "scissor") ? true : false; //The rock wins over the scissor
				break;
			case 'paper':
				$win = ($boardSel == "rock") ? true : false; //The paper wins over the rock
				break;
			case 'scissor':
				$win = ($boardSel == "paper") ? true : false; //The scissor wins over the paper
				break;
		}
		
		if($win != true){
			if($userSel == $boardSel) {
				$newMsg .= '<span style="color: red; background-color: black;">Draw!</span><br /><br />';
			}else{
				$newMsg .= '<span style="color: red; background-color: black;">You lost, B&!</span><br /><br />';
				$bans_class->BanUser($_SERVER['REMOTE_ADDR'], 'SERVER', 1, $banseconds, '', $banmessage, 0, 0);
			}
		}else{
			$newMsg .= '<span style="color: red; background-color: black;">A winner is you!</span><br /><br />';
		}
		
		$post['message'] = $newMsg . $post['message'];

		$post['email'] = '';
		$post['email_save'] = false;
	}

	return $post;
}

function rockpaperscissor__get_rockpaperscissor() {
	$rockpaperscissors = explode('|', module_setting_get('rockpaperscissor', 'rockpaperscissor'));
	$rockpaperscissor_index = rand(0, (count($rockpaperscissors) - 1));
	$rockpaperscissor = $rockpaperscissors[$rockpaperscissor_index];
	
	return $rockpaperscissor;
}

?>
```