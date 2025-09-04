<?php

/**
 * LSCache English Language File
 *
 * Contains language strings for the LSCache addon module.
 */

$_ADDONLANG = [
    // General
    'lscache' => 'LSCache',
    'lscache_desc' => 'LiteSpeed LSCache integration for WHMCS',

    // Settings
    'enable' => 'Enable LSCache',
    'enable_desc' => 'Enable LiteSpeed LSCache for public pages',
    'ttl' => 'Public TTL (seconds)',
    'ttl_desc' => 'Time to live for cached public pages (default: 300)',
    'esi' => 'Enable ESI',
    'esi_desc' => 'Enable ESI for private content blocks (requires LSWS Enterprise)',
    'routes' => 'Cacheable Routes',
    'routes_desc' => 'Comma-separated list of cacheable route slugs or regex patterns',
    'vary_cookies' => 'Vary Cookies',
    'vary_cookies_desc' => 'Comma-separated list of cookie names for cache variations',
    'stale_on_purge' => 'Send Stale on Purge',
    'stale_on_purge_desc' => 'Serve stale content while purging',

    // Admin Interface
    'cache_status' => 'Cache Status',
    'tips' => 'Tips',
    'purge_cache' => 'Purge Cache',
    'purge_all' => 'Purge All',
    'purge_public' => 'Purge Public',
    'purge_announcements' => 'Purge Announcements',
    'purge_knowledgebase' => 'Purge Knowledgebase',
    'purge_downloads' => 'Purge Downloads',
    'purge_serverstatus' => 'Purge Server Status',
    'last_purge' => 'Last Purge',

    // Messages
    'activated' => 'LSCache addon has been activated successfully.',
    'deactivated' => 'LSCache addon has been deactivated successfully.',
    'purge_success' => 'Cache purged successfully',
    'no_purge_history' => 'No purge history available',

    // Tips
    'tip_anonymous' => 'Public pages are cached only for anonymous users',
    'tip_logged_in' => 'Logged-in users and sensitive pages (cart, login, admin) are never cached',
    'tip_post' => 'POST requests and pages with cookies are not cached',
    'tip_esi' => 'ESI requires LiteSpeed Web Server Enterprise edition',
    'tip_purge' => 'Use purge buttons to clear cache when content changes',
];
