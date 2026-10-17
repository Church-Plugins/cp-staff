# Contact Form Setup

Visitors can send a message to a staff member. WordPress delivers it with `wp_mail()`. The visitor's name and address are the reply-to.

## Enabling the Form

1. Go to **Staff > Settings**, then open the **Advanced** tab.
2. Check **Staff contact modal**.
3. Click **Save Changes**.

Put an **Email** on the staff member and publish them. The form sends to that address. A draft staff member can't receive messages.

On the single staff page, the email button is shown when **Staff contact modal** is checked and the staff member has an **Email**.

## Settings

These fields are on **Staff > Settings**, on the **Advanced** tab.

| Setting | Description | Default |
|---------|-------------|---------|
| **Staff contact modal** | Shows the contact form for a staff member who has an **Email**. While it is unchecked, messages are not sent. | Off |
| **Display staff's email address** | Shows the staff email in the form | Off |
| **From Address** | Address on the From header | Site admin email |
| **From Name** | Name on the From header | Site title |

## How the Form Works

1. The visitor opens the form from the email icon.
2. The heading is **Send a message to**, followed by the staff member's name.
3. The form asks for **Your Full Name:**, **Your Email:**, **Email Subject:**, and **Email Message:**.
4. All four are required. **Your Email:** must be an email address.
5. The visitor clicks **Send**.
6. If the checks pass, the visitor sees **Email sent!** The dialog closes after 3 seconds.

When **Display staff's email address** is checked, the form also shows **To:** with the staff email.

When **Staff contact modal** is checked, the page includes the staff email as base64 in a meta tag.

## Email Delivery

`wp_mail()` sends the message to the **Email** saved on the published staff member. The form sends the staff member's ID, so the address can't be changed in the browser. The From header uses **From Name** and **From Address**. The reply-to is the visitor. The subject line starts with **[Web Inquiry]**.

If mail does not arrive:

1. Confirm the staff member has an **Email**.
2. Send WordPress mail through your provider, for example with WP Mail SMTP.
3. Use a **From Address** your provider accepts.

Spam protection is covered in [Contact Form Security](contact-form-security.md).

Email problems are covered in [Troubleshooting](troubleshooting.md).

Form markup is covered in [Template Overrides](customization.md).

Filters for the subject and message are covered in the [Developer Guide](developer-guide.md).
