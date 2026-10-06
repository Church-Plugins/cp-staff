# Staff Contact Forms

CP Staff includes a built-in system for visitors to contact staff members through secure contact forms.

## Contact Form Overview

The contact form system allows website visitors to:

- Send messages directly to staff members
- Access the form through staff profile cards or single staff pages
- Submit messages without seeing the staff email address (optional)

## Enabling Contact Forms

1. Go to Staff > Settings > Advanced
2. Check **Staff contact modal** to enable contact forms

## Contact Form Settings

### Basic Settings

| Setting | Description |
|---------|-------------|
| Staff contact modal | Enable/disable the contact form feature |
| Display staff's email address | Show or hide the staff member's email in the form |
| From Address | The email address that will appear in the "From" field (defaults to site admin email) |
| From Name | The name that will appear in the "From" field (defaults to the site title) |

### Security Settings

CP Staff includes several security features to protect staff from spam:

#### CAPTCHA Protection

1. Check **Enable captcha on message form**
2. Enter **Recaptcha site key** and **Recaptcha secret key** (Google reCAPTCHA v3)
3. Captcha is added only when the box is checked and both keys are saved. Uncheck the box and save to turn it off. A secret key without a site key does not block messages.

#### Email Throttling

1. Check **Enable staff contact form throttling**
2. Set **Max submissions per day from same user** (2-10)
3. This limits submissions from the same IP address or email

#### Staff Protection

**Prevent staff from sending emails** blocks contact-form messages when the sender's address contains your site's domain. Sites that have not saved this setting stay protected. Uncheck the box and save to allow those addresses.

## How the Contact Form Works

1. Visitor clicks the email icon on a staff card or profile
2. Contact form modal appears
3. Visitor enters:
   - **Your Full Name:**
   - **Your Email:**
   - **Email Subject:**
   - **Email Message:**
4. After submission:
   - The form is validated (required fields, CAPTCHA, throttling)
   - The form submits the staff member's post ID. The message is sent to the email saved on that published staff record.
   - The staff member sees who sent it and can reply directly
   - The visitor sees **Email sent!**

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
2. **CAPTCHA failures**: Verify your site key and secret key
3. **Messages being blocked**: Check if the throttling limits need adjustment
4. **Staff can't receive emails**: Messages are blocked when the sender's address contains your site's domain.

For persistent issues, check server logs or contact your host about email delivery.