<?php
namespace Framework;

/**
 * Class Config
 *
 * @package Framework
 */
class Config
{

    public static function get( $key, $default = null ) {
        if ( ! self::has($key) ) return $default;
        
        $key_array = preg_split('/\./', $key );
        $return = self::$data;

        foreach($key_array as $k) {
            if ( !isset($return[$k]) ) return $default;
            $return = $return[$k];
        }
        return $return;
    }
    
    public static function has( $key ) {
        $key_array = preg_split('/\./', $key );
        if ( !isset($key_array[0]) ) return false;

        if ( !isset(self::$data[$key_array[0]])) {
            self::$data[$key_array[0]] =
                include __APP__ . '/config/' . $key_array[0] . '.php';
            if ( self::$data[$key_array[0]] == false ) {
                self::$data[$key_array[0]] = null;
            }
        }

        foreach($key_array as $k) {
            if ( !isset($return[$k]) ) return false;
        }

        return true;
    }

    private static $data;
}
