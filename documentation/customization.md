# Customizing CP Staff

This guide covers the various ways to customize the appearance and behavior of the CP Staff plugin.

## Template Overrides

CP Staff uses a template system that allows you to override any template file in your theme:

1. Create a `cp-staff` directory in your active theme
2. Copy template files from the plugin's `/templates/` directory to your theme's `cp-staff/` directory
3. Modify the templates as needed

### Available Templates

| Template | Path | Description |
|----------|------|-------------|
| Archive | `/templates/archive.php` | Staff directory listing with hierarchical departments (v1.2.1+) |
| Single | `/templates/single.php` | Individual staff member pages |
| Staff Card | `/templates/parts/staff-card.php` | Individual staff card in grids |
| Email Modal | `/templates/parts/email-modal.php` | Contact form modal |
| Info Modal | `/templates/parts/info-modal.php` | Staff info modal (when using modal click action) |

### Hierarchical Department Templates (v1.2.1+)

The hierarchical department structure uses helper functions to create the nested department display:

- `cp_staff_display_department()` - Displays staff for a specific department
- `cp_staff_display_hierarchical_departments()` - Recursively displays departments and their children

When overriding the archive.php template, you can maintain or customize this hierarchical structure.

### Template Override Example

To customize the staff card:

1. Create a directory: `yourtheme/cp-staff/parts/`
2. Copy `wp-content/plugins/cp-staff/templates/parts/staff-card.php` to `yourtheme/cp-staff/parts/staff-card.php`
3. Modify the file as needed
4. The plugin will automatically use your version instead of its default

## CSS Customization

### Main CSS Classes

| Context | CSS Classes |
|---------|-------------|
| Staff Grid | `.cp-staff-grid` |
| Staff Cards | `.cp-staff-card`, `.cp-staff-card--image-wrapper`, `.cp-staff-card--name`, `.cp-staff-card--role` |
| Single Staff | `.cp-staff-single`, `.cp-staff-single--image-wrapper`, `.cp-staff-single--name`, `.cp-staff-single--role`, `.cp-staff-single--bio`, `.cp-staff-single--social-links` |
| Department Headings | `.cp-staff-department-heading` |
| Hierarchical Departments | `.cp-staff-department-wrapper`, `.cp-staff-department-children`, `.cp-staff-department-children--depth-3` (etc.) |
| Email Modal | `.cp-staff-email-modal`, `.cp-staff-email-form` |

### Adding Custom CSS

Option 1: Add to your theme's style.css:

```css
/* Customize staff card appearance */
.cp-staff-card {
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.cp-staff-card:hover {
    transform: translateY(-5px);
}

/* Customize department headings */
.cp-staff-department-heading {
    border-bottom: 2px solid #3498db;
    padding-bottom: 10px;
    margin-bottom: 25px;
}
```

Option 2: Use the WordPress Customizer CSS editor

## PHP Filters and Actions

CP Staff provides various filters and actions to customize behavior:

### Common Filters

#### Label Customization

```php
// Customize post type labels
add_filter('cploc_single_cp_staff_label', function($label) {
    return 'Team Member';
});

add_filter('cploc_plural_cp_staff_label', function($label) {
    return 'Our Team';
});
```

#### Email Customization

```php
// Customize email subject
add_filter('cp_staff_email_subject', function($subject, $original) {
    return '[Website Contact] ' . $original;
}, 10, 2);

// Customize email message suffix
add_filter('cp_staff_email_message_suffix', function($suffix) {
    return '<br><br>--<br>Sent via First Church website';
});
```

#### Archive Display

```php
// Disable the archive page programmatically
add_filter('cp_staff_disable_archive', '__return_true');
```

#### Query Customization

```php
// Modify the staff list shortcode query
add_filter('cp_staff_list_query_args', function($args, $atts) {
    // Limit to 6 staff members
    $args['posts_per_page'] = 6;
    
    // Add custom ordering
    $args['orderby'] = 'date';
    $args['order'] = 'DESC';
    
    return $args;
}, 10, 2);

// Customize department ordering (v1.2.1+)
add_filter('cp_staff_departments_args', function($args, $parent_id, $depth) {
    // Order departments by ID instead of name
    $args['orderby'] = 'term_id';
    $args['order'] = 'ASC';
    
    // If using a plugin like WP Term Order:
    // $args['orderby'] = 'term_order';
    
    return $args;
}, 10, 3);

// Customize heading levels for departments (v1.2.1+)
add_filter('cp_staff_archive_starting_heading_level', function($level) {
    // Start with h2 instead of h3
    return 2;
});
```

## JavaScript Customization

CP Staff uses a few JavaScript files that you can extend:

- `assets/js/main.js` - Front-end functionality
- `assets/js/captcha.js` - reCAPTCHA integration

Example: Add custom behavior after form submission

```javascript
// Listen for form submission success
jQuery(document).on('cp_staff_email_sent', function(e, response) {
    // Custom code after successful email
    console.log('Email sent successfully');
    
    // Perhaps show a custom thank you message
    setTimeout(function() {
        alert('Thank you for your message!');
    }, 1000);
});
```

## Advanced Customization

### Creating Custom Template Files

You can create entirely new template files for special staff layouts:

1. Create a file in your theme: `yourtheme/cp-staff/custom-staff-grid.php`
2. Add custom markup and query code
3. Include the template in your page templates or use get_template_part()

### Adding Custom Fields

To add custom fields to staff members:

1. Use a plugin like ACF or create a custom metabox
2. Target the `cp_staff` post type
3. Access the custom field data in your template overrides

Example adding a custom field with CMB2:

```php
add_action('cmb2_admin_init', 'add_custom_staff_fields');
function add_custom_staff_fields() {
    $cmb = new_cmb2_box([
        'id' => 'staff_custom_meta',
        'title' => 'Additional Staff Information',
        'object_types' => ['cp_staff'],
        'context' => 'normal',
        'priority' => 'default',
    ]);
    
    $cmb->add_field([
        'name' => 'Biography Quote',
        'id' => 'staff_quote',
        'type' => 'text',
    ]);
}
```

Then use in templates:

```php
$quote = get_post_meta(get_the_ID(), 'staff_quote', true);
if (!empty($quote)) {
    echo '<div class="staff-quote">"' . esc_html($quote) . '"</div>';
}
```