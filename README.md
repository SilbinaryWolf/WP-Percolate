This is the plugin for importing stories from Percolate into Wordpress. It requires Wordpress 3.1 and is tested up to 3.3.1.

Get Started
-----------

In order to use this plugin you will need an API key issued to you from Percolate, along with your user ID. For installation instructions please visit our [Wordpress partners page](http://partners.percolate.com/category/plugin-documentation/wordpress/).


Changelog
-----------
### 3.2.3

* Imports most recent 30 posts

### 3.2.2

* Allow user to select 3 different image sizes
* Imports post base on publish date

### 3.2.1

* Fix issue with last imported ID validation during import
* Fix issue with postback callback function when auto import is run in the admin
* Update to debug section, assing last imported time and next import times.

### 3.2.0

* Now works with api v3.0 
* Titles entered in Percolate get mapped to titles in Wordpress.


### 3.1.2

* Bug: Fix for not importing the full post body. 

### 3.1

* Updated post timestamp to honor a timezone set in wordpress. The offset of the wordpress setting is applied to the `posted_on` time when importing. 
* Added postToPercolate function.
* Cleaned up settings page. Got rid of search by username and re-ordered the input options.


### 3.0

* Automatic plugin updater
* Ability to start importing posts from today going forward, instead of starting from the beginning of account creation. 
* Fix for inserting images into post body in wordpress 3.3.1
* Made the path to the javascript file agnostic of the plugin directory name.

-----------
_Method of Deployment_

1. Update this readme with updated change log.
2. Increment the version number in the header of `percolate-import.php` (for display purposes in the wp-admin)
3. Increment the version number at the bottom of this file. (this triggers the upgrade on instances where the plugin is installed.) 

--------------------------------------------------
_Please do not remove this version declaration_
~Current Version:3.2.3~



