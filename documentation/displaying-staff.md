# Displaying Staff

This guide covers the various ways to display staff members on your website using the CP Staff plugin.

## Default Archive Page

By default, CP Staff creates an archive page that displays all staff members organized by department. This page is typically accessible at:

```
https://yoursite.com/staff/
```

The archive page:
- Groups staff by department (including hierarchical department structures)
- Shows staff cards with name, title, and photo
- Links to individual staff member pages (if enabled)
- Provides email and phone contact options

### Hierarchical Departments

As of version 1.2.1, the archive page supports hierarchical departments:

- Parent departments are displayed first
- Child departments are nested under their parents
- Each department level uses appropriate heading levels (h3, h4, etc.)
- Departments without staff members can still display their child departments

### Ordering Staff and Departments

Staff are ordered by:
1. Menu order (primary sort)
2. Title (secondary sort)

Departments are ordered by name by default, but this can be modified using term order.

#### Plugins for Custom Ordering

For the best experience with ordering:
- **Staff Members**: Use the "Simple Page Ordering" plugin to drag and drop staff into your preferred order
- **Departments**: Use the "WP Term Order" plugin to arrange departments using drag and drop

#### Custom Ordering Using Code

Developers can use the `cp_staff_departments_args` filter to customize department ordering:

```php
// Example: Order departments by ID instead of name
add_filter( 'cp_staff_departments_args', function( $args, $parent_id, $depth ) {
    $args['orderby'] = 'term_id';
    $args['order'] = 'DESC';
    return $args;
}, 10, 3 );

// Example: If using WP Term Order plugin
add_filter( 'cp_staff_departments_args', function( $args, $parent_id, $depth ) {
    $args['orderby'] = 'term_order';
    return $args;
}, 10, 3 );
```

### Disabling the Archive Page

If you prefer to use shortcodes on a custom page:

1. Go to Staff > Settings > Staff
2. Check "Disable Archive Page"
3. Create a new page and use the shortcodes below

## Shortcodes

### Staff Archive Shortcode

Display the complete staff archive, identical to the default archive page:

```
[cp_staff_archive]
```

### Staff List Shortcode

Display a customized list of staff members:

```
[cp_staff_list]
```

#### Staff List Parameters

| Parameter | Description | Example |
|-----------|-------------|---------|
| `cp_department` | Filter by department slug | `[cp_staff_list cp_department="pastoral-team"]` |
| `exclude_cp_department` | Exclude department | `[cp_staff_list exclude_cp_department="support-staff"]` |
| `static` | Disable click/contact functionality | `[cp_staff_list static="true"]` |

#### Multiple Departments

Show staff from multiple departments:

```
[cp_staff_list cp_department="pastoral-team,worship-team"]
```

#### Combining Parameters

```
[cp_staff_list cp_department="leadership" exclude_cp_department="pastoral-team" static="true"]
```

### CP Location Integration (Planned Feature)

Integration with the CP Location plugin is planned for a future release, which will allow filtering staff by location:

```
[cp_staff_list cp_location="main-campus"]
```

*Note: This feature is currently in development and not yet available.*

## Single Staff Display

When a visitor clicks on a staff member's card (if enabled), they'll see:

- Larger photo (or alternate image if set)
- Name and title
- Full biography
- Social media links
- Contact options

### Click Action Settings

Control what happens when visitors click a staff card:

1. Go to Staff > Settings > Advanced
2. Set "Staff click action" to:
   - None: Disable clicking entirely
   - Link to single staff page: Open the staff member's dedicated page (default)
   - Display popup modal: Show information in a modal window

## Template Customization

You can customize how staff are displayed by overriding templates:

1. Create a `cp-staff` directory in your theme
2. Copy template files from the plugin's `/templates/` directory to your theme's `cp-staff/` directory
3. Modify the templates as needed

Available templates:
- `archive.php` - Staff directory
- `single.php` - Individual staff pages
- `parts/staff-card.php` - Individual staff card
- `parts/email-modal.php` - Email contact form

See [Customization](customization.md) for more details on template overrides.

## CSS Styling

The plugin includes basic styling that works with most themes. Main CSS classes:

- `.cp-staff-grid` - Container for staff grid
- `.cp-staff-card` - Individual staff card
- `.cp-staff-single` - Single staff display
- `.cp-staff-department-heading` - Department headings

### Hierarchical Department CSS Classes

For styling hierarchical departments (v1.2.1+):

- `.cp-staff-department-wrapper` - Container for each department and its children
- `.cp-staff-department-children` - Container for staff in a department
- `.cp-staff-department-children--depth-3` - Department depth marker (3, 4, etc.)

These classes make it easy to create custom styling for different levels of department hierarchy.

### CSS Examples for Hierarchical Departments

```css
/* Indent child departments */
.cp-staff-department-children--depth-4 {
  margin-left: 2rem;
}
.cp-staff-department-children--depth-5 {
  margin-left: 4rem;
}

/* Style department headings differently by level */
h3.cp-staff-department-heading {
  border-bottom: 2px solid #333;
}
h4.cp-staff-department-heading {
  border-bottom: 1px solid #666;
}
```

See [Customization](customization.md) for more styling details.