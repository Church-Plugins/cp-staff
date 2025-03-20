# Staff Contact Forms

CP Staff includes a built-in system for visitors to contact staff members through secure contact forms.

## Contact Form Overview

The contact form system allows website visitors to:

- Send messages directly to staff members
- Access the form through staff profile cards or single staff pages
- Submit messages without seeing the staff email address (optional)

## Enabling Contact Forms

1. Go to Staff > Settings > Advanced
2. Check "Staff contact modal" to enable contact forms
3. Configure additional options as needed

## Contact Form Settings

### Basic Settings

| Setting | Description |
|---------|-------------|
| Use email modal | Enable/disable the contact form feature |
| Display staff's email address | Show or hide the staff member's email in the form |
| From Address | The email address that will appear in the "From" field (defaults to site admin email) |
| From Name | The name that will appear in the "From" field (defaults to site name) |

### Security Settings

CP Staff includes several security features to protect staff from spam:

#### CAPTCHA Protection

1. Check "Enable captcha on message form"
2. Enter your Google reCAPTCHA v3 site key and secret key
3. This will add invisible CAPTCHA validation to all submissions

#### Email Throttling

1. Check "Enable staff contact form throttling"
2. Select the maximum number of submissions allowed per day (2-10)
3. This limits submissions from the same IP address or email

#### Staff Protection

1. Check "Prevent staff from sending emails" (enabled by default)
2. This blocks submissions from email addresses containing your site's domain
3. Prevents staff from receiving emails from themselves or colleagues

## How the Contact Form Works

1. Visitor clicks the email icon on a staff card or profile
2. Contact form modal appears
3. Visitor enters:
   - Their full name
   - Their email address
   - Subject line
   - Message content
4. After submission:
   - The form is validated (required fields, CAPTCHA, throttling)
   - An email is sent to the staff member
   - The staff member sees who sent it and can reply directly
   - The visitor sees a success message

## Customizing the Contact Form

You can customize the appearance and behavior of the contact form:

### Template Override

1. Create a `cp-staff` directory in your theme
2. Copy `parts/email-modal.php` from the plugin to your theme's `cp-staff/parts/` directory
3. Modify the template as needed

### Form Text Customization

Use these filters to customize text in the contact form:

```php
// Customize the subject prefix
add_filter('cp_staff_email_subject', function($subject, $original) {
    return '[Contact Request] ' . $original;
}, 10, 2);

// Customize the message suffix
add_filter('cp_staff_email_message_suffix', function($suffix) {
    return '<br><br>--<br>This message was sent via our website contact form.';
});
```

## Troubleshooting

If contact forms aren't working correctly:

1. **Emails not sending**: Check your site's email configuration using a plugin like WP Mail SMTP
2. **CAPTCHA failures**: Verify your site and secret keys are correct, or temporarily disable CAPTCHA
3. **Messages being blocked**: Check if the throttling limits need adjustment
4. **Staff can't receive emails**: Make sure "Prevent staff from sending emails" doesn't block legitimate messages

For persistent issues, check server logs or contact your host about email delivery.