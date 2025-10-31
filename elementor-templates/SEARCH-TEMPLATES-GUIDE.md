# Elementor Search Templates Guide

**Version**: 2.8.0  
**Last Updated**: October 27, 2025

This guide explains how to create custom search/archive result templates using Elementor Pro's Theme Builder with our custom filter widgets.

---

## 📋 Available Filter Widgets

The theme provides **3 custom Elementor filter widgets** for search/archive pages:

1. **Price Range Filter** - Dual slider for filtering by price (`nd_travel_meta_box_price`)
2. **Date Range Filter** - From/To date pickers for travel dates (`nd_travel_meta_box_availability_from/to`)
3. **Taxonomy Filter** - Configurable checkbox/radio filter for:
   - Destinations (`nd_travel_cpt_1_tax_0`)
   - Durations (`nd_travel_cpt_1_tax_1`)
   - Difficulties (`nd_travel_cpt_1_tax_2`)
   - Min Ages (`nd_travel_cpt_1_tax_3`)
   - Typologies (`nd_travel_cpt_2`)

All widgets automatically filter results via URL parameters and integrate with Elementor's **Archive Posts** widget.

---

## 🎨 Creating Search Templates

### Step 1: Access Theme Builder

1. Go to **WordPress Admin → Templates → Theme Builder**
2. Find **Search Results** section
3. Click **Add New** to create a new template

### Step 2: Build Template Structure

**Recommended Layout**:

```
┌─────────────────────────────────────────────┐
│ Section: Header                            │
│ - Archive Title widget                     │
│ - Text: "Found X results"                  │
└─────────────────────────────────────────────┘
┌──────────────┬──────────────────────────────┐
│ Sidebar (30%)│ Content (70%)                │
│              │                              │
│ Price Range  │ Archive Posts widget         │
│ Filter       │ (Loop Grid/Masonry)          │
│              │                              │
│ Date Range   │ Shows: Posts, Adventures,    │
│ Filter       │ Typologies based on search   │
│              │                              │
│ Taxonomy     │ Pagination at bottom         │
│ Filters      │                              │
│ (multiple)   │                              │
└──────────────┴──────────────────────────────┘
```

### Step 3: Add Filter Widgets

**In the Sidebar Section**:

1. Add **Price Range Filter** widget:
   - Title: "Price Range"
   - Currency: €
   - Min/Max auto-detected from database
   - Step: 10

2. Add **Date Range Filter** widget:
   - Title: "Travel Dates"
   - From Label: "From"
   - To Label: "To"

3. Add **Taxonomy Filter** widgets (one for each taxonomy):
   - **Destinations**: Set taxonomy to "Destinations", enable search box
   - **Durations**: Set taxonomy to "Durations"
   - **Difficulties**: Set taxonomy to "Difficulties"
   - **Min Ages**: Set taxonomy to "Min Ages"

### Step 4: Configure Archive Posts Widget

**In the Content Section**:

1. Add **Archive Posts** widget (Elementor Pro)
2. Configure settings:
   - **Layout**: Choose Grid or Masonry
   - **Columns**: 3 (desktop), 2 (tablet), 1 (mobile)
   - **Posts Per Page**: 12
   - **Pagination**: Show pagination
   - **Query**:
     - Include: Current Query (this is critical!)
     - Post Type: Inherit from current query

3. Design each post card:
   - Featured Image
   - Post Title
   - Excerpt
   - Custom fields (price, duration, etc. via Dynamic Tags)
   - Read More button

---

## 🎯 Display Conditions (Per Post Type)

To show **different layouts for different post types**, create **3 separate templates**:

### Template 1: Search Results - Blog Posts
- **Display Condition**: `Search Results` + `Post Type: Post`
- **Sidebar**: Show all filters
- **Archive Posts**: Default blog post layout

### Template 2: Search Results - Adventures/Packages
- **Display Condition**: `Search Results` + `Post Type: nd_travel_cpt_1`
- **Sidebar**: Show Price, Date, Destinations, Durations, Difficulties filters
- **Archive Posts**: Adventure card layout (image, price, duration, difficulty)

### Template 3: Search Results - Typologies
- **Display Condition**: `Search Results` + `Post Type: nd_travel_cpt_2`
- **Sidebar**: Hide (set to full-width layout)
- **Archive Posts**: Typology card layout (icon, title, subtitle)

---

## 💾 Exporting Templates for Auto-Import

After building templates in Elementor:

1. Go to **Elementor → Tools → Export Template**
2. Select your search template
3. Click **Export** to download JSON file
4. Rename following convention:
   - `01-search-results-posts.json`
   - `02-search-results-packages.json`
   - `03-search-results-typologies.json`
5. Place in: `/elementor-templates/search/`
6. Templates auto-import on theme activation

---

## 🔧 How Filtering Works

### URL Parameter System

Filters modify URL parameters and reload the page:

```
/?s=adventure&price_min=100&price_max=500&date_from=2025-11-01&date_to=2025-12-31&cpt_1_tax_0[]=4829&cpt_1_tax_1[]=17
```

**Parameters**:
- `s` - Search query
- `price_min` / `price_max` - Price range (from Price Range Filter)
- `date_from` / `date_to` - Date range (from Date Range Filter)
- `cpt_1_tax_0[]` - Destination IDs (from Taxonomy Filter)
- `cpt_1_tax_1[]` - Duration IDs
- `cpt_1_tax_2[]` - Difficulty IDs
- `cpt_1_tax_3[]` - Min Age IDs

### WordPress Integration

Filters are applied via `pre_get_posts` hook (handled by nd-travel plugin or child theme).

To add custom filtering logic:

```php
add_action( 'pre_get_posts', function( $query ) {
	if ( ! $query->is_search() || ! $query->is_main_query() ) {
		return;
	}

	// Price filter
	if ( isset( $_GET['price_min'] ) || isset( $_GET['price_max'] ) ) {
		$meta_query = $query->get( 'meta_query' ) ?: array();
		$meta_query[] = array(
			'key'     => 'nd_travel_meta_box_price',
			'value'   => array( $_GET['price_min'], $_GET['price_max'] ),
			'type'    => 'NUMERIC',
			'compare' => 'BETWEEN',
		);
		$query->set( 'meta_query', $meta_query );
	}

	// Date filter (similar logic for availability dates)
	// Taxonomy filter (use tax_query)
});
```

---

## 🎨 Styling Tips

### Match Parent Theme Design

- Use parent theme color variables: `#EA5B10` (accent), `#000` (text), `#fff` (background)
- Font: Parent theme default (usually Roboto or similar)
- Card shadows: `box-shadow: 0 2px 8px rgba(0,0,0,0.1)`
- Border radius: `4px`
- Transitions: `0.3s ease`

### Responsive Design

- **Desktop (>1024px)**: Sidebar + content (30% / 70%)
- **Tablet (768-1024px)**: Sidebar + content (35% / 65%)
- **Mobile (<768px)**: Sidebar below content (full-width stack)

Add mobile filter toggle button:
```html
<button class="mobile-filter-toggle">Show Filters</button>
```

---

## 🧪 Testing Checklist

Before exporting templates:

- [ ] Test search with query: `/?s=test`
- [ ] Verify filters update URL parameters
- [ ] Check pagination works
- [ ] Test different post types show correct template
- [ ] Mobile responsive (sidebar collapses)
- [ ] No console errors
- [ ] Matches parent theme styling
- [ ] Archive Posts widget shows correct query results

---

## 📚 Additional Resources

- **Elementor Pro Docs**: [Theme Builder - Search Results](https://elementor.com/help/customize-the-search-results-archive/)
- **Display Conditions**: [How to Set Display Conditions](https://elementor.com/help/conditions/)
- **Archive Posts Widget**: [Archive Posts Documentation](https://elementor.com/help/archive-posts-widget/)

---

**Need Help?** Check existing templates in `/elementor-templates/` for examples of section structures and widget configurations.
