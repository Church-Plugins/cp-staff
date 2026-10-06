# Troubleshooting

## Staff Archive Page Not Showing

1. Go to **Staff > Settings**, open the **Staff** tab, and leave **Disable Archive Page** unchecked. Click **Save Changes**.
2. The archive slug comes from **Plural Label**. **Our Team** gives `/our-team/`.
3. Switch to a default WordPress theme to see whether the theme is involved.

## Contact Form Emails Not Being Received

1. Confirm the staff member has an **Email** in **Staff Details**.
2. Send WordPress mail through your provider, for example with WP Mail SMTP.
3. Check the recipient's spam folder.
4. Set **From Address** under **Staff > Settings**, on the **Advanced** tab, to an address your mail provider accepts.

## CAPTCHA Not Working

1. Check **Recaptcha site key** and **Recaptcha secret key** under **Staff > Settings**, on the **Advanced** tab.
2. The form uses Google reCAPTCHA v3. Use v3 keys, not v2.
3. Register the site domain in the reCAPTCHA admin console.

## Static Staff Lists

`[cp_staff_list static="true"]` leaves out the card links and the email and phone icons.

## Department and Staff Order

Departments on the archive are ordered by name.

Staff are ordered by **Order**, then by name.

1. Edit the staff member.
2. Open the actions menu (⋮) next to the staff member's title and choose **Order…**. Set **Order**, then click **Save**.

## Rate Limiting

**Enable staff contact form throttling** is off until you check it. **Max submissions per day from same user** starts at 3.

1. On the **Advanced** tab, raise **Max submissions per day from same user**. The choices are 2 through 10.
2. The count resets on the next calendar day.
3. People who share an IP address share that IP's count. Each sender email has its own count.

## Template Overrides Not Working

1. The theme directory must be named `cp-staff`.
2. Keep the same folders the plugin uses under `templates/`.
3. Clear any page cache after you change a template.

More help is at [Getting Help](https://docs.churchplugins.com/knowledge-base/getting-help-cp-staff/).
