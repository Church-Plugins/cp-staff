# Archive Page

The archive lists staff members who are assigned to a department, grouped by department.

## Default Archive

With the default **Plural Label**, the archive address is:

```
https://yoursite.com/staff/
```

The archive slug comes from **Plural Label**. **Our Team** gives `/our-team/`.

The archive page:

- Groups staff by department, including child departments
- Shows a card with the name, title, and photo
- Links the photo and the name to the staff member's page when the biography has content
- With **Staff contact modal** checked, the email icon opens the contact form for a staff member who has an **Email**
- Shows a phone link when the staff member has a **Phone**

Cards are sorted by **Order**, then by name.

## Hierarchical Departments

- Parent departments are listed before their children.
- Child departments are nested under the parent.
- Headings start at h3. A department that has staff uses the next heading level for its children.
- Departments without staff members can still display their child departments.

Departments are ordered by name.

The `cp_staff_archive_starting_heading_level` filter sets the first heading level. The default is 3.

```php
add_filter( 'cp_staff_archive_starting_heading_level', function( $level ) {
	return 2;
} );
```

## Archive Title

On the archive URL, the title is the **Plural Label**. The `cp_staff_archive_title` filter changes that title.

```php
add_filter( 'cp_staff_archive_title', function( $title ) {
	return 'Meet Our Team';
} );
```

## Disabling the Archive

1. Go to **Staff > Settings**, then open the **Staff** tab.
2. Check **Disable Archive Page**.
3. Click **Save Changes**.

The `cp_staff_disable_archive` filter turns the archive off as well.

```php
add_filter( 'cp_staff_disable_archive', '__return_true' );
```

Shortcodes for a page you create are covered in [Shortcodes](shortcodes.md).

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

## Single Staff Page

The single staff page shows:

- A photo when the staff member has a featured image. **Alternate image** is used when it is set.
- The name and **Title**
- The biography
- Social links
- An email button when **Staff contact modal** is checked and the staff member has an **Email**

## Staff Click Action

1. Go to **Staff > Settings**, then open the **Advanced** tab.
2. Set **Staff click action** to one of:
   - **None**
   - **Link to single staff page (if content exists for Staff member)**
   - **Display popup modal**

Template files are covered in [Template Overrides](customization.md).

Styles are covered in [CSS Styling](https://docs.churchplugins.com/knowledge-base/css-styling-cp-staff/).

Department query filters are covered in the [Developer Guide](developer-guide.md).
