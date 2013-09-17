<?php

include '../../../../src/bbit/tree/util/PreorderLabeling8.php';

class PreorderLabeling8Test {

	public static function printbin($a, $x = true) {
		self::sendHeader();
		for($i = 0, $n = strlen($a); $i < $n; $i++) {
			echo str_pad(ord($a[$i]), 5, ' ', STR_PAD_LEFT);
			echo ' ';
			echo str_pad(base_convert(ord($a[$i]), 10, 2), 8, '0', STR_PAD_LEFT);
		}
		if($x) echo PHP_EOL;
	}

	public static function sanitizeTest() {
		self::sendHeader();
		foreach(array(
			array("",				true),
			array("\x2",			true),
			array("\x88",			true),
			array("\xFE",			true),
			array("\1\x2",			true),
			array("\1\x88",			true),
			array("\1\xFE",			true),
			array("\1\x2\x2",		true),
			array("\1\x88\x88",		true),
			array("\1\xFE\xFE",		true),
			array("\1\x2\1\x2",		true),
			array("\1\x88\1\x88",	true),
			array("\1\xFE\1\xFE",	true),

			array("\1\xFE\xFE\0",	"\1\xFE\xFE"),
			array("\0",				""),
			array("\0\0",			""),

			array("\0\1\xFE\xFE",	null),
			array("\1\0\xFE\xFE",	null),
			array("\1\xFE\0\xFE",	null),

			array(null,				""),
			array(false,			""),
			array(true,				null),
			array(1,				null),
			array(2,				"2"),

		) as $a) {
			$e = $a[1] === true ? $a[0] : $a[1];
			$l = PreorderLabeling8::sanitize($a[0]);
			echo $e === $l ? 'ok' : 'fail';
			self::printbin($l, false);
			echo PHP_EOL;
		}
	}

	public static function depthTest() {
		self::sendHeader();
		foreach(array(
			"\x80"						=> 1,
			"\x7F\x80"					=> 1,
			"\x7F\x7F\x80"				=> 1,

			"\x80\x80"					=> 2,
			"\x80\x7F\x80"				=> 2,
			"\x80\x7F\x7F\x80"			=> 2,
			"\x7F\x80\x80"				=> 2,
			"\x7F\x7F\x80\x80"			=> 2,
			"\x7F\x80\x7F\x80"			=> 2,
			"\x7F\x7F\x80\x7F\x7F\x80"	=> 2,

			"\x80\x80\x80"				=> 3,
			"\x80\x7F\x80\x80"			=> 3,
			"\x80\x7F\x7F\x80\x80"		=> 3,

		) as $l => $e) {
			$d = PreorderLabeling8::depth($l);
			echo $e, ' ', $d, ' ', $d === $e ? 'ok' : 'fail';
			echo PHP_EOL;
		}
	}

	public static function childsTest() {
		self::sendHeader();
		$a = PreorderLabeling8::childs('', 1000);
		foreach($a as $l) self::printbin($l);
		echo PHP_EOL;
	}

	public static function siblingsTest() {
		self::sendHeader();
		$a = PreorderLabeling8::siblings("\x7C", "\x7E", 1000);
		foreach($a as $l) self::printbin($l);
		echo PHP_EOL;
	}

	public static function offsetTest() {
		self::sendHeader();
		foreach(array(
			""							=> null,

			"\x2"						=> 0,
			"\x88"						=> 0,
			"\xFE"						=> 0,
			"\1\x2"						=> 0,
			"\1\x88"					=> 0,
			"\1\xFE"					=> 0,
			"\x80"						=> 0,
			"\x7F\x80"					=> 0,
			"\x7F\x7F\x80"				=> 0,

			"\x80\x80"					=> 1,
			"\x80\x7F\x80"				=> 1,
			"\x80\x7F\x7F\x80"			=> 1,

			"\1\x2\x2"					=> 2,
			"\1\x88\x88"				=> 2,
			"\1\xFE\xFE"				=> 2,
			"\1\x2\1\x2"				=> 2,
			"\1\x88\1\x88"				=> 2,
			"\1\xFE\1\xFE"				=> 2,
			"\x7F\x80\x80"				=> 2,
			"\x7F\x80\x7F\x80"			=> 2,
			"\x80\x80\x80"				=> 2,

			"\x7F\x7F\x80\x80"			=> 3,
			"\x7F\x7F\x80\x7F\x7F\x80"	=> 3,
			"\x80\x7F\x80\x80"			=> 3,

			"\x80\x7F\x7F\x80\x80"		=> 4,

		) as $l => $e) {
			$o = PreorderLabeling8::offset($l);
			echo $e, ' ', $o, ' ', $o === $e ? 'ok' : 'fail';
			if($o != $e) self::printbin($l, false);
			echo PHP_EOL;
		}
	}

	public static function levelTest() {
		self::sendHeader();
		foreach(array(
			""							=> "",
			"\x2"						=> "\x2",
			"\x88"						=> "\x88",
			"\xFE"						=> "\xFE",
			"\1\x2"						=> "\1\x2",
			"\1\x88"					=> "\1\x88",
			"\1\xFE"					=> "\1\xFE",
			"\x80"						=> "\x80",
			"\x7F\x80"					=> "\x7F\x80",
			"\x7F\x7F\x80"				=> "\x7F\x7F\x80",

			"\x80\x80"					=> "\x80",
			"\x80\x7F\x80"				=> "\x7F\x80",
			"\x80\x7F\x7F\x80"			=> "\x7F\x7F\x80",
			"\1\x2\x2"					=> "\x2",
			"\1\x88\x88"				=> "\x88",
			"\1\xFE\xFE"				=> "\xFE",
			"\1\x2\1\x2"				=> "\1\x2",
			"\1\x88\1\x88"				=> "\1\x88",
			"\1\xFE\1\xFE"				=> "\1\xFE",

			"\x7F\x80\x80"				=> "\x80",
			"\x7F\x80\x7F\x80"			=> "\x7F\x80",
			"\x80\x80\x80"				=> "\x80",
			"\x7F\x7F\x80\x80"			=> "\x80",
			"\x7F\x7F\x80\x7F\x7F\x80"	=> "\x7F\x7F\x80",
			"\x80\x7F\x80\x80"			=> "\x80",
			"\x80\x7F\x7F\x80\x80"		=> "\x80",

		) as $l => $e) {
			$level = PreorderLabeling8::level($l);
			echo $e === $level ? 'ok' : 'fail';
			echo PHP_EOL;
			if($level != $e) {
				self::printbin($e); self::printbin($level);
			}
		}
	}

	public static function upTest() {
		self::sendHeader();
		foreach(array(
			""							=> null,
			"\x2"						=> "",
			"\x88"						=> "",
			"\xFE"						=> "",
			"\1\x2"						=> "",
			"\1\x88"					=> "",
			"\1\xFE"					=> "",
			"\x80"						=> "",
			"\x7F\x80"					=> "",
			"\x7F\x7F\x80"				=> "",

			"\x80\x80"					=> "\x80",
			"\x80\x7F\x80"				=> "\x80",
			"\x80\x7F\x7F\x80"			=> "\x80",
			"\1\x2\x2"					=> "\1\x2",
			"\1\x88\x88"				=> "\1\x88",
			"\1\xFE\xFE"				=> "\1\xFE",
			"\1\x2\1\x2"				=> "\1\x2",
			"\1\x88\1\x88"				=> "\1\x88",
			"\1\xFE\1\xFE"				=> "\1\xFE",

			"\x7F\x80\x80"				=> "\x7F\x80",
			"\x7F\x80\x7F\x80"			=> "\x7F\x80",
			"\x80\x80\x80"				=> "\x80\x80",

			"\x7F\x7F\x80\x80"			=> "\x7F\x7F\x80",
			"\x7F\x7F\x80\x7F\x7F\x80"	=> "\x7F\x7F\x80",
			"\x80\x7F\x80\x80"			=> "\x80\x7F\x80",
			"\x80\x7F\x7F\x80\x80"		=> "\x80\x7F\x7F\x80",

		) as $l => $e) {
			$up = PreorderLabeling8::up($l);
			echo $e === $up ? 'ok' : 'fail';
			echo PHP_EOL;
			if($up != $e) {
				self::printbin($e); self::printbin($up);
			}
		}
	}

	public static function ancestorsTest() {
		self::sendHeader();
		foreach(array(
			""							=> array(),
			"\x2"						=> array(),
			"\x88"						=> array(),
			"\xFE"						=> array(),
			"\1\x2"						=> array(),
			"\1\x88"					=> array(),
			"\1\xFE"					=> array(),
			"\x80"						=> array(),
			"\x7F\x80"					=> array(),
			"\x7F\x7F\x80"				=> array(),

			"\x80\x80"					=> array("\x80"),
			"\x80\x7F\x80"				=> array("\x80"),
			"\x80\x7F\x7F\x80"			=> array("\x80"),
			"\1\x2\x2"					=> array("\1\x2"),
			"\1\x88\x88"				=> array("\1\x88"),
			"\1\xFE\xFE"				=> array("\1\xFE"),
			"\1\x2\1\x2"				=> array("\1\x2"),
			"\1\x88\1\x88"				=> array("\1\x88"),
			"\1\xFE\1\xFE"				=> array("\1\xFE"),
			"\x7F\x80\x80"				=> array("\x7F\x80"),
			"\x7F\x80\x7F\x80"			=> array("\x7F\x80"),
			"\x7F\x7F\x80\x80"			=> array("\x7F\x7F\x80"),
			"\x7F\x7F\x80\x7F\x7F\x80"	=> array("\x7F\x7F\x80"),

			"\x80\x80\x80"				=> array("\x80", "\x80\x80"),
			"\x80\x7F\x80\x80"			=> array("\x80", "\x80\x7F\x80"),
			"\x80\x7F\x7F\x80\x80"		=> array("\x80", "\x80\x7F\x7F\x80"),

		) as $l => $e) {
			$ancestors = PreorderLabeling8::ancestors($l);
			echo $e === $ancestors ? 'ok' : 'fail';
			echo PHP_EOL;
			if($ancestors !== $e) {
				foreach($e as $a) self::printbin($a); echo PHP_EOL; foreach($ancestors as $a) self::printbin($a);
			}
		}
	}

	public static function splitTest() {
		self::sendHeader();
		foreach(array(
			""							=> array(),

			"\x2"						=> array("\x2"),
			"\x88"						=> array("\x88"),
			"\xFE"						=> array("\xFE"),
			"\1\x2"						=> array("\1\x2"),
			"\1\x88"					=> array("\1\x88"),
			"\1\xFE"					=> array("\1\xFE"),
			"\x80"						=> array("\x80"),
			"\x7F\x80"					=> array("\x7F\x80"),
			"\x7F\x7F\x80"				=> array("\x7F\x7F\x80"),

			"\x80\x80"					=> array("\x80", "\x80"),
			"\x80\x7F\x80"				=> array("\x80", "\x7F\x80"),
			"\x80\x7F\x7F\x80"			=> array("\x80", "\x7F\x7F\x80"),
			"\1\x2\x2"					=> array("\1\x2", "\x2"),
			"\1\x88\x88"				=> array("\1\x88", "\x88"),
			"\1\xFE\xFE"				=> array("\1\xFE", "\xFE"),
			"\1\x2\1\x2"				=> array("\1\x2", "\1\x2"),
			"\1\x88\1\x88"				=> array("\1\x88", "\1\x88"),
			"\1\xFE\1\xFE"				=> array("\1\xFE", "\1\xFE"),
			"\x7F\x80\x80"				=> array("\x7F\x80", "\x80"),
			"\x7F\x80\x7F\x80"			=> array("\x7F\x80", "\x7F\x80"),
			"\x7F\x7F\x80\x80"			=> array("\x7F\x7F\x80", "\x80"),
			"\x7F\x7F\x80\x7F\x7F\x80"	=> array("\x7F\x7F\x80", "\x7F\x7F\x80"),

			"\x80\x80\x80"				=> array("\x80", "\x80", "\x80"),
			"\x80\x7F\x80\x80"			=> array("\x80", "\x7F\x80", "\x80"),
			"\x80\x7F\x7F\x80\x80"		=> array("\x80", "\x7F\x7F\x80", "\x80"),

		) as $l => $e) {
			$split = PreorderLabeling8::split($l);
			echo $e === $split ? 'ok' : 'fail';
			echo PHP_EOL;
			if($split !== $e) {
				foreach($e as $a) self::printbin($a);
				echo PHP_EOL;
				foreach($split as $a) self::printbin($a);
			}
		}
	}

	public static function sendHeader() {
		static $sent;
		$sent || header('Content-Type: text/plain');
		$sent = true;
	}

	public static function testAll() {
		self::sanitizeTest();
		self::childsTest();
		self::siblingsTest();
		self::depthTest();
		self::offsetTest();
		self::levelTest();
		self::upTest();
		self::ancestorsTest();
		self::splitTest();
	}

}
