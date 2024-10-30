---
title: Router
tags: router,amp,mrf,sw
---
# Router

This class instantiates all the MarfeelPress routers existing in the app.

## List of routers

### MRF Router

Gets instantiated if warda is activated from the settings page of the plugin. Serves marfeelized pages.
If it is not active, marfeel is served via garda, and this router is never instantiated.

### SW Router

Always gets instantiated.

### Ads.txt Router

Always gets instantiated.

### AMP Router

Gets instantiated only in AMP is activated via the settings page of the plugin.
