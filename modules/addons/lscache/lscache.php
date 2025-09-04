<?php

/**
 * LSCache WHMCS Addon Module
 *
 * This module provides LiteSpeed LSCache integration for WHMCS made by LivingGOD,
 * enabling safe caching of public pages while excluding sensitive areas.
 *
 * @package WHMCS\Addon\LSCache
 * @version 1.0.0
 * @author LivingGOD
 * @license Apache-2.0
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Define addon module configuration
 */
function lscache_config()
{
    return [
        'name' => 'LSCache',
        'description' => 'LiteSpeed LSCache integration for WHMCS - safely caches public pages',
        'version' => '1.0.0',
        'author' => 'LivingGOD',
        'fields' => [
            'enable' => [
                'FriendlyName' => 'Enable LSCache',
                'Type' => 'yesno',
                'Size' => '25',
                'Description' => 'Enable LiteSpeed LSCache for public pages',
                'Default' => 'yes',
            ],
            'ttl' => [
                'FriendlyName' => 'Public TTL (seconds)',
                'Type' => 'text',
                'Size' => '25',
                'Description' => 'Time to live for cached public pages (default: 300)',
                'Default' => '300',
            ],
            'esi' => [
                'FriendlyName' => 'Enable ESI',
                'Type' => 'yesno',
                'Size' => '25',
                'Description' => 'Enable ESI for private content blocks (requires LSWS Enterprise)',
                'Default' => 'no',
            ],
            'routes' => [
                'FriendlyName' => 'Cacheable Routes',
                'Type' => 'textarea',
                'Rows' => '5',
                'Cols' => '50',
                'Description' => 'Comma-separated list of cacheable route slugs or regex patterns',
                'Default' => 'home,announcements,knowledgebase,downloads,serverstatus',
            ],
            'vary_cookies' => [
                'FriendlyName' => 'Vary Cookies',
                'Type' => 'text',
                'Size' => '50',
                'Description' => 'Comma-separated list of cookie names for cache variations',
                'Default' => '',
            ],
            'stale_on_purge' => [
                'FriendlyName' => 'Send Stale on Purge',
                'Type' => 'yesno',
                'Size' => '25',
                'Description' => 'Serve stale content while purging',
                'Default' => 'no',
            ],
        ],
    ];
}

/**
 * Activate the addon module
 */
function lscache_activate()
{
    return [
        'status' => 'success',
        'description' => 'LSCache addon has been activated successfully.',
    ];
}

/**
 * Deactivate the addon module
 */
function lscache_deactivate()
{
    return [
        'status' => 'success',
        'description' => 'LSCache addon has been deactivated successfully.',
    ];
}

/**
 * Upgrade function (if needed)
 */
function lscache_upgrade($vars)
{
    $version = $vars['version'];

    // Handle version-specific upgrades here
    // Example:
    // if ($version < 1.1) {
    //     // Upgrade logic
    // }
}
