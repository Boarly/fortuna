<?php

/**
 * model class for categories table
 * 
 * @package Fortuna
 * @subpackage Fortuna_Model
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_Model_Category extends Fortuna_Db_Table_Abstract {
    
    /**
     * name of the table
     * 
     * 
     * @var string
     * @access protected 
     */
	protected $_name    = 'category';
    
    /**
     * primary key of table
     * 
     * 
     * @var string
     * @access protected 
     */
	protected $_primary = 'id';
    
    /**
     * get slug for $title
     * 
     * 
     * @param  string $title title of article to get slug for
     * @access protected
     * @since  1.0
     * @return type 
     */
	protected function _getSlug($title){
		$slug = trim($title); // trim the string
		$slug = preg_replace('/[^a-zA-Z0-9 -]/','',$slug ); // only take alphanumerical characters, but keep the spaces and dashes too...
		$slug = str_replace(' ','-', $slug); // replace spaces by dashes
		$slug = strtolower($slug);  // make it lowercase

		return $slug;
	}
    
    /**
     * get all categories for overview
     * 
     * 
     * @since  1.0
     * @return array 
     */
	public function getCategories() {
		$select = $this
				  ->select()
				  ->setIntegrityCheck(false)
				  ->from($this->_name . ' AS c', array('id', 'name', 'slug'));


		return $this->fetchAll($select)->toArray();
	}
    
    /**
     * get array in selectbox form of all
     * categories despite of $notCategoryId
     * 
     * 
     * @param int|null $notCategoryId category id to exclude from return
     * @return array
     * @since 1.0 
     */
	public function getCategoriesSelect($notCategoryId = null) {
		$select = $this
				  ->select()
				  ->setIntegrityCheck(false)
				  ->from($this->_name . ' AS c', array('id', 'name'));

		if (!is_null($notCategoryId)) {
			$select->where('c.id <> ?', $notCategoryId);
		}

		$data = array();

		$data[""] = '';

		foreach ($this->fetchAll($select)->toArray() as $row) {
			$data[$row['id']] = $row['name'];
		}

		return $data;
	}
    
    /**
     * get category data for $id
     * 
     * 
     * @param int $id id of the category
     * @return array
     * @since 1.0 
     */
	public function getCategory($id) {
		$select = $this
				  ->select()
				  ->from($this->_name . ' AS c', array('*'))
				  ->where('c.id = ?', $id);

		$res = $this->fetchRow($select);

		if ($res instanceof Zend_Db_Table_Row) {
			$data = $res->toArray();

			return $data;
		} else {
			return array();
		}
	}
    
    /**
     * get number of overall categories
     * 
     * 
     * @return int
     * @since 1.0
     */
	public function getCategoriesCount() {
		$select = $this
				  ->select()
				  ->from($this->_name . ' AS c', array('count' => 'COUNT(*)'))
				  ->limit(1);

		$res = $this->fetchRow($select);

		if ($res instanceof Zend_Db_Table_Row) {
			return $res->count;
		}

		return 0;
	}
    
    /**
     * save category by inserting $data or updating it
     * 
     * 
     * @param  array    $input data to save
     * @param  int|null $id    id where to save category
     * @return mixed
     * @since 1.0
     */
	public function saveCategory(array $input, $id = null) {
		$data = array(
			'name'      => $input['name'],
			'slug'		=> $this->_getSlug($input['name'])
		);

		if (empty($id)) {
			return $this->insert($data);
		} else {
			return $this->update($data, 'id = ' . $id);
		}
	}
    
    /**
     * get category by $slug
     * 
     * @param  string $slug category-identifying slug
     * @return array
     * @since 1.0
     * 
     */
	public function getCategoryBySlug($slug) {
		$select = $this
				  ->select()
				  ->from($this->_name . ' AS c', array('*'))
				  ->where('c.slug = ?', $slug)
				  ->limit(1);

		$res = $this->fetchRow($select);

		if ($res instanceof Zend_Db_Table_Row) {
			$data = $res->toArray();
			return $data;
		} else {
			return array();
		}
	}
}