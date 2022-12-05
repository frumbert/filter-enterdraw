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
 * enter a draw. something you only do once. show a message to submit; a message that you have submitted.
 * stores agains the user preference as the key `enterdraw-course:{id}-mod:{id}`
 * Assumes there is ONE instance on the page.
 *
 * @package    filter
 * @subpackage enterdraw
 * @copyright  tim@avideelearning.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class filter_enterdraw extends moodle_text_filter {

    // currently support 16 replacements
    public function filter($text, array $options = array()) {
    global $COURSE, $PAGE, $OUTPUT, $CFG;

        $start = preg_quote('[[ENTERDRAW:START]]');
        $middle = preg_quote('[[ENTERDRAW:ELSE]]');
        $end = preg_quote('[[ENTERDRAW:END]]');
        $action = '/\[\[ENTERDRAW:BUTTON[^[](.*)\]\]/';
        $expr = "/(.*){$start}(.*){$middle}(.*){$end}(.*)/";

        preg_match($expr, $text, $matches);
        if (empty($matches)) return $text;

        $modinfo = get_fast_modinfo($COURSE);
        $cminfo = $modinfo->get_cm($PAGE->cm->id);
        $prefname = "enterdraw-course:{$COURSE->id}-mod:{$cminfo->id}";

        // did we post back?
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postparam = optional_param('id',0,PARAM_INT);
            if ($postparam > 0 && confirm_sesskey()) {
                require_once($CFG->dirroot.'/filter/enterdraw/classes/event_submitted.php');
                $event = \filter_enterdraw\event\event_submitted::create([
                    'context' => $PAGE->context,
                    'courseid' => $COURSE->id,
                    'other' => ['modulename' => $cminfo->name]]);
                $event->trigger();
                set_user_preferences([$prefname=>1]);
            }
        }

        $preference = get_user_preferences($prefname);

        $find = stripslashes("{$start}{$matches[2]}{$middle}{$matches[3]}{$end}");
        if (empty($preference)) {
            $replace = $matches[2];
            preg_match($action, $replace, $button);

            $form = $OUTPUT->single_button($PAGE->url, $button[1], 'post', ['class'=>'enterdraw']);
            
            $replace = preg_replace($action, $form, $replace);
        } else {
            $replace = $matches[3];
        }

        $text = str_replace($find, $replace, $text);
        return $text;
    }


}