# LSCache WHMCS Addon

A production-ready WHMCS addon module that safely enables LiteSpeed FULL-PAGE caching for anonymous, read-only pages while ensuring sensitive areas remain uncached.

## Features

- **Safe Caching**: Only caches public pages for anonymous users
- **Automatic Detection**: Prevents caching of logged-in users, POST requests, and sensitive endpoints
- **Flexible Configuration**: Configurable TTL, ESI support, cacheable routes, and cookie variations
- **Admin Interface**: Easy cache management with purge controls
- **LiteSpeed Integration**: Uses proper LSCache headers for optimal performance

## Installation

1. Upload the `modules/addons/lscache/` directory to your WHMCS installation
2. Go to **Setup > Addon Modules** in WHMCS admin
3. Find "LSCache" and click **Activate**
4. Configure the settings as needed
5. The module will automatically start caching eligible pages

## Configuration

### Basic Settings

- **Enable LSCache**: Turn the module on/off
- **Public TTL**: Cache lifetime in seconds (default: 300)
- **Enable ESI**: Enable ESI for private content blocks (requires LSWS Enterprise)
- **Cacheable Routes**: Comma-separated list of page slugs to cache
- **Vary Cookies**: Cookies that create cache variations
- **Send Stale on Purge**: Serve stale content during purge operations

### Default Cacheable Pages

- Home page (`/`)
- Announcements (`/announcements`)
- Knowledgebase (`/knowledgebase`)
- Downloads (`/downloads`)
- Server Status (`/serverstatus`)

### Excluded Pages (Never Cached)

- Admin area (`/admin/`)
- Client area (`/clientarea.php`)
- Shopping cart (`/cart.php`)
- Invoice viewing (`/viewinvoice.php`)
- Credit card management (`/creditcard.php`)
- File downloads (`/dl.php`)
- Login/logout pages (`/login.php`, `/logout.php`)

## .htaccess Configuration

Add the following to your `.htaccess` file to ensure sensitive endpoints are never cached:

```apache
<IfModule LiteSpeed>
  RewriteEngine On
  # Hard-exclude sensitive endpoints
  RewriteRule ^(admin/|clientarea\.php|cart\.php|viewinvoice\.php|creditcard\.php|dl\.php|login\.php|logout\.php) - [E=cache-control:no-cache]
  # (Optional) Global enable at root – the addon still controls per-page via headers
  CacheEnable public /
</IfModule>
```

## Testing

### Cache Hit Verification

1. **Logged-out GET request** to homepage:
   - First request: `X-LiteSpeed-Cache-Control: public,max-age=300`
   - Second request: `x-litespeed-cache: hit`

2. **Access sensitive page** (e.g., `/cart.php`):
   - Response: `X-LiteSpeed-Cache-Control: no-cache`

3. **Purge test**:
   - Click "Purge Announcements" in admin
   - Response includes: `X-LiteSpeed-Purge: tag=whmcs:public,tag=announcements`
   - Next request to announcements: MISS then HIT

4. **Vary cookies test**:
   - Set `currency=USD` and `currency=EUR` cookies
   - Two separate cache variants are created

5. **ESI test** (if enabled):
   - Headers include `esi=on`

## Troubleshooting

### Common Issues

- **Pages not caching**: Check if user is logged in or page is in excluded list
- **Admin not working**: Ensure CSRF tokens are enabled
- **ESI not working**: Requires LiteSpeed Web Server Enterprise
- **Vary not working**: Check cookie names are correct

### Debug Headers

Monitor these response headers to verify caching:

- `X-LiteSpeed-Cache-Control`: Cache control directive
- `X-LiteSpeed-Tag`: Cache tags for purging
- `X-LiteSpeed-Vary`: Cookie variations
- `x-litespeed-cache`: HIT/MISS status

### Performance Tips

- Set appropriate TTL based on content update frequency
- Use ESI for dynamic content blocks within cached pages
- Configure vary cookies for multi-language or currency sites
- Monitor cache hit ratios in LiteSpeed logs

## Requirements

- WHMCS v8.x+
- LiteSpeed Web Server with LSCache enabled
- PHP 7.4+
- MySQL/MariaDB

## License

Apache-2.0 license - see LICENSE file for details.

## Support

For issues and questions:

1. Check LiteSpeed documentation: https://docs.litespeedtech.com/lscache/
2. Review WHMCS hooks documentation: https://developers.whmcs.com/hooks/
3. Submit issues to the GitHub repository

## Changelog

### v1.0.0
- Initial release
- Basic caching functionality
- Admin purge interface
- ESI support
- Cookie variations
