---
title: "Contact-form setup"
slug: contact-form-security-cp-staff
section: options-and-features-cp-staff
order: 3
excerpt: "Contact-form settings are on Staff > Settings, on the Advanced tab."
status: publish
---

# Contact-form setup

The contact form settings are on **Staff > Settings**, on the **Advanced** tab.

## Defaults

**Enable captcha on message form** and **Prevent staff from sending emails** start checked. Uncheck either one and click **Save Changes** to turn it off.

**Enable staff contact form throttling** is off until you check it. **Max submissions per day from same user** starts at 3.

## CAPTCHA

Check **Enable captcha on message form**, then enter **Recaptcha site key** and **Recaptcha secret key** from a Google reCAPTCHA v3 site.

### Getting reCAPTCHA Keys

1. Go to the [Google reCAPTCHA admin console](https://www.google.com/recaptcha/admin)
2. Register a new site and select **reCAPTCHA v3**
3. Add your website domain
4. Copy the **Site Key** and **Secret Key** into the CP Staff settings

Captcha runs only when **Enable captcha on message form** is checked and both keys are set. Otherwise the form sends without captcha.

## Email Throttling

Check **Enable staff contact form throttling**, then set **Max submissions per day from same user**. The choices are 2 through 10.

CP Staff counts the visitor's IP address and the sender's email address separately. If either count goes over the max, the submission is blocked. The counts are kept for the calendar day.

## Domain Blocking

**Prevent staff from sending emails** blocks a message when the sender's address contains the site's domain. Uncheck it to allow those addresses.

## Recipient

The form sends the staff member's post ID, not an email address. CP Staff sends the message to the **Email** saved on that staff member. The staff member must be published.

## Nonce

Each submission includes a WordPress nonce. CP Staff rejects the request when the nonce does not verify.

## Check Order

CP Staff runs these checks in order:

1. **Staff contact modal** is checked. If not, the form shows "Messaging is not available."
2. The staff member's ID was sent. If not, the form shows "Please refresh the page and try again."
3. The nonce, and a published staff member with an **Email**
4. **Your Full Name:**
5. **Your Email:**
6. The daily submission count, when throttling is on
7. **Email Subject:**
8. **Email Message:**
9. The site-domain block, when **Prevent staff from sending emails** is checked
10. The reCAPTCHA score, when captcha is checked and both keys are set

A failed check returns an error in the form.

Form setup is covered in [Contact Form Setup](contact-forms.md). Email delivery is covered in [Troubleshooting](troubleshooting.md).
