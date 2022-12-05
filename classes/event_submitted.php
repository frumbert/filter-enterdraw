<?php
// This file is part of Moodle - http://moodle.org/
//
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
 * user submits the entry form
 *
 * @package    filter_enterdraw
 * @copyright  2022 tim st clair
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace filter_enterdraw\event;

defined('MOODLE_INTERNAL') || die();

class event_submitted extends \core\event\base {

    /**
     * Initialise the event data.
     */
    protected function init() {
        $this->data['objecttable'] = null;
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventsubmitted', 'filter_enterdraw');
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
    global $PAGE;
        return $PAGE->url;
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' submitted EnterDraw on activity " .
            "'{$this->other['modulename']}' in course with id '$this->courseid'.";
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     */
    protected function validate_data() {
        parent::validate_data();
        if (!isset($this->other['modulename'])) {
            throw new \coding_exception('The \'modulename\' value must be set in other.');
        }
    }

    /**
     * This is used when restoring course logs where it is required that we
     * map the objectid to it's new value in the new course.
     *
     * @return array
     */
    public static function get_objectid_mapping() {
        parent::get_objectid_mapping();
        return array('db' => \core\event\base::NOT_MAPPED, 'restore' => \core\event\base::NOT_MAPPED);
    }
}
