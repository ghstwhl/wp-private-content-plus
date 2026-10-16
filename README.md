# WP Private Content Plus

Advanced content restrictions for WordPress — protect posts, pages, custom post
types, menus, widgets, search results and file attachments by role, capability,
specific users, or groups of users.

A copy of the plugin by **Rakhitha Nimesh** ([wpexpertdeveloper.com](https://www.wpexpertdeveloper.com/wp-private-content-plus/)),
maintained in this repository. See [License](#license) for attribution.

> **Version note.** `main` holds 3.6.2 as released. The `v3.6.3` branch carries a
> security and PHP 8 review of that release — thirty-two changes, one commit each —
> recorded in [`CHANGELOG.md`](CHANGELOG.md), with the plugin version bumped to
> 3.6.3 to match.

## What it does

Restrictions are configured per item and can be applied site-wide:

- **Posts, pages and custom post types** — visible to everyone, guests, members,
  selected roles, or named users, with an optional per-post redirect.
- **Author restriction** — a post visible only to its own author, hidden
  everywhere else including listings and feeds.
- **Global rules** — apply a visibility setting to all posts, or all pages, at
  once.
- **Site lockdown** — the whole site behind a redirect for visitors who are not
  logged in, with a skip list of allowed pages, posts and URLs.
- **IP restrictions** — allow visitors only from a list of addresses.
- **Password protection** — guard a single block with a shortcode, or put a
  password on the entire site.
- **Navigation menus and admin screens** — restrict menu items and dashboard
  screens per role.
- **Widgets** — show or hide each widget by role.
- **Search** — block specific posts and pages from appearing in results, and
  restrict which post types each role can search.
- **Attachments** — attach files to a post with per-file visibility and a
  separate download permission, served through the plugin rather than linked
  directly.
- **Private pages and user groups** — a per-user page reachable by shortcode,
  and groups of users for shared restrictions.
- **Scheduled content** — show a block between two dates.

### Shortcodes

| Shortcode | Purpose |
|---|---|
| `[wppcp_private_content]` | Restrict a block by role, capability or user |
| `[wppcp_private_page]` | The current user's private page |
| `[wppcp_guest_content]` | Shown to logged-out visitors |
| `[wppcp_member_content]` | Shown to logged-in users |
| `[wppcp_scheduled_content]` | Shown between two dates |
| `[wppcp_private_content_by_registration]` | Shown to users registered before or after a date |
| `[wppcp_password_protected_content]` | Guarded by a password |
| `[wppcp_woocommerce_product_content]` | Shown to buyers of a product |
| `[wppcp_user_restricted_posts]` | Posts restricted to a user |
| `[wppcp_user_role_restricted_posts]` | Posts restricted to a role |

### Integrations

WooCommerce (product-purchase restrictions and tab visibility) and User Profiles
Made Easy (a private page tab on user profiles) are supported when active.
bbPress forums and topics are covered by the global restriction logic.

## Requirements

- WordPress 4.3.1 or later (tested to 6.7.1)
- No PHP version is declared in the plugin header. As of `v3.6.3` the code is
  PHP 8.x compatible, which earlier releases were not.

## Installation

Copy this directory into `wp-content/plugins/` and activate **WP Private Content
Plus**. Settings are under **Private Content Settings** in the admin menu (slug
`wppcp-settings`).

Activation creates two tables: `{prefix}wppcp_private_page` for private page
content and `{prefix}wppcp_group_users` for user group membership.

## Repository layout

```
wp-private-content-plus.php   Plugin bootstrap: constants, activation, class loading
functions.php                 Shared helpers
classes/                      One class per concern; all loaded by the bootstrap
admin/templates/              Settings screens
templates/                    Front-end and meta-box output, via WPPCP_Template_Loader
js/  css/  images/  lang/     Assets and translations
readme.txt                    WordPress.org readme — the full release history lives here
```

### Where enforcement happens

Worth knowing before changing access control, since the points are not
centralised:

- Most per-post, global, lockdown and IP checks run on `template_redirect`.
- The password module runs on `template_include` **and** `template_redirect`
  **and** `rest_authentication_errors` — feeds and REST are served before
  `template_include`, so one hook is not enough.
- The attachment download endpoint runs on `init`, which is earlier than
  `template_redirect`; it therefore carries its own copies of the site-wide
  checks rather than inheriting them.
- REST restrictions hook `rest_prepare_post` and `rest_prepare_page`.

## Development

No build step, no dependency manager, no test suite — plain PHP, jQuery-based
JavaScript and hand-written CSS. Edit the files and reload.

There is no automated verification in this repository. Changes to the access
control paths in particular are worth testing by hand against a running install,
in both a logged-out and a logged-in session.

## Known gaps

- `WPPCP_Social_Locker` is never instantiated and is absent from the bootstrap's
  include list, so `[wppcp_social_locker]` renders as literal text. The vendored
  library it would load, `js/shareIt-js/`, is dead weight and contains a Google
  API key belonging to that library's original author.
- REST restrictions cover posts and pages only; custom post types are not hooked.
- What WordPress does with a `WP_Error` returned from `rest_prepare_post` inside
  a *collection* response has not been verified against core, so the REST
  denials are confirmed for single items only.
- The templates directory has no automated check that every file is loaded
  through WordPress rather than requested directly.

## License

GPLv2 or later — see [`LICENSE`](LICENSE). Original plugin copyright Rakhitha
Nimesh; see [`readme.txt`](readme.txt) for the upstream project, contributors and
the full release history.
