---
title: Ads-txt controller
tags: adstxt,ads
---

# Ads-Txt Controller

This class contains the method to render the ads.txt and to get the ads.txt.

## render_ads_txt()

This method does return the ads.txt merged content.
First it retrieves the ads.txt and then depending on  whether it has content_merged, is LONGTAIL
or else, it returns the ads.txt, the Marfeel lines or a 404;

## get_ads_txt()

This method does check if the content is older than 1h (3600s), if so, checks that the status is OK
and if so, refreshes the ads.txt content and returns it.
If content is not old, ir returns the one already set.
