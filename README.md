# WordPress Plugin Theme Update from GitHub

This guide explains how to implement automatic updates for your WordPress plugin or theme using GitHub releases.

## Setup Instructions

### 1. Plugin Setup
Add the following code to your plugin's main file:

```php
function my_plugin_update_check($transient) {
    // ... (your provided code)
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