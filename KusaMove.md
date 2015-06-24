# Usage #

Configure the area at the top of the script and run.

**Warning!  This script has only been tested a few times.  You should try this on a test thread before running it on the thread you wish to transfer to make sure it works properly.**

This is designed for kusaba 1.0.1 and 1.0.2, earlier versions are not compatible, and later versions already include a move thread feature.

# kusamove.php #
```
<?php
require 'config.php';

/* Configure these! */
$id         = '9991';
$board_from = 'someboard';
$board_to   = 'anotherboard';
/* End configuration */

$tc_db->Execute("START TRANSACTION");
$temp_id = 0;

$tc_db->Execute("UPDATE " . KU_DBPREFIX . "posts_" . $board_from . " SET `id` = " . $temp_id . " WHERE `id` = '" . $id . "'");

$tc_db->Execute("INSERT INTO " . KU_DBPREFIX . "posts_" . $board_to . " SELECT * FROM " . KU_DBPREFIX . "posts_" . $board_from . " WHERE `id` = " . $temp_id);
$new_id = $tc_db->Insert_Id();

processPost($new_id, $new_id, $id);

$tc_db->Execute("DELETE FROM " . KU_DBPREFIX . "posts_" . $board_from . " WHERE `id` = " . $temp_id);

$results = $tc_db->GetAll("SELECT `id` FROM " . KU_DBPREFIX . "posts_" . $board_from . " WHERE `parentid` = '" . $id . "' ORDER BY `id` ASC");
foreach ($results as $line) {
	$tc_db->Execute("UPDATE " . KU_DBPREFIX . "posts_" . $board_from . " SET `id` = " . $temp_id. " WHERE `id` = " . $line['id']);
	
	$tc_db->Execute("INSERT INTO " . KU_DBPREFIX . "posts_" . $board_to . " SELECT * FROM " . KU_DBPREFIX . "posts_" . $board_from . " WHERE `id` = " . $temp_id);
	$insert_id = $tc_db->Insert_Id();
	
	processPost($insert_id, $new_id, $id);
	
	$tc_db->Execute("UPDATE " . KU_DBPREFIX . "posts_" . $board_to . " SET `parentid` = " . $new_id . " WHERE `id` = " . $insert_id);
	
	$tc_db->Execute("DELETE FROM " . KU_DBPREFIX . "posts_" . $board_from . " WHERE `id` = " . $temp_id);
}

$tc_db->Execute("COMMIT");

echo 'Move complete.';

function processPost($id, $newthreadid, $oldthreadid) {
	global $tc_db, $board_from, $board_to;
	
	$message = $tc_db->GetOne("SELECT `message` FROM " . KU_DBPREFIX . "posts_" . $board_to . " WHERE `id` = " . $id . " LIMIT 1");
	
	if ($message != '') {
		$message_new = str_replace('/read.php/' . $board_from . '/' . $oldthreadid, '/read.php/' . $board_to . '/' . $newthreadid, $message);
		
		if ($message_new != $message) {
			$tc_db->GetOne("UPDATE " . KU_DBPREFIX . "posts_" . $board_to . " SET `message` = '" . mysql_real_escape_string($message) . "' WHERE `id` = " . $id);
		}
	}
}

?>
```