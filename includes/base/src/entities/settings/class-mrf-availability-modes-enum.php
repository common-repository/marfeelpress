<?php

namespace Base\Entities\Settings;

abstract class Mrf_Availability_Modes_Enum {
	const OFF = 'OFF';
	const LOGGED = 'LOGGED';
	const ALL = 'ALL';
	const DEFAULT_MODE = self::OFF;
}
