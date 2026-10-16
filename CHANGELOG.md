# Changelog

All notable changes to **this repository** are recorded here. Format based on
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

WP Private Content Plus is a plugin by Rakhitha Nimesh (wpexpertdeveloper.com),
maintained here as a copy. Version history before 3.6.2 — and the user-facing
release notes WordPress.org displays — live in [`readme.txt`](readme.txt).
This file covers the changes made in this repository, which begin at the 3.6.2
release.

## [3.6.3] - 2026-10-16

A security and PHP 8 compatibility review of 3.6.2 produced the thirty-two
changes below, one commit each on the `v3.6.3` branch. Short hashes are given so
the reasoning behind any one of them can be read in full with `git show`.

### Security

- **Password protection accepted a forged cookie.** The site-wide password gate
  compared a client cookie to the literal string `ACTIVE` and served the real
  template when it matched, so a single request header walked past it. The
  cookie is now a keyed hash of the configured password, compared in constant
  time, and set `HttpOnly` and `Secure`. The password comparison also moved from
  `==` to `hash_equals()`, closing the numeric-string case where a password such
  as `0e12345` is satisfied by `0e99999`. `5a609b1`
- **Password protection did not cover feeds or REST.** The module was registered
  only on `template_include`, which runs for neither, so a password-protected
  site published every post over RSS and `/wp-json/`. Feeds are now refused and
  REST requests answered with a 401. `bb60eb2`
- **The attachment download endpoint was authorised by its URL alone.** It
  consulted only the attachment's *download* permission (default `all`) and
  never its visibility, never anything about the post it was told to read, and
  never the site-wide gates that run later in the request. Any post whose meta
  happened to list the attachment id was a valid ticket for the file. `b6a4b05`
- **The download path was not confined to the uploads directory.**
  `wp_get_attachment_url()` pastes the raw `_wp_attached_file` meta onto the base
  URL, so a value containing `../` resolved outside uploads and was served. Both
  sides are now resolved with `realpath()` and the result must sit under the
  uploads directory. `94c69ab`
- **Attachment ids were not validated when saved.** Whatever id appeared in the
  POST array key was stored, so any id at all could be listed and then fetched
  through the endpoint above. `a4c5049`
- **Downloads ignored the author restriction.** The front end gates these posts
  through `validate_post_author_restrictions()`; the download path did not
  consult it. `0613bc7`
- **Gated downloads were sent `Cache-Control: public`**, letting a shared cache
  store and replay a members-only file to other visitors. `2258f3d`
- **REST skipped every visibility check for logged-in users.** All of them sat
  inside `if ( ! is_user_logged_in() )`, so any authenticated account — a
  subscriber — could read role- and user-restricted content over `/wp-json/`.
  The checks now run for everyone and are evaluated per mode. `c5e0cce`
- **REST did not consult the author restriction** either. `e7ad8cf`
- **Site lockdown exempted feeds.** A locked-down site kept publishing every
  post over RSS. Feeds are refused outright rather than redirected, and the
  check runs before the skip list. `443a9d5`
- **A query string defeated the site lockdown skip list.** The current URL was
  rebuilt without its query while the redirect target sat in the skip list, so
  `/?p=N` and `/?s=term` were skipped and a locked site was browsable. `f984871`
- **The IP allowlist trusted client-controlled headers.** `Client-IP` and
  `X-Forwarded-For` were preferred over `REMOTE_ADDR` and were the sole basis of
  the allowlist test. Headers are now believed only when the request arrived
  from a proxy the site names via the `wppcp_trusted_proxies` filter, and the
  chain is walked right to left. `2c9c3ec`
- **Any member could read any other member's private page.** The shortcode took
  `user_id` from its own attribute and checked only that the visitor was logged
  in. `91671ce`
- **Two shortcodes allowed unauthorised enumeration.** `[wppcp_user_restricted_posts]`
  and `[wppcp_user_role_restricted_posts]` ran a query from a caller-authored
  attribute with no authorisation, publishing titles and permalinks of
  restricted posts to any viewer. One also interpolated `user_id` into a REGEXP
  meta compare unescaped, so `.*` matched every post carrying the key. `01dd7f0`
- **The author restriction predicate denied the post's own author.**
  `$this->current_user` holds an int on one call path and a `WP_User` object on
  another; reading `->ID` off the int yielded null. `aa4d6af`
- **Restricted posts kept their title and excerpt in listings.** Archiving,
  search and feeds replaced `post_content` with the restriction message but left
  the real title, excerpt and permalink on display. `9ba7f08`
- **select2 dropdowns rendered unescaped data.** The `escapeMarkup` overrides
  return markup unchanged — which is what lets the formatters build their own
  rows — so escaping is the formatters' job, and they were not doing it. `6b3dbda`
- **Admin menu permissions never blocked direct URL access.**
  `basename()` of an `admin.php?page=…` request URI yields the whole
  `admin.php?page=slug`, never the slug, so the check never fired. `6935b8d`

### Fixed

- **Search overwrote `post__not_in` instead of merging it.** Both callbacks run
  on `pre_get_posts` at priority 10 and the search module registered second, so
  its `set()` discarded the restricted-post ids excluded moments earlier. `a443750`
- **The widget callback returned the raw form submission.** `widget_update_callback`
  receives the widget's own sanitised instance first and the raw data second;
  returning the raw array discarded the sanitisation every widget on the site
  had just performed. `6c4fd81`
- **A misspelled capability in three AJAX handlers.** `wpppcp_manage_options`
  never matched, so the handlers were reachable only by full administrators
  rather than by users granted `wppcp_manage_options`. `6b56e1e`
- **A quoted SQL placeholder.** `post_type='%s'` inside a double-quoted string
  left the placeholder quoted, so `prepare()` produced a literal and the query
  could not match. `3677cde`
- **The admin listed themselves in their own group user picker.**
  `$this->current_user` was neither declared nor assigned, so the comparison
  against null was always true. `08c989d`
- **`protection_status()` used `$this->current_user` as both an int and an
  object**, breaking its role branch for one caller and its users branch for the
  other. `4b0a9eb`
- **Search read the page option keys under a comment naming posts**, so enabling
  the global post restriction never affected search results while enabling the
  global page restriction emptied every one of them. The branch also had to be
  made to respect the visibility setting, since applying it regardless would
  have emptied search for the default "Everyone" setting. The global check is
  now keyed by post type in one place, so search and REST share it. `7da2e99`, `6611301`
- **The lockdown search branch disagreed with the module it mirrors.** It
  whitelisted only `lockdown_allowed_posts` while the module's skip list covers
  allowed pages as well, and it ignored the private-content module switch, so a
  stale `lockdown_status` kept search emptied with the module off. `7da2e99`

### PHP 8 compatibility

- **The attachment handler fataled.** A guard written as `!= ''` let the string
  `'0'` through; on PHP 8 the comparison semantics changed so the block ran with
  id 0 and `readfile( '' )` threw a `ValueError`. `f60e718`
- **Ten classes created properties on the fly.** Dynamic properties are
  deprecated in PHP 8.2 and become an error in PHP 9. `26f5d6d`
- **Deleted users caused warnings** in the private page lists — `get_user_by()`
  returns `false` and the display name was read straight off it. `7986f7a`
- **The WooCommerce shortcode fataled** on sites without WooCommerce, and read a
  property off `get_userdata( 0 )`, which is `false` for logged-out visitors. `66f93ac`

### Housekeeping

- **Twelve templates had no `ABSPATH` guard**, unlike the rest of the plugin. `86bc577`
- **An unescaped group title** was concatenated into an option element. `d65ba86`
