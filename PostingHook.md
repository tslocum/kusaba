This hook processes a post once the internal script is through processing it.

# Initialization Example #
```
/* Module initialization */
function yourmodule_init() {
	global $hooks;
	
	$hooks['posting'][] = 'yourmodule';
}
```

# Processing Example #
```
/* Posting hook processing */
function yourmodule_process_posting($post) {
	$post['message'] = str_replace('hamburger', 'hotdog', $post['message']);
	
	return $post;
}
```

# Arguments #

1 argument:

Array **$post**
  * **$post['name']**
> > The text entered in the Name field
  * **$post['tripcode']**
> > The processed tripcode, minus the indicator (!)
  * **$post['email']**
> > The text entered in the Email field
  * **$post['subject']**
> > The text entered in the Subject field
  * **$post['message']**
> > The text entered in the Message box

# Return #

The process function must return the modified $post variable, which will be passed to subsequent modules using this hook, and then finally inserted into the database.