What is jsass ?
==============================

This project is a plugin for [Jelix](http://jelix.org) PHP framework. It allows you to use easily [Sass](http://sass-lang.com/) dynamic stylesheet language in Jelix.

This is a plugin of CSSprepro which is itself an htmlresponse plugin for Jelix.



Installation
============

Under Jelix default configuration, create (if missing) a "CSSprepro" directory in your project's "plugins" directory.
Clone this repository in that directory with :

    git clone --recursive git@github.com:brice-t/jSass.git


Note that you should have your app plugin directory in your modulesPath (defaultconfig.ini.php or entry point's config.ini.php) to get it working.
The value should be at least :

    modulesPath="app:modules/"

You need an up and running installation of Sass on command line to get it working (e.g. on a Debian-based distro, ''apt-get install ruby-sass'' will do the trick).



Usage
=====

When including a CSS file (e.g. with addCSSLink()) you should set 'sass'=>true or 'scss'=>true as a param.

Another way of having a file compiled with Sass is including as file with .scss or .sass extension. You can set expected extensions in the comma-separated value of _CSSprepro\_jSass\_extensions_ under the _jResponseHtml_ section.

E.g. in your response :

`$this->addCSSLink($gJConfig->urlengine['basePath'].'themes/'.$gJConfig->theme.'/Css/style.sass');`

or

`$this->addCSSLink($gJConfig->urlengine['basePath'].'themes/'.$gJConfig->theme.'/Css/style.sassFile', array( 'sass' => true ));`


Your config file must activate jSass plugin :

    [jResponseHtml]
    plugins=jSass

N.B. : the directories containing Sass files should be writable by your web server ! Indeed, compiled files will be written in that very same directory so that relative urls go on working ...




Config
======

You can configure jSass's behviour regarding compilation:

    [jResponseHtml]
    ;...
    ; always|onchange|once
    CSSprepro_jSass_compile=always

If CSSprepro\_jSass\_compile's value is not valid or empty, its default value is onchange.

* always : compile Sass file on all requests
* onchange : compile Sass file only if it has changed
* once : compile Sass file once and never compile it again (until compiled file is removed)

