# APC #

One of the optimizations I created for 7chan and later added to normal kusaba releases is support for APC string storage.  Ask your host to install APC for you if you don't have it already, and then set KU\_APC to true.  kusaba will then begin to cache strings such as configurations and board post boxes, reducing the load on the database.

In addition to this, APC will cache PHP files in opcode format, taking compile time out of each access.


# Cleanup #

Once in a while, such as a few times a week, click Cleanup in the manage panel.  This will remove all overhead in every table, along with search for files which don't belong in the src or thumb directories.