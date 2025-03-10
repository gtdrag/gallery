=== TAGG - The Awesome Gallery Generator ===
Contributors: yourname
Tags: gallery, logos, partners, clients, portfolio
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple and beautiful logo gallery plugin for displaying partner and client logos.

== Description ==

TAGG (The Awesome Gallery Generator) is a lightweight plugin that allows you to create beautiful galleries of logos for your partners, clients, or sponsors.

**Features:**

* Easy to use admin interface
* Custom logo post type with fields for company name and website URL
* Category support for organizing logos
* Responsive grid layout that works on all devices
* Customizable columns and display options
* Simple shortcode to display galleries anywhere

### How to Use

1. Add your logos via the TAGG Logos section in the admin menu
2. Organize logos into categories (optional)
3. Place the shortcode `[tagg_gallery]` on any page or post
4. Customize using shortcode attributes

### Shortcode Options

The `[tagg_gallery]` shortcode accepts the following parameters:

* `category` - Show logos from a specific category (use category slug)
* `columns` - Number of columns to display (1-6)
* `limit` - Maximum number of logos to show
* `link` - Whether to link logos to their website URLs (yes/no)

**Example:**
`[tagg_gallery category="partners" columns="4" limit="8" link="yes"]`

== Installation ==

1. Upload the `tagg` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to TAGG Logos to start adding your logo items
4. Use the shortcode `[tagg_gallery]` to display your logo gallery

== Frequently Asked Questions ==

= How do I add a logo to the gallery? =

1. Go to TAGG Logos > Add New in your WordPress admin
2. Enter the company name as the title
3. Upload the logo image as the featured image
4. Enter the website URL in the Logo Details box
5. Optionally assign the logo to a category
6. Click Publish

= How do I display logos from a specific category? =

Use the category parameter in the shortcode:
`[tagg_gallery category="partners"]`

= Can I display logos in different sizes or layouts? =

You can adjust the number of columns using the columns parameter:
`[tagg_gallery columns="4"]`

= How do I prevent logos from linking to websites? =

Use the link parameter:
`[tagg_gallery link="no"]`

== Screenshots ==

1. Frontend logo gallery display
2. Admin logo management screen
3. Logo editing screen
4. Settings page

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release 