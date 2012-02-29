Jet
================

A lightweight, easy modifiable and fast PHP framework with MVC.
Work on PHP5.3+

Now Beta 4. Try and report bugs !


WARNING
-------
*Before use, you need to update permissions on the ***project/logs*** dir to authorize Jet to write on dir/files*

Features
--------

Jet provide lot of tools for your project :

* Easy configuration
* MVC structure
* Routing
  * Route parsing
  * Route segment and named segment (article/[id]:num/:slug)
  * Custom *Not Found* page
* HTTP Request/Response
  * Cache-Control
  * Etags
  * LastModified
* RESTfull support
* External lib support (templating class like Twig, custom lib...)
* Light ORM
* Highly modifiable
* CLI tool: Create/destroy Apps, manage database **(only with MySQL)**
* Fixtures **(only with MySQL)**
* Light and fast

First time here!
----------------

The wiki is currently in development, but all the code is documented!
Fell free to help and write the wiki !

CLI usage
---------

The `jet.php` tool, found on the main dir, is a CLI tool created to easily create/remove Apps and deploy Tables from your models. You can found 2 example of models with theyre structures on the `models` dirs.
Before using it, you need to modify your `project/config.php` file and figure out where is your mysql `socket.sock`.
For example, the MAMP MySQL socket is `mysql:unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock`.

For help just type in your terminal : `php jet.php help`. A list of available commands will be prompted.

**All db arguments are only (for the moment) compatible with MySQL !**

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

* ORM inspired of [Paris](http://github.com/j4mie/paris) & [Idiorm](http://github.com/j4mie/idiorm) from j4mie
* Thanks to [Taluu](https://github.com/Taluu) for help, support, tips, tricks and the *render time contest*
* Thanks to [Floweb](https://github.com/floweb) for help