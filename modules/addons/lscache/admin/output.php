<?php

/**
 * LSCache Admin Output
 *
 * Provides the admin interface for LSCache management,
 * including cache status and purge controls.
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

/**
 * Main admin output function
 */
function lscache_output($vars)
{
    // Check CSRF token for POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        check_token();
    }

    // Handle purge actions
    if (isset($_POST['action'])) {
        handle_purge_action($_POST['action']);
        exit; // Prevent double output
    }

    // Get current settings
    $settings = get_lscache_settings();

    // Display admin interface
    echo '<div class="lscache-admin">';
    echo '<h2>LiteSpeed LSCache Management</h2>';

    // Status section
    echo '<div class="status-section">';
    echo '<h3>Cache Status</h3>';
    echo '<p><strong>Enabled:</strong> ' . ($settings['enable'] === 'on' ? 'Yes' : 'No') . '</p>';
    echo '<p><strong>TTL:</strong> ' . ($settings['ttl'] ?: '300') . ' seconds</p>';
    echo '<p><strong>ESI:</strong> ' . ($settings['esi'] === 'on' ? 'Enabled' : 'Disabled') . '</p>';
    echo '<p><strong>Cacheable Routes:</strong> ' . ($settings['routes'] ?: 'home,announcements,knowledgebase,downloads,serverstatus') . '</p>';
    echo '<p><strong>Vary Cookies:</strong> ' . ($settings['vary_cookies'] ?: 'None') . '</p>';
    echo '<p><strong>Stale on Purge:</strong> ' . ($settings['stale_on_purge'] === 'on' ? 'Yes' : 'No') . '</p>';
    echo '</div>';

    // Tips section
    echo '<div class="tips-section">';
    echo '<h3>Tips</h3>';
    echo '<ul>';
    echo '<li>Public pages are cached only for anonymous users</li>';
    echo '<li>Logged-in users and sensitive pages (cart, login, admin) are never cached</li>';
    echo '<li>POST requests and pages with cookies are not cached</li>';
    echo '<li>ESI requires LiteSpeed Web Server Enterprise edition</li>';
    echo '<li>Use purge buttons to clear cache when content changes</li>';
    echo '</ul>';
    echo '</div>';

    // Purge controls
    echo '<div class="purge-section">';
    echo '<h3>Purge Cache</h3>';
    echo '<form method="post" action="' . $_SERVER['REQUEST_URI'] . '">';
    echo '<input type="hidden" name="action" value="purge_all">';
    echo '<button type="submit" class="btn btn-danger">Purge All</button>';
    echo '</form>';

    echo '<form method="post" action="' . $_SERVER['REQUEST_URI'] . '" style="display:inline;">';
    echo '<input type="hidden" name="action" value="purge_public">';
    echo '<button type="submit" class="btn btn-warning">Purge Public</button>';
    echo '</form>';

    echo '<form method="post" action="' . $_SERVER['REQUEST_URI'] . '" style="display:inline;">';
    echo '<input type="hidden" name="action" value="purge_announcements">';
    echo '<button type="submit" class="btn btn-warning">Purge Announcements</button>';
    echo '</form>';

    echo '<form method="post" action="' . $_SERVER['REQUEST_URI'] . '" style="display:inline;">';
    echo '<input type="hidden" name="action" value="purge_knowledgebase">';
    echo '<button type="submit" class="btn btn-warning">Purge Knowledgebase</button>';
    echo '</form>';

    echo '<form method="post" action="' . $_SERVER['REQUEST_URI'] . '" style="display:inline;">';
    echo '<input type="hidden" name="action" value="purge_downloads">';
    echo '<button type="submit" class="btn btn-warning">Purge Downloads</button>';
    echo '</form>';

    echo '<form method="post" action="' . $_SERVER['REQUEST_URI'] . '" style="display:inline;">';
    echo '<input type="hidden" name="action" value="purge_serverstatus">';
    echo '<button type="submit" class="btn btn-warning">Purge Server Status</button>';
    echo '</form>';
    echo '</div>';

    // Last purge timestamp (placeholder - would need database storage)
    echo '<div class="last-purge-section">';
    echo '<h3>Last Purge</h3>';
    echo '<p>No purge history available</p>';
    echo '</div>';

    echo '</div>';

    // Add some basic CSS
    echo '<style>
        .lscache-admin { margin: 20px 0; }
        .lscache-admin h2 { color: #333; }
        .lscache-admin h3 { color: #666; margin-top: 30px; }
        .status-section, .tips-section, .purge-section, .last-purge-section {
            background: #f9f9f9;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .tips-section ul { margin: 0; padding-left: 20px; }
        .purge-section form { display: inline; margin-right: 10px; }
        .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-warning { background: #ffc107; color: black; }
    </style>';
}

/**
 * Handle purge actions
 *
 * @param string $action
 */
function handle_purge_action($action)
{
    require_once __DIR__ . '/../hooks.php'; // Include service class

    switch ($action) {
        case 'purge_all':
            Lscache_Service::purgeAll();
            break;
        case 'purge_public':
            Lscache_Service::purgeByTag('whmcs:public');
            break;
        case 'purge_announcements':
            Lscache_Service::purgeByTag('whmcs:public,tag=announcements');
            break;
        case 'purge_knowledgebase':
            Lscache_Service::purgeByTag('whmcs:public,tag=knowledgebase');
            break;
        case 'purge_downloads':
            Lscache_Service::purgeByTag('whmcs:public,tag=downloads');
            break;
        case 'purge_serverstatus':
            Lscache_Service::purgeByTag('whmcs:public,tag=serverstatus');
            break;
        default:
            return;
    }

    // Return success response
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Cache purged successfully']);
}

/**
 * Get LSCache settings
 *
 * @return array
 */
function get_lscache_settings()
{
    $settings = [];
    $addonSettings = Capsule::table('tbladdonmodules')
        ->where('module', 'lscache')
        ->get(['setting', 'value']);

    foreach ($addonSettings as $setting) {
        $settings[$setting->setting] = $setting->value;
    }

    return $settings;
}
