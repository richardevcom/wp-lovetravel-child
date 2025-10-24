## WordPress Reinstall Checklist

- ✅ **DONE**: Backupot svarīgās konfigurācijas - theme, elementor, valodas, utt.
- ✅ **DONE**: Resetot tīru wordpress.
- ✅ **DONE**: Backupot child theme un resetot pilnībā tīru child-theme struktūru.
- ✅ **DONE**: Uzstādīt motīvu + importēt demo saturu.
- ❌ **CANCELED**: ~~Importēt konfigurācijas (theme, elementor, valodas, utt.)~~;
- Gala tests, vai viss ok. Satīrīt, optimizēt;

---

### nd-travel Plugin Errors
- Child theme has no errors in debug.log ✅
- `nd-travel` plugin shows warnings/errors (non-blocking)
- **TODO**: Investigate and resolve nd-travel plugin compatibility issues
- See `/wp-content/debug.log` for details
```
[06-Oct-2025 03:00:22 UTC] PHP Warning:  Undefined variable $nd_travel_setting_show in /var/www/html/wp-content/plugins/nd-travel/addons/elementor/search/layout/layout-1.php on line 205
[06-Oct-2025 03:00:22 UTC] PHP Warning:  Undefined variable $nd_travel_setting_label in /var/www/html/wp-content/plugins/nd-travel/addons/elementor/search/layout/layout-1.php on line 205
[06-Oct-2025 03:00:22 UTC] PHP Warning:  Undefined variable $nd_travel_setting_icon in /var/www/html/wp-content/plugins/nd-travel/addons/elementor/search/layout/layout-1.php on line 205
[06-Oct-2025 03:00:22 UTC] PHP Warning:  Undefined variable $nd_travel_setting_show in /var/www/html/wp-content/plugins/nd-travel/addons/elementor/search/layout/layout-1.php on line 205
[06-Oct-2025 03:00:22 UTC] PHP Warning:  Undefined variable $nd_travel_setting_label in /var/www/html/wp-content/plugins/nd-travel/addons/elementor/search/layout/layout-1.php on line 205
[06-Oct-2025 03:00:22 UTC] PHP Warning:  Undefined variable $nd_travel_setting_icon in /var/www/html/wp-content/plugins/nd-travel/addons/elementor/search/layout/layout-1.php on line 205
[06-Oct-2025 03:00:22 UTC] PHP Warning:  Trying to access array offset on false in /var/www/html/wp-content/plugins/nd-travel/addons/elementor/packages/layout/layout-1.php on line 7
```
---
13.10.2025 todo:
- ✅ **DONE**: Add proper favicon with comprehensive browser support
- ✅ **DONE**: Duplicate `Min Age` custom taxonomy and create `Month` taxonomy (created Taxonomy Manager class + registered Month taxonomy with all 12 months)
- Change, fix and finalize colors/styles;
- Header top-navbar - removed
- Header navigation - logo | nav links | search (input/button)
- Leaflet map integration
- Sticky navigation on scroll down - shrink and change logo to favicon
- Under new adventures place subscription section and then hotest adventures section, then leafmap
---
# 20.10.2025
- Update order in which Elementor Search element/component displays `Months` taxonomy (it should be with custom order and I should be able to order them myself from admin panel);
- Update/edit `http://localhost:8080/search-page/?nd_travel_archive_form_destinations=0&nd_travel_cpt_1_tax_1=&nd_travel_cpt_1_tax_2=&nd_travel_cpt_1_tax_4=55` search pages sidebar widget to either show months range for user to pick and filter OR.. date range (with calendar popup);
- For `sales` badge add z-index, so it doesnt hide behind image on package (single) hover on home page packages loop;