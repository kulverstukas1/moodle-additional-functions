<?php

// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Additional functions plugin methods
 * @author    Kulverstukas, http://9v.lt
 * More info:
 *   https://docs.moodle.org/dev/Enrolment_API
 *   https://docs.moodle.org/dev/Adding_a_web_service_to_a_plugin
 *   http://www.examulator.com/er/components/course.html
 *   https://docs.moodle.org/dev/Tutorial
 */
 
require_once($CFG->libdir.'/externallib.php');

class local_additional_functions_external extends external_api {

    public static function get_user_enrolment_id_parameters() {
        return new external_function_parameters(
            array(
                'course_id' => new external_value(PARAM_INT, 'The course ID to search at', VALUE_REQUIRED),
                'user_id' => new external_value(PARAM_INT, 'The user ID to find', VALUE_REQUIRED)
            )
        );
    }
    
    public static function get_user_enrolment_id($course_id, $user_id) {
        global $USER, $DB;
        
        // parameter validation
        $params = self::validate_parameters(
            self::get_user_enrolment_id_parameters(),
            array('course_id' => $course_id, 'user_id' => $user_id)
        );

        // context validation
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

        $ueid = $DB->get_record_sql(
            'SELECT {user_enrolments}.enrolid AS enrolid, {user_enrolments}.userid AS userid, {enrol}.courseid AS courseid
            FROM {user_enrolments}, {enrol}
            WHERE {user_enrolments}.enrolid = {enrol}.id AND {enrol}.courseid = ? AND {user_enrolments}.userid = ?',
            array($course_id, $user_id)
        );
        if ($ueid != null) {
            return $ueid->enrolid;
        }
        return null;
    }

    public static function get_user_enrolment_id_returns() {
        return new external_value(PARAM_INT, 'The user enrolment ID for the given course and user ID\'s');
    }



}
