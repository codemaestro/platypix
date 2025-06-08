# Steve's 80th Birthday Photo Album

Web-based slideshow app that can dynamically update photos in real time as users upload new photos to the server.

Watch the progress of this app develop over time on the [codemaestro Substack](https://open.substack.com/pub/codemaestro/p/doing-a-thing-platypix-slideshow)

## Deployment

Configure GitHub webhooks to call the `deployprod.php` script when you push a change to the repo. In the project folder (outside of `dist`) you'll need the following files:

* An empty `deployprod.log` file.

* Populate an `.env` file with the following values:

  * LOGFILE: location of the `deployprod.log` file.

  * SECRET: a SHA1 hash of the passphrase written in the GitHub action.

## File structure

```text
platypix/
├── assets/
│   └── qrcode-template.afdesign
├── dist/
│   ├── images/
│   │   └── (all the photos)
│   ├── uploads/
│   │   ├── error/
│   │   └── processed/
│   ├── favicon.ico
│   ├── index.html
│   ├── slides.css
│   ├── slides.js
│   ├── slides.php
│   └── slideupload.php
├── readme/
│   └── platypix.png
├── .gitignore
├── LICENSE
└── README.md
```

## More information

Visit the [platypix](https://github.com/codemaestro/platypix) project on GitHub. You can also parse this QR code.

![QR code](./readme/platypix.png)

Last update: 2025-06-06 04:51
