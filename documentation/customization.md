# Template Overrides

Copy a CP Staff template into your theme. CP Staff uses that copy in place of the plugin file.

## How Overrides Work

1. Create a `cp-staff` directory in your theme.
2. Copy the file from the plugin `templates/` directory into `cp-staff/`, keeping the same folders.
3. Edit the copy.

The theme file is chosen before the plugin file.

## Available Templates

| Template | Path | Description |
|----------|------|-------------|
| Archive | `templates/archive.php` | Department listing |
| Single | `templates/single.php` | Single staff page |
| Default | `templates/default-template.php` | Page wrapper for the archive and single views |
| Staff card | `templates/parts/staff-card.php` | Card used in the listing |
| Email modal | `templates/parts/email-modal.php` | Contact form |

## Staff Card Example

1. Create `yourtheme/cp-staff/parts/`.
2. Copy `wp-content/plugins/cp-staff/templates/parts/staff-card.php` to `yourtheme/cp-staff/parts/staff-card.php`.
3. Edit the theme copy.

## Template Variables

### Staff card

`templates/parts/staff-card.php` provides:

- `$click_action` — stored **Staff click action** value: `none`, `link`, or `modal`. It is printed as a class on the card: `click-action-none`, `click-action-link`, or `click-action-modal`.
- `$static` — true when the template is included with `static`.
- `$staff_title`, `$staff_email`, and `$staff_phone` — the **Title**, **Email**, and **Phone** fields.
- `$clickable` — true when the biography has content and `$static` is false. The photo and the name link to the staff page when `$clickable` is true.

The email icon is output when `$static` is false and `$staff_email` is set. The phone icon links with `tel:` when `$static` is false and `$staff_phone` is set.

### Single staff page

`templates/single.php` reads:

- **Title**
- **Social**, as a list of network and URL pairs
- **Email**, when **Staff contact modal** is checked
- The **Alternate image** attachment id (`alt_image_id`). An empty value uses the featured image. The photo markup is output when the staff member has a featured image.

### Archive

`templates/archive.php` defines:

- `cp_staff_display_department( $department_id, $department_name, $heading_level )` prints one department heading and its cards. `$heading_level` defaults to 3.
- `cp_staff_display_hierarchical_departments( $parent_id, $depth )` prints a department and its children. `$parent_id` defaults to 0. `$depth` defaults to 3.

## Tips

- Copy the template into the theme before editing. A change inside the plugin directory is replaced on update.
- Edit a small piece, then reload the page.

Styles are covered in [CSS Styling](https://docs.churchplugins.com/knowledge-base/css-styling-cp-staff/).

Filters, actions, and script hooks are covered in the [Developer Guide](developer-guide.md).
