/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/


LiveStreet 0.5.1

INSTALLATION
1. Copy files to the engine to the desired directory site
2. Go the address http://you_site/install/
3. Follow the instructions of the installer.

Upgrading from 0.4.2
0. Be sure to make backup of your site and database
1. Update to version 0.5 can only database, so copy the new version over the old NOT to install, use a clean directory
2. Copy files to the engine to the desired directory site
3. Copy the file to a new directory on your config.local.php old version 0.4.2 and copy the directory /uploads/ all files
4. Enter the address http://you_site/install/
5. Follow the instructions of the installer. When you create the database required By clicking the "Convert base 0.4.2 in 0.5.1"

Upgrading from 0.5
0. Be sure to make backup of your site and database
1. Delete old files (except /config/config.local.php and directory /uploads/ all files) and copy the new files to a directory site
2.1 Enter the address http://you_site/install/ and to step in creating a database to mark checkbox "Convert 0.5 DB to 0.5.1 "
2.2 Or execute the SQL patch /install/convert_0.5_to_0.5.1.sql in phpMyAdmin or via the console MySQL, replacing prefix tables (prefix_) in the database on your


Configuration and Tuning Engines
Settings are in a file /config/config.php. For them to change is desirable to override these settings in the file config.local.php, this will avoid problems with future upgrades.
Management plug-ins can be found at /admin/plugins/

STANDARD TEMPLATES
In version 0.5 includes 4 templates: new + developer - js library MooTools and new-jquery + developer-jquery - a library jQuery.
Future versions will only support new-jquery templates and developer-jquery, so it is strongly recommended to choose them for installation.

OPPORTUNITIES SEARCH
LiveStreet supports full-text search on the site using the search engine Sphinx.
Accordingly, if you need search on the site, you must install and configure the server Sphinx, a sample configuration file (sphinx.conf) is located in the directory /install/


DISABLE SPONSORED LINK
Since LiveStreet engine is free, we insert in each release of some internal pages link to sponsor's current release.
This helps us to get financial investments to the project and accelerate its development.
If you are totally against sponsored links, you can remove it by removing the file /classes/hooks/HookSponsor.class.php
We urge you not to delete link the sponsor.


For all questions, please visit Eanglish community http://livestreetcms.net
Official site of the project http://livestreetcms.com
Russian community http://livestreet.ru