<?php

// /privacy, /terms and /refunds. A draft written to match how the app works: have it reviewed before launch.
// :operator, :email, :trial_days and :live_days are filled in by LegalController from config and admin settings.
return [
    'common' => [
        'updated' => 'Last updated: 7 November 2026',
        'contact' => 'Questions? Write to',
    ],

    'privacy' => [
        'title' => 'Privacy policy',
        'intro' => ':operator makes online wedding invitations. This page explains what we keep about couples and their guests, why, and how to have it removed.',
        'sections' => [
            [
                'heading' => 'What couples give us',
                'body' => [
                    'When you sign in with Google, we receive your name, email address and profile picture. We use them to identify your account and to email you about your invitation and payments.',
                    'Everything you enter for your invitation: the names of the couple and parents, dates, venues, wording, photos, music, video links and gift account details (bank, account name and number, KHQR image).',
                    'Payment records: the plan, amount, reference and the screenshot of your transfer. Screenshots are stored privately; only we can open them.',
                ],
            ],
            [
                'heading' => 'Guests',
                'body' => [
                    'Couples add their guests: name, title, side, group and, if they choose, a phone number. We keep this only to make each guest’s personal link and show their name on it, on the couple’s behalf.',
                    'When a guest taps the envelope to open the invitation, we record that it was opened and when, so the couple can see who has seen it. A link preview in Telegram or Messenger does not count as an open.',
                    'Guests don’t need an account. If you are a guest and want your name removed, ask the couple or write to us.',
                ],
            ],
            [
                'heading' => 'Photos',
                'body' => [
                    'Photos are resized and saved again before anyone sees them, which removes hidden details such as the camera and the place where the photo was taken (GPS location). The original upload is deleted.',
                ],
            ],
            [
                'heading' => 'Who sees it',
                'body' => [
                    'Anyone with an invitation link can see that invitation, so share links only with your guests. Invitation pages ask search engines not to list them.',
                    'We never sell personal data or use it for advertising. We use a few services to run Mongkol: Google (sign-in), our server host (storage), an email service (our emails to you) and Telegram (alerts to us about new payments, with your email and the payment reference). YouTube shows a video only when a guest taps play.',
                ],
            ],
            [
                'heading' => 'Cookies and storage',
                'body' => [
                    'Signed-in couples get a session cookie to stay signed in. Guests’ browsers remember the language they chose. We use no advertising or tracking cookies.',
                ],
            ],
            [
                'heading' => 'How long we keep it',
                'body' => [
                    'An invitation stays online until :live_days days after the wedding. After that its links show a short notice instead of the invitation. Payment records are kept for our accounts.',
                    'You can export your guest list at any time. To delete your invitation, its photos and guest list, or your whole account, email us and we will do it within 7 days.',
                ],
            ],
            [
                'heading' => 'Changes',
                'body' => [
                    'If we change this policy, we will update the date at the top. For important changes we will email couples with an active invitation.',
                ],
            ],
        ],
    ],

    'terms' => [
        'title' => 'Terms of use',
        'intro' => 'These terms apply when you use :operator to create and share a wedding invitation. By signing in, you agree to them.',
        'sections' => [
            [
                'heading' => 'The service',
                'body' => [
                    ':operator lets a couple build an online wedding invitation, share a link for everyone and a personal link for each guest, and show the details of the ceremonies, venues and gifts.',
                ],
            ],
            [
                'heading' => 'Free preview',
                'body' => [
                    'Every new invitation has a free preview for :trial_days days. During it, guests can open the links and see a “Preview” ribbon, and personal links are limited.',
                    'When the preview ends without payment, the links show a short notice instead of the invitation. Nothing you entered is lost; paying brings the invitation back.',
                ],
            ],
            [
                'heading' => 'Paying',
                'body' => [
                    'You pay by KHQR bank transfer for the plan you choose, with the reference shown (for example KW-12) in the transfer note, and upload a screenshot. We check the transfer and approve it, usually within a day.',
                    'Once approved, the invitation is live until :live_days days after the wedding date. Prices and what each plan includes are shown on the upgrade page. Refunds follow our refund policy.',
                ],
            ],
            [
                'heading' => 'Your content',
                'body' => [
                    'You own what you put in your invitation. You let us store and show it to the people you share your links with.',
                    'You must have the right to use the photos, music and videos you add, and your guests’ agreement to list their names and phone numbers.',
                    'Don’t use :operator for anything unlawful, hateful or misleading, or for anything other than an event invitation. We may suspend an invitation that breaks these rules; if it was paid, we will tell you why.',
                ],
            ],
            [
                'heading' => 'Availability',
                'body' => [
                    'We work to keep invitations online, but we can’t promise the service will never be interrupted. We are not responsible for losses caused by an interruption beyond refunding what you paid for an invitation that never went live.',
                ],
            ],
            [
                'heading' => 'Changes and law',
                'body' => [
                    'We may update these terms; the date at the top shows the latest version. These terms are governed by the laws of the Kingdom of Cambodia.',
                ],
            ],
        ],
    ],

    'refunds' => [
        'title' => 'Refund policy',
        'intro' => 'We want paying to feel safe. Here is when you get your money back.',
        'sections' => [
            [
                'heading' => 'Before we approve: full refund',
                'body' => [
                    'If you have paid but we haven’t approved the payment yet, you can change your mind and get your money back in full. Write to us with your reference (for example KW-12).',
                    'If we can’t approve a payment, for example because the amount is wrong, we tell you why. If money reached us for a payment we didn’t approve, we return it in full.',
                ],
            ],
            [
                'heading' => 'After approval: no refund',
                'body' => [
                    'Once a payment is approved, the invitation is live and the service has been delivered, so the payment is final.',
                    'One exception: if the invitation can’t work because of a fault on our side that we can’t fix, we refund you in full.',
                ],
            ],
            [
                'heading' => 'How refunds are paid',
                'body' => [
                    'We return the money to the account it came from, within 7 days of agreeing the refund.',
                ],
            ],
        ],
    ],
];
