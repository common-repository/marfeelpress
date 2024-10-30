---
title: Ads-txt loader
tags: adstxt,ads
---
# Ads-Txt Loader

This file encapsulates different methods that consume Insight's AdsTxtInsightController.java to get
and perform actions on the Ads.txt.

## load_merged( $ads_txt )

If an Mrf_Ads_Txt object is passed, it will consume Insight's merger and return the object back with
a filled content_filled field.

Api url example `https://insight.marfeel.com/hub/insight/adstxt/random.com/?action=merge)`

Mrf_Ads_Txt Object input example:

```json
{
  "has_plugin": false,
  "mrf_lines": "google.com, pub-3733785150938669, RESELLER, f08c47fec0942fa0
google.com, pub-2117324400584663, RESELLER, f08c47fec0942fa0
aps.amazon.com, 713b5f85-602b-49d9-a3b8-33750c16a4fc, DIRECT
openx.com, 540191398, RESELLER, 6a698e2ec38604c6
pubmatic.com, 157150, RESELLER, 5d62403b186f2ace
districtm.io, 100962, RESELLER
appnexus.com, 1908, RESELLER, f5ab79cb980f11d1
rubiconproject.com, 18020, RESELLER, 0bfd66d529a55807
rhythmone.com, 1654642120, RESELLER, a670c89d4a324e47
adtech.com, 12068, RESELLER, e1a5b5b6e3255540
pubmatic.com, 157138, Reseller, 5d62403b186f2ace",
  "content": "random.com, pub-3733785150938669, RESELLER, 1231321323",
  "content_merged": "",
  "status": 2,
  "timestamp": 1562233984
}
```

## load_mrf_lines()

It will return the Marfeel lines for Ads.txt consuming Insight's AdsTxtInsightController.java.

Api url example:
`https://insight.marfeel.com/hub/insight/adstxt/random.com/mrflines`
