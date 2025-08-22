# Changelog

All notable changes to the Retain Admin Color plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-08-23

### Added
- Initial release of Retain Admin Color plugin
- Core functionality to retain WordPress admin color schemes
- Support for all default WordPress admin color schemes (Fresh, Light, Modern, Blue, Coffee, Ectoplasm, Midnight, Ocean, Sunrise)
- Object-oriented plugin architecture following WordPress coding standards
- Automatic color scheme detection and retention for logged-in users
- No configuration required - works immediately upon activation
- Comprehensive README.md documentation
- Proper plugin header with all required information
- Security index.php files to prevent directory browsing
- Languages directory prepared for future internationalization
- Clean uninstall process
- WordPress hooks integration (admin_init, get_user_option_admin_color, admin_enqueue_scripts)
- PHP 7.4+ compatibility with modern syntax (type hints, etc.)
- GPL v2 license compliance

### Technical Details
- Singleton pattern implementation for main plugin class
- Proper hook initialization and cleanup
- Secure coding practices with input sanitization
- Follows WordPress Plugin API best practices
- Compatible with WordPress 5.0+ and PHP 7.4+

### Files Added
- `retain-admin-color.php` - Main plugin file
- `README.md` - Comprehensive documentation
- `uninstall.php` - Clean uninstall process
- `index.php` - Security file
- `languages/index.php` - Security file for languages directory

---

**Note**: This is the initial release. Future versions will be documented above this line.
