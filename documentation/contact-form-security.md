# Contact Form Security

The contact form settings are on **Staff > Settings**, on the **Advanced** tab.

## Defaults

**Enable staff contact form throttling** is off until you check it. **Max submissions per day from same user** starts at 3.

## CAPTCHA

Enter **Recaptcha site key** and **Recaptcha secret key** from a Google reCAPTCHA v3 site.

Captcha needs both a site key and a secret key.

## Email Throttling

Check **Enable staff contact form throttling**, then set **Max submissions per day from same user**. The choices are 2 through 10.

CP Staff counts the visitor's IP address and the sender's email address separately. If either count goes over the max, the submission is blocked. The counts are kept for the calendar day.

## Domain Blocking

**Prevent staff from sending emails** blocks a message when the sender's address contains the site's domain.

## Nonce

Each submission includes a WordPress nonce. CP Staff rejects the request when the nonce does not verify.

## Check Order

CP Staff runs these checks in order:

1. The nonce
2. **Your Full Name:**
3. **Your Email:**
4. The daily submission count, when throttling is on
5. **Email Subject:**
6. **Email Message:**
7. The site-domain block
8. The reCAPTCHA score, when captcha is set up

A failed check returns an error in the form.

Form setup is covered in [Contact Form Setup](contact-forms.md). Email delivery is covered in [Troubleshooting](troubleshooting.md).
