i have a table with calls, named calls, and a table named call_history_import. 

call hitory import has all calls that come in.  i would like to create a screen that forces
users to create a calls record for every call history import record.  

the call history import 
table has a call status field.  this is initially set to 0.  once a call has been linked to a 
call, then i would like the call status field set to 1.

in the call_history_import table there is a calls_id field which is a fk to calls.id field in 
the calls table.

if there was a calls record that was created within an hour or two after the call history import record, 
then i would like to link the two records and set the status to 1.