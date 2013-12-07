<?php

/**
 * model class for articles table
 * 
 * @package Fortuna
 * @subpackage Fortuna_Model
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_Model_Article extends Fortuna_Db_Table_Abstract {
    
    /**
     * name of table
     * 
     * @var string
     * @access protected 
     */
	protected $_name = 'article';
    
    /**
     * name of primary key
     * 
     * @access protected
     * @var string 
     */
	protected $_primary = 'id';
    
    /**
     * generate slug based on $title
     * 
     * 
     * @param  string $title Full title of article
     * @since  1.0
     * @return string
     * @access protected
     */
    protected function _getSlug($title){
		$slug = trim($title); // trim the string
		$slug= preg_replace('/[^a-zA-Z0-9 -]/','',$slug ); // only take alphanumerical characters, but keep the spaces and dashes too...
		$slug= str_replace(' ','-', $slug); // replace spaces by dashes
		$slug= strtolower($slug);  // make it lowercase

		return $slug;
	}
    
    
    /**
     * get all articles
     * 
     * @since 1.0
     * @return array  
     */
	public function getArticles() {
		$select = $this
				  ->select()
				  ->setIntegrityCheck(false)
				  ->from($this->_name . ' AS a', array('id', 'title', 'date', 'is_home', 'slug'));

		return $this->fetchAll($select)->toArray();
	}
    
    /**
     * get home article id
     * 
     * 
     * @return int|boolean
     * @since 1.0
     * 
     */
	public function getHomeId() {
		$select = $this
				  ->select()
				  ->from($this->_name . ' AS a', array('id'))
				  ->where('is_home = 1')
				  ->orWhere('is_home = 0')
				  ->order('is_home DESC')
				  ->limit(1);

		$res = $this->fetchRow($select);

		if ($res instanceof Zend_Db_Table_Row) {
			return $res->id;
		}

		return false;
	}
    
    /**
     * get article data by $id
     * 
     * @param int $id Id of article to get
     * @return array
     * @since 1.0
     *  
     */
	public function getArticle($id) {
		$select = $this
				  ->select()
				  ->from($this->_name . ' AS a', array('*'))
				  ->where('a.id = ?', $id)
				  ->limit(1);

		$res = $this->fetchRow($select);

		if ($res instanceof Zend_Db_Table_Row) {
			$data = $res->toArray();

			$acm = new Fortuna_Model_ArticleCategory();

			$data['categories'] = $acm->getArticleCategories($id);

			return $data;
		} else {
			return array();
		}
	}
    
    /**
     * get article data by $slug
     * 
     * 
     * @param string $slug slug of article to get
     * @return array
     * @since 1.0
     * 
     */
	public function getArticleBySlug($slug) {
		$select = $this
				  ->select()
				  ->from($this->_name . ' AS a', array('*'))
				  ->where('a.slug = ?', $slug)
				  ->limit(1);

		$res = $this->fetchRow($select);

		if ($res instanceof Zend_Db_Table_Row) {
			$data = $res->toArray();
			return $data;
		} else {
			return array();
		}
	}
    
    /**
     * get number of overall articles
     * 
     * @return int
     * @since 1.0
     */
	public function getArticlesCount() {
		$select = $this
				  ->select()
				  ->from($this->_name . ' AS a', array('count' => 'COUNT(*)'))
				  ->limit(1);

		$res = $this->fetchRow($select);

		if ($res instanceof Zend_Db_Table_Row) {
			return $res->count;
		}

		return 0;
	}

    /**
     * get number of articles created at
     * a day, an array is returnded with timestamp
     * and count of articles
     * 
     * @return array
     * @since 1.0
     */
	public function getArticlesLately() {
		$select = $this
				  ->select()
				  ->setIntegrityCheck(false)
				  ->from($this->_name . ' AS a', array('date', 'count' => 'COUNT(*)'))
				  ->group('DATE_FORMAT(FROM_UNIXTIME(a.date), "%d/%m/%Y")');

		$data = array();

		foreach ($this->fetchAll($select)->toArray() as $row) {
			$data[] = array(intval($row['date'] * 1000), (float) $row['count']);
		}

		return $data;
	}
    
    /**
     * save an article by updating or inserting of $input
     * 
     * @param array    $input data to save
     * @param int|null $id    id of dataset to update
     * @since 1.0
     * 
     */
	public function saveArticle(array $input, $id = null) {
		$data = array(
			'title'     => $input['title'],
			'content'	=> stripslashes($input['content']),
			'slug'		=> $this->_getSlug($input['title']),
			'is_home'   => isset($input['is_home']) && $input['is_home'] ? 1 : 0
		);
		if (empty($id)) {
			$data['date'] = time();

			$id = $this->insert($data);
		} else {
			$this->update($data, 'id = ' . $id);
		}

		$acm = new Fortuna_Model_ArticleCategory();
		$acm->delete('article_id = ' . $id);

		if (!empty($input['categories'])) {
			foreach ($input['categories'] as $categoryId) {
				$acm->addRelation($id, $categoryId);
			}
		}
	}
}