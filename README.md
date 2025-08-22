# Retain Admin Color

**Contributors:** Lax Mariappan  
**Tags:** admin, color scheme, user interface, customization  
**Requires at least:** 5.0  
**Tested up to:** 6.3  
**Requires PHP:** 7.4  
**Stable tag:** 1.0.0  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html  

A simple WordPress plugin that retains the admin color scheme of logged-in users, preventing it from being overridden by themes or other plugins.

## Description

The Retain Admin Color plugin ensures that your chosen admin color scheme (like Ectoplasm, Ocean, Midnight, etc.) is properly retained and displayed in the WordPress admin area. This plugin solves the common issue where admin color schemes get overridden and default to the standard WordPress colors.

### Key Features

- **Automatic Color Retention**: Preserves the user's selected admin color scheme
- **No Configuration Required**: Works immediately upon activation
- **Lightweight**: Minimal code footprint with no database overhead
- **Compatible**: Works with all WordPress admin color schemes
- **Clean Code**: Follows WordPress coding standards and best practices

### Supported Admin Color Schemes

- Fresh (Default)
- Light
- Modern
- Blue
- Coffee
- Ectoplasm
- Midnight
- Ocean
- Sunrise

## Installation

### Automatic Installation

1. Log in to your WordPress admin panel
2. Navigate to **Plugins > Add New**
3. Search for "Retain Admin Color"
4. Click **Install Now** and then **Activate**

### Manual Installation

1. Download the plugin zip file
2. Log in to your WordPress admin panel
3. Navigate to **Plugins > Add New > Upload Plugin**
4. Choose the zip file and click **Install Now**
5. Click **Activate** to enable the plugin

### FTP Installation

1. Download and extract the plugin zip file
2. Upload the `retain-admin-color` folder to your `/wp-content/plugins/` directory
3. Log in to your WordPress admin panel
4. Navigate to **Plugins** and activate "Retain Admin Color"

## Usage

Once activated, the plugin works automatically with no configuration required:

1. **Activate the Plugin**: Enable "Retain Admin Color" from your Plugins page
2. **Set Your Color Scheme**: Go to **Users > Your Profile** and choose your preferred Admin Color Scheme
3. **Automatic Retention**: The plugin will ensure your color scheme is retained across all admin pages

## Frequently Asked Questions

### Does this plugin require any configuration?

No! The plugin works automatically once activated. There are no settings pages or configuration options needed.

### Which admin color schemes are supported?

All WordPress core admin color schemes are supported, including Fresh, Light, Modern, Blue, Coffee, Ectoplasm, Midnight, Ocean, and Sunrise.

### Will this plugin slow down my website?

No, the plugin is very lightweight and only runs in the admin area. It has minimal impact on performance.

### Does this plugin work with custom admin themes?

The plugin is designed to work with WordPress core admin color schemes. Compatibility with custom admin themes may vary.

### Can I use this plugin on multisite installations?

Yes, the plugin is compatible with WordPress multisite installations and will work for each user individually.

## Technical Details

### Hooks and Filters

The plugin uses the following WordPress hooks:

- `admin_init`: Initialize admin color retention
- `get_user_option_admin_color`: Filter to force user's color choice
- `admin_enqueue_scripts`: Enqueue necessary styles

### Requirements

- **WordPress Version**: 5.0 or higher
- **PHP Version**: 7.4 or higher
- **User Permissions**: Users must be logged in to benefit from color retention

### File Structure

```
retain-admin-color/
├── retain-admin-color.php  # Main plugin file
├── README.md              # This file
├── LICENSE               # GPL v2 license
└── languages/           # Translation files (future)
```

## Changelog

### Version 1.0.0 (2025-08-23)

- **Initial Release**
- Core functionality to retain admin color schemes
- Support for all WordPress admin color schemes
- Clean, object-oriented code following WordPress standards
- Comprehensive documentation

## Development

### Coding Standards

This plugin follows:
- WordPress PHP Coding Standards
- WordPress Documentation Standards
- Object-oriented programming principles
- PHP 7.4+ features and syntax

### Contributing

Contributions are welcome! Please feel free to submit pull requests or open issues on the project repository.

### Testing

The plugin has been tested with:
- WordPress 5.0+
- PHP 7.4+
- Various admin color schemes
- Multisite installations

## Support

For support, feature requests, or bug reports, please visit the plugin's repository or contact the author.

## License

This plugin is licensed under the GPL v2 or later.

```
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

---

**Made with ❤️ by [Lax Mariappan](https://laxmariappan.com)**
