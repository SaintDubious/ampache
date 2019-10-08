<?php
/* vim:set softtabstop=4 shiftwidth=4 expandtab: */
/**
 *
 * LICENSE: GNU Affero General Public License, version 3 (AGPLv3)
 * Copyright 2001 - 2019 Ampache.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

require_once 'lib/init.php';

UI::show_header();

// Switch on the actions
switch ($_REQUEST['action']) {
    case 'add_shout':
        // Must be at least a user to do this
        if (!Access::check('interface', '25')) {
            UI::access_denied();

            return false;
        }

        if (!Core::form_verify('add_shout', 'post')) {
            UI::access_denied();

            return false;
        }

        // Remove unauthorized defined values from here
        if (isset($_POST['user'])) {
            unset($_POST['user']);
        }
        if (isset($_POST['date'])) {
            unset($_POST['date']);
        }

        if (!Core::is_library_item(Core::get_post('object_type'))) {
            UI::access_denied();

            return false;
        }

        $shout_id = Shoutbox::create($_POST);
        header("Location:" . AmpConfig::get('web_path') . '/shout.php?action=show_add_shout&type=' . $_POST['object_type'] . '&id=' . (int) ($_POST['object_id']));

        return false;
    case 'show_add_shout':
        // Get our object first
        $object = Shoutbox::get_object($_REQUEST['type'], Core::get_request('id'));

        if (!$object || !$object->id) {
            AmpError::add('general', T_('Invalid object selected'));
            AmpError::display('general');
            break;
        }

        $object->format();
        if (strtolower(get_class($object)) == 'song') {
            $data = $_REQUEST['offset'];
        }

        // Now go ahead and display the page where we let them add a comment etc
        require_once AmpConfig::get('prefix') . UI::find_template('show_add_shout.inc.php');
    break;
    default:
        header("Location:" . AmpConfig::get('web_path'));
    break;
} // end switch on action

/* Show the Footer */
UI::show_query_stats();
UI::show_footer();