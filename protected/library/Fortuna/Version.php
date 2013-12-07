<?php

/**
 * class containing installed version of the Fortuna lib
 * 
 * @package Fortuna
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
final class Fortuna_Version {
    const VERSION = '1.0';

    /**
     * Compare the specified version string $version
     * with the current VERSION.
     *
     * @param  string  $version  A version string (e.g. "0.7.1").
     * @since  1.0
     * @return int           -1 if the $version is older,
     *                           0 if they are the same,
     *                           and +1 if $version is newer.
     *
     */
    public static function compareVersion($version) {
        $version = strtolower($version);
        $version = preg_replace('/(\d)pr(\d?)/', '$1a$2', $version);
        return version_compare($version, strtolower(self::VERSION));
    }
}
