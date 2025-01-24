# WordPress Plugin Theme Update from GitHub

This guide explains how to implement automatic updates for your WordPress plugin or theme using GitHub releases.

## Setup Instructions

### 1. Plugin Setup
Add the following code to your plugin's main file:

```update.php
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

```

### 2. Theme Setup
Add the following to your theme's `functions.php`:

```php
define('GITHUB_ACCESS_TOKEN', 'YOUR_GITHUB_TOKEN'); // Replace with your actual token
```

## Configuration Steps

1. **Generate GitHub Token**
   - Go to GitHub Settings > Developer Settings > Personal Access Tokens
   - Generate a new token with `repo` access
   - Copy the token

2. **Update Configuration**
   - Replace `YOUR_GITHUB_TOKEN` with your actual GitHub token
   - Update `plugin_slug` to match your plugin's main file path
   - Update `repo_url` to point to your GitHub repository

## Important Notes

- Keep your GitHub token secure
- Do not commit the token to public repositories
- The plugin checks for updates by comparing version numbers in GitHub releases
- Releases must use semantic versioning (e.g., "1.0.0" or "v1.0.0")

## Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher
- Active GitHub repository with releases

## Security

- Always use HTTPS for API connections
- Regularly rotate your GitHub access token
- Implement proper error handling

## Support

For issues or questions, please open an issue in the GitHub repository.

## License

This code is released under the GPL v2 or later.