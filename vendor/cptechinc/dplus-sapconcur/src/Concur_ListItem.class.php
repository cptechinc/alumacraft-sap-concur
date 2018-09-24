<?php 
	/**
	 * Class to handle dealing with List Items
	 */
	class Concur_ListItem extends Concur_Endpoint {
		use StructuredClassTraits;
		
		protected $endpoints = array(
			'list-items' => 'https://www.concursolutions.com/api/v3.0/common/listitems'
		);
		
		/**
		 * Structure of Purchase Order
		 * @var array
		 */
		protected $structure = array(
			'header' => array(
                'ID'         => array('dbcolumn' => '', 'required' => false),
                'Level1Code' => array('dbcolumn' => '', 'required' => false),
                'Level2Code' => array('dbcolumn' => '', 'required' => false),
                'Level3Code' => array('dbcolumn' => '', 'required' => false),
                'Level4Code' => array('dbcolumn' => '', 'required' => false),
                'Level5Code' => array('dbcolumn' => '', 'required' => false),
                'Level6Code' => array('dbcolumn' => '', 'required' => false),
                'Level7Code' => array('dbcolumn' => '', 'required' => false),
                'Level8Code' => array('dbcolumn' => '', 'required' => false),
                'Level9Code' => array('dbcolumn' => '', 'required' => false),
                'Level10Code' => array('dbcolumn' => '', 'required' => false),
                'listID'     => array('dbcolumn' => '', 'required' => false),
                'Name'       => array('dbcolumn' => '', 'required' => false),
                'ParentID'   => array('dbcolumn' => '', 'required' => false),
                'URI'        => array('dbcolumn' => '', 'required' => false),
            )
		);
		
		/* =============================================================
			CONCUR INTERFACE FUNCTIONS
		============================================================ */
        /**
		 * Sends GET Request to retreive List by ID
		 * // Example URL https://www.concursolutions.com/api/v3.0/common/listitems/?listID=gWvhrLa3BoKEGwQA$plZyPLJpe2Jy$pse9YAw
		 * @param  string $id List ID
		 * @return array      Response from Concur
		 */
		public function get_list($id) {
			$url = new Purl\Url($this->endpoints['list-items']);
            $url->query->set('listID', $id);
			return $this->get_curl($url->getUrl());
		}
        
        /**
		 * Sends GET Request to retreive list item by ID
		 * // Example : https://www.concursolutions.com/api/v3.0/common/listitems/?listID=gWoOk4$p8qPNb8y5o2wnWKByWYG1zauXN7fA
		 * @param  string $listitemID List Item ID
		 * @return array              Response from Concur
		 */
		public function get_listitem($listitemID) {
			$url = $this->endpoints['list-items'] . "/$listitemID";
			return $this->get_curl($url);
		}
        
        /**
         * Sends POST Request to create list item
         * @param  array $item  Item Key Value array to send
         * @return array        Array Response
         */
        public function create_listitem(array $item) {
            $listitem = $this->create_sectionarray($this->structure['header'], $item);
            $this->response =  $this->post_curl($this->endpoints['list-item'], $listitem, $json = true);
			$this->process_response();
			return $this->response;
        }
        
        /**
         * Sends PUT Request to update list item
         * @param  string $id   Concur List Item ID
         * @param  array  $item Item Key Value array to send
         * @return array        Array Response
         */
        public function update_listitem(string $id, array $item) {
            $listitem = $this->create_sectionarray($this->structure['header'], $item);
            $this->response =  $this->put_curl($this->endpoints['list-item'], $listitem, $json = true);
			$this->process_response();
			return $this->response;
        }
        
		/* =============================================================
			ERROR CODES AND POSSIBLE SOLUTIONS
		============================================================ */
		
		
		/* =============================================================
			CLASS FUNCTIONS
		============================================================ */
		
	}
