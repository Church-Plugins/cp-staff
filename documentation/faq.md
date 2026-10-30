---
title: "Frequently Asked Questions"
slug: faq-cp-staff
section: support-cp-staff
order: 1
excerpt: "Answers to common questions about CP Staff setup, configuration, display options, and customization."
status: publish
---

# Frequently Asked Questions

## General

### What is CP Staff?

CP Staff is a WordPress plugin for churches to create and manage staff directories. It provides staff profiles with contact details, department organization, a public contact form system, and flexible display options.

### Does CP Staff work with my theme?

CP Staff works with any properly coded WordPress theme. It includes minimal default styling and provides template overrides for full customization. See [Template Overrides](customization.md) for details.

### Can I use CP Staff for non-staff directories?

Yes. You can change the labels in **Staff > Settings > Staff** to use terms like "Team," "Leadership," "Volunteers," or any other name that fits your organization.

## Setup and Configuration

### How do I change the staff page URL?

Change the **Plural Label** in **Staff > Settings > Staff**. The URL automatically updates to match. After changing, visit **Settings > Permalinks** and click **Save Changes** to flush rewrite rules.

### Can I hide the default staff archive page?

Yes. Check **Disable Archive Page** in **Staff > Settings > Staff**, then use the `[cp_staff_archive]` or `[cp_staff_list]` shortcode on a custom page.

### How do I reorder staff members?

Staff display in **Menu Order** first, then alphabetically. Edit a staff member and set the **Order** value in the **Page Attributes** box. For drag-and-drop ordering, install the **Simple Page Ordering** plugin.

### How do I reorder departments?

Install the **WP Term Order** plugin for drag-and-drop ordering. Developers can also use the `cp_staff_departments_args` filter. See [Departments](departments.md).

## Contact Forms

### How do I enable the contact form?

Navigate to **Staff > Settings > Advanced** and check **Staff contact modal**. Ensure each staff member has an email address set in their profile. See [Contact Form Setup](contact-forms.md).

### Staff are not receiving contact form emails. What should I do?

Install a mail delivery plugin like **WP Mail SMTP** to route emails through a reliable SMTP provider. Check the **From Address** in settings and verify your hosting supports email sending. See [Troubleshooting](troubleshooting.md).

### Can visitors see the staff member's email address?

Only if you enable **Display staff's email address** in **Staff > Settings > Advanced**. By default, the email address is hidden and messages are delivered without revealing it.

### How does spam protection work?

CP Staff supports Google reCAPTCHA v3 (invisible), rate limiting by IP and email address, and domain blocking to prevent internal emails. See [Security](contact-form-security.md).

## Display

### What happens when a visitor clicks a staff card?

This depends on the **Staff click action** setting in **Staff > Settings > Advanced**: nothing (disabled), open the staff member's dedicated page, or show a popup modal with their details.

### Can I show only staff from specific departments?

Yes, use the `[cp_staff_list]` shortcode with the `cp_department` parameter. See [Shortcodes](shortcodes.md).

### Does CP Staff support nested departments?

Yes. Departments are hierarchical — you can create parent and child departments. The archive page and shortcodes respect this hierarchy. See [Departments](departments.md).

## Developer

### Where can I find the available hooks and filters?

See the [Developer Guide](developer-guide.md) for a complete list of actions and filters with code examples.

### Can I add custom fields to staff profiles?

Yes. Target the `cp_staff` post type with CMB2, ACF, or any custom metabox solution. See the [Developer Guide](developer-guide.md) for a CMB2 example.
