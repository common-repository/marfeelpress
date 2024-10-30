---
title: Base router
tags: base router,router,mrf,amp,sw,warda
---
# Base Router

This class is acting like an abstract for routing, and has to be extended to be used.
It allows to proxy pages depending on the criteria set by the child class.

## Public methods

### valid_route ( $post )

Must be implemented by child class

### route ()

Must be implemeneted by child class

### route_if_necessary()

Depending on `valid_route` result, calls the `route` method, or does nothing.

### detect_device ()

Sets the device type ('s' or '').
See device type details in `class-marfeel-press-device-detection.php`
