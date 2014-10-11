<?php

/**
 * @file
 * Lists all my films and episodes
 */

// Locations where my files and series are
define('SERIES', 'C:/MediaShare/TV Shows');
define('FILMS', 'C:/MediaShare/Films');

// Get the directories I care about
$directories   = glob(SERIES . '/*', GLOB_ONLYDIR);
$directories[] = FILMS;

// The file types I care about
$types = array(
  '.mkv',
  '.avi',
  '.mp4'
);

// For all the container directories (eg Series name)
foreach ($directories as $dir) {
  // Make the printable h4 title
  $name  = str_replace(SERIES . '/', '', $dir);
  $name  = str_replace(FILMS, 'Films', $name);
  $array = getDirectory($dir);
  // If there are files in this directory
  if (!empty($array)) {
    print "<h4>$name</h4>";
    print '<ul>';
    // write out the list
    foreach ($array as $directory => $files) {
      if (is_array($files)) {
        foreach ($files as $file) {
          // But only the ones worth watching ;)
          if (!empty($types)) {
            $ext = strtolower(mb_substr($file, (4 * -1)));
            if (in_array($ext, $types)) {
              $url = $directory . '/' . $file;
              print '<li><a href="file://' . $url . '">' . $file . '</a></li>';
            }
            // Or all of them
          } else {
            $url = $directory . '/' . $file;
            print '<li><a href="file://' . $url . '">' . $file . '</a></li>';
          }
        }
      }
    }
    print '</ul>';
  }
}

/**
 * Get me all the things
 */
function getDirectory($path = '.') {
  $dirTree     = array();
  $dirTreeTemp = array();
  $ignore[]    = '.';
  $ignore[]    = '..';
  $dh          = @opendir($path);
  
  while (false !== ($file = readdir($dh))) {
    if (!in_array($file, $ignore)) {
      if (!is_dir("$path/$file")) {
        $dirTree["$path"][] = $file;
      } else {
        $dirTreeTemp = getDirectory("$path/$file", $ignore);
        if (is_array($dirTreeTemp)) {
          $dirTree = array_merge($dirTree, $dirTreeTemp);
		}
      }
    }
  }
  closedir($dh);
  
  return $dirTree;
}
