# Departments

Departments are a hierarchical taxonomy, `cp_department`. The archive groups staff by department.

## Creating Departments

1. Go to **Staff > Departments**.
2. Enter **Name**.
3. **Slug** is filled in from the name when you leave it blank.
4. **Description** is optional.
5. Choose **Parent Category** to place this department under another department.
6. Click **Add New Department**.

On a staff member, assign departments in the **Departments** panel. Click **Add New Department** to create one there. A person assigned to more than one department is listed under each of those departments on the archive page.

## Hierarchical Departments

- Parent departments are listed before their children.
- Child departments are nested under the parent.
- Headings start at h3. A department that has staff uses the next heading level for its children.
- Departments without staff members can still display their child departments.

## Department Ordering

The archive orders departments by name.

The `cp_staff_departments_args` filter receives the term arguments, the parent ID, and the depth.

```php
add_filter( 'cp_staff_departments_args', function( $args, $parent_id, $depth ) {
	$args['orderby'] = 'term_id';
	return $args;
}, 10, 3 );
```

## Shortcodes

`[cp_staff_list]` accepts department slugs. A slug does not include its child departments.

```
[cp_staff_list cp_department="pastoral-staff"]
[cp_staff_list cp_department="worship,kids"]
[cp_staff_list exclude_cp_department="support-staff"]
```

The other parameters are covered in [Shortcodes](shortcodes.md).

## Department Labels

```php
add_filter( 'cp_department_single_label', function( $label ) {
	return 'Ministry';
} );

add_filter( 'cp_department_plural_label', function( $label ) {
	return 'Ministries';
} );
```

More filters are covered in the [Developer Guide](developer-guide.md).
