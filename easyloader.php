<?php 
/**
 * EASYLOADER.PHP v.1.0.0
 * 
 * Maybe the last autoloader you'll ever need for PHP scripts.  While it solves many of my problems,
 * it may not be the right fit for you.
 * 
 * Place this script in the root of a project and include in your entry point script.
 * 
 * Recurses all the directories looking for .PHP files to create an association between
 * the filename and a classname.  For instance class hello_world in a PHP file named hello_world.php 
 * will be cached as such.   As each instance of the easyloader generates a unique method to 
 * spi_autoload handler at call time it is able to handle many project subtrees provided
 * no naming collisions occur in whatever namespaces get included.
 * 
 * Published on December 11th 2016 as a result of significant annoyance with crappy implementations
 * like that found and used by composer.  In fact, dropping this into the root of many annoying 
 * composer projects and including easyloader instead often resolves awful bootstrap and autoload 
 * problems.
 *  
 * The MIT License (MIT)
 * Copyright (c) 2016 SkinnerCo Paul Skinner skinnerco.ca+easyloader@gmail.com 
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and 
 * associated documentation files (the "Software"), to deal in the Software without restriction, 
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, 
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, 
 * subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all copies or substantial 
 * portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING  BUT 
 * NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND 
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES 
 * OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR 
 * IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

// Generate a autoload method for this include.
$autoload_uuid = str_replace("-","_",md5(mt_rand(1,mt_getrandmax())));
$autoloader_method = "skinnerco_autoloader_". $autoload_uuid;
// manage the global array of autoloaders and their paths
if (!isset($autoloaders)) {
	$autoloaders = array();
	$autoloader_paths = array();
	$autoloader_debug = array();
}
// push this autoloader into the array of autoloaders
$autoloaders[$autoloader_method] = $autoload_uuid;
// and the paths
$autoloader_paths[$autoload_uuid][] = __DIR__;
// add other paths to recurse for php files mapping to class names
// $autoloader_paths[$autoload_uuid][] = "/security/crypto";

//indicate if we'd like to debug the autoload activities for an instance of easyloader.
$autoloader_debug[$autoload_uuid] = 0;
// write the autoloader method to eval below;
$function = "function $autoloader_method(\$class) {
	global \$autoloaders, \$autoloader_paths, \$autoloader_debug;
	static \$filecache = null;
	\$debug =0;
	
	\$autoloader_uuid = \$autoloaders[__FUNCTION__];
	if (isset(\$autoloader_debug[\$autoloader_uuid])) {
		\$debug = 1;
		openlog(\"easyloader.php\", LOG_PID | LOG_PERROR,LOG_USER );
	}
	if (\$filecache == null) {
		foreach (\$autoloader_paths[\$autoloader_uuid] as \$autoloader_classroot) {
			if (\$debug) {
				syslog(LOG_INFO,\"Recursing \$autoloader_classroot\");
			}
			
			\$dir_iterator = new RecursiveDirectoryIterator(\$autoloader_classroot);
			\$iterator = new RecursiveIteratorIterator(\$dir_iterator);
			\$files = new RegexIterator(\$iterator, '/^.+\.php\$/i', RecursiveRegexIterator::GET_MATCH);
			foreach (\$files as \$file) {
				\$info = pathinfo(\$file[0]);
				if (\$debug) {
					syslog(LOG_INFO,\"Mapping class \".\$info[\"filename\"] . \" to \" .\$file[0]);
				}
				
				\$filecache[\$info[\"filename\"]] = \$file[0];
			}
		}
	}
	
	if (isset(\$filecache[\$class])) {
		if (\$debug) {
			syslog(LOG_INFO,\"Including file \" . \$filecache[\$class]);
			closelog();
		}
		include(\$filecache[\$class]);
		return 1;
	}
	return 0;
}";
// eval the function 
eval($function);
// register the autoload function with PHP
spl_autoload_register($autoloader_method);
?>