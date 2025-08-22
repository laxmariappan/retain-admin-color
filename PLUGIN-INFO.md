# Plugin Information

## Retain Admin Color Plugin Details

**Plugin Name:** Retain Admin Color  
**Version:** 1.0.0  
**Author:** Lax Mariappan  
**WordPress Compatibility:** 5.0+  
**PHP Compatibility:** 7.4+  

## Plugin Structure

```
retain-admin-color/
├── retain-admin-color.php    # Main plugin file (Entry point)
├── README.md                 # Documentation
├── CHANGELOG.md              # Version history
├── uninstall.php             # Clean uninstall process
├── index.php                 # Security (prevents directory browsing)
├── LICENSE                   # GPL v2 license
└── languages/                # Internationalization
    └── index.php             # Security file
```

## How It Works

1. **Detection**: The plugin detects the logged-in user's admin color preference
2. **Retention**: Uses WordPress hooks to ensure the color scheme is maintained
3. **Override Protection**: Prevents other plugins/themes from overriding the user's choice
4. **Body Class**: Ensures the correct CSS class is applied to maintain visual consistency

## WordPress Hooks Used

- `init` - Load text domain for internationalization
- `admin_init` - Initialize admin color retention logic  
- `get_user_option_admin_color` - Filter to force user's color choice
- `admin_enqueue_scripts` - Enqueue custom CSS
- `admin_head` - Inject JavaScript for body class correction

## Key Features

✅ **Zero Configuration** - Works immediately upon activation  
✅ **Lightweight** - Minimal performance impact  
✅ **Secure** - Follows WordPress security best practices  
✅ **Standards Compliant** - Adheres to WordPress coding standards  
✅ **PHP 7.4+ Ready** - Uses modern PHP features with type hints  
✅ **OOP Architecture** - Clean, maintainable code structure  

## Testing Checklist

- [ ] Plugin activates without errors
- [ ] Admin color scheme is retained for ectoplasm 
- [ ] Admin color scheme is retained for other color schemes
- [ ] Plugin deactivates cleanly
- [ ] No conflicts with other plugins
- [ ] Works with WordPress multisite
