# TPB Modal Template System

## Overview

This system provides a unified styling and structure template for all TPB modals, making it easy to maintain consistent design across all modal types while allowing for specific customizations.

## File Structure

```
assets/css/
├── modal-template.css    # Base styling for all modals
└── modal.css            # Specific overrides and customizations

templates/
├── parts/
│   ├── modal-base.php              # Base HTML structure
│   └── modal-branded-simplified.php # Simplified template for branded stations
├── modal-flower-stations.php       # Uses modal-base.php
├── modal-category-stations.php     # Uses modal-base.php
├── modal-branded-stations.php      # Uses modal-branded-simplified.php
├── modal-menu-boards.php           # Uses modal-base.php
└── modal-quickcheckout-stations.php # Uses modal-base.php
```

## How It Works

### 1. CSS Template System

- **`modal-template.css`**: Contains all base styling for modals, including:
  - Modal overlay and container styles
  - Close button styling
  - Left panel (image) styling
  - Right panel (content) styling
  - Header container (title & price) styling
  - Stepper container styling
  - All form elements, cards, typography, animations
  - Responsive design rules

- **`modal.css`**: Contains specific overrides and customizations that extend the template

### 2. HTML Template System

- **`modal-base.php`**: Provides the standard modal structure with configurable parameters
- **`modal-branded-simplified.php`**: Provides a simplified structure for branded stations
- Individual modal files use these templates with specific configurations

## Usage

### Creating a New Modal

1. **Create the template file** (e.g., `modal-new-type.php`):
```php
<?php
$modal_type = 'new-type';
$title = 'Configure Your New Type';
$description = 'Please make your selections.';

include plugin_dir_path(__FILE__) . 'parts/modal-base.php';
?>
```

2. **Add specific styling** (if needed) in `modal.css`:
```css
.tpb-qv-modal[data-modal-type="new-type"] {
    /* Specific overrides for this modal type */
}
```

### Customizing Existing Modals

1. **For styling changes**: Edit `modal-template.css` for global changes or `modal.css` for specific overrides
2. **For structure changes**: Edit the appropriate template in `templates/parts/`
3. **For content changes**: Edit the individual modal files

### Template Parameters

The `modal-base.php` template accepts these parameters:

- `$modal_type`: The type identifier for the modal
- `$title`: The modal title text
- `$description`: The initial description text
- `$custom_classes`: Array of additional CSS classes

## Key Features

### Consistent Styling
- All modals use the same base styles
- Consistent typography, spacing, colors, and animations
- Unified close button, image panel, and content panel styling

### Easy Customization
- Template system allows for easy modifications
- Specific overrides can be added without affecting other modals
- Responsive design built-in

### Maintainability
- Single source of truth for base styling
- Changes to the template affect all modals
- Clear separation between base styles and customizations

## CSS Classes Reference

### Main Container Classes
- `.tpb-qv-overlay` - Modal overlay
- `.tpb-qv-modal` - Modal container
- `.tpb-qv-left-panel` - Image panel
- `.tpb-qv-right-panel` - Content panel

### Header Classes
- `.tpb-qv-header-container` - Header wrapper
- `.tpb-qv-title` - Modal title
- `.tpb-qv-title-simplified` - Simplified title style
- `.tpb-qv-base-price` - Price/description area

### Content Classes
- `.tpb-qv-stepper-container` - Stepper content area
- `.tpb-step` - Individual step
- `.tpb-grid` - Grid layout
- `.tpb-card` - Product cards

### Form Classes
- `.tpb-select` - Select dropdowns
- `.tpb-radio` - Radio buttons
- `.tpb-actions` - Action buttons area

## Best Practices

1. **Always use the template system** - Don't create standalone modal files
2. **Make global changes in `modal-template.css`** - This affects all modals
3. **Make specific changes in `modal.css`** - Use data attributes for targeting
4. **Test all modals** - Changes to the template affect all modal types
5. **Use semantic class names** - Follow the existing naming convention

## Migration Guide

If you have existing modals that don't use the template system:

1. **Identify the modal type** and its specific requirements
2. **Create a template file** using `modal-base.php` as a starting point
3. **Move any specific styling** to `modal.css` with appropriate selectors
4. **Test the modal** to ensure it works correctly
5. **Remove old styling** from individual files

## Troubleshooting

### Styles Not Applying
- Check that `modal-template.css` is loaded before `modal.css`
- Verify CSS specificity - use `!important` if needed
- Check for conflicting styles in other CSS files

### Template Not Loading
- Verify the include path is correct
- Check that the template file exists
- Ensure PHP syntax is correct

### Responsive Issues
- Check that responsive rules are in `modal-template.css`
- Verify viewport meta tag is present
- Test on actual devices, not just browser dev tools
