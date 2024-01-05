=== WANotifier - Send Message Notifications Using Cloud API ===
Contributors: wanotifier
Donate link: https://wanotifier.com
Tags: whatsapp, whatsapp cloud api, woocommerce whatsapp, whatsapp for woocommerce, whatsapp integration, gravity forms whatsapp, contact form 7 whatsapp, abandoned cart
Requires at least: 5.0
Tested up to: 6.2
Stable tag: 2.5.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Send WhatsApp notifications using the official WhatsApp Cloud APIs for Woocommerce orders and form sumissions for Gravity Forms, Contact Form 7 & more.

== Description ==

**Send WhatsApp notifications** for Woocommerce orders, abadoned cart recovery, form submissions from _Contact Form 7_, _Gravity Forms_, _WPForms_ & other popular form plugins and for a lot of other such actions using the **official WhatsApp Cloud APIs**.

**NOTE:** This plugin requires you to have an account at [WANotifier.com](https://wanotifier.com/). Create your **FREE forever** account now by [clicking here](https://app.wanotifier.com/create-account/).

Here's everything that you can do with this plugin...

== WhatsApp Integration with Core WordPress ==

* Send WhatsApp notification when a **new post or page is published**
* Send WhatsApp notification when a post of any **custom post type is published**
* Send WhatsApp notification when a **new comment is added**
* Send WhatsApp notification when a **new user is registered**

== WhatsApp Notifications for WooCommerce Orders ==

* Send WhatsApp notification when a **new WooCommerce order is placed**
* Send WhatsApp notification when a **new WooCommerce order is placed with COD payment method**
* Send WhatsApp notification on all **order status changes**

== WhatsApp Integration with WooCommerce Addons ==

* [WooCommerce Cart Abandonment Recovery](https://wordpress.org/plugins/woo-cart-abandonment-recovery/) (v1.2.25 or higher) - send WhatsApp notification for abondoned cart recovery

== WhatsApp Integration with Form Plugins ==

Send WhatsApp notification when a form is submitted in any of the following form plugins:

* [Contact Form 7](https://wordpress.org/plugins/contact-form-7/)
* [Gravity Forms](https://www.gravityforms.com/)
* [WPForms](https://wordpress.org/plugins/wpforms-lite/)
* [Ninja Forms](https://wordpress.org/plugins/ninja-forms/)
* [Formidable Forms](https://wordpress.org/plugins/formidable/)
* [Fluent Forms](https://wordpress.org/plugins/fluentform/)

We keep adding more integrations with new plugin updates. If you want integration with your favorite plugins, you can request us [here](https://wanotifier.com/support/) or you can contact your developer create custom triggers using the filter hooks we provide.

== About WANotifier.com ==

WANotifier.com is one of it's kind SaaS tool that allows you to **send unlimited WhatsApp broadcast messages** and **transactional notifications** from your website using [WhatsApp's official Cloud APIs](https://developers.facebook.com/docs/whatsapp/cloud-api/overview)!

WhatsApp provides Cloud APIs to let businesses send WhatsApp messages to their customers using their APIs.

Earlier, if you had to send WhatsApp messages or notifications, there were only two ways to do it:

1. You either used **hack-y browser extensions or desktop / mobile apps** that would work on top of WhatsApp Web or your WhatsApp phone app to send the messages in a *shady* and [unauthorized](https://faq.whatsapp.com/1104252539917581/) way. This was a good way to get your phone number **banned** by WhatsApp!

2. Or, you had to sign up with one of the WhatsApp approved **Business Service Providers (BSPs)**, who acted like brokers between you and WhatsApp, and you had to pay them high monthly fees to use APIs. Not only you had to pay high monthly fees, they even charged you a premium of 15–20% on top of WhatsApp API's [conversation-based pricing](https://developers.facebook.com/docs/whatsapp/pricing/).

**But that changes with WANotifier.com!**

Now you can send the bulk broadcast messages and transactional notifications using the **official WhatsApp way** but without needing to pay a fortune to the middlemen BSPs.

WANotifier.com uses **WhatsApp's official Cloud API** and acts as a simple bridge between you and WhatsApp to send messages without limitation or charging you a premium on top of their per conversation cost. You settle your API usage billing directly with them!

**Note: WhatsApp Cloud API are paid APIs. They charge you a small fees per conversation as [shown here](https://developers.facebook.com/docs/whatsapp/pricing/) that you need to settle with them directly on their portal.**

If you're looking for a **cost friendly and robust** solution for sending WhatsApp broadcasts or messages, this tool is for you!

**You can learn more about WANotifier.com using the following links:**

* [Visit website](https://wanotifier.com/)
* [How it Works?](https://wanotifier.com/#how-it-works)
* [Features](https://wanotifier.com/#features)
* [Pricing](https://wanotifier.com/pricing/)
* [Create your FREE account](https://app.wanotifier.com/create-account/)

== WANotifier.com Features ==

Here's everything that you can do with WANotifier.com, no coding knowledge required!

**Create & Manage Message Templates**

* Create text message templates
* Create media templates with an image, video or PDF
* Add call-to-action buttons to your templates
* Create dynamic message templates with variables
* Fetch your existing message templates from WhatsApp

**Contacts Management**

* Add and manage contacts with our mini-CRM
* Import contacts using CSV
* Import contacts from 3rd party apps using webhooks
* Subscribe / unsubscribe contacts from your messages
* Contact management with Contact Lists and Tags
* Add custom attributes to contacts

**Notifications**

* Send bulk marketing messages on WhatsApp
* Send action-based transactional message notifications
* Trigger notifications from 3rd party apps using webhooks
* Get full analytics on the sent, delivered and read messages

**Automations**

* Automatically send greeting messages for incoming messages
* Send delay in response message when you're not online to reply
* Auto-unsubscribe system to unsubscribe users when they send a particular word like STOP
* Custom auto-replies to send a pre-defined message when user send a particular word or phrase

**Inbox**

* Send & receive messages from a full-fledged Inbox

And a lot more. Visit [WANotifier.com](https://wanotifier.com) to learn more.

== Installation ==

1. Download the plugin zip, upload it to the `/wp-content/plugins/` directory and unzip. Or install the plugin via 'Plugins' page in your WordPress backend.
2. Activate the plugin through the 'Plugins' page.
3. Click on menu item in the left side admin menu.
4. Follow the instructions on the screen to complete your setup.

== Changelog ==
= 2.5.0 - 2023-11-03 =
* add: Added new option of export woocommerce customers user under tools menu.

= 2.4.6 - 2023-11-03 =
* fix: error log warnings

= 2.4.5 - 2023-11-01 =
* mod: some info text updates

= 2.4.4 - 2023-08-08 =
* fix: Product list not sending in New Order Trigger

= 2.4.3 - 2023-06-21 =
* fix: Woocommerce Cart Abandoned Recovery plugin not triggering notifications

= 2.4.2 - 2023-06-13 =
* add: option to select whether to trigger in real time or asynchonously using Action Scheduler
* fix: minor bug fixes

= 2.4.1 - 2023-06-01 =
* fix: Fluent Forms and Ninja Forms triggers getting triggered for all form subsmissions

= 2.4.0 - 2023-05-25 =
* add: Woocommerce Cart Abandoned Recovery plugin integration

= 2.3.0 - 2023-05-04 =
* add: default country code for recipient fields.
* add: option to enable hidden custom meta keys that start with underscore.
* mod: changed custom meta data fields type so it's available to map in both body and header.
* fix: country code not getting added to billing and shipping phone numbers for US numbers in WooCommerce.
* fix: woocommerce custom meta key not saving issue.

= 2.2.4 - 2023-05-03 =
* fix: Some minor typo fixes

= 2.2.3 - 2023-04-24 =
* add: WooCommerce order meta and customer user meta data fields and recipient fields
* add: new data field for Order payment URL
* add: few style fixes

= 2.2.2 - 2023-04-19 =
* add: new trigger - new order placed with COD payment method
* add: added trigger description to the Trigger dropdown
* add: custom fields support in Recipient Fields (experimental)

= 2.2.1 - 2023-04-17 =
* add: further speed boost - optimized code for fewer db queries

= 2.2.0 - 2023-04-13 =
* fix: made triggers more unique with site key
* add: UI and content updates
* add: replaced direct firing of actions with action scheduler to drastically improve performance
* add: custom meta fields for post types
* mod: changed WooCommerce new order hook from woocommerce_thankyou to woocommerce_new_order

= 2.1.3 - 2023-03-09 =
* fix: Contact Form 7 error in logs

= 2.1.2 - 2023-02-28 =
* fix: Contact Form 7 forms not visible
* fix: tel* fields not showing in Recipient Fields for Contact Form 7

= 2.1.1 - 2023-02-13 =
* fix: Order product items field was sending empty data

= 2.1.0 - 2023-02-03 =
* add: Click to chat feature
* add: support for custom post types
* add: product names field for WooCommerce

= 2.0.10 - 2023-01-29 =
* fix: error on saving triggers

= 2.0.9 - 2023-01-26 =
* Updated onboarding instructions & added testimonials

= 2.0.8 - 2023-01-25 =
* Added Fluent Forms integration

= 2.0.7 - 2023-01-18 =
* Added Formidable Forms integration

= 2.0.6 - 2023-01-13 =
* Added Ninja Forms integration

= 2.0.5 - 2023-01-12 =
* Fix: Recipient fields related bug in WPForms integration

= 2.0.4 - 2023-01-11 =
* Added WPForms integration

= 2.0.3 - 2023-01-06 =
* Fix: Added missing WordPress fields to Contact Form 7

= 2.0.2 - 2023-01-04 =
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
