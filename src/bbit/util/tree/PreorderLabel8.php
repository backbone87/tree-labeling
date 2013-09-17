<?php

namespace bbit\util\tree;

class PreorderLabel8 {

	const FIRST = 0x01;
	const LAST = 0x02;

	public static function create($label) {
		$label = PreorderLabeling8::sanitize($label);
		if($label === null) {
			throw new InvalidArgumentException('#1 must be a valid sanitizable label');
		}
		return new self($label);
	}

	public static function compare(self $a, self $b) {
		return strcmp($a->label, $b->label);
	}

	private $label;

	protected function __construct($label) {
		$this->label = $label;
	}

	public function __toString() {
		return $this->getBinary();
	}

	public function toBinary() {
		return $this->label;
	}

	public function isRoot() {
		return !strlen($this->label);
	}

	public function isSelf(self $other) {
		return $this->label === $other->label;
	}

	public function isAncestorOf(self $other) {
		return strncmp($this->label, $other->label, strlen($this->label)) == 0;
	}

	public function isParentOf(self $other) {
		return $this->label === PreorderLabeling8::up($other->label);
	}

	public function isDescendantOf(self $other) {
		return strncmp($this->label, $other->label, strlen($other->label)) == 0;
	}

	public function isChildOf(self $other) {
		return PreorderLabeling8::up($this->label) === $other->label;
	}

	public function isPreceding(self $other) {
		return strcmp($this->label, $other->label) < 0;
	}

	public function isFollowing(self $other) {
		return strcmp($this->label, $other->label) > 0;
	}

	public function isSiblingOf(self $other) {
		return $this->label !== $other->label && PreorderLabeling8::up($this->label) === PreorderLabeling8::up($other->label);
	}

	public function isPrecedingSiblingOf(self $other) {
		return $this->isPreceding($other) && $this->isSiblingOf($other);
	}

	public function isFollowingSiblingOf(self $other) {
		return $this->isFollowing($other) && $this->isSiblingOf($other);
	}

	public function getDepth() {
		return PreorderLabeling8::depth($this->label);
	}

	public function getParent() {
		if($this->isRoot()) {
			throw new LogicException('The root node has no parent');
		}
		return self::createSafe(PreorderLabeling8::up($this->label));
	}

	public function getAncestors() {
		if($this->isRoot()) {
			throw new LogicException('The root node has no ancestors');
		}
		return self::createAllSafe(PreorderLabeling8::ancestors($this->label));
	}

	public function getDescendantLimit() {
		return PreorderLabeling8::descendants($this->label);
	}

	public function createChild() {
		return self::createSafe(PreorderLabeling8::child($this->label));
	}

	public function createChildren($n = 1) {
		return self::createAllSafe(PreorderLabeling8::children($this->label, $n));
	}

	public function createSibling($other = self::LAST) {
		$siblings = $this->createSiblings($other);
		return $siblings[0];
	}

	public function createSiblings($other = self::LAST, $n = 1) {
		if($this->isRoot()) {
			throw new LogicException('The root node can not have siblings');
		}
		if($other === self::FIRST) {
			$a = '';
			$b = $this->label;

		} elseif($other === self::LAST) {
			$a = $this->label;
			$b = '';

		} elseif(!($other instanceof self)) {
			throw new InvalidArgumentException('#1 must be ' . __CLASS__ . '::FIRST, ' . __CLASS__ . '::LAST or an instance of ' . __CLASS__);

		} elseif(!$this->isSiblingOf($other)) {
			throw new InvalidArgumentException('#1 must be a sibling');

		} else {
			$a = $this->label;
			$b = $other->label;
		}
		return self::createAllSafe(PreorderLabeling8::siblings($a, $b, $n));
	}

	protected static function createSafe($label) {
		return new self($label);
	}

	protected static function createAllSafe($labels) {
		foreach($labels as &$label) {
			$label = new self($label);
		}
		return $labels;
	}

}
