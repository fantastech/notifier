=== Notifier - Send Bulk Messages & Transactional Notifications ===
Contributors: ramshengale, fantastech
Donate link: https://wanotifier.com
Tags: whatsapp, whatsapp cloud api, bulk messaging, notifications, cloud api, notification, notifier, marketing, bulk message
Requires at least: 5.0
Tested up to: 6.0.1
Stable tag: 0.1.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Send bulk WhatsApp messages and transactional notifications to your contacts and Woocommerce customers using WhatsApp's official [Cloud API](https://developers.facebook.com/docs/whatsapp/cloud-api/overview) from the WordPress backend.

== Description ==

Send bulk WhatsApp messages and transactional notifications using WhatsApp's official [Cloud API](https://developers.facebook.com/docs/whatsapp/cloud-api/overview) from the WordPress backend.

[Notifier](https://wanotifier.com) is the world's first and only FREE WordPress plugin that allows you to **send unlimited WhatsApp broadcast messages** and **transactional notifications** (like WooCommerce order notifications) right from your WordPress backend using **WhatsApp's official [Cloud API](https://developers.facebook.com/docs/whatsapp/cloud-api/overview)**!

WhatsApp recently launched it's Cloud API to let businesses send WhatsApp messages to customer using their official API. Before this if you had to send bulk WhatsApp messages or notifications, there were only two ways to do it:

1. You either used **hack-y Chrome extensions or Windows / mobile apps** that would work on top of WhatsApp Web or your WhatsApp phone app to send the messages in a *shady* and [unauthorized](https://faq.whatsapp.com/1104252539917581/) way. This was a good way to get your phone number **banned** by WhatsApp!

2. Or you had to sign up with one of the WhatsApp approved **Business Service Providers** and pay them high monthly fees to use their SaaS to send the messages. Not only that, they charged a premium of 10% - 20% on top of [WhatsApp's official pricing](https://developers.facebook.com/docs/whatsapp/pricing/).

But that changes with this plugin!

Now you can send the bulk broadcast messages and transactional notifications using the **official WhatsApp way** but without needing to pay for a costly middle-ware SaaS tools. Notifier uses **WhatsApp's official Cloud API** and acts as a FREE bridge between you and WhatsApp to send messages without limitation or charging you a premium on top of their per conversation cost. You settle your billing directly with them!

*Note:* WhatsApp Cloud API allows you to **send upto 1,000 messages for FREE per month**. After that WhatsApp charges you a small fees per conversation as [shown here](https://developers.facebook.com/docs/whatsapp/pricing/).

If you're looking for a **safe, cost friendly and robust** solution for sending WhatsApp broadcasts or messages, this plugin is for you!

= VERY IMPORTANT NOTES BEFORE YOU USE THE PLUGIN =

**PHONE NUMBER**

*   You need to setup your phone number with WhatsApp Business account to be able to use this plugin. Once you setup your phone number with WhatsApp Business account you will **not be able to** use the number in the WhatsApp mobile app.
*   If you were using this phone number on your WhatsApp mobile app, you'll need to **delete the account (whic h will delete all the previous chat history)**. To avoid this you can choose to **use a different spare phone number** just for sending notifications via this plugin.

**ONE-WAY COMMUNICATION**

*   Another important thing to note is that this plugin **only allows you to send [message templates](https://developers.facebook.com/docs/whatsapp/cloud-api/guides/send-message-templates)** which need to be approved by WhatsApp. You **won't be able to send custom text messages or receive customer replies** using this plugin (as of now).
* This can be restrictive to some businesses who use their WhatsApp number for 2-way communication with their customers. If this is you, we suggest **not using this plugin** and coming back later when we add 2-way communication feature for chat at a later date.

= PLUGIN FEATURES =

* Send unlimited bulk marketing messages to your WhatsApp contacts.
* Send transactional WhatsApp notifications to your users on specific triggers / user actions like WooCommerce order placement.
* Create and manage text-based message templates from WordPress backend.
* Create and manage contacts from WordPress backend.
* Import contacts using CSV or import phone numbers from your WooCommerce customers.
* Update WhatsApp business profile details from plugin settings.

= UPCOMING FEATURES =

* Support for more languages in message templates. Currently only supports en_US.
* Inbox - multi=agent, 2-way message communication with your contacts.
* Quick reply buttons in message templates.
* More fields for contacts to use it like a CRM.
* Ability to re-submit rejected templates for approaval.
* More notification triggers as per request.

= GET MORE FEATURES WITH THE PRO PLUGIN (to be released soon) =

* Media support in message templates to send photos, video or document in notification messages.
* Dynamic message template with variables support to send user/customer specific data in notifications. Think of this as email merge tags.
* Ability to fetch and use default WhatsApp message templates.
* Webhooks - create notification on the site and trigger it from any of your apps using Zapier or a similar tool.

== Installation ==

1. Download the plugin zip, upload it to the `/wp-content/plugins/` directory and unzip. Or install the plugin via 'Plugins' page in your WordPress backend.
2. Activate the plugin through the 'Plugins' page.
3. Click on Notifier in the left side admin menu to setup the plugin.

== Frequently Asked Questions ==

= Does this plugin show responses from customers on WhatsApp? =

No. Currently the plugin only allows one-way communication and you can only send pre-approved message templates. You can not read the user responses and 2-way communication is not possible right now.

= What costs are involved using this plugin? =

The plugin itself is free and WhatsApp provides 1000 business initiated messages per month. But you need to pay them per message after that. You can learn more about their pricing [here](https://developers.facebook.com/docs/whatsapp/pricing/). Billing for this is handled by WhatsApp themselves and not via the plugin.

== Screenshots ==

1. Plugin use disclaimer
2. Step-by-step plugin setup instructions
3. Dashboard
4. Message templates page
5. Add new message template
6. Contacts page
7. Notifications
8. Add new notification
9. WhatsApp Business profile settings
10. API configuration

== Changelog ==

= 0.1 =
* Launch of the beta version of the plugin.

== Upgrade Notice ==

= 0.1 =
Launch of the beta version of the plugin.
