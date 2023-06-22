# Semantic Versioning for WordPress

![Screenshot of plugins list with Semantic Versioning for WordPress enabled](https://github.com/blakewilson/semantic-versioning-plugin/blob/main/.wordpress-org/screenshot-1.png?raw=true)

## Usage

### For End-Users

If you use a WordPress plugin that uses Semantic Versioning and you are tired of accidently updating it (or having it automatically update) with breaking changes, this plugin is for you. Some of the benefits:

- Maintains auto updates for non-major updates
- Disables auto updates for major updates (You will have to manually click "update now" to get the latest update).
- Shows an update message for major updates that the new version may include breaking changes

### For Developers

If you are a WordPress plugin author and use Semantic Versioning, you'll want your users to understand that major updates (`x.0.0`) may contain breaking changes. Additionally, auto updates should not be enabled in this scenario.

Integrating with this plugin is easy:

1. Include the `Semantic Versioning: true` header in your `plugin.php` file:

```php
<?php
/**
 * Plugin Name: My Plugin
 * Description: My description
 * Semantic Versioning: true
 */
```

And that's it! Once your end-users install your plugin, along with this plugin, they will be protected against auto updates for major releases, and see a helpful message in the plugins list next to your plugin update:

![Screenshot of plugins list with Semantic Versioning for WordPress enabled](https://github.com/blakewilson/semantic-versioning-plugin/blob/main/.wordpress-org/screenshot-1.png?raw=true)
