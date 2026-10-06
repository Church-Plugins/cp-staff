# Developer Guide

Hooks and template files for CP Staff.

## Plugin Architecture

- Post type: `cp_staff`
- Taxonomy: `cp_department` (hierarchical)
- Namespace: `CP_Staff\`
- Access the plugin with `cp_staff()`
- Theme overrides live in `yourtheme/cp-staff/`

## Settings Access

```php
// Staff tab
$value = \CP_Staff\Admin\Settings::get_staff( 'singular_label', 'Staff' );
$value = \CP_Staff\Admin\Settings::get_staff( 'plural_label', 'Staff' );
$value = \CP_Staff\Admin\Settings::get_staff( 'disable_archive', false );

// Advanced tab
$value = \CP_Staff\Admin\Settings::get( 'click_action', 'none' );
$value = \CP_Staff\Admin\Settings::get( 'use_email_modal', false );
$value = \CP_Staff\Admin\Settings::get( 'enable_captcha', 'on' );
```

The second argument is the value used when that option is not stored.

## Post Meta Fields

| Meta key | Type | Description |
|----------|------|-------------|
| `title` | string | Role |
| `email` | string | Email address |
| `phone` | string | Phone number |
| `acronyms` | string | Credentials or abbreviations |
| `social` | array | Links, each with `url` and `network` |
| `alt_image` | string | Alternate image URL |
| `alt_image_id` | int | Alternate image attachment ID |

## Filters

### Display

```php
add_filter( 'cp_staff_archive_title', function( $title ) {
	return 'Meet Our Team';
} );

add_filter( 'cp_staff_archive_starting_heading_level', function( $level ) {
	return 2;
} );

add_filter( 'cp_staff_disable_archive', '__return_true' );

add_filter( 'cp_staff_default_template_classes', function( $classes ) {
	$classes[] = 'my-custom-class';
	return $classes;
} );
```

`cp_staff_archive_starting_heading_level` defaults to 3.

### Labels

```php
add_filter( 'cploc_single_cp_staff_label', function( $label ) {
	return 'Team Member';
} );

add_filter( 'cploc_plural_cp_staff_label', function( $label ) {
	return 'Our Team';
} );

add_filter( 'cp_department_single_label', function( $label ) {
	return 'Ministry';
} );

add_filter( 'cp_department_plural_label', function( $label ) {
	return 'Ministries';
} );
```

### Queries

```php
add_filter( 'cp_staff_list_query_args', function( $args, $atts ) {
	$args['posts_per_page'] = 6;
	return $args;
}, 10, 2 );

add_filter( 'cp_staff_departments_args', function( $args, $parent_id, $depth ) {
	$args['orderby'] = 'name';
	return $args;
}, 10, 3 );
```

### Email

```php
add_filter( 'cp_staff_email_subject', function( $subject, $raw_subject ) {
	return '[Website Contact] ' . $raw_subject;
}, 10, 2 );

add_filter( 'cp_staff_email_message_suffix', function( $suffix ) {
	return '<br><br>--<br>Sent via our website contact form.';
} );

add_filter( 'cp_staff_email_message', function( $message ) {
	return $message;
} );
```

The subject filter receives the subject with the **[Web Inquiry]** prefix already applied, then the subject the visitor typed.

### Taxonomy

```php
add_filter( 'cp_department_taxonomy_types', function( $types ) {
	$types[] = 'custom_post_type';
	return $types;
} );
```

## Actions

```php
add_action( 'cp_register_post_types', function() {
	// Post types are registered.
} );

add_action( 'cp_register_taxonomies', function() {
	// Taxonomies are registered.
} );

add_action( 'cp_staff_default_template_after_header', function() {
	echo '<div class="custom-banner">Staff Directory</div>';
} );

add_action( 'cp_staff_default_template_before_footer', function() {
	echo '<div class="custom-cta">Join our team!</div>';
} );
```

## CP Locations

When CP Locations is active, CP Staff adds the staff post type to the location taxonomy. That taxonomy stays off until `CP_LOCATIONS_TAX_ENABLED` is defined as true in `wp-config.php`, because CP Locations sets it to false when it loads, or the `cploc_location_taxonomy_enabled` filter returns true. The constant defaults to false.

With the taxonomy enabled, the staff editor includes a **Locations** box. Choose locations under **Assign Locations**.

## Template Files

Copy a file to `yourtheme/cp-staff/`, keeping the same folders.

| Template | Purpose |
|----------|---------|
| `archive.php` | Department listing |
| `single.php` | Single staff page |
| `default-template.php` | Page wrapper for the archive and single views |
| `parts/staff-card.php` | Staff card |
| `parts/email-modal.php` | Contact form |

Override steps are covered in [Template Overrides](customization.md).
