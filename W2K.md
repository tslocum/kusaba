# Instructions #

Each run of the script will work for only one board; you then need to move the script to another board and reconfigure it with the new appropriate board name.

You will need to have already installed kusaba to the directory holding all of the board directories to finish the process.

Edit w2k.php with your own information, but do not run it yet.

Rename the directory for the board you are currently converting to something different.  This is so the board addition to kusaba is allowed, as it will fail if it is unable to make the board directory.

Add the board in the manage panel.  It will create a blank board.

Delete the board's folder which kusaba created, and rename your old folder back to the right name.

Upload w2k.php to the board folder and run it.

If everything goes well, you will be presented with a sql query which you will run in phpMyAdmin, or any other equivalent you have.  Make sure you copy it from the page source, and not directly off of the page.

Delete any files which were specific to wakaba, as kusaba will not delete them for you.

Rebuild all html files in the manage panel so everything is changed over to kusaba, and enjoy your upgraded board.


# w2k.php #

```
<?php
/* Configuration */
	$boardname     = 'CHANGEME'; // directory of the board which you are currently converting.  should not contain slashes
	$ku_randomseed = 'CHANGEME'; // your KU_RANDOMSEED value.  used for encrypting ip addresses
	$convertthumbs = true; // wakaba will always save an image thumbnail as a jpeg.  kusaba saves it in the same format as the original, which causes a problem.  by setting this to true, the script will attempt to convert the thumbnail and delete the original
	$commentstable = 'comments'; // the table which is currently holding the board's posts.  you probably won't need to modify this
	$dbhost        = 'localhost';
	$dbusername    = 'root';
	$dbpassword    = '';
	$dbdatabase    = 'wakabasql';
	$dbprefix      = '';

$link = mysql_connect($dbhost, $dbusername, $dbpassword);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
echo 'Connected to database server successfully.<br>';

$db_selected = mysql_select_db($dbdatabase, $link);
if (!$db_selected) {
    die ('Unable to connect to database: ' . mysql_error());
}
echo 'Selected database successfully.<br>';

echo 'Processing posts...<hr>';
$lastpost = 1;
$insertvalues = '';
$result = mysql_query("SELECT * FROM `" . $commentstable . "`");
while ($line = mysql_fetch_assoc($result)) {
	/* IP address */
		$line['ip'] = long2ip($line['ip']);
		$ip = md5_encrypt($line['ip'], $ku_randomseed);
		$ipmd5 = md5($line['ip']);
		
	/* Tripcode */
		if ($line['trip'] != '') {
			$line['trip'] = substr($line['trip'], 1);
		}
	
	/* Password */
		$line['password'] = md5($line['password']);
	
	/* Comment */
		if (strpos($line['comment'], '<p>') === 0) {
			$line['comment'] = substr($line['comment'], 3);
			if (strrpos($line['comment'], '</p>') === (strlen($line['comment']) - 4)) {
				$line['comment'] = substr($line['comment'], 0, -4);
			}
		}
		if ($line['comment'] != '') {
			$line['comment'] = preg_replace('/onclick\="highlight\((.+)\)/', 'onclick="javascript:highlight(\'\\1\', true)', $line['comment']);
			$line['comment'] = str_replace('<br />', '<br>', $line['comment']);
		}
		
	/* Image */
		if ($line['image'] != '') {
			$line['image'] = substr($line['image'], 4);
			$filetype = substr($line['image'], (strrpos($line['image'], '.') + 1));
			$filename = substr($line['image'], 0, strrpos($line['image'], '.'));
			
			if ($convertthumbs && ($filetype == 'png' || $filetype == 'gif')) {
				exec('convert thumb/' . $filename. 's.jpg thumb/' . $filename. 's.' . $filetype);
				if (is_file('thumb/' . $filename. 's.' . $filetype)) {
					unlink('thumb/' . $filename. 's.jpg');
				}
			}
			
			$image_w = $line['width'];
			$image_h = $line['height'];
			$thumb_w = $line['tn_width'];
			$thumb_h = $line['tn_height'];
		} else {
			$filename = '';
			$filetype = '';
			$image_w = 0;
			$image_h = 0;
			$thumb_w = 0;
			$thumb_h = 0;
		}
	
	$insertvalues .= "(" . $line['num'] . ", " . $line['parent'] . ", '" . mysql_real_escape_string($line['name']) . "','" . $line['trip'] . "', '" . mysql_real_escape_string($line['email']) . "', '" . mysql_real_escape_string($line['subject']) . "','" . mysql_real_escape_string($line['comment']) . "', '" . $filename . "', '', '" . $filetype . "', '', " . $image_w . ", " . $image_h . ", '" . $line['size'] . "', '', " . $thumb_w . ", " . $thumb_h . ", '" . $line['password'] . "', " . $line['timestamp'] . ", " . $line['lasthit'] . ", '" . $ip . "', '" . $ipmd5 . "', '', '0', '0', '0', '0', '0'),\n";

	$lastpost = $line['num'];
}

$insertvalues = substr($insertvalues, 0, -2);

if ($insertvalues != '') {
	echo '<h1>SQL</h1>' . "\n";
	echo '<pre>' . "\n" .
	'Make sure you copy this from the page source, and not directly off the page.' . "\n" . "\n" .
	'INSERT INTO `' . $dbprefix . 'posts_' . $boardname . '` (
`id` ,
`parentid` ,
`name` ,
`tripcode` ,
`email` ,
`subject` ,
`message` ,
`filename` ,
`filename_original` ,
`filetype` ,
`filemd5` ,
`image_w` ,
`image_h` ,
`filesize` ,
`filesize_formatted` ,
`thumb_w` ,
`thumb_h` ,
`password` ,
`postedat` ,
`lastbumped` ,
`ip` ,
`ipmd5` ,
`tag` ,
`stickied` ,
`locked` ,
`posterauthority` ,
`deletedat` ,
`IS_DELETED`
)
VALUES 
';

	echo $insertvalues . ";\n" . 
	'ALTER TABLE `' . $dbprefix . 'posts_' . $boardname . '`  AUTO_INCREMENT =' . ($lastpost + 1) . "\n" . "\n" .
	'</pre>';
} else {
	echo 'Something went wrong.  No posts were found in the comments table.';
}


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