=== WA Notifier ===
Contributors: ramshengale, fantastech
Donate link: https://wanotifier.com
Tags: whatsapp, cloud api, notification, notifications, marketing
Requires at least: 5.0
Tested up to: 6.0
Stable tag: 4.3
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Send WhatsApp broadcast messages and transactional notification to your contacts and Woocommerce customers using WhatsApp's official [Cloud API](https://developers.facebook.com/docs/whatsapp/cloud-api/overview).

== Description ==

[WA Notifier](https://wanotifier.com) is the world's first and only FREE WordPress plugin that allows you to **send unlimited WhatsApp broadcast messages** and **transactional notifications** (like WooCommerce order notifications) right from your WordPress backend using **WhatsApp's official Cloud API**!

WhatsApp recently launched it's [Cloud API](https://developers.facebook.com/docs/whatsapp/cloud-api/overview) to let businesses send WhatsApp messages to customer using their official API. Before this if you had to send bulk WhatsApp messages or notifications, there were only two ways to do it:

1. You either used **hack-y Chrome extensions or mobile apps** that would sit on top of WhatsApp web or your WhatsApp phone app to send the messages in a *not-so-elegant*, [unauthorized](https://faq.whatsapp.com/1104252539917581/) way.

2. Or you had to sign up with one of the WhatsApp approved **Business Service Providers** and pay them high monthly fees to use their SaaS to send the messages. Not only that, they charged a premium of 10% - 20% on top of [WhatsApp's official pricing](https://developers.facebook.com/docs/whatsapp/pricing/).

But that changes with this plugin!

Now you can send the bulk broadcast messages and transactional notifications using the official WhatsApp way but without needing to pay for a costly middle-ware SaaS tools. WA Notifier is a FREE bridge between you and WhatsApp that lets you send messages without limitation or charging you a premium on top of their per conversation cost. You settle your billing directly with them!

*Note:* WhatsApp Cloud API allows you to **send upto 1,000 messages for FREE per month**. After that WhatsApp charges you a small fees per conversion as [shown here](https://developers.facebook.com/docs/whatsapp/pricing/).

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
* Create and manage message tempaltes from WordPress backend.
* Manage all your contacts from a single screen.
* Import contacts using CSV or import from your WooCommerce customers.

= UPCOMING FEATURES =

* Support for more languages in message templates. Currently only supports en_US.
* Inbox - 2-way message communication with your contacts.
* Reply to buttons in message templates.
* More fields for contacts as per demand.
* Ability to re-submit rejected templates for approaval.
* More notification triggers as per demand.

= GET MORE FEATURES WITH THE PRO PLUGIN (to be released soon) =

* Media support in message templates to send photos, video or document in message.
* Variables support in message templates to send user/customer specific data in notifications. Think of this as email merge tags.
* Ability to fetch and use default WhatsApp message templates.
* Webhooks - create notification on the site and trigger it from any of your apps using Zapier or a similar tool.

== Installation ==

1. Download the plugin zip, upload it to the `/wp-content/plugins/` directory and unzip. Or install the plugin via 'Plugins' page in your WordPress backend.
2. Activate the plugin through the 'Plugins' page.
3. Click on WA Notifier in the left side admin menu to setup the plugin.

== Frequently Asked Questions ==

= Can I receive WhatsApp responses from customers using this plugin? =

No. Currently the plugin only allows you to send messages and that too using approved message templates. 2-way communication is not possible right now.

= What costs are involved using this plugin? =

The plugin itself is free. WhatsApp provides 1000 free messages per month but you need to pay them per message post that. You can learn more about it [here](https://developers.facebook.com/docs/whatsapp/pricing/). Billing for this is handled by WhatsApp themselves and not via the plugin.

== Screenshots ==

1. This is the first screen shot
2. This is the second screen shot

== Changelog ==

= 0.1 =
* Launch of the beta version of the plugin.

== Upgrade Notice ==

= 0.1 =
Launch of the beta version of the plugin.
