<?php

/**
 * @package core
 */

use \Composer\Semver\Comparator;

/**
 * The VersionComparator provides functions to compare semver compatible versions.
 * It uses \Composer\Semver\Comparator under the hood but translate the `.x`
 * notation used in Symphony in `.*` for semver.
 * @since Symphony 3.0.0
 */
class VersionComparator
{
    /**
     * The base version used to compare
     *
     * @var string
     */
    private $base;

    /**
     * Creates a new version comparator object, based on $version.
     *
     * @param string $version
     */
    public function __construct($version)
    {
        General::ensureType([
            'version' => ['var' => $version, 'type' => 'string'],
        ]);
        $this->base = $version;
    }

    public function fixVersion($version)
    {
        $version = str_replace('.x', '.*', $version);
        return $version;
    }

    public function lessThan($version)
    {
        return Comparator::lessThan($this->base, $this->fixVersion($version));
    }

    public function greaterThan($version)
    {
        return Comparator::greaterThan($this->base, $this->fixVersion($version));
    }

    public function compareTo($version)
    {
        $version = $this->fixVersion($version);
        if ($this->base === $version) {
            return 0;
        }
        return $this->lessThan($version) ? -1 : 1;
    }

    public static function compare($a, $b)
    {
        return (new VersionComparator($a))->compareTo($b);
    }
}
