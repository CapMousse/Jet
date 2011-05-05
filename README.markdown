ShwaarkFramework
================

A lightweight, easy modifiable and fast PHP framework with MVC.

Tested on PHP 5.2+, PHP 5.3+

Features
--------

* PHP 5.3 support
* MVC support in OOP
* Modules support
* Personnal routes support
* Highly modifiable
* Light and fast

Changelog
---------

#### V1.0 - released 2011-05-05

First Stable release

* Bugfix *Core*: Rebuild loader
* Bugfix *Core*: Rebuild route parsing
* Bugfix *Core*: Rebuild controller & action dispatcher
* Bugfix *Controller* : loadModel cache not working, can't reload the same Model

* Change *Config*: Drop all static vars for nested array.
* Change *Controller* : Set some private method to public method, in case on nested object
* Change *Controller* : Rename getView/includeModel method to loadView/loadModel method
* Change *View* : Rebuild all block call
* Change *All* : Remove unused var, rename var, added lots of comment...

* Added *Controller* : Added module support with loadModule
* Added *Controller* : Added a isActtion & isController method to check if asked Action/Controller is the actuel Action/Controller
* Added *Controller* : Added a render method
* Added *Idiorm & Paris* : Added a end method to reinit current used model
* Added *View* : Added a getVar method
* Added *View* : Added a slugify method

* Removed *Helper* : Completely remove the Helper class, use Module instead
* Removed *Session control* : Remove the stollen session control to implement a better control soon

#### beta 0.1 - released 2011-04-10

* Initial release


First time here!
----------------

The wiki is currently in development, but all the code is documented!

Feel free to contribute!
------------------------

* Fork
* Report bug
* Help in development

Licence
-------

Released under a [BSD license](http://en.wikipedia.org/wiki/BSD_licenses)

Credits
-------

* Build with [Paris](http://github.com/j4mie/paris) & [Idiorm](http://github.com/j4mie/idiorm) from j4mie for ORM support