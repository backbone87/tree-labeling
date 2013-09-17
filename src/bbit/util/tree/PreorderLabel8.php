<?php

namespace bbit\util\tree;

/**
 * This is a light-weight OO wrapper around the PreorderLabeling8 utilities for
 * type-safe easy usage of tree labels.
 *
 * @author Oliver Hoff <oliver@hofff.com>
 */
class PreorderLabel8 {

	/** @var integer */
	const FIRST = 0x01;
	/** @var integer */
	const LAST = 0x02;

	/**
	 * Creates a new label instance from the given label binary or, if absent,
	 * the root node label.
	 *
	 * @param mixed $label
	 * @throws InvalidArgumentException If the label is not valid and can not be
	 * 		sanitized
	 * @return PreorderLabel8
	 */
	public static function create($label = null) {
		$label = PreorderLabeling8::sanitize($label);
		if($label === null) {
			throw new InvalidArgumentException('#1 must be a valid sanitizable label');
		}
		return new self($label);
	}

	/**
	 * @param PreorderLabel8 $a
	 * @param PreorderLabel8 $b
	 * @return number
	 */
	public static function compare(self $a, self $b) {
		return strcmp($a->label, $b->label);
	}

	/** @var string */
	private $label;

	/**
	 * @param string $label
	 */
	protected function __construct($label) {
		$this->label = $label;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->getBinary();
	}

	/**
	 * @return string
	 */
	public function toBinary() {
		return $this->label;
	}

	/**
	 * Checks if this is the root node label.
	 *
	 * @return boolean
	 */
	public function isRoot() {
		return !strlen($this->label);
	}

	/**
	 * Checks if the given label is same as this one.
	 *
	 * @param PreorderLabel8 $other
	 * @return boolean
	 */
	public function isSelf(self $other) {
		return $this->label === $other->label;
	}

	/**
	 * Checks if this is an ancestor of the given label.
	 *
	 * @param PreorderLabel8 $other
	 * @return boolean
	 */
	public function isAncestorOf(self $other) {
		return strncmp($this->label, $other->label, strlen($this->label)) == 0;
	}

	/**
	 * Checks if this is the parent of the given label.
	 *
	 * @param PreorderLabel8 $other
	 * @return boolean
	 */
	public function isParentOf(self $other) {
		return $this->label === PreorderLabeling8::up($other->label);
	}

	/**
	 * Checks if this is a descendant of the given label.
	 *
	 * @param PreorderLabel8 $other
	 * @return boolean
	 */
	public function isDescendantOf(self $other) {
		return strncmp($this->label, $other->label, strlen($other->label)) == 0;
	}

	/**
	 * Checks if this is a child of the given label.
	 *
	 * @param PreorderLabel8 $other
	 * @return boolean
	 */
	public function isChildOf(self $other) {
		return PreorderLabeling8::up($this->label) === $other->label;
	}

	/**
	 * Checks if this is preceding the given label in document order.
	 *
	 * @param PreorderLabel8 $other
	 * @return boolean
	 */
	public function isPreceding(self $other) {
		return strcmp($this->label, $other->label) < 0;
	}

	/**
	 * Checks if this is following the given label in document order.
	 *
	 * @param PreorderLabel8 $other
	 * @return boolean
	 */
	public function isFollowing(self $other) {
		return strcmp($this->label, $other->label) > 0;
	}

	/**
	 * Checks if this is a sibling of the given label.
	 *
	 * @param PreorderLabel8 $other
	 * @return boolean
	 */
	public function isSiblingOf(self $other) {
		return $this->label !== $other->label && PreorderLabeling8::up($this->label) === PreorderLabeling8::up($other->label);
	}

	/**
	 * Checks if this is a preceding sibling of the given label.
	 *
	 * @param PreorderLabel8 $other
	 * @return boolean
	 */
	public function isPrecedingSiblingOf(self $other) {
		return $this->isPreceding($other) && $this->isSiblingOf($other);
	}

	/**
	 * Checks if this is a following sibling of the given label.
	 *
	 * @param PreorderLabel8 $other
	 * @return boolean
	 */
	public function isFollowingSiblingOf(self $other) {
		return $this->isFollowing($other) && $this->isSiblingOf($other);
	}

	/**
	 * @return integer
	 */
	public function getDepth() {
		return PreorderLabeling8::depth($this->label);
	}

	/**
	 * @throws LogicException If this is the root node label
	 * @return PreorderLabel8
	 */
	public function getParent() {
		if($this->isRoot()) {
			throw new LogicException('The root node has no parent');
		}
		return self::createSafe(PreorderLabeling8::up($this->label));
	}

	/**
	 * @throws LogicException If this is the root node label
	 * @return array<PreorderLabel8>
	 */
	public function getAncestors() {
		if($this->isRoot()) {
			throw new LogicException('The root node has no ancestors');
		}
		return self::createAllSafe(PreorderLabeling8::ancestors($this->label));
	}

	/**
	 * @throws LogicException If this is the root node label
	 * @return string
	 */
	public function getDescendantLimit() {
		if($this->isRoot()) {
			throw new LogicException('The root node has no descendant limit (all different labels are descendants)');
		}
		return PreorderLabeling8::descendants($this->label);
	}

	/**
	 * Retrieves the first child label of this node label. The preorder of the
	 * returned label can only be ensured, if there are no children of this
	 * label yet.
	 * You can use the createSibling method on an existing child node label, if
	 * you want to find more child labels for this node.
	 *
	 * @return PreorderLabel8
	 */
	public function createChild() {
		return self::createSafe(PreorderLabeling8::child($this->label));
	}

	/**
	 * Retrieves mutliple child labels of this node label. The preorder of the
	 * returned labels can only be ensured, if there are no children of this
	 * label yet.
	 *
	 * You can use the createSiblings method on an existing child node label, if
	 * you want to find more child labels for this node.
	 *
	 * At least one child label is returned.
	 *
	 * @param integer $n The number of child labels to return
	 * @return array<PreorderLabel8>
	 */
	public function createChildren($n = 1) {
		return self::createAllSafe(PreorderLabeling8::children($this->label, $n));
	}

	/**
	 * Retrieves a sibling label $s that totally orders between this node $this
	 * and the given node $other so that:
	 * 		$this < $s < $other, if $this < $other
	 * or	$this > $s > $other, if $this > $other
	 *
	 * There are two "virtual" nodes PreorderLabel8::FIRST and
	 * PreorderLabel8::LAST which act always as the first or last child of this
	 * nodes parent.
	 * One can use these special values to create a sibling label that
	 * orders before this label, if this label is the actual first child of its
	 * parent or after this label, if this label is the actual last child of its
	 * parent.
	 *
	 * @param PreorderLabel8|PreorderLabel8::FIRST|PreorderLabel8::LAST $other
	 * @throws LogicException If this is the root node label
	 * @throws InvalidArgumentException If the given value is not an instance of
	 * 		PreorderLabel8 or one of the two special values, or if the given
	 * 		node label does not share its parent with this label
	 * @return PreorderLabel8
	 */
	public function createSibling($other = self::LAST) {
		$siblings = $this->createSiblings($other);
		return $siblings[0];
	}

	/**
	 * Retrieves sibling labels that totally order between this node $this
	 * and the given node $other so that for each returned label $s:
	 * 		$this < $s < $other, if $this < $other
	 * or	$this > $s > $other, if $this > $other
	 *
	 * There are two "virtual" nodes PreorderLabel8::FIRST and
	 * PreorderLabel8::LAST which act always as the first or last child of this
	 * nodes parent.
	 * One can use these special values to create sibling labels that
	 * order before this label, if this label is the actual first child of its
	 * parent or after this label, if this label is the actual last child of its
	 * parent.
	 *
	 * At least one sibling label is returned.
	 *
	 * @param PreorderLabel8|PreorderLabel8::FIRST|PreorderLabel8::LAST $other
	 * @param integer $n The number of child labels to return
	 * @throws LogicException If this is the root node label
	 * @throws InvalidArgumentException If the given value is not an instance of
	 * 		PreorderLabel8 or one of the two special values, or if the given
	 * 		node label does not share its parent with this label
	 * @return array<PreorderLabel8>
	 */
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

	/**
	 * @param string $label
	 * @return PreorderLabel8
	 */
	protected static function createSafe($label) {
		return new self($label);
	}

	/**
	 * @param array<string> $labels
	 * @return array<PreorderLabel8>
	 */
	protected static function createAllSafe($labels) {
		foreach($labels as &$label) {
			$label = new self($label);
		}
		return $labels;
	}

}
