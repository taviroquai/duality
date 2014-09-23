<?php

namespace Duality\System\Core;

/**
 * Commander interface
 */
interface InterfaceCommander
{
	public function listen();

	public static function parseFromGlobals();
}