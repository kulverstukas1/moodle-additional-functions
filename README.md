# Additional Moodle helper functions

This repo is to contain any helper functions that I needed for various tools.


Currently there is only one web service function which returns user enrolment ID with the given course ID and user ID. This is needed for several Moodle core functions such as `core_enrol_edit_user_enrolment`. For some reason Moodle has no function (that I know of or was unable to find) to return a user enrolment ID and that is weird.
Therefore, I created this to help me.


Moodle plugin development is a bit cryptic it seems, so this might not be a perfectly crafted solution and I accept any fixes or suggestions to improve it. Should work with all Moodle versions, but I can't guarantee that.

This plugin is based on a official web service template: https://github.com/moodlehq/moodle-local_wstemplate

To install it, extract contents into `moodle/local/additional_functions` and run the setup as normal. In the docs you'll see that a function `local_get_user_enrolment_id` have appeared.
Next go ahead into your web service and add that function to the list. Now you can query it like any other giving it the required parameters.
