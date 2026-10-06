# Contact Form Setup

Visitors can send a message to a staff member. WordPress delivers it with `wp_mail()`. The visitor's name and address are the reply-to.

## Enabling the Form

1. Go to **Staff > Settings**, then open the **Advanced** tab.
2. Check **Staff contact modal**.
3. Click **Save Changes**.

Put an **Email** on the staff member. The form sends to that address.

On the single staff page, the email button is shown when **Staff contact modal** is checked and the staff member has an **Email**.

## Settings

These fields are on **Staff > Settings**, on the **Advanced** tab.

| Setting | Description | Default |
|---------|-------------|---------|
| **Staff contact modal** | Shows the contact form for a staff member who has an **Email** | Off |
| **Display staff's email address** | Shows the staff email in the form | Off |
| **From Address** | Address on the From header. A blank value uses the site admin email. | Site admin email |
| **From Name** | Name on the From header. A blank value uses the site title. | Site title |

## How the Form Works

1. The visitor opens the form from the email icon.
2. The heading is **Send a message to**, followed by the staff member's name.
3. The form asks for **Your Full Name:**, **Your Email:**, **Email Subject:**, and **Email Message:**.
4. All four are required. **Your Email:** must be an email address.
5. The visitor clicks **Send**.
6. On success the visitor sees **Email sent!** The dialog closes after 3 seconds.

When **Display staff's email address** is checked, the form also shows **To:** with the staff email.

When **Staff contact modal** is checked, the page includes the staff email as base64 in a meta tag.

## Email Delivery

`wp_mail()` sends the message to the staff member's **Email**. The From header uses **From Name** and **From Address**. The reply-to is the visitor. The subject line starts with **[Web Inquiry]**.

If mail does not arrive:

1. Confirm the staff member has an **Email**.
2. Send WordPress mail through your provider, for example with WP Mail SMTP.
3. Use a **From Address** your provider accepts.

Spam protection is covered in [Contact Form Security](https://docs.churchplugins.com/knowledge-base/contact-form-security-cp-staff/).

Email problems are covered in [Troubleshooting](https://docs.churchplugins.com/knowledge-base/troubleshooting-cp-staff/).

Form markup is covered in [Template Overrides](customization.md).

Filters for the subject and message are covered in the [Developer Guide](https://docs.churchplugins.com/knowledge-base/developer-guide-cp-staff/).
