This is the plugin for importing stories from Percolate into Wordpress. It requires Wordpress 3.1 and is tested up to 3.3.1.

Get Started
-----------

In order to use this plugin you will need an API key issued to you from Percolate, along with your user ID. For installation instructions please visit our [Wordpress partners page](http://partners.percolate.com/category/plugin-documentation/wordpress/).


Changelog
-----------

### 3.0

* Automatic plugin updater
* Ability to start importing posts from today going forward, instead of starting from the beginning of account creation. 
* Fix for inserting images into post body in wordpress 3.3.1
* Made the path to the javascript file agnostic of the plugin directory name.

### 3.1

* Updated post timestamp to honor a timezone set in wordpress. The offset of the wordpress setting is applied to the `posted_on` time when importing. 
* Added postToPercolate function.
* Cleaned up settings page. Got rid of search by username and re-ordered the input options.


-----------
_Method of Deployment_

1. Update this readme with updated change log.
2. Increment the version number in the header of `percolate-import.php` (for display purposes in the wp-admin)
3. Increment the version number at the bottom of this file. (this triggers the upgrade on instances where the plugin is installed.) 

--------------------------------------------------
_Please do not remove this version declaration_
~Current Version:3.0~



