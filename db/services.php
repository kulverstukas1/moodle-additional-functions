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
 * Web service local plugin additional functions and service definitions.
 * @author    Kulverstukas, http://9v.lt
 */
 
$functions = array(
    'local_get_user_enrolment_id' => array(
        'classname'   => 'local_additional_functions_external',
        'methodname'  => 'get_user_enrolment_id',
        'classpath'   => 'local/additional_functions/externallib.php',
        'description' => 'Returns the user enrolment ID for the given course ID',
        'type'        => 'read',
    ),
    
    'local_get_user_enrolment_dates' => array(
        'classname'   => 'local_additional_functions_external',
        'methodname'  => 'get_user_enrolment_dates',
        'classpath'   => 'local/additional_functions/externallib.php',
        'description' => 'Returns the user enrolment start and end dates for the given course ID',
        'type'        => 'read',
    )
);
