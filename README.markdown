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

#### V0.2 - release 2011-05-35
* Added *Core* : Move all route parsing to a core class
* Added *Debug* : Create a class to log all debug message

* Change *Route parsing* : now you can name your route like "test/[id]:num/"
* Change *route action call* : Now action il call with arguments ($id for above exemple)
* Change *config access* : Now with ```Shwaark::$config```

* Bugfix: controller now loaded propreply
* Bugfix: perf drop on route parsing resolved
* Bugfix: view can now load an other view
* Bugfix: debug now log error to log file
* Bugfix: module are now loaded propreply
* Bugfix: loadView not working prepreply

#### V0.1 - released 2011-05-13
* Added *Cache*: Now, you have memory!
* Added *Controller: **loadController** you can include other controller in your controller

* Change *View* : **createBlock** change by **beginBlock**
* Change *Controller*: **loadModel** you can now load and don't factory a model. Usefull for relation

* Removed *Controller*: remove **isController** and **isAction**

Plus global optimisation, changes and other stuffs

#### V1.0 - released 2011-05-05

* First Stable release

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