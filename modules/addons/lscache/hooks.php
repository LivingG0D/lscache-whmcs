<?php

/**
 * LSCache WHMCS Hooks
 *
 * This file contains hooks to integrate LiteSpeed LSCache with WHMCS.
 * It emits appropriate cache headers based on page type and user status.
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

/**
 * LSCache Service Class
 *
 * Provides static methods for cache control operations.
 */
class Lscache_Service
{
    /**
     * Get allowlist of cacheable routes from settings
     *
     * @return array
     */
    public static function allowlist()
    {
        $routes = Capsule::table('tbladdonmodules')
            ->where('module', 'lscache')
            ->where('setting', 'routes')
            ->value('value');

        if (!$routes) {
            return ['home', 'announcements', 'knowledgebase', 'downloads', 'serverstatus'];
        }

        return array_map('trim', explode(',', $routes));
    }

    /**
     * Check if URI is sensitive and should not be cached
     *
     * @param string $uri
     * @return bool
     */
    public static function isSensitiveUri($uri)
    {
        $sensitivePatterns = [
            '/admin/',
            '/clientarea\.php',
            '/cart\.php',
            '/viewinvoice\.php',
            '/creditcard\.php',
            '/dl\.php',
            '/login\.php',
            '/logout\.php',
        ];

        foreach ($sensitivePatterns as $pattern) {
            if (preg_match($pattern, $uri)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set public cache headers
     *
     * @param string $section
     * @param int $ttl
     * @param bool $esi
     * @param array $varyCookies
     */
    public static function setPublicCache($section, $ttl, $esi, $varyCookies)
    {
        $cacheControl = "public,max-age={$ttl}";
        if ($esi) {
            $cacheControl .= ",esi=on";
        }

        header("X-LiteSpeed-Cache-Control: {$cacheControl}");
        header("X-LiteSpeed-Tag: whmcs:public,{$section}");

        if (!empty($varyCookies)) {
            $vary = 'cookie=' . implode(',cookie=', $varyCookies);
            header("X-LiteSpeed-Vary: {$vary}");
        }
    }

    /**
     * Set no-cache headers
     */
    public static function setNoCache()
    {
        header('X-LiteSpeed-Cache-Control: no-cache');
    }

    /**
     * Purge all cache
     */
    public static function purgeAll()
    {
        header('X-LiteSpeed-Purge: *');
    }

    /**
     * Purge by tag
     *
     * @param string $tag
     */
    public static function purgeByTag($tag)
    {
        header("X-LiteSpeed-Purge: tag={$tag}");
    }
}

/**
 * Get addon setting value
 *
 * @param string $setting
 * @return mixed
 */
function lscache_get_setting($setting)
{
    return Capsule::table('tbladdonmodules')
        ->where('module', 'lscache')
        ->where('setting', $setting)
        ->value('value');
}

/**
 * Check if LSCache is enabled
 *
 * @return bool
 */
function lscache_is_enabled()
{
    return lscache_get_setting('enable') === 'on';
}

/**
 * Common cache logic for client area pages
 *
 * @param array $vars
 * @param string $section
 */
function lscache_handle_page($vars, $section)
{
    if (!lscache_is_enabled()) {
        return;
    }

    // Check if user is logged in
    if (isset($vars['loggedin']) && $vars['loggedin']) {
        Lscache_Service::setNoCache();
        return;
    }

    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'HEAD') {
        Lscache_Service::setNoCache();
        return;
    }

    // Check for sensitive URI
    $uri = $_SERVER['REQUEST_URI'];
    if (Lscache_Service::isSensitiveUri($uri)) {
        Lscache_Service::setNoCache();
        return;
    }

    // Check if section is in allowlist
    $allowlist = Lscache_Service::allowlist();
    if (!in_array($section, $allowlist)) {
        Lscache_Service::setNoCache();
        return;
    }

    // Set public cache
    $ttl = (int) lscache_get_setting('ttl') ?: 300;
    $esi = lscache_get_setting('esi') === 'on';
    $varyCookies = array_map('trim', explode(',', lscache_get_setting('vary_cookies') ?: ''));

    Lscache_Service::setPublicCache($section, $ttl, $esi, $varyCookies);
}

/**
 * Hook for homepage
 */
add_hook('ClientAreaHomepage', 1, function($vars) {
    lscache_handle_page($vars, 'home');
});

/**
 * Hook for announcements page
 */
add_hook('ClientAreaPageAnnouncements', 1, function($vars) {
    lscache_handle_page($vars, 'announcements');
});

/**
 * Hook for knowledgebase page
 */
add_hook('ClientAreaPageKnowledgebase', 1, function($vars) {
    lscache_handle_page($vars, 'knowledgebase');
});

/**
 * Hook for downloads page
 */
add_hook('ClientAreaPageDownloads', 1, function($vars) {
    lscache_handle_page($vars, 'downloads');
});

/**
 * Hook for server status page
 */
add_hook('ClientAreaPageServerStatus', 1, function($vars) {
    lscache_handle_page($vars, 'serverstatus');
});

/**
 * Fallback hook for other pages
 */
add_hook('ClientAreaPage', 1, function($vars) {
    if (!lscache_is_enabled()) {
        return;
    }

    // Determine section from filename or template
    $section = '';
    if (isset($vars['filename'])) {
        $filename = $vars['filename'];
        if ($filename === 'index') {
            $section = 'home';
        } elseif ($filename === 'announcements') {
            $section = 'announcements';
        } elseif ($filename === 'knowledgebase') {
            $section = 'knowledgebase';
        } elseif ($filename === 'downloads') {
            $section = 'downloads';
        } elseif ($filename === 'serverstatus') {
            $section = 'serverstatus';
        }
    }

    if ($section) {
        lscache_handle_page($vars, $section);
    } else {
        // Default to no-cache for unknown pages
        Lscache_Service::setNoCache();
    }
});
