# Guesty Calendar Widgets by Buildup Bookings

A lightweight WordPress plugin for Guesty API integration with calendar widgets and property listings.

## Installation

1. Upload the `guesty-widgets-bub` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to Guesty Widgets in the WordPress admin menu
4. Configure your Guesty API credentials (Client ID & Client Secret)
5. Generate your access token to sync properties from Guesty

## Features

- **Guesty API Integration** - Seamlessly connect your WordPress site with Guesty's property management system via OAuth2
- **Calendar Widget** - Interactive booking calendar with availability checking
- **Custom Post Type** - Dedicated "Guesty Listings" post type for managing properties
- **Property Filtering** - FacetWP integration for advanced property search and filtering
- **Amenities Taxonomy** - Organize properties by amenities with custom taxonomy
- **ACF Integration** - Custom field support with photo gallery functionality for property data
- **Multiple Display Options** - Show properties in slider or grid layouts
- **Custom Templates** - Single property pages and archive views with custom designs
- **Token Management** - Automatic token renewal with scheduled CRON jobs
- **Responsive Design** - Mobile-friendly displays with Slick Carousel and jQuery UI

## Usage

### Settings Configuration
1. Go to Guesty Widgets in the admin menu
2. Enter your Guesty Client ID and Client Secret
3. Generate your bearer token
4. Configure your booking URL
5. Sync your properties from Guesty

### Calendar Widget Shortcode

Display an interactive booking calendar for a specific property:

```
[display_calendar listingid="PROPERTY_ID" buttontext="Book Now" buttoncolor="#E19159" textcolor="white"]
```

**Shortcode Attributes:**
- `listingid` - The Guesty listing ID (required)
- `buttontext` - Custom button text (default: "Book Now")
- `buttoncolor` - Hex color code for button background (default: "#E19159")
- `textcolor` - Hex color code for button text (default: "white")

**Example:**
```
[display_calendar listingid="123456" buttontext="Reserve Now" buttoncolor="#2B5B84" textcolor="#ffffff"]
```

### Property Management
- Properties are automatically synced from Guesty API
- Each property includes: name, address, amenities, photos, and availability
- Custom single property templates for enhanced display
- Archive page with filtering capabilities via FacetWP
- Amenities can be managed as custom taxonomy terms

### Token Management
- Bearer tokens are automatically generated and stored
- Scheduled CRON jobs renew tokens daily to maintain API connection
- Manual token refresh available in settings page

---

**Version:** 2.1.2  
**Author:** Buildup Bookings  
**Website:** [buildupbookings.com](https://www.buildupbookings.com/)

