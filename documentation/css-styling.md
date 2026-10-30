---
title: "CSS Styling"
slug: css-styling-cp-staff
section: advanced-cp-staff
order: 4
excerpt: "Customize the appearance of CP Staff using CSS classes provided by the plugin."
status: publish
---

# CSS Styling

CP Staff includes minimal default styling that works with most themes. You can customize the appearance using CSS classes provided by the plugin.

## Main CSS Classes

### Staff Grid and Cards

| Class | Element |
| --- | --- |
| `.cp-staff-grid` | Container for the staff card grid |
| `.cp-staff-card` | Individual staff card |
| `.cp-staff-card--image-wrapper` | Card image container |
| `.cp-staff-card--image-overlay` | Overlay with email/phone action icons |
| `.cp-staff-card--name` | Staff member name |
| `.cp-staff-card--name-link` | Clickable name link (when click action is enabled) |
| `.cp-staff-card--role` | Staff member title/role |
| `.cp-staff--action-icon` | Email and phone action icons |

### Single Staff Page

| Class | Element |
| --- | --- |
| `.cp-staff-single` | Single staff page container |
| `.cp-staff-single--image-wrapper` | Profile image container |
| `.cp-staff-single--name` | Staff member name |
| `.cp-staff-single--role` | Title/role |
| `.cp-staff-single--bio` | Biography content |
| `.cp-staff-single--social-links` | Social media links container |

### Department Headings

| Class | Element |
| --- | --- |
| `.cp-staff-department-heading` | Department heading text |
| `.cp-staff-department-wrapper` | Container for a department and its children |
| `.cp-staff-department-children` | Container for staff within a department |
| `.cp-staff-department-children--depth-3` | Depth indicator (3, 4, 5, etc.) |

### Modals

| Class | Element |
| --- | --- |
| `.cp-staff-email-modal` | Email contact modal container |
| `.cp-staff-email-form` | Contact form element |
| `.cp-staff--email-modal-popup` | Email modal popup wrapper (jQuery UI dialog) |
| `.cp-staff-info-modal` | Info modal container (for modal click action) |
| `.cp-staff-info-modal--popup` | Info modal popup wrapper (jQuery UI dialog) |

## Styling Examples

Add these to your theme's `style.css` or the WordPress Customizer CSS editor.

### Card Hover Effect

```
.cp-staff-card {
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.cp-staff-card:hover {
    transform: translateY(-5px);
}
```

### Department Heading Styles

```
.cp-staff-department-heading {
    border-bottom: 2px solid #3498db;
    padding-bottom: 10px;
    margin-bottom: 25px;
}
```

### Indent Nested Departments

```
.cp-staff-department-children--depth-4 {
    margin-left: 2rem;
}

.cp-staff-department-children--depth-5 {
    margin-left: 4rem;
}
```

### Grid Column Control

```
.cp-staff-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
}

@media (max-width: 768px) {
    .cp-staff-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
```

## Adding Custom CSS

**Option 1**: Add to your theme's `style.css` file

**Option 2**: Use the WordPress Customizer at **Appearance > Customize > Additional CSS**

**Option 3**: Use a child theme to keep customizations safe from theme updates

For structural changes beyond CSS, see [Template Overrides](customization.md).
