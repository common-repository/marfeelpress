---
title: MRF Router
tags: router,amp,mrf,sw
---

# MRF Router

This class is used by the *warda* functionality, proxying requests.

Tenants using this router don't use the MarfeelGarda.

It extends the base router (`class-marfeel-press-base-router`), implementing the `valid_route`
and `route` methods.

## Public methods

### valid_route ( $post )

Returns true if the requested route should be marfeelized.
A page should be marfeelized if:

* Page is requested by a smartphone, true if:
  * Page is NOT requested by the MarfeelMan user-agent
  * Page is NOT requested while we have the `fromt` cookie or query parameter
  * is mobile according to varnish header, device header _or_ user-agent.
* Marfeel is active
* Page is requested by the native app
* Page is NOT an AMP page (has its own router)

### route ()

Executes the marfeel proxy (from `class-marfeel-press-proxy.php`)
