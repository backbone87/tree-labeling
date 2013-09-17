<?php

class TreePreorderLabeling8 {

	/**
	 * Returns a valid label by converting the given value to a string and
	 * truncating trailing NUL bytes off of the end.
	 * If the value fails to convert or the resulting string is not a valid
	 * label, null is returned.
	 * A label is valid when it
	 * a) contains no NUL bytes and the last bit is 0 or
	 * b) has a zero length.
	 *
	 * @param mixed $a Value to convert
	 * @return string|null The label on success; otherwise null
	 */
	public static function sanitize($a) {
		if($a !== null and !is_scalar($a) and !is_object($a) || !method_exists($a, '__toString')) return null;
		$a = rtrim(strval($a), "\0");
		$n = strlen($a);
		if($n == 0) return $a;
		if(strpos($a, "\0") !== false) return null;
		if(ord($a[$n - 1]) & 1) return null;
		return $a;
	}

	/**
	 * Retrieves a label $b so that for all descendant labels $d of $a and for
	 * all following sibling labels $s of $a:
	 * $a < $d < $b <= $s
	 * If $a has zero length null is returned.
	 *
	 * @param string $a A valid label
	 * @return string|null The first value greater than all descendant labels
	 * 		of $a or null if $a has zero length
	 */
	public static function descendants($a) {
		$n = strlen($a);
		if(!$n--) return null;
		$a[$n] = $a[$n] | "\1";
		return $a;
	}

	/**
	 * Retrieves the byte offset of the last level label in $a.
	 * If $a has zero length null is returned.
	 *
	 * @param string $a A valid label
	 * @return int|null The offset of the last level label
	 */
	public static function offset($a) {
		$n = strlen($a) - 1;
		if($n == -1) { return null; }
		while($n-- && ($a[$n] & "\1") === "\1");
		return $n + 1;
	}

	/**
	 * Truncates the last level label off of $a and returns it.
	 *
	 * @param string $a A valid label
	 * @return string The last level label of $a
	 */
	public static function level(&$a) {
		$i = static::offset($a);
		if($i === null) { $a = null; return ''; }
		$level = substr($a, $i);
		$a = substr($a, 0, $i);
		return $level;
	}

	/**
	 * Retrieves the parent label of $a.
	 *
	 * @param string $a A valid label
	 * @param string $level
	 * @return string|null
	 */
	public static function up($a, &$level = null) {
		$level = static::level($a);
		return $a;
	}

	/**
	 * Retrieves all ancestor labels of $a.
	 *
	 * @param string $a A valid label
	 * @return array<string>
	 */
	public static function ancestors($a) {
		$ancestors = array();
		while($a = static::up($a)) $ancestors[] = $a;
		return array_reverse($ancestors);
	}

	/**
	 * Retrieves all level labels of $a.
	 *
	 * @param string $a A valid label
	 * @return array<string>
	 */
	public static function split($a) {
		$levels = array();
		while(strlen($a)) $levels[] = static::level($a);
		return array_reverse($levels);
	}

	/**
	 * Retrieves the depth of $a, which is equal to the amount of level labels.
	 *
	 * @param string $a A valid label
	 * @return int
	 */
	public static function depth($a) {
		for($d = 0, $i = 0, $n = strlen($a); $i < $n; $i++) {
			($a[$i] & "\1") === "\0" && $d++;
		}
		return $d;
	}

	/**
	 * Retrieves the first child label of $a. The preorder of the label can only
	 * be ensured, if $a has had no children yet. You can use the sibling
	 * method, if you want to find child labels for parent nodes already having
	 * one or more children.
	 *
	 * @param string $a A valid label
	 * @return string
	 */
	public static function child($a) {
		return $a . chr(128);
	}

	/**
	 * Retrieves $n child labels of $a. The preorder of the labels can only be
	 * ensured, if $a has had no children yet. You can use the siblings method,
	 * if you want to find multiple child labels for parent nodes already having
	 * one or more children.
	 * At least one child label is returned.
	 *
	 * @param string $a A valid label
	 * @param integer $n The amount of child labels to return
	 * @return array<string>
	 */
	public static function children($a, $n = 1) {
		$i = strlen($a);
		$children = array(static::child($a));
		$n = max(0, intval($n) - 1); $p = 2;
		while($n--) {
			$children[] = static::sibling('', $children[0], $i);
			$m = min($n, $p - 2);
			while($m-- && $n) { $n--; $children[] = static::sibling($children[$m], $children[$m + 1], $i); }
			if($n) { $n--; $children[] = static::sibling($children[$p - 2], '', $i); $p *= 2; }
			sort($children, SORT_STRING);
		}
		return $children;
	}

	/**
	 * Retrieves a sibling label $s that totally orders between $a and $b:
	 * $a < $s < $b
	 *
	 * For $a and $b one of the following conditions must be met:
	 * - length($a) > 0 and up($a) === up($b) and (redundant) length($b) > 0
	 * - either $a or $b can be a zero length label, but not both. If $a is zero
	 * length, then it is treated as the absolute minimum of the last level
	 * label of $b. If $b is zero length, then it is treated as the absolute
	 * maximum of the last level label of $a.
	 *
	 * @param string $a A valid label
	 * @param string $b A valid label
	 * @param int|null $i The offset to the first byte of the last level label
	 * 		or null to calculate offset
	 * @return string
	 */
	public static function sibling($a, $b, $i = null) {
		$n = strlen($a); $m = strlen($b);
		$i === null && $i = static::offset($n ? $a : $b);
		for(;;) {
			if($i >= $n) {
				while($i < $m && $b[$i] === "\1") $i++;
				$d = ceil(ord($b[$i]) / 2);
				if($d == 1) {
					return substr($b, 0, $i) . "\1" . "\x80";
				} else {
					return substr($b, 0, $i) . chr($d & 254);
				}

			} elseif($i >= $m) {
				while($i < $n && $a[$i] === "\xFF") $i++;
				$d = ceil((ord($a[$i]) + 255) / 2);
				if($d == 255) {
					return substr($a, 0, $i) . "\xFF" . "\x80";
				} else {
					return substr($a, 0, $i) . chr($d & 254);
				}

			} else {
				$x = ord($a[$i]); $d = ord($b[$i]) - $x;
				if($d > 1) {
					$d = ceil($d / 2); $x += $d;
					if(($x & 1) && $d < 2) {
						return substr($a, 0, $i) . chr($x) . "\x80";
					} else {
						return substr($a, 0, $i) . chr($x & 254);
					}
				}
			}
			$i++;
		}
	}

	/**
	 * Retrieves $n sibling labels so that each returned label $s totally orders
	 * between $a and $b:
	 * $a < $s < $b
	 *
	 * For $a and $b one of the following conditions must be met:
	 * - length($a) > 0 and up($a) === up($b) and (redundant) length($b) > 0
	 * - either $a or $b can be a zero length label, but not both. If $a is zero
	 * length, then it is treated as the absolute minimum of the last level
	 * label of $b. If $b is zero length, then it is treated as the absolute
	 * maximum of the last level label of $a.
	 *
	 * At least one sibling label is returned.
	 *
	 * @param string $a A valid label
	 * @param string $b A valid label
	 * @param integer $n The amount of sibling labels to return
	 * @param int|null $i The offset to the first byte of the last level label
	 * 		or null to calculate offset
	 * @return array<string> The sibling labels
	 */
	public static function siblings($a, $b, $n = 1, $i = null) {
		$i === null && $i = static::offset(strlen($a) ? $a : $b);
		$siblings = array(static::sibling($a, $b, $i));
		$n = max(0, intval($n) - 1); $p = 2;
		while($n--) {
			$siblings[] = static::sibling($a, $siblings[0], $i);
			$m = min($n, $p - 2);
			while($m-- && $n) { $n--; $siblings[] = static::sibling($siblings[$m], $siblings[$m + 1], $i); }
			if($n) { $n--; $siblings[] = static::sibling($siblings[$p - 2], $b, $i); $p *= 2; }
			sort($siblings, SORT_STRING);
		}
		return $siblings;
	}

}
