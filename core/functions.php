<?php
/**
 * File that declares basic PHP function overrides to the namespace 'laabs'
 * @package core
 */
namespace laabs;

use core\Type\ArrayObject;

/**
 *  Symbolic links creation for Windows systems
 *  @param string $target The path to target file/dir
 *  @param string $link   The path of the symlink to create
 *
 *  @return bool True if creation of link succeeded
 */
function symlink($target, $link)
{
    if (DIRECTORY_SEPARATOR == "\\") {
        $target = str_replace('/', DIRECTORY_SEPARATOR, $target);
        $link = str_replace('/', DIRECTORY_SEPARATOR, $link);
        $param = false;
        if (is_dir($target)) {
            $param = "/D";
        }
        $output = array();
        $return = false;
        $cmd = 'mklink ' . $param . ' "' . $link . '" "' . $target . '"';

        exec($cmd, $output, $return);

        if ($return > 0) {
            return false;
        } else {
            return true;
        }
    } else {
        return \symlink($target, $link);
    }
}

/**
 * Generates a local unique identifier
 * Improvements : the use of base conversion to get short ids with good entropy
 * @param string $prefix      A prefix for the generated id, if a specific class of characters is needed (XML ids must start with alpha)
 * @param bool   $moreEntropy Use more entropy
 *
 * @return string The unique id
 */
function uniqid($prefix = "", $moreEntropy = true)
{
    $parts = \explode(' ', microtime());
    $sec = $parts[1];
    if (!isset($parts[0])) {
        $msec = 0;
    } else {
        // Only using decimal part of microseconde
        $msec = substr($parts[0], strpos($parts[0], '.') + 1);
    }

    $uniqid = str_pad(base_convert($sec, 10, 36), 6, '0', STR_PAD_LEFT) . '-' . str_pad(base_convert($msec, 10, 36), 4, '0', STR_PAD_LEFT);

    if ($moreEntropy) {
        $uniqid .= '-' . str_pad(base_convert(mt_rand(), 10, 36), 6, '0', STR_PAD_LEFT);
    }

    return $prefix . $uniqid;
}

/**
 * Explode a string into an array
 * Improvements : Always returns an array and removes empty items
 * @param string $delimiter The delimiter string
 * @param string $string    The string to explode
 * @param bool   $noEmpty   If true, empty strings will not be included
 *
 * @return array The array of exploded values, empty array if empty string
 */
function explode($delimiter, $string, $noEmpty = true)
{
    $array = \explode($delimiter, $string);

    if (!$array) {
        return array();
    }

    if ($noEmpty) {
        $array = array_map("trim", $array);
        foreach ($array as $index => $value) {
            if ($value === "") {
                unset($array[$index]);
            }
        }
    }

    return array_values($array);
}

/**
 * Implode an array into a string
 * Improvements : Empty rows can be excludedto
 * @param string $glue    The glue string
 * @param array  $array   The array to implode
 * @param bool   $noEmpty If true, empty items will not be included into string
 *
 * @return string The string of imploded values, empty string if empty array
 */
function implode($glue, array $array, $noEmpty = true)
{
    $arrayValues = array_values($array);

    if ($noEmpty) {
        foreach ($arrayValues as $index => $value) {
            if (trim($value) == "") {
                unset($arrayValues[$index]);
            }
        }
    }

    return \implode($glue, $arrayValues);
}



/**
 * Returns the basename of a path including class names and namespaces
 *
 * Improvements : works on file system path and php namespaces
 * @param string $name   a name
 * @param string $suffix a suffix
 *
 * @return string The base name
 */
function basename($name, $suffix = null)
{
    if (LAABS_NS_SEPARATOR == DIRECTORY_SEPARATOR || strpos($name, LAABS_NS_SEPARATOR) === false) {
        return \basename($name, $suffix);
    }

    $filename = str_replace(LAABS_NS_SEPARATOR, DIRECTORY_SEPARATOR, $name);

    return \basename($filename, $suffix);
}

/**
 * Returns the dirname of a path including class names and namespaces
 * Improvements : works on file system path and php namespaces
 * @param string $name
 *
 * @return string the directory or namespace
 */
function dirname($name)
{
    if (LAABS_NS_SEPARATOR == DIRECTORY_SEPARATOR || strpos($name, LAABS_NS_SEPARATOR) === false) {
        return \dirname($name);
    }

    $filename = str_replace(LAABS_NS_SEPARATOR, DIRECTORY_SEPARATOR, $name);

    return str_replace(DIRECTORY_SEPARATOR, LAABS_NS_SEPARATOR, \dirname($filename));

    if (strpos($name, LAABS_NS_SEPARATOR) !== false) {
        $array = @explode(LAABS_NS_SEPARATOR, $name);

        return implode(LAABS_NS_SEPARATOR, array_splice($array, 0, -1));
    }

    return \dirname($name);
}

/**
 * Tokenizes a string
 * Improvements : tokenizes all strings even non php (no open tag), removes open tag
 * @param string $string The string to tokenize
 *
 * @return array The tokens
 */
function token_get_all($string)
{
    if (strpos("<?php ", $string) === false) {
        $string = "<?php " . $string;
    }

    $tokens = \token_get_all($string);

    // Ignore php open tag
    $phpOpenTag = array_shift($tokens);

    // Force token array structure and set offset
    $offset = 0;
    foreach ($tokens as $i => $token) {
        if (is_scalar($token)) {
            $tokens[$i] = array(false, $token, false);
        } else {
            $tokens[$i][3] = $offset;
        }
    }

    return $tokens;
}

/**
 * List the traits used by a given class
 * Improvements : Ability to go deep and return traits used by traits and traits used by parent classes with no limitation
 * @param string $class    The class to list traits of
 * @param bool   $autoload Use autoload or not
 * @param bool   $deep     List traits of traits and traits of ancestors
 *
 * @return array An array of the unique traits used by the class
 */
function class_uses($class, $autoload = true, $deep = true)
{
    if (!$deep) {
        return \class_uses($class);
    }

    $traits = array();
    if (is_object($class)) {
        $class = get_class($class);
    }

    // Get traits of all ancestor classes
    do {
        $traits = array_merge(\class_uses($class, $autoload), $traits);
    } while ($class = get_parent_class($class));

    // Get traits of all ancestor traits
    $ancestorTraits = $traits;
    while (!empty($ancestorTraits)) {
        $traitTraits = \class_uses(array_pop($ancestorTraits), $autoload);
        $traits = array_merge($traitTraits, $traits);
        $ancestorTraits = array_merge($traitTraits, $ancestorTraits);
    };

    foreach ($traits as $trait => $same) {
        $traits = array_merge(\class_uses($trait, $autoload), $traits);
    }

    return array_unique($traits);
}

/**
 * Returns the type of a variable
 * Improvements : Returns the class of var is an object and $class set true
 * @param mixed  $var   The variable to type check
 * @param string $class Return the class of object instead of php base type 'object'
 *
 * @return string The type of the variable
 * Possible values are
 *  * "boolean"
 *  * "integer"
 *  * "double"
 *  * "string"
 *  * "array"
 *  * "object" or the object class if claa name requested
 *  * "resource"
 *  * "NULL"
 *  * "unknown type"
 */
function gettype($var, $class = true)
{
    $type = \gettype($var);

    switch ($type) {
        case 'object':
            if ($class) {
                return \get_class($var);
            } else {
                return $type;
            }
            break;

        case 'double':
        case 'real':
            return 'float';

        case 'int':
        case 'integer':
            return 'integer';

        case 'bool':
        case 'boolean':
            return 'boolean';

        case 'NULL':
            return 'null';
    }

    return $type;
}

/**
 * Checks if an array is associative
 * @param array $array The array to check
 *
 * @return bool
 */
function is_assoc($array)
{
    return (bool) count(array_filter(array_keys($array), 'is_string'));
}

/**
 * Get constans value from its name
 * Improvements : Checks constant is defined before returning value. If not, return constant name
 * @param string $name The name of the constant
 *
 * @return mixed The value of the defined constant or the name if not defined
 */
function constant($name)
{
    if (defined($name)) {
        return \constant($name);
    }

    return $name;
}

/**
 * Calculate the MD5 hash value for a string
 * Get a 25 bit MD5 instead of 32 (base 36)
 * @param string $str       The string to hash
 * @param bool   $rawOutput Return as raw string
 * @param bool   $short     Return short 25 MD5 instead of 32
 *
 * @return $string The MD5
 */
function md5($str, $rawOutput=false, $short=true)
{
    // If short not requested, return classic MD5
    if (!$short) {
        return \md5($str, $rawOutput);
    }

    $md5 = \md5($str);
    $rawMd5 = \md5($str, true);

    $shortMd5 = \base_convert($md5, 16, 36);

    // If raw output requested, re-convert new hash to its character representation (base36 to chr)
    if ($rawOutput) {
        $base36Chunks = str_split($shortMd5, 2);
        $base36Chrs = array();
        foreach ($base36Chunks as $base36Chunk) {
            $base36Chrs[] = chr(\base_convert($base36Chunk, 36, 10));
        }

        $shortMd5 = implode('', $base36Chrs);
    }

    return $shortMd5;
}

/**
 * Base conversion that accepts 64 also
 * @param string  $number   The number to convert
 * @param integer $frombase The original encoding
 * @param integer $tobase   The target encoding
 *
 * @return string
 */
function base_convert($number, $frombase, $tobase)
{
    if ($tobase != 64 && $frombase != 64 ) {
        return \base_convert($number, $frombase, $tobase);
    }

    $base64 = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_";

    if ($tobase == 64) {
        $b64 = "";

        if ($frombase != 10) {
            $dec = \base_convert($number, $frombase, 10);
        } else {
            $dec = $number;
        }

        for ($t = ($dec != 0 ? floor(log($dec, 64)) : 0); $t >= 0; $t--) {
            $bcp = bcpow(64, $t);
            $a   = floor($dec / $bcp) % 64;
            $b64 = $b64 . substr($base64, $a, 1);
            $dec  = $dec - ($a * $bcp);
        }

        return $b64;
    }

    if ($frombase == 64) {
        $dec = "";

        $len = strlen($number) - 1;

        for ($t = $len; $t >= 0; $t--) {
          $bcp = bcpow(64, $len - $t);
          $dec = $dec + strpos($base64, substr($number, $t, 1)) * $bcp;
        }

        if ($tobase != 10) {
            $number = \base_convert($dec, 10, $tobase);
        } else {
            $number = $dec;
        }

        return $number;
    }
}


/**
 * Format a local date/time
 * Improvements: supports microseconds
 * @param string  $format    The format for the date/time. See php.net . Default is ISO format
 * @param integer $timestamp The timestamp to format, if null the current timestamp is used
 *
 * @return string
 */
function date($format="Y-m-d\TH:i:s.uP", $timestamp=false)
{
    if (!$timestamp) {
        $timestamp = \date('Y-m-d\TH:i:s') . substr(microtime(), 1, 9);
    }

    $datetime = new \DateTime($timestamp);

    return $datetime->format($format);
}

/**
 * Format a gmt date/time
 * Improvements: supports microseconds
 * @param string  $format    The format for the date/time. See php.net . Default is ISO format
 * @param integer $timestamp The timestamp to format, if null the current timestamp is used
 *
 * @return string
 */
function gmdate($format="Y-m-d\TH:i:s.u\Z", $timestamp=false)
{
    if (!$timestamp) {
        $timestamp = \gmdate('Y-m-d\TH:i:s') . substr(microtime(), 1, 9);
    }

    $datetime = new \DateTime($timestamp);

    return $datetime->format($format);
}

/**
 * Coalesce empty values
 * @param mixed $value       The input value
 * @param mixed $replacement The value to return if value is empty
 *
 * @return mixed
 */
function coalesce($value, $replacement)
{
    if (empty($value)) {
        return $replacement;
    }

    return $value;
}

/**
 * Create a new tmp file opened in w+ (read+write) and return handler
 * Improvements : creates directory, uses laabs tmp directory
 *
 * @return resource
 */
 function tmpfile()
 {
    $dir = \laabs::getTmpDir();
    if (!is_dir($dir)) {
        mkdir($dir, 0755);
    }

    $uid = \laabs\uniqid();

    $filename = $dir . DIRECTORY_SEPARATOR . $uid;

    return fopen($filename, "w+");
 }

/**
 * Create a new tmp file and return filename
 * Improvements : creates directory, uses laabs tmp directory
 * @param string $dir
 * @param string $prefix
 *
 * @return string
 */
function tempnam($dir=false, $prefix=false)
{
    if (!$dir) {
        $dir = \laabs::getTmpDir();
    }

    if (!is_dir($dir)) {
        mkdir($dir, 0755);
    }

    return \tempnam($dir, $prefix);
}

/**
 * Create a new tmp dir and return path
 * Improvements : Not a php function
 * @param string $prefix
 *
 * @return string
 */
function tempdir($prefix="")
{
    $dir = \laabs::getTmpDir();

    $dirname = $dir . DIRECTORY_SEPARATOR . \laabs\uniqid($prefix);

    while (is_dir($dirname)) {
        $dirname = $dir . DIRECTORY_SEPARATOR . \laabs\uniqid($prefix);
    }

    mkdir($dirname, 0775);

    return $dirname;
}

/**
 * Checks if a file or directory exists on the file system
 * Improvements : accepts a case-insensitive match
 * @param string  $filename
 * @param boolean $matchcase
 *
 * @return boolean
 */
function file_exists($filename, $matchcase=false)
{
    if (\file_exists($filename)) {
        return true;
    }

    if ($matchcase == true) {
        return false;
    }

    $dir = dirname($filename);

    $files = glob($dir . '/*');

    $lcaseFilename = strtolower($filename);
    foreach ($files as $file) {
        if (strtolower($file) == $lcaseFilename) {
            return true;
        }
    }

    return false;
}

/**
 * Get the real canonical path of a file
 * Improvements : Case insensitive
 * @param string $filename
 *
 * @return string
 */
function realpath($filename)
{
    $dirname = \laabs\dirname($filename);

    $dirfiles = glob($dirname . DIRECTORY_SEPARATOR . '*');
    $lfilename = strtolower($filename);
    foreach ($dirfiles as $dirfile) {
        if (strtolower($dirfile) == $lfilename) {
            return \realpath($dirfile);
        }
    }
}

/**
 * Remove directory
 * Improvements : Recursive
 * @param string $dirname
 * @param bool   $recurse
 *
 * @return bool
 */
function rmdir($dirname, $recurse = true)
{
    $objects = scandir($dirname);
    foreach ($objects as $object) {
        if ($object != "." && $object != "..") {
            if (is_dir($dirname.DIRECTORY_SEPARATOR.$object)) {
                rmdir($dirname.DIRECTORY_SEPARATOR.$object, true);
            } else {
                unlink($dirname.DIRECTORY_SEPARATOR.$object);
            }
        }
    }
    \rmdir($dirname);

    return true;
}

/**
 * Checks if a value exists in an array
 *
 * @param $value
 * @param $object
 *
 * @return bool
 */
function in_array($value, $object)
{
    if ($object instanceof \ArrayObject) {
        $object = $object->getArrayCopy();
    }

    if (\in_array($value, $object)) {
        return true;
    }

    return false;
}

/**
 * Calculates hash from a resource
 *
 * @param string   $algo
 * @param resource $handler
 *
 * @return string
 */
function hash_stream($algo, $handler)
{
    $metadata = stream_get_meta_data($handler);

    if ($metadata['wrapper_type'] == 'plainfile') {
        $hash = strtolower(hash_file($algo, $metadata['uri']));
    } else {
        $tmpfile = tempnam();
        $tmphdl = fopen($tmpfile, 'w');
        stream_copy_to_stream($handler, $tmphdl);
        rewind($handler);
        fclose($tmphdl);
        $hash = strtolower(hash_file($algo, $tmpfile));
        unlink($tmpfile);
    }

    return $hash;
}
