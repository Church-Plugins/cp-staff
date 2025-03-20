# Staff Management

This guide covers creating and managing staff profiles with the CP Staff plugin.

## Staff Member Overview

Each staff member in CP Staff is represented as a custom post type with:

- Name (post title)
- Biography (post content)
- Featured image (profile photo)
- Custom fields for additional information
- Department organization (taxonomy)
- Page attributes for ordering

## Creating Staff Profiles

1. Go to Staff > Add New in your WordPress admin
2. Enter the staff member's name in the title field
3. Add a biographical description in the main content editor
4. Set a featured image for the profile photo

### Staff Details Box

The Staff Details metabox provides fields for additional information:

| Field | Description |
|-------|-------------|
| Title | The staff member's position or role |
| Email | Email address for contact purposes |
| Phone | Phone number with click-to-call functionality |
| Acronyms | Optional acronyms or credentials |
| Social | Social media profile links |
| Alt Image | Optional alternate image (often a portrait-oriented image for single staff view) |

### Department Organization

Assign staff to departments using the Department taxonomy box:

1. Select one or more existing departments, or
2. Click "+ Add New Department" to create a new department
3. Save the staff profile to apply the department assignment

## Managing Staff Order

You can control the order in which staff appear:

1. On the Staff list page, staff are ordered by Menu Order first, then by Name
2. To change a staff member's position:
   - Edit the staff member
   - In the Page Attributes box, set the Order number (lower numbers appear first)
   - Save changes

## Bulk Management

Use the main staff listing screen for bulk actions:

1. Filter by department using the dropdown at the top
2. Use the bulk actions dropdown to:
   - Move multiple staff to trash
   - Edit departments in bulk

## Customizing Labels

You can customize the terminology used throughout the plugin:

1. Go to Staff > Settings > Staff
2. Change the Singular Label (e.g., "Staff Member", "Leader", "Pastor")
3. Change the Plural Label (e.g., "Staff", "Team", "Leadership")
4. Note that changing the plural label will change the URL structure and may affect SEO

## Importing Staff (Planned Feature)

Bulk import functionality for staff members is planned for a future release.

Once implemented, this feature will allow you to:
1. Import staff data from CSV files
2. Map CSV columns to staff fields
3. Batch process multiple staff entries at once

*Note: This feature is currently in development and not yet available.*

## Best Practices

- Use high-quality, consistently sized images for all staff
- Maintain consistent biographical formats for a professional look
- Regularly audit staff listings to ensure information is current
- Organize departments logically from a visitor perspective