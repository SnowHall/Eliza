<?php
/**
 * Eliza - Simple php acceptance testing framework
 *
 *
 * @author		SnowHall - http://snowhall.com
 * @website		http://elizatesting.com
 * @email		support@snowhall.com
 *
 * @version		0.2.0
 * @date		April 18, 2013
 *
 * Eliza - simple framework for BDD development and acceptance testing.
 * Eliza has user-friendly web interface that allows run and manage your tests from your favorite browser.
 *
 * Copyright (c) 2009-2013
 */

class Helpers {

  /**
   * Function opposite to standart PHP function nl2br. Replace <br> tags to EOL characters.
   *
   * @param string $text Processed text
   * @return string Text with replaces
   */
  public static function br2nl($text) {
    $text = preg_replace('/<br\s*[\/]?>/i',PHP_EOL,$text);
    return $text;
  }

  /**
   * Return form value from container (POST or array) by attribute
   *
   * @param array $containers Container - POST or manual array
   * @param string $attribute Form field name
   * @param string $defaultValue Default value if container has no attribute
   * @return string
   */
  public static function getFormValue($containers, $attribute, $defaultValue = '')
  {
    if (!is_array($containers)) $containers = array($containers);

    foreach($containers as $container) {
      if (isset($container[$attribute])) $returnValue = $container[$attribute];
    }

    if (empty($returnValue)) $returnValue = $defaultValue;

    return htmlspecialchars($returnValue);
  }

  /**
   * Finds files matching mask in the specified directory
   *
   * @param string $directoryPath Scanning directory path
   * @param string $mask File mask
   * @return array Matched files names
   */
  public static function scanDirectory($directoryPath, $mask = '*.php') {
    $files = scandir($directoryPath);

    $withMask = $mask ? !fnmatch($mask, $file) : false;
    foreach ($files as $key=>$file) {
      if ($file == '.' || $file == '..' || $withMask || fnmatch('*-local.php', $file)) {
        unset($files[$key]);
        continue;
      }
    }
    return $files;
  }

  /**
   * Return converted content of JSON file
   *
   * @param string $fileName JSON file name
   * @return array JSON decoded content
   */
  public static function getFromJson($fileName) {
    $items = array();
    if (file_exists($fileName)) {
      $items = json_decode(file_get_contents($fileName),true);
    }
    else {
      fopen($fileName, 'w');
    }
    return $items;
  }

	/**
	 * Check writable and readable file or directory
	 *
	 * @param string $path Absolute path to directory or file
	 * @return boolean True if path available
	 */
	public static function isPathAvailable($path) {
		if (is_writable($path) && is_readable($path)) return true;
		return false;
	}
}