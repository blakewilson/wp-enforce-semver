# Semantic Versioning for WP

![Screenshot of plugins list with Semantic Versioning for WordPress enabled](https://github.com/blakewilson/semantic-versioning-plugin/blob/main/.wordpress-org/screenshot-1.png?raw=true)

Easily enforce [SemVer](https://semver.org) in your WordPress plugins.

By using this class, auto updates will be disabled when the plugin's new version is a `major` update, and provide a helpful notice in the update section of the plugin that there may be breaking changes.

## Usage

1. Install the class as a dependency via composer:

```
composer install blakewilson/wp-enforce-semver
```

2. Initialize the class in your plugin:

```php
use EnforceSemVer;

new EnforceSemVer('my-plugin/my-plugin.php');
```

And that's it! Once your end-users install your plugin, they will be protected against auto updates for major releases, and see a helpful message in the plugins list next to your plugin update:

![Screenshot of plugins list with Semantic Versioning for WordPress enabled](https://github.com/blakewilson/semantic-versioning-plugin/blob/main/.wordpress-org/screenshot-1.png?raw=true)

Additionally, this notice text can be modified via a filter:

```php
add_filter( 'semantic_versioning_notice_text', function($notice_text, $plugin_file_name) {
	// Modify notice text here.

	return $notice_text;
});
```
