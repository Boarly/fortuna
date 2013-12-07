<?php

/**
 * view helper class to output gravatar image urls
 * by email
 * 
 * @package Fortuna
 * @subpackage Fortuna_View_Helper
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_View_Helper_Gravatar extends Zend_View_Helper_Abstract {
 

    const URL = "http://www.gravatar.com/avatar.php";
 
 
    /**
     * Simple Gravatar View Helper
     * 
     * @param string  $email  the email address the gravatar is registered with
     * @param string  $rating the image rating to diplay at most
     * @param integer $size   size of gravate to request
     * 
     * @return string
     */
    public function gravatar($email, $rating = 'G', $size = 48) {
 
        $params = array(
            'gravatar_id' => md5(strtolower($email)),
            'rating'      => $rating,
            'size'        => $size
        );
 
        return self::URL . '?' . http_build_query($params, '', '&amp;');
    }
}