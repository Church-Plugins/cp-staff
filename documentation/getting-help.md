---
title: "Getting Help"
slug: getting-help-cp-staff
section: support-cp-staff
order: 2
excerpt: "How to contact support, what information to include, and self-service troubleshooting steps."
status: publish
---

# Getting Help

This guide covers how to get support for CP Staff and what information to include when reaching out.

## Support Channels

### ChurchPlugins Support Portal

For direct support, visit [churchplugins.com/support](https://churchplugins.com/support). Premium license holders receive priority support. <!-- TODO: confirm -->

### Documentation

Browse the full [CP Staff documentation](README.md) for guides on setup, configuration, and customization.

### Community

Connect with other ChurchPlugins users to share tips and ask questions through the [ChurchPlugins community](https://churchplugins.com). <!-- TODO: confirm -->

## Before Contacting Support

Try these self-service steps first:

1. **Check the [FAQ](faq.md)** for answers to common questions
2. **Review [Troubleshooting](troubleshooting.md)** for known issues and solutions
3. **Update the plugin** to the latest version — your issue may already be fixed
4. **Test with a default theme** (e.g., Twenty Twenty-Four) to rule out theme conflicts
5. **Disable other plugins temporarily** to rule out plugin conflicts

## What to Include in a Support Request

Providing detailed information helps resolve your issue faster:

- **WordPress version** (found in **Dashboard > Updates**)
- **CP Staff version** (found in **Plugins** list)
- **PHP version** (found in **Tools > Site Health > Info > Server**)
- **Active theme name and version**
- **Description of the issue** — what you expected vs. what happened
- **Steps to reproduce** — how to recreate the problem
- **Screenshots** — if the issue is visual, include screenshots of the problem
- **Browser console errors** — open your browser's developer tools (F12) and check the Console tab
- **Error logs** — check `wp-content/debug.log` if `WP_DEBUG_LOG` is enabled

## Enabling Debug Mode

If support asks for debug information:

1. Open your site's `wp-config.php` file
2. Add or update these lines:

   ```
   define( 'WP_DEBUG', true );
   define( 'WP_DEBUG_LOG', true );
   define( 'WP_DEBUG_DISPLAY', false );
   ```
3. Reproduce the issue
4. Check `wp-content/debug.log` for relevant error messages
5. Share the relevant log entries with support
6. Disable debug mode when finished by setting `WP_DEBUG` back to `false`
