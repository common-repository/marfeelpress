<?php
/**
 * Little piece of PHP to make Marfeel_Marfeel_Mobile_Detect auto-loadable in PSR-0 compatible PHP autoloaders like
 * the Symfony Universal ClassLoader by Fabien Potencier. Since PSR-0 handles an underscore in
 * classnames (on the filesystem) as a slash, "Marfeel_Marfeel_Mobile_Detect.php" autoloaders will try to convert
 * the classname and path to "Mobile\Detect.php". This script will ensure autoloading with:
 *  - Namespace:       Detection
 *  - Classname:       MobileDetect
 *  - Namespased:      \Marfeel\Detection\MobileDetect
 *  - Autoload path:   ./namespaced
 *  - Converted path:  ./namespaced/Detection/MobileDetect.php
 *
 * Don't forget to use MobileDetect (instead of Marfeel_Marfeel_Mobile_Detect) as class in code when autoloading.
 *
 * Thanks to @WietseWind.
 * For details please check: https://github.com/serbanghita/Mobile-Detect/pull/120
 */

namespace Marfeel\Detection;


class MobileDetect extends \Marfeel_Marfeel_Mobile_Detect {}
