---
title: Adstxt api
tags: adstxt,api,rest,ads
---

# Ads-Txt Api

This class is an API that serves the local ads.txt object to whoever consumes it outside this project.
In particular, we are using it to pass the ads.txt to Leroy's Ads.txt editor.

## URI used

`https://PUBLISHER_ID/wp-json/marfeelpress/v1/definitions/index/ads/ads_txt`

## Response example

```json
    {
        "has_plugin": false,
        "mrf_lines": "google.com, pub-3733785150938669, RESELLER, f08c47fec0942fa0 google.com,
        pub-2117324400584663, RESELLER, f08c47fec0942fa0 aps.amazon.com,
        713b5f85-602b-49d9-a3b8-33750c16a4fc, DIRECT openx.com, 540191398, RESELLER,
        6a698e2ec38604c6 pubmatic.com, 157150, RESELLER, 5d62403b186f2ace districtm.io, 100962,
        RESELLER appnexus.com, 1908, RESELLER, f5ab79cb980f11d1 rubiconproject.com, 18020, RESELLER,
        0bfd66d529a55807 rhythmone.com, 1654642120, RESELLER, a670c89d4a324e47 adtech.com, 12068,
        RESELLER, e1a5b5b6e3255540 pubmatic.com, 157138, Reseller, 5d62403b186f2ace ",
        "content": "google.com, pub-3733785150938669, RESELLER, f08c47fec0942fa0 google.com",
        "content_merged": "google.com, pub-3733785150938669, RESELLER, f08c47fec0942fa0 google.com,
        pub-3733785150938669, RESELLER, f08c47fec0942fa0 google.com, pub-2117324400584663, RESELLER,
        f08c47fec0942fa0 aps.amazon.com, 713b5f85-602b-49d9-a3b8-33750c16a4fc, DIRECT openx.com,
        540191398, RESELLER, 6a698e2ec38604c6 pubmatic.com, 157150, RESELLER, 5d62403b186f2ace
        districtm.io, 100962, RESELLER appnexus.com, 1908, RESELLER, f5ab79cb980f11d1
        rubiconproject.com, 18020, RESELLER, 0bfd66d529a55807 rhythmone.com, 1654642120,
        RESELLER, a670c89d4a324e47 adtech.com, 12068, RESELLER, e1a5b5b6e3255540 pubmatic.com,
        157138, Reseller, 5d62403b186f2ace ",
        "status": 0,
        "timestamp": 1562278202
    }
```

## get()

Basic get that retrieves the ads.txt after updating it's value.

## update_status()

Does retrieve the current ads.txt stored in DB as an Mrf_Ads_Txt.
Does get the status of the ads.txt in Insight.

If response is OK, the **status** and **mrf_lines** are updated.
If response is OK and ads.txt status retrieved is KO (1), the content is overriden. We want to show
this other content that a file or another plugin is serving as ads.txt.

## get_ads_txt_status_from_insight

Retrieves the ads.txt status that the tenant has in store in Insight. Being:

0 = Tenant has no ads.txt
1 = KO: ads.txt has NOT all MArfeel lines
2 = OK: ads.txt has all Marfeel lines
