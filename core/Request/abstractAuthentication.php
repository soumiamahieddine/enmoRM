<?php
namespace core\Request;
/**
 * Class to represent authentication
 */
abstract class abstractAuthentication
{
    public static $mode;

    protected function httpDigestParse($authDigest)
    {
        // protection contre les données manquantes
        $neededParts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
        $data = array();
        $keys = implode('|', array_keys($neededParts));
     
        preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $authDigest, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            $data[$m[1]] = $m[3] ? $m[3] : $m[4];
            unset($neededParts[$m[1]]);
        }

        return $neededParts ? false : $data;
    }
}