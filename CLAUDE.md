# CP Staff Plugin Guidelines

## Project Overview

CP Staff is a WordPress plugin for church staff management. It provides:

- Custom post type for staff members
- Department taxonomy for organization
- Staff member details (title, email, phone, social links)
- Staff directory with filtering capabilities
- Staff contact form with reCAPTCHA protection
- Template overrides for theme customization

## Documentation Practices

- Update documentation whenever functionality changes
- Documentation files are located in `/documentation/` directory
- Keep README.md in sync with major features and changes
- Update relevant documentation files when:
  - Adding/modifying features
  - Changing API behavior
  - Updating template structures
  - Modifying shortcode parameters

## Key Files and Structure

- `/cp-staff.php` - Main plugin file with initialization
- `/includes/Init.php` - Main plugin class with core functionality
- `/includes/Setup/PostTypes/Staff.php` - Staff post type definition
- `/templates/` - Template files for front-end display
- `archive.php` - Staff directory
- `single.php` - Single staff member display
- `parts/` - Reusable template parts

## Post Types and Taxonomies

- Post Type: `cp_staff`
- Supports: title, editor, thumbnail, page-attributes
- Custom meta: title, email, phone, acronyms, social links, alt image
- Taxonomy: `cp_department`
- Used to organize staff members

## Code Conventions

- PHP Namespaces: Uses `CP_Staff` namespace
- OOP Approach: Class-based implementation with singletons
- WordPress Coding Standards

## Common Tasks

### Queries

- Staff queries should sort by menu_order and title:

 ```php
query_args = array(
post_type' => 'cp_staff',
orderby'   => array('menu_order' => 'ASC', 'title' => 'ASC'),
;
``
 
 ### Templates
 - Templates can be overridden in theme:
   - Create `/cp-staff/` directory in theme
   - Copy templates from plugin to theme directory
   
 ### Plugin Integrations
 - Compatible with CP Location plugin
 - Uses CMB2 for custom fields
 - Integrates with Google reCAPTCHA v3
 
 ## Commands
 ### Build/Development
 - `npm run dev` - Start development server
- `npm run build` - Build production assets
