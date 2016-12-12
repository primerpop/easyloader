# easyloader
A general purpose classmapping and auto include script for PHP.

  
  Maybe the last autoloader you'll ever need for PHP scripts.  While it solves many of my problems,
  it may not be the right fit for you.
  
  Place this script in the root of a project and include in your entry point script.
  
  Recurses all the directories looking for .PHP files to create an association between
  the filename and a classname.  For instance class hello_world in a PHP file named hello_world.php 
  will be cached as such.   As each instance of the easyloader generates a unique method to 
  spi_autoload handler at call time it is able to handle many project subtrees provided
  no naming collisions occur in whatever namespaces get included.
  
  Published on December 11th 2016 as a result of significant annoyance with crappy implementations
  like that found and used by composer.  In fact, dropping this into the root of many annoying 
  composer projects and including easyloader instead often resolves awful bootstrap and autoload 
  problems.

# Usage

Download or copy and paste into the directory containing your PHP classes and include it
in an entry point script as you normally would.

include("../easyloader.php");

