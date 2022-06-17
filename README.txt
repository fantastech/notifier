=== WA Notifier - Send Messages Using WhatsApp Cloud API ===
Contributors: ramshengale
Donate link: https://wanotifier.com
Tags: whatsapp, cloud api, notification, notifications, marketing
Requires at least: 5.0
Tested up to: 6.0
Stable tag: 4.3
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Send WhatsApp notifications and broadcasts from your WordPress website using WhatsApp's official [Cloud API](https://developers.facebook.com/docs/whatsapp/cloud-api/overview).

== Description ==

[WA Notifier](https://wanotifier.com) is the first and only FREE WordPress plugin that allows you to send WhatsApp notifications and broadcasts to your users from your WordPress website backend using WhatsApp's official [Cloud API](https://developers.facebook.com/docs/whatsapp/cloud-api/overview).

No sign up required with any 3rd party WhatsApp Business Service Providers. This is a self-hosted free WordPress plugin that uses WhatsApp's recently launched Cloud API to allow you to send notification messages to your customers on WhatsApp right from within the WordPress dasboard.

All you need to do is setup your [Facebook Developers account](https://developers.facebook.com/), [create an app](https://developers.facebook.com/apps/create/) for WhatsApp Cloud API using your phone number and add the app's credentials in the plugin. That's all and you're ready to go!

With WhatsApp Cloud API you can send upto 1000 messages for free per month. Post that WhatsApp charges you a small fees per message as [shown here](https://developers.facebook.com/docs/whatsapp/pricing/).

**IMPORTANT NOTES**

*   You need to setup your phone number with WhatsApp Business account to be able to use this plugin.
*   Once you setup your phone number with WhatsApp Business account you will not be able to use the number in the WhatsApp Business mobile app.
*   If you were using this phone number on your WhatsApp Business mobile app, you'll need to delete the account (which will delete all the previous chat history). Make sure to backup your chat before you decide to use the number for this plugin. You can also choose to use a different number just for sending notifications via this plugin.
*   This plugin only allows you to send [message templates](https://developers.facebook.com/docs/whatsapp/cloud-api/guides/send-message-templates). You won't be able to send custom text messages or receive customer replies using this plugin (as of now). This can be restrictive to some businesses who use their WhatsApp number for 2-way communication with their customers. If this is you, we suggest **not using this plugin** and coming back later when we add 2-way communication feature for chat at a later date.
*   Before you can send a message template to your users, it needs to be approved by WhatsApp first. When you create a new message template from the plugin backend, it is sent to WhatsApp for approval. They review the template as per their [guidelines](https://developers.facebook.com/docs/whatsapp/message-templates/guidelines/) and once it is approved, you can then send it to your users / customers.

**PLUGIN FEATURES**

* Create message tempaltes from WordPress backend and send to WhatsApp for Approval.
* Manage all your contacts from a single screen.
* Import contacts using CSV or import from your exiting site users.
* Manage your contacts with lists and tags.
* Send transactional WhatsApp notifications to your users on specific triggers / user actions.
* Send bulk marketing WhatsApp messages to your users.

**UPCOMING FEATURES**

* Inbox - 2-way message communication with your contacts
* Merge tags in message templates and notifications.
* Multi language support
* Woocommerce order related notifications

== Installation ==

1. Upload `wa-notifier.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Click on WA Notifier in the left side admin menu to setup the plugin.

== Frequently Asked Questions ==

= Can I receive WhatsApp responses from customers using this plugin? =

No. Currently the plugin only allows you to send messages and that too using approved message templates. 2-way communication is not possible right now.

= What costs are involved using this plugin? =

This is a completely free plugin. WhatsApp provides 1000 free messages per month but you need to pay them per message post that. You can learn more about it [here](https://developers.facebook.com/docs/whatsapp/pricing/).

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 0.1 =
* Launch of the beta version of the plugin.

== Upgrade Notice ==

= 0.1 =
Launch of the beta version of the plugin.
