<?php
/**
 * User: vagenas
 * Date: 9/11/14
 * Time: 9:53 PM
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @copyright 2015 Panagiotis Vagenas <pan.vagenas@gmail.com>
 */

namespace totos;

if (!defined('WPINC')) {
    die;
}

require_once dirname(dirname(dirname(__FILE__))).'/core/stub.php';

/**
 * Class framework
 *
 * @package totos
 * @since   150610
 *
 * @assert ($GLOBALS[__NAMESPACE__])
 *
 * @property \totos\xml        $©xml
 * @method \totos\xml          ©xml()
 *
 * @property \totos\totos      $©totos
 * @method \totos\totos      ©totos()
 */
class framework extends \xd__framework
{
    static function classMemStats($class)
    {
        $className = get_class($class);
        echo "<p>";
        echo "<strong>$className:</strong> - ( ".number_format(strlen(serialize($class)) / 1024, 2).'k )';
        echo "</p>";
    }
}

$GLOBALS[__NAMESPACE__] = new framework(
  array(
    'plugin_root_ns' => __NAMESPACE__, // The root namespace
    'plugin_var_ns'  => 'tts',
    'plugin_cap'     => 'manage_options',
    'plugin_name'    => 'Totos.gr XML Feed',
    'plugin_version' => '150110',
    'plugin_site'    => 'https://github.com/panvagenas/totos-xml-feed',

    'plugin_dir'     => dirname(dirname(dirname(__FILE__))) // Your plugin directory.

  )
);
