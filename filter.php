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

    public function filter($text, array $options = array()) {
    global $COURSE, $PAGE, $OUTPUT, $CFG;

        // consume and remove draw data blocks
        $blocknames = ['TOTAL','OPEN','CLOSED','ENTERED'];
        $blocks = [];
        foreach ($blocknames as $block) {
            $begin = '\[\[ENTERDRAW\:' . $block . '\]\]';
            $end = '\[\[\/ENTERDRAW\:' . $block . '\]\]';
            $expr = "/{$begin}(.*){$end}/";
            if (preg_match_all($expr, $text, $matches)) {
                $blocks[$block] = $matches[1][0];
                $text = preg_replace($expr, '', $text);
            }
        }

        if (count($blocks) === 0) return $text;

        // there might be leftover empty paragraph markers
        $text = preg_replace('/<p(\s.*?)?><\/p>/', '', $text);

        $modinfo = get_fast_modinfo($COURSE);
        $cminfo = $modinfo->get_cm($PAGE->cm->id);
        $prefname = "enterdraw-course:{$COURSE->id}-mod:{$cminfo->id}";

        // has the draw closed?
        $closed = false;
        $total = intval($blocks['TOTAL']);
        $used = $this->count_prefs($prefname);
        if ($total > 0 && $used >= $total) {
            $closed = true;
        }

        // did we post back?
        if (!$closed && $_SERVER['REQUEST_METHOD'] === 'POST') {
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

        $entered = false;
        $preference = get_user_preferences($prefname);
        if (!empty($preference)) {
            $entered = true;
        }

        // figure out which block to draw
        $display = '';
        if ($closed) {
            $display = $blocks['CLOSED'];
        } else if ($entered) {
            $display = $blocks['ENTERED'];
        } else {
            $display = $blocks['OPEN'];
            $button = '/\[\[ENTERDRAW:BUTTON[^[](.*)\]\]/';
            preg_match($button, $display, $matches);
            $form = $OUTPUT->single_button($PAGE->url, $matches[1], 'post', ['class'=>'enterdraw']);
            $display = preg_replace($button, $form, $display);
        }

        // display the block at the expected output location
        $text = str_replace('[[ENTERDRAW:OUTPUT]]', $display, $text);
        return $text;

    }

    private function count_prefs($name) {
    global $DB;
        return $DB->count_records_select('user_preferences', 'name=:n', ['n' => $name]);
    }


}