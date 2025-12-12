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
        $context = context_user::instance($USER->id);
        self::validate_context($context);

        $ueids = $DB->get_records_sql(
            'SELECT {user_enrolments}.id AS id, {user_enrolments}.status AS status, {user_enrolments}.enrolid AS enrolid, {user_enrolments}.userid AS userid, {enrol}.courseid AS courseid
            FROM {user_enrolments}, {enrol}
            WHERE {user_enrolments}.enrolid = {enrol}.id AND {enrol}.courseid = ? AND {user_enrolments}.userid = ?',
            array($course_id, $user_id)
        );
     
        if ($ueids) {
            return json_encode(array_values($ueids));
        }
     
        return null;
    }

    public static function get_user_enrolment_id_returns() {
        return new external_value(PARAM_TEXT, 'The list of user enrolment IDs in JSON format for the given course and user ID');
    }

    //========================================================
    
    public static function get_user_enrolment_dates_parameters() {
        return new external_function_parameters(
            array(
                'course_id' => new external_value(PARAM_INT, 'The course ID to search at', VALUE_REQUIRED),
                'user_id' => new external_value(PARAM_INT, 'The user ID to find', VALUE_REQUIRED)
            )
        );
    }

    public static function get_user_enrolment_dates($course_id, $user_id) {
        global $USER, $DB;
        
        // parameter validation
        $params = self::validate_parameters(
            self::get_user_enrolment_dates_parameters(),
            array('course_id' => $course_id, 'user_id' => $user_id)
        );
        
        // context validation
        $context = context_user::instance($USER->id);
        self::validate_context($context);
               
        $enrolDates = $DB->get_records_sql(
            'SELECT {user_enrolments}.timestart AS timestart, {user_enrolments}.timeend AS timeend, {user_enrolments}.userid AS userid, {enrol}.courseid AS courseid
            FROM {user_enrolments}, {enrol}
            WHERE {user_enrolments}.enrolid = {enrol}.id AND {enrol}.courseid = ? AND {user_enrolments}.userid = ?',
            array($course_id, $user_id)
        );
        
        if ($enrolDates) {
            return json_encode(array_values($enrolDates));
        }
        return null;
    }
    
    public static function get_user_enrolment_dates_returns() {
        return new external_value(PARAM_TEXT, 'The enrolment start and end dates in JSON format');
    }
    
    //========================================================

    public static function get_user_recent_courses_parameters() {
        return new external_function_parameters(
            array('usernames' => new external_value(PARAM_TEXT, 'The usernames to search for in JSON format', VALUE_REQUIRED))
        );
    }

    public static function get_user_recent_courses($usernames) {
        global $USER, $DB;
        
        // parameter validation
        $params = self::validate_parameters(
            self::get_user_recent_courses_parameters(),
            array('usernames' => $usernames)
        );
        
        // context validation
        $context = context_user::instance($USER->id);
        self::validate_context($context);

        $usernamesArr = json_decode($usernames);

        $courseLastAccess = array();
        if ($usernamesArr) {
            [$insql, $inparams] = $DB->get_in_or_equal($usernamesArr);
            $iter = $DB->get_recordset_sql(
                "SELECT {user_lastaccess}.id, {user}.username as username, {course}.shortname as shortname, {user_lastaccess}.timeaccess as course_access_time
                FROM {user}, {course}, {user_lastaccess}
                WHERE {user}.username $insql AND {user_lastaccess}.userid = {user}.id AND {user_lastaccess}.courseid = {course}.id AND {course}.shortname NOT LIKE '-%'
                ORDER BY course_access_time DESC",
                $inparams
            );
            
            foreach ($iter as $i) {
                $courseLastAccess[$i->username][] = $i;
            }
        }
        
        return json_encode($courseLastAccess);
    }

    public static function get_user_recent_courses_returns() {
        return new external_value(PARAM_TEXT, 'The course last access information with username, shortname and Unix time');
    }

    //========================================================
    
    public static function get_user_last_access_parameters() {
        return new external_function_parameters(
            array('usernames' => new external_value(PARAM_TEXT, 'The usernames to search for in JSON format', VALUE_REQUIRED))
        );
    }

    public static function get_user_last_access($usernames) {
        global $USER, $DB;
        
        // parameter validation
        $params = self::validate_parameters(
            self::get_user_last_access_parameters(),
            array('usernames' => $usernames)
        );
        
        // context validation
        $context = context_user::instance($USER->id);
        self::validate_context($context);
        
        $usernamesArr = json_decode($usernames);
        
        $courseLastAccess = array();
        if ($usernamesArr) {
            [$insql, $inparams] = $DB->get_in_or_equal($usernamesArr);
            $iter = $DB->get_recordset_sql(
                "SELECT username, lastaccess
                FROM {user}
                WHERE username $insql",
                $inparams
            );
            
            foreach ($iter as $i) {
                $courseLastAccess[$i->username] = $i->lastaccess;
            }
        }
        return json_encode($courseLastAccess);
    }

    public static function get_user_last_access_returns() {
        return new external_value(PARAM_TEXT, 'The course last access information grouped by username');
    }

}
