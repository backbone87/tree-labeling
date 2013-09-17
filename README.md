tree-labeling
=============

Efficient tree labeling using a variant of http://dbs.uni-leipzig.de/file/Insert_Friendly-TR2005.pdf

Features (as described by the linked paper):

- From a given node label one can determine the parent label and the label of 
	all ancestor nodes up to the root node, without requiring I/O operations (DB queries)

- The depth of a node within the tree can be determined from its label

- Given two node labels A and B, one can determine from the labels alone if B 
	with regard to A is a parent, child, ancestor, descendant, preceding node, 
	following node, preceding sibling, or following sibling

- By a range scan through the node label index from a given context node label, 
	one can determine the set of preceding, following and descendant nodes

- The level labels of the representation are single (variable-length) values
