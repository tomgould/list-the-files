<?php
/**
 * @file
 * Lists all the things
 */

// Locations where my files and series are
define('SERIES',   'C:/path/to/TV Shows');
define('FILMS',    'C:/path/to/Films');
define('INCOMING', 'C:/path/to/Incoming');

// Get the directories I care about
$directories[] = INCOMING;
$directories[] = FILMS;
$directories = array_merge($directories, glob(SERIES . '/*', GLOB_ONLYDIR));

// The file types I care about
$types = array(
  '.mkv',
  '.avi',
  '.mp4'
);

// For all the directories
foreach($directories as $dir) {

  // Make the printable h4 title
  $name = str_replace(SERIES . '/', '', $dir);
  $name = str_replace(FILMS, 'Films', $name);
  $name = str_replace(INCOMING, 'Incoming', $name);
  $array = get_directory($dir);

  // If there are files in this directory
  if (!empty($array)) {
    print "<h4>$name</h4>";
    
    // Make a sortable array
    $container = array();
    foreach($array as $directory => $files) {
      if (is_array($files)) {
        foreach($files as $file) {
          if (!empty($types)) {
            $ext = strtolower(mb_substr($file, (4 * -1)));
            if (in_array($ext, $types)) {
              $url = $directory . '/' . $file;
              $container[mb_strtoupper(str_replace(' ', '.', $file)) ] = array(
                'file' => $file,
                'url' => $url,
              );
            }
          }
          else {
            $url = $directory . '/' . $file;
            $container[mb_strtoupper(str_replace(' ', '.', $file)) ] = array(
              'file' => $file,
              'url' => $url,
            );
          }
        }
      }
    }

    ksort($container);
    print '<ul>';
    foreach($container as $item) {
      print '<li><a href="file://' . $item['url'] . '">' . $item['file'] . '</a></li>';
    }
    print '</ul>';
  }
}

/**
 * Get me all the things
 */
function get_directory($path = '.') {
  $dirTree = array();
  $dirTreeTemp = array();
  $ignore[] = '.';
  $ignore[] = '..';
  $dh = @opendir($path);
  while (false !== ($file = readdir($dh))) {
    if (!in_array($file, $ignore)) {
      if (!is_dir($path . '/' . $file)) {
        $dirTree["$path"][] = $file;
      }
      else {
        $dirTreeTemp = get_directory($path . '/' . $file, $ignore);
        if (is_array($dirTreeTemp)) {
          $dirTree = array_merge($dirTree, $dirTreeTemp);
        }
      }
    }
  }
  closedir($dh);
  
  return $dirTree;
}