# Credit #

This script was created by meltingwax.

# Instructions #

Edit with your own random seed (KU\_RANDOMSEED in config.php)

Upload to the same directory as the .htaccess containing the bans.

Run from your browser and delete the file.

# b2k.php #

```
<?php

// allow these banned users to read the boards (will only apply to bans being imported right now)
$allow_read = 0; 

// random seed value found in your config.php
$random_seed = "Find this in your kusaba's config.php";

// --------------------------------------

// beginning of sql stuff
echo "INSERT INTO `banlist`<br>";
echo "(`allowread`, `ip`, `ipmd5`, `globalban`, `by`, `reason`)<br>";
echo "VALUES<br>";


// loop to print sql rows
$bans = read_bans();
$num_bans = count($bans);

$i = 0;

foreach ( $bans as $ip => $reason ) {
	print "($allow_read, '" . md5_encrypt($ip, $random_seed) . "', '" . md5($ip) . "', 1, 'WAKABA/KAREHA BAN IMPORTER', '$reason')";
	
	if ( $i < $num_bans - 1 )
		print ",<br>\n";
	else
		print "<br>\n";
	
	$i++;
}


// read bans and store it like ip => reason in assosciative array
function read_bans() {
	$bans;
	$ip;
	$reason;
	
	$lines = file('.htaccess');

	foreach ( $lines as $line_num => $line ) {		
		if ( preg_match("/^Deny from (.*)/", $line, $matches) ) {
			$ip = $matches[1];
			$bans[$ip] = $reason;
		}
		elseif ( preg_match("/^# Banned at (.*?) \((.*)/ ", $line, $matches) ) {
			$reason = $matches[2];
			$reason = substr($reason, 0, strlen($reason) - 1);
			$reason = filter($reason);
		}
	}
	
	return $bans;
}

// filter text for the reason so it doesn't ruin sql
function filter($text) {
	$text = str_replace("'", "\'", $text);
	$text = str_replace("\\", "\\\\", $text);

	return $text;
}


/*
 * Stuff from kusaba found in inc/func/encryption.php
 */
function get_rnd_iv($iv_len) {
	$iv = '';
	while ($iv_len-- > 0) {
		$iv .= chr(mt_rand() & 0xff);
	}
	return $iv;
}

function md5_encrypt($plain_text, $password, $iv_len = 16) {
	$plain_text .= "\x13";
	$n = strlen($plain_text);
	if ($n % 16) $plain_text .= str_repeat("\0", 16 - ($n % 16));
	$i = 0;
	$enc_text = get_rnd_iv($iv_len);
	$iv = substr($password ^ $enc_text, 0, 512);
	while ($i < $n) {
		$block = substr($plain_text, $i, 16) ^ pack('H*', md5($iv));
		$enc_text .= $block;
		$iv = substr($block . $iv, 0, 512) ^ $password;
		$i += 16;
	}
	return base64_encode($enc_text);
}

?>
```