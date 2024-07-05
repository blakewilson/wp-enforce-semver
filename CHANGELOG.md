# wp-enforce-semver

## Unreleased

- Calls the `in_plugin_update_message-{$file}` action more reliably

## 3.0.0

- BREAKING: `plugins_list_show_breaking_changes_message` is now fired on the `admin_init` action instead of the `plugins_loaded` action to avoid running outside of the admin interface

## 2.0.2

- Set priority to `20` in the `plugins_list_show_breaking_changes_message` callback (#4). Thanks @mindctrl!

## 2.0.1

- Fixed: Installing package from Composer/Packagist

## 2.0.0

- Switched the semantic versioning WordPress plugin out for a class you can install via Composer.

## 1.0.0

- Initial release
