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
 * The renderer to go back to another page.
 *
 * @package    mod_league
 * @category   output
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_league\output;

// Prevents direct execution via browser.
defined('MOODLE_INTERNAL') || die();

require_once('../../config.php');

/**
 * The renderer class to print a go back button.
 *
 * @package    mod_league
 * @since      Moodle 3.0
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class go_back_view implements \renderable {
 
    /** @var string Title header (if exists). */
    public $title = null;
    
    /** @var string Content on the page (if exists). */
    public $content = null;
    
    /** @var int Course Module ID. */
    public $cmid = null;
    
    /** @var string Page to go back. */
    public $page = 'view.php';
    
    /** @var object Array with more hiden information to the button. */
    public $hidden = null;
    
    /**
     * Class constructor.
     * 
     * @param int $cmid Course Module ID.
     * @param string $title Title header (if exists).
     * @param string $content Content on the page (if exists).
     * @param string $page Page to go back.
     * @param object $hidden Array with more hiden information on the button.
     */
    public function __construct($cmid, $title = null, $content = null, $page = 'view.php', $hidden = null) {
        $this->title = $title;
        $this->content = $content;
        $this->cmid = $cmid;
        $this->page = $page;
        $this->hidden = $hidden;
    }
}