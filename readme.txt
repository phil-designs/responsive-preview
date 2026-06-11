=== PhilDesigns Responsive Preview ===
Contributors: phildesigns
Tags: responsive, preview, viewport, mobile, testing
Requires at least: 6.7
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 3.1.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Preview your WordPress site across device viewports, user agents, and animated width walks — directly from the admin toolbar.

== Description ==

Responsive Preview adds a link to the WordPress admin toolbar that opens your site inside a full-screen preview interface. You can switch between device viewport sizes, spoof browser user agents, and use the Responsive Walk feature to animate a smooth sweep from 1440px down to 320px.

= Features =

* **Device Viewports** — desktop, laptops, iPad, iPhones, Pixel, and more; toggle portrait/landscape on rotatable devices
* **Custom Devices** — define your own device presets with a name, width, and height; manage them from the settings page
* **User Agent Switcher** — 13 baked-in agents (Chrome, Firefox, Safari, Edge on desktop and mobile); overrides navigator.userAgent in the iframe via JavaScript
* **Responsive Walk** — play/pause animation that sweeps the iframe from 1440px to 320px over 10 seconds, with a scrubbable progress bar
* **Settings page** — enable or disable any viewport from the device dropdown

= Usage =

1. After activation, click **Responsive Preview** in the admin toolbar (top bar).
2. A new window opens with your site in the preview frame.
3. Use the **SELECT VIEWPORT** dropdown to switch device sizes.
4. Use **SELECT USER AGENT** to override the browser user agent in the iframe.
5. Click the orange play button at the bottom to start a Responsive Walk.

== Installation ==

1. Upload the `responsive-preview` folder to `/wp-content/plugins/`
2. Activate the plugin through the **Plugins** menu in WordPress
3. Click **Responsive Preview** in the admin toolbar to open the preview

== Frequently Asked Questions ==

= Does User Agent switching affect server-side detection? =

No. The user agent is overridden via JavaScript after the iframe loads, so it only affects client-side JavaScript-based UA detection. Server-side UA detection (PHP, server logs) will still see the real browser UA.

= Can I add my own device sizes? =

Yes. Go to **Settings → Responsive Preview**, scroll to the Custom Devices section, and add as many devices as you like.

= Why can't I remove the Desktop viewport? =

The Desktop option (100% width) is always enabled so there is always at least one viewport available.

== Screenshots ==

1. The preview interface showing the admin toolbar link, device selector, and an iPhone-sized viewport.
2. The Responsive Walk bar at the bottom showing the play button and progress scrubber.
3. The settings page with viewport toggles and the custom devices table.

== Changelog ==

= 3.1.0 =
* Added User Agent Switcher — 13 baked-in agents (Chrome, Firefox, Safari, Edge · desktop & mobile)
* Added Responsive Walk — animated iframe width sweep from 1440px to 320px with scrubbable progress bar

= 3.0.0 =
* Switched preview bar to flexbox layout
* Moved orientation toggle out of select box
* Added Settings link on Plugins page
* Added support for enabling/disabling viewports and custom device management
* Updated colours, fonts, and styles

= 2.0.0 =
* Added viewport dropdown with popular device sizes
* Added FontAwesome icons

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 3.1.0 =
Adds User Agent Switcher and Responsive Walk features.
