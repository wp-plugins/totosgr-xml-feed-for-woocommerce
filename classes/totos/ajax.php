<?php
/**
 * Created by PhpStorm.
 * User: Vagenas Panagiotis <pan.vagenas@gmail.com>
 * Date: 17/10/2014
 * Time: 10:11 μμ
 */

namespace totos;

if (!defined('WPINC')) {
    exit('Do NOT access this file directly: '.basename(__FILE__));
}

/**
 * Class ajax
 *
 * @package totos
 * @author  Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @since   141017
 */
class ajax extends \xd_v141226_dev\ajax
{
    /**
     * Generates totos.xml
     *
     * @important AJAX HOOKED
     * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since     141017
     */
    public function ®ajaxGenerateTotosXML()
    {
        if (!$this->©user->is_super_admin()) {
            $this->sendJSONError('Authorization failed', 401);
        }

        $foundProducts = $this->©totos->do_your_woo_stuff();
        if ($foundProducts > 0) {
            $this->sendJSONSuccess(array('result' => true, 'productsUpdated' => $foundProducts));
        } else {
            $this->sendJSONError($this->__('No products found'), 200);
        }
    }
}
