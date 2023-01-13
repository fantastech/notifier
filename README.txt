=== WANotifier - Send Message Notifications Using Cloud API ===
Contributors: ramshengale, fantastech
Donate link: https://wanotifier.com
Tags: whatsapp, whatsapp cloud api, woocommerce whatsapp, woocommerce whatsapp order notification, whatsapp for woocommerce, woocommerce whatsapp order, gravity forms whatsapp, contact form 7 whatsapp, click to chat
Requires at least: 5.0
Tested up to: 6.1.1
Stable tag: 2.0.6
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Send WhatsApp message notifications for Woocommerce orders, Gravity Forms and Contact Form 7 subsmissions using the official WhatsApp Cloud APIs.

== Description ==

**Send WhatsApp message notifications** for _Woocommerce_ orders, _Gravity Forms_ and _Contact Form 7_ subsmissions using the official **WhatsApp Cloud APIs**.

This WordPress plugin allows you to integrate your WordPress website with your account at [WANotifier.com](https://wanotifier.com) to help you send WhatsApp notifications when some action is performed on your website like new user registration, WooCommerce order related actions, form submissions and much more!

**Important Note: This plugin requires you to have an account at WANotifier.com to be able to trigger WhatsApp notifications. You can create a *FREE account* with us [by clicking here](https://app.wanotifier.com/create-account/).**

= What you can do with this plugin? =

**WordPress**

* Send WhatsApp notification when a **new post is published**
* Send WhatsApp notification when a **new comment is added**
* Send WhatsApp notification when a **new user is registered**

**WooCommerce**

* Send WhatsApp notification when a **new WooCommerce order is placed**
* Send WhatsApp notification when **order status changes to processing**
* Send WhatsApp notification when **order status changes to completed**
* Send WhatsApp notification when **order status changes to cancelled**
* Send WhatsApp notification when **order status changes to failed**
* Send WhatsApp notification when **order status changes to on-hold**
* Send WhatsApp notification when **order status changes to refunded**
* Send WhatsApp notification for any custom added order status

**Gravity Forms**

* Send WhatsApp notifications when a **Gravity Forms form is submitted**

**Contact Form 7**

* Send WhatsApp notifications when a **Contact Form 7 form is submitted**

**WPForms**

* Send WhatsApp notifications when a **WPForms form is submitted**

**Ninja Forms**

* Send WhatsApp notifications when a **Ninja Forms form is submitted**

Want more triggers for your favorite plugins? Request us [here](https://wanotifier.com/support/) or contact your developer create custom triggers using filter hooks.

= About WANotifier.com =

WANotifier.com is one of it's kind SaaS tool that allows you to **send unlimited WhatsApp broadcast messages** and **transactional notifications** (like WooCommerce order notifications) using [WhatsApp's official Cloud APIs](https://developers.facebook.com/docs/whatsapp/cloud-api/overview)!

WhatsApp provides Cloud APIs to let businesses send WhatsApp messages to their customers using the APIs.

Earlier, if you had to send WhatsApp messages or notifications, there were only two ways to do it:

1. You either used **hack-y browser extensions or desktop / mobile apps** that would work on top of WhatsApp Web or your WhatsApp phone app to send the messages in a *shady* and [unauthorized](https://faq.whatsapp.com/1104252539917581/) way. This was a good way to get your phone number **banned** by WhatsApp!

2. Or, you had to sign up with one of the WhatsApp approved **Business Service Providers** and pay them high monthly fees to use their software to send messages. Not only you had to pay high monthly fees, they even charged you a premium of 10–15% on top of WhatsApp API's [conversation-based pricing](https://developers.facebook.com/docs/whatsapp/pricing/).

But that changes with WANotifier.com!

Now you can send the bulk broadcast messages and transactional notifications using the **official WhatsApp way** but without needing to pay a fortune to middle-ware SaaS tools.

WANotifier.com uses **WhatsApp's official Cloud API** and acts as a simple bridge between you and WhatsApp to send messages without limitation or charging you a premium on top of their per conversation cost. You settle your API usage billing directly with them!

*Note:* WhatsApp Cloud API allows you to **send upto 1,000 messages for FREE per month**. After that WhatsApp charges you a small fees per conversation as [shown here](https://developers.facebook.com/docs/whatsapp/pricing/).

If you're looking for a **safe, cost friendly and robust** solution for sending WhatsApp broadcasts or messages, this tool is for you!

**You can learn more about WANotifier.com using the following links:**

* [Website](https://wanotifier.com/)
* [How it Works?](https://wanotifier.com/#how-it-works)
* [Features](https://wanotifier.com/#features)
* [Pricing](https://wanotifier.com/pricing/)
* [Create your FREE account](https://app.wanotifier.com/create-account/)

= WANotifier.com Features =

Here's everything that you can do with WANotifier's SaaS tool:

**General**

* Send and receive unlimited messages and replies
* Add/import unlimited contacts
* Create and send unlimited notifications

**Create & Manage Message Templates**

* Text-only message templates
* Media templates with an image, video or PDF
* Add call-to-action buttons to your templates
* Dynamic message templates with variables
* Fetch your existing message templates from WhatsApp

**Contacts Management**

* Add unlimited contacts
* Import contacts using CSV
* Import contacts from 3rd party apps using webhooks

**Notifications**

* Send unlimited one-way marketing messages
* Send action-based transactional message notifications
* Trigger notifications from 3rd party apps using webhooks

**Inbox**

* Inbox for sending & receiving messages
* Multi-agent support for chat (Coming soon)

== Installation ==

1. Download the plugin zip, upload it to the `/wp-content/plugins/` directory and unzip. Or install the plugin via 'Plugins' page in your WordPress backend.
2. Activate the plugin through the 'Plugins' page.
3. Click on menu item in the left side admin menu.
4. Follow the instructions on the screen to complete your setup.

== Changelog ==

= 2.0.6 - 2022-01-13 =
* Added Ninja Forms integration

= 2.0.5 - 2022-01-12 =
* Fix: Recipient fields related bug in WPForms integration

= 2.0.4 - 2022-01-11 =
* Added WPForms integration

= 2.0.3 - 2022-01-06 =
* Fix: Added missing WordPress fields to Contact Form 7

= 2.0.2 - 2022-01-04 =
* Fix: Trigger sync message showing on deletiong of triggers
* Fix: Few typos

= 2.0.1 - 2022-12-30 =
* Fix: Woocommerce new order notification not sending

= 2.0.0 - 2022-12-30 =
* Major upgrade with new way to manage triggers
* Added ability to use custom Woocommerce order statuses.
* Send WhatsApp notifications on Gravity Forms form submission.
* Send WhatsApp notifications on Contact Form 7 form submission.

= 1.0.5 - 2022-12-26 =
* New: Improved on-boarding and How to? instructions

= 1.0.4 - 2022-11-10 =
* New: api enpoint upgrade

= 1.0.3 - 2022-11-08 =
* Fix: checkout not happening error

= 1.0.2 - 2022-10-28 =
* Fix: firing multiple notifications at the same time

= 1.0.1 - 2022-10-27 =
* Tested upto WP 6.1

= 1.0.0 - 2022-10-09 =
* Converted the plugin to provide integration with WANotifier.com

= 0.1.1 - 2022-08-04 =
* Fix - Minor bug fixes and code cleanup

= 0.1.0 - 2022-07-30 =
* Launch of the beta version of the plugin.

== Upgrade Notice ==

= 2.0.6 - 2022-01-13 =
* Added Ninja Forms integration

= 2.0.5 - 2022-01-12 =
* Fix: Recipient fields related bug in WPForms integration

= 2.0.4 - 2022-01-11 =
* Added WPForms integration

= 2.0.3 - 2022-01-06 =
* Fix: Added missing WordPress fields to Contact Form 7

= 2.0.2 - 2022-01-04 =
* Fix: Trigger sync message showing on deletiong of triggers
* Fix: Few typos

= 2.0.1 - 2022-12-30 =
* Fix: Woocommerce new order notification not sending

= 2.0.0 - 2022-12-30 =
* Major upgrade with new way to manage triggers
* Added ability to use custom Woocommerce order statuses.
* Send WhatsApp notifications on Gravity Forms form submission.
* Send WhatsApp notifications on Contact Form 7 form submission.

= 1.0.5 - 2022-12-26 =
* New: Improved on-boarding and How to? instructions

= 1.0.4 - 2022-11-10 =
* New: api enpoint upgrade

= 1.0.3 - 2022-11-08 =
* Fix: checkout not happening error

= 1.0.2 - 2022-10-28 =
* Fix: firing multiple notifications at the same time

= 1.0.1 - 2022-10-27 =
* Tested upto WP 6.1

= 1.0.0 - 2022-10-09 =
* Converted the plugin to provide integration with WANotifier.com

= 0.1.1 - 2022-08-04 =
* Fix - Minor bug fixes and code cleanup

= 0.1.0 - 2022-07-30 =
* Launch of the beta version of the plugin.
