<?php
function my_plugin_update_check($transient) {
    if (empty($transient->checked)) {
        return $transient;
    }

    $plugin_slug = plugin_basename(dirname(__FILE__, 2) . '/vshop-core.php');
    $repo_url = 'https://api.github.com/repos/your-name/file/releases/latest';
    $access_token = defined('GITHUB_ACCESS_TOKEN') ? GITHUB_ACCESS_TOKEN : '';

    $args = [
        'headers' => [
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'WordPress/' . get_bloginfo('version')
        ]
    ];

    // Only add authorization header if token exists
    if (!empty($access_token)) {
        $args['headers']['Authorization'] = 'Bearer ' . $access_token;
    }

    $response = wp_remote_get($repo_url, $args);

    if (is_wp_error($response)) {
        return $transient;
    }

    $release = json_decode(wp_remote_retrieve_body($response));
    
    // Check if we have a valid release object
    if (empty($release) || !isset($release->tag_name)) {
        return $transient;
    }

    // Remove 'v' prefix from version number if present
    $latest_version = ltrim($release->tag_name, 'v');

    // Compare versions and update if needed
    if (isset($transient->checked[$plugin_slug]) && 
        version_compare($transient->checked[$plugin_slug], $latest_version, '<')) {
        
        $transient->response[$plugin_slug] = (object) [
            'slug' => dirname($plugin_slug),
            'new_version' => $latest_version,
            'url' => $release->html_url,
            'package' => $release->zipball_url,
            'tested' => get_bloginfo('version')
        ];
    }

    return $transient;
}

add_filter('site_transient_update_plugins', 'my_plugin_update_check');
