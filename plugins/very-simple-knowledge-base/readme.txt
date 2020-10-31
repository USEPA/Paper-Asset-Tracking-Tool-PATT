=== Very Simple Knowledge Base ===
Contributors: Guido07111975
Version: 5.3
License: GNU General Public License v3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Requires at least: 4.4
Tested up to: 5.3
Stable tag: trunk
Tags: simple, knowledge base, bulletin board, faq, wiki, link portal


This is a lightweight plugin to create a knowledge base. Add the shortcode on a page or use the widget to display your categories and posts.


== Description ==
= About =
This is a lightweight plugin to create a Knowledge Base, Bulletin Board, FAQ, Wiki or Link Portal.

Add the shortcode on a page or use the widget to display your categories and posts.

While adding the shortcode or the widget you can add several attributes to personalize your knowledge base.

You can also list categories and posts from a custom post type (such as "product" or "event").

= How to use = 
After installation create a page and add the shortcode to display your categories and posts:

* For 1 column: `[knowledgebase-one]`
* For 2 columns: `[knowledgebase-two]`
* For 3 columns: `[knowledgebase-three]`
* For 4 columns: `[knowledgebase]`

In mobile screens 2 columns (except the 1 column knowledge base).

Or go to Appearance > Widgets and use the widget to display your categories and posts.

Default settings categories:

* Ascending order (A-Z)
* Empty categories are hidden
* Parent and subcategories are listed separately

Default settings posts:

* Descending order (by date)
* All posts are displayed

= Shortcode attributes =
You can add attributes to the 4 shortcodes mentioned above.

* Include certain categories: `include="1,3,5"`
* Exclude certain categories: `exclude="8,10,12"`
* Display empty categories too: `hide_empty="0"`
* Display category description: `description="true"`
* Change number of posts per category: `posts_per_page="5"`
* Display posts in ascending order: `order="asc"`
* Display posts by title: `orderby="title"`
* Display posts in random order: `orderby="rand"`
* Display number of posts (post count) `count="true"`
* Display post meta (date and author): `meta="true"`
* Display View All link: `all_link="true"`
* Change label of View All link: `all_link_label="your label here"`
* Change label of post without title: `no_title_label="your label here"`
* Change CSS class of knowledge base: `class="your-class-here"`

Examples:

* One attribute: `[knowledgebase posts_per_page="5"]`
* One attribute: `[knowledgebase-two include="1,3,5"]`
* Multiple attributes: `[knowledgebase include="1,3,5" hide_empty="0" meta="true"]`

= Widget attributes =
The widget supports the same attributes. You don't have to add the main shortcode tag or the brackets.

Examples:

* One attribute: `posts_per_page="5"`
* Multiple attributes: `include="1,3,5" hide_empty="0" meta="true"`

= Post tags =
Besides listing posts by category you can also list posts by tag: `taxonomy="post_tag"`

= Custom post types =
You can also list categories and posts from a custom post type (such as "product" or "event").

To list these categories and posts you should add 2 shortcode attributes: "taxonomy" and "post_type"

You can for example list WooCommerce products:

* List products: `taxonomy="product_cat" post_type="product"`
* List products with product category image: `taxonomy="product_cat" post_type="product" woo_image="true"`
* List products by tag instead of category: `taxonomy="product_tag" post_type="product"`

= Link Portal =
To display a list of website links you can install the [Page Links To](https://wordpress.org/plugins/page-links-to) plugin.

While creating a post you can set a redirect to an URL (website) of your choice.

When you click the post link in the frontend of your website it will redirect you to this URL (so the post will not open).

= Browser support =
The knowledge base might not display properly in IE8 and older because I have used CSS selector "nth-of-type".

= Question? =
Please take a look at the FAQ section.

= Translation =
Not included but plugin supports WordPress language packs.

More [translations](https://translate.wordpress.org/projects/wp-plugins/very-simple-knowledge-base) are very welcome!

= Credits =
Without the WordPress codex and help from the WordPress community I was not able to develop this plugin, so: thank you!

Enjoy!


== Installation ==
Please check Description section for installation info.


== Frequently Asked Questions ==
= Where is the settingspage? =
Plugin has no settingspage, use the shortcode with attributes or the widget with attributes to make it work.

= Does this plugin have its own knowledge base post type? =
No, it's build to create a knowledge base by using the native categories and posts.

You can also list categories and posts from a custom post type (such as "product" or "event").

= How can I change the layout or colors? =
Besides the number of columns, the layout or colors of the knowledge base can only be changed by using custom CSS.

= Where to find the category ID? =
Each category URL contains an unique ID. You will find this ID when hovering the category title in your dashboard or when editing the category.

It's the number that comes after: `tag_ID=`

= Where to find the tag ID? =
Each tag URL contains an unique ID. You will find this ID when hovering the tag title in your dashboard or when editing the tag.

It's the number that comes after: `tag_ID=`

= Is it possible to list a subcategory underneath its parent? =
No, this is not possible. Parent and subcategories are listed separately.

= Is a post without a title also displayed? =
Yes, if a post has no title it will be displayed in the frontend of your website with a default label.

You can change this label by using an attribute.

= Does this plugin have its own knowledge base block? =
No, it does not have its own knowledge base block and I'm not planning to add this feature.

= Why no Semantic versioning? =
At time of initial plugin release I wasn't aware of the Semantic versioning (sequence of three digits).

= How can I make a donation? =
You like my plugin and you're willing to make a donation? Nice! There's a PayPal donate link at my website.

= Other question or comment? =
Please open a topic in plugin forum.


== Changelog ==
= Version 5.3 =
* Removed file vskb-list
* Added file vskb-template instead
* Minor changes in code

= Version 5.2 =
* After some discussion I have decided to remove the max character length of widget inputs again

= Version 5.1 =
* Fix: version 5.0 breaks the knowledge base, please update to version 5.1 (thanks Brian)
* Minor changes in code

= Version 5.0 =
* Fix: view all link
* Fix: replaced deprecated get_woocommerce_term_meta() with get_term_meta()
* Because of fix above plugin now requires at least WP 4.4
* Minor changes in code
* Added, removed and changed escaping and sanitizing of many items

= Version 4.9 =
* Added CSS class to each knowledge base: vskb-one-container, vskb-two-container, vskb-three-container, vskb-four-container
* Added attribute to change this CSS class per knowledge base
* This can be useful if you want to apply different styling when having multiple knowledge bases
* Increased max character length of widget inputs

For all versions please check file changelog.


== Screenshots ==
1. Very Simple Knowledge Base page (Twenty Nineteen theme).
2. Very Simple Knowledge Base page (Dashboard).
3. Very Simple Knowledge Base WooCommerce products (Twenty Nineteen theme).
4. Very Simple Knowledge Base widget (Dashboard).