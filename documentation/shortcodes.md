# Shortcodes

CP Staff has two shortcodes for placing staff on a page or post.

## Staff Archive Shortcode

```
[cp_staff_archive]
```

This prints the archive template: staff assigned to a department, grouped by department. It takes no attributes.

On the staff archive URL, the page also shows the archive title. On any other page, the shortcode prints the department listing only.

For a filtered list, use `[cp_staff_list]`.

## Staff List Shortcode

```
[cp_staff_list]
```

The list is published staff, ordered by **Order**, then by name, up to 999 people. Each card is rendered in a `cp-staff-grid`.

### Parameters

| Parameter | Description | Example |
|-----------|-------------|---------|
| `cp_department` | Department slugs, separated by commas. Child departments are not included unless you list their slugs. | `cp_department="pastoral-staff"` |
| `exclude_cp_department` | Department slugs to leave out | `exclude_cp_department="support"` |
| `static` | `true` leaves out the card links and the email and phone icons | `static="true"` |
| `department` | Older name for `cp_department` | `department="pastoral-staff"` |

### Examples

One department:

```
[cp_staff_list cp_department="worship-team"]
```

Several departments:

```
[cp_staff_list cp_department="pastoral-staff,worship-team"]
```

Leave a department out:

```
[cp_staff_list exclude_cp_department="administrative"]
```

A list with no card links or contact icons:

```
[cp_staff_list cp_department="leadership" static="true"]
```

Combine parameters:

```
[cp_staff_list cp_department="leadership" exclude_cp_department="support" static="true"]
```

## CP Locations

When CP Locations is active, CP Staff adds the staff post type to the location taxonomy. That taxonomy stays off until you turn it on: add `define( 'CP_LOCATIONS_TAX_ENABLED', true );` to `wp-config.php`, or return true from the `cploc_location_taxonomy_enabled` filter. The constant defaults to false.

With the taxonomy enabled, the staff editor includes a **Locations** box. Choose locations under **Assign Locations**. Each term slug is `location_` plus the location post ID, such as `location_42`.

The staff list shortcode can limit the list to one location:

```
[cp_staff_list cp_location="location_42"]
```

To leave a location out:

```
[cp_staff_list exclude_cp_location="location_42"]
```

## Query Customization

The `cp_staff_list_query_args` filter receives the query arguments and the shortcode attributes.

```php
add_filter( 'cp_staff_list_query_args', function( $args, $atts ) {
	$args['posts_per_page'] = 6;
	return $args;
}, 10, 2 );
```

Styles are covered in [CSS Styling](https://docs.churchplugins.com/knowledge-base/css-styling-cp-staff/). Template files are covered in [Template Overrides](customization.md).
