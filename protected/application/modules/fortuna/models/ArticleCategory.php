<?php

/**
 * model of article categories table
 * 
 * @package Fortuna
 * @subpackage Fortuna_Model
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_Model_ArticleCategory extends Fortuna_Db_Table_Abstract {
    
    /**
     * name of table
     * 
     * @var string
     * @access protected
     * 
     */
	protected $_name    = 'article_category';
    
    /**
     * primary key of table
     * 
     * @var array
     * @access protected 
     */
	protected $_primary = array('category_id', 'article_id');
    
    /**
     * get an articles' categories
     * 
     * @param int     $articleId id of article to look up
     * @param boolean $onlyIds   option to return only ids of categories
     * @return array
     * @since 1.0
     * 
     */
	public function getArticleCategories($articleId, $onlyIds = true) {
		$select = $this
				  ->select()
				  ->from($this->_name . ' AS a', array('category_id'))
				  ->where('a.article_id = ?', $articleId);

		if (!$onlyIds) {
			$cm = new Fortuna_Model_Category();
			
			$select->setIntegrityCheck(false);
			$select->join($cm->getTableName() . ' AS c', 'c.id = a.category_id', array('*'));
		}

		$data = array();

		foreach ($this->fetchAll($select)->toArray() as $row) {
			$data[] = $onlyIds ? $row['category_id'] : $row;
		}

		return $data;
	}
    
    /**
     * add an relation to the database between $articleId and $categoryId
     * 
     * @param int $articleId  id of the article
     * @param int $categoryId id of the category
     * @return int
     * @since 1.0 
     */
	public function addRelation($articleId, $categoryId) {

		return $this->insert(array(
	    	'category_id' => $categoryId,
	    	'article_id'  => $articleId,
	    ));
	}

    /**
     * get the number of articles per category
     * 
     * @return array
     * @since 1.0
     */
	public function getArticlesPerCategory() {
		$cm = new Fortuna_Model_Category();

		$countSelect = $this
				     ->select()
				     ->from($this->_name , array('count' => 'COUNT(*)'))
				     ->setIntegrityCheck(false)
				     ->where('category_id = c.id')
				     ->limit(1);

		$select = $this
				  ->select()
				  ->setIntegrityCheck(false)
				  ->from($this->_name . ' AS ac', array('data' => new Zend_Db_Expr('(' . $countSelect . ')')))
				  ->join($cm->getTableName() . ' AS c', 'c.id = ac.category_id', array('label' => 'c.name'))
				  ->group('ac.category_id');

		$data = array();

		foreach ($this->fetchAll($select)->toArray() as $k => $row) {
			$data[$k] = $row;
			$data[$k]['data'] = intval($row['data']);
		}

		return $data;

	}
    
    /**
     * get the average number of articles
     * per category
     * 
     * @return int
     * @since 1.0
     */
	public function getArticlesCategoryAvg() {
		$avgSelect = $this
				     ->select()
				     ->from($this->_name . ' AS ac', array('avg' => new Zend_Db_Expr('COUNT(*) / COUNT(distinct category_id)')))
				     ->setIntegrityCheck(false)
				     ->limit(1);


		$res = $this->fetchRow($avgSelect);

		if ($res instanceof Zend_Db_Table_Row) {
			return round($res->avg, 2);
		}

		return 0;
	}
    
    /**
     * get articles by $categoryId
     * 
     * @param type $categoryId id of the category to get articles from
     * @since 1.0
     * @uses Fortuna_Model_Article
     * @return type 
     */
	public function getArticlesByCategoryId($categoryId) {
		$am = new Fortuna_Model_Article();

		$select = $this
				  ->select()
				  ->from($am->getTableName() . ' AS a', array('*'))
				  ->setIntegrityCheck(false)
				  ->join($this->_name . ' AS ac', 'ac.article_id = a.id')
				  ->where('ac.category_id = ?', $categoryId);

		return $this->fetchAll($select)->toArray();
	}
}