# Platypix Dynamic Web Photo Album

Web-based slideshow app that can dynamically update photos in real time as users upload new photos to the server.

Watch the progress of this app develop over time on the [codemaestro Substack](https://open.substack.com/pub/codemaestro/p/doing-a-thing-platypix-slideshow)

## Deployment

Configure GitHub webhooks to call the `deployprod.php` script when you push a change to the repo. In the project folder (outside of `dist`) you'll need the following files:

* An empty `deployprod.log` file.

* Populate an `.env` file with the following values (copy from `.env-TEMPLATE`):

  * LOGFILE: location of the `deployprod.log` file.

  * ACCESSLOG: location of the `accesslog.log` file.

  * SECRET: The passphrase included in the GitHub action.

  * ACCESSPASS: Password for sign on auth

  * COOKIESET: An arbitrary key name for the login cookie

  * COOKIEVAL: An arbitrary comparison value

## File structure

```text
platypix/
├── assets/
│   └── qrcode-template.afdesign
├── dist/
│   ├── images/
│   │   ├── .htaccess               For viewing file list - delete if not needed
│   │   └── (all the photos)
│   ├── uploads/
│   │   ├── error/                  Files that failed processing (either process)
│   │   ├── original/               Files for processing by slideprocess.php - local server only
│   │   ├── processed/              Original files processed by slideupload.php to /images
│   │   └── saved/                  Original files processed by slideprocess.php to /images
│   ├── deployprod.php              Call this script from the GitHub webhook (rename this in production)
│   ├── favicon.ico
│   ├── index.php
│   ├── login.php
│   ├── slideprocess.php            Run from local server (rename in production)
│   ├── slides.css
│   ├── slides.js
│   ├── slides.php
│   └── slideupload.php
├── lib/
│   ├── auth.php                    Authentication library for inclusion
│   └── footer.php                  Shared footer for inclusion in all files
├── logs/                           Default location to write log files
│   └── .donotdelete
├── readme/
│   └── platypix.png
├── .env-TEMPLATE
├── .gitignore
├── LICENSE
└── README.md
```

## More information

Visit the [platypix](https://github.com/codemaestro/platypix) project on GitHub. You can also parse this QR code.

![QR code](./readme/platypix.png)

Last update: 2025-06-07 23:30
