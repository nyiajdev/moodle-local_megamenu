# Moodle Mega Menu

Enhance Boost and Boostrap 4 compatible themes with a mega menu to display courses.

![Mega menu preview](https://raw.githubusercontent.com/nyiajdev/moodle-local_megamenu/master/pix/preview.png)

FEATURES

* Displays dropdown links near profile on navbar
* Choose one or more course categories to display active courses in lists
* Add stylistic image to menus
* Optionally restrict by capability
* Optionally restrict by logged in or not

## How to install

#### Option 1: Install from Moodle.org (recommended)
1. Login as an admin and go to Site administration > Plugins > Install plugins. (If you can't find this location, then plugin installation is prevented on your site.)
2. Click the button 'Install plugins from Moodle plugins directory'.
3. Search for "Mega menu", click the Install button then click Continue.
4. Confirm the installation request
5. Check the plugin validation report

#### Option 2: Install from zip package
1. Download Mega menu from <https://moodle.org/plugins/pluginversions.php?plugin=local_megamenu>
2. Login to your Moodle site as an admin and go to Administration > Site administration > Plugins > Install plugins.
3. Upload the ZIP file. You should only be prompted to add extra details (in the Show more section) if the plugin is not automatically detected.
4. If your target directory is not writeable, you will see a warning message.
5. Check the plugin validation report

#### Option 3: Install manually on server
1. Download Mega menu from <https://moodle.org/plugins/pluginversions.php?plugin=local_megamenu>
2. Upload or copy it to your Moodle server.
Unzip it in the `/local` directory.
3. In your Moodle site (as admin) go to Settings > Site administration > Notifications (you should, for most plugin types, get a message saying the plugin is installed).

For more detailed info, visit <https://docs.moodle.org/39/en/Installing_plugins>

## How to use

1. Go to Site administration > Plugins > Local plugins > Mega menu > Manage menus
2. Click Create menu
3. Give your menu and name, dropdown label, and choose course categories
4. Set other options if desired, save
5. Enjoy 

## License ##

2020 NYIAJ LLC

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.
