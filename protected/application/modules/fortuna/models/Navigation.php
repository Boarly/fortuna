<?php

/**
 * model class for navigation table
 * 
 * @package Fortuna
 * @subpackage Fortuna_Model
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_Model_Navigation extends Fortuna_Db_Table_Abstract {
    
    /**
     * name of table
     * 
     * @var    string
     * @access protected
     */
	protected $_name    = 'navigation';
    
    /**
     * primary key of table
     * 
     * 
     * @var string
     * @access protected 
     */
	protected $_primary = 'id';
    
    /**
     * save $category underneath $parent
     * 
     * 
     * @param  int      $category id of category
     * @param  int|null $parent   parent navigation id
     * @return int
     * @since 1.0
     */
	protected function _saveCategory($category, $parent = null) {

		return $this->insert(array(
        	'category_id' => $category,
        	'parent_id'   => $parent,
        	'article_id'  => null
        ));
	}
    
    /**
     * save $article underneath $parent
     * 
     * 
     * @param int      $article id of article
     * @param int|null $parent  parent navigation id
     * @return int
     * @since 1.0 
     */
	protected function _saveArticle($article, $parent = null) {

		return $this->insert(array(
        	'article_id'   => $article,
        	'parent_id'    => $parent,
        	'category_id'  => null
        ));
	}
    
    /**
     * get the nav tree out of database
     * including all two levels
     * 
     * 
     * @return array
     * @since 1.0
     * @uses Fortuna_Model_Article
     * @uses Fortuna_Model_Category
     */
	public function getNavTree() {
		$am = new Fortuna_Model_Article();
		$cm = new Fortuna_Model_Category();

		$select = $this
				  ->select()
  				  ->setIntegrityCheck(false)
				  ->from($this->_name . ' AS n', array(
				    	'slug'  => new Zend_Db_Expr('IF(n.category_id IS NOT NULL, c.slug, a.slug)'),
				    	'label' => new Zend_Db_Expr('IF(n.category_id IS NOT NULL, c.name, a.title)'),
				    	'child' => new Zend_Db_Expr('IF(n.parent_id IS NOT NULL, 1, 0)'),
				  		'type'  => new Zend_Db_Expr('IF(n.category_id IS NOT NULL, "category", "article")'),
				  		'id'    => new Zend_Db_Expr('IF(n.category_id IS NOT NULL, n.category_id, n.article_id)'),
				  		'parent_id'
				  ))
				  ->joinLeft($cm->getTableName() . ' AS c', 'c.id = n.category_id', array())
				  ->joinLeft($am->getTableName() . ' AS a', 'a.id = n.article_id', array())
				  ->order(array('n.id ASC', 'n.parent_id'));

		$tree     = array();
		$parentK  = null;
		$parentId = null;

		foreach ($this->fetchAll($select)->toArray() as $k => $row) {
			if ($row['child']) {
				if (is_null($parentK) || $parentId != $row['parent_id']) {
					$parentId = $row['parent_id'];
					$parentK  = $k - 1;

					$tree[$parentK]['childs'] = array();
				}

				$tree[$parentK]['childs'][] = $row;

				continue;
			} else {
				$tree[$k] = $row;
			}
		}

		return $tree;
	}
    
    /**
     * save nav tree $items
     * 
     * @param  array $items
     * @return boolean
     * @since 1.0
     */
	public function saveNavTree(array $items) {
		$this->_db->beginTransaction();

		try {

			$this->_db->query('TRUNCATE TABLE `' . $this->_name . '`;');

			$this->_db->query('ALTER TABLE `' . $this->_name . '` AUTO_INCREMENT = 1;');

			foreach ($items as $k => $item) {

				$itemId = null;
				$parent = null;

		        if (is_string($item) && !empty($item)) {
		            $itemId = $item;
		        } elseif (is_array($item)) {
		        	$itemId = $k;
		        } else {
		        	continue;
		        }

		        if (strpos($itemId, 'article_') !== false) {
	                $parent = $this->_saveArticle(str_replace('article_', '', $itemId));
	            } else {
	                $parent = $this->_saveCategory(str_replace('category_', '', $itemId));
	            }

	            if (is_array($item)) {
	            	foreach ($item as $child) {
	            		if (strpos($child, 'article_') !== false) {
			                $this->_saveArticle(str_replace('article_', '', $child), $parent);
			            } elseif (strpos($child, 'category_') !== false) {
			                $this->_saveCategory(str_replace('category_', '', $child), $parent);
			            }
	            	}
	            }

		    }

		    $this->_db->commit();

		    return true;
		} catch (Exception $e) {
			$this->_db->rollBack();

			return false;
		}
    }
}