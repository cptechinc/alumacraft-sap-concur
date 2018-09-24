<?php 
	/**
	 * Class to handle dealing with List Items for Alumacraft's Inventory
	 */
	class Concur_ListItemInventory extends Concur_ListItem {
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
                'ID'         => array('dbcolumn' => 'concurID', 'required' => false),
                'Level1Code' => array('dbcolumn' => 'itemID', 'required' => false),
                'listID'     => array('dbcolumn' => 'listID', 'required' => false),
                'Name'       => array('dbcolumn' => 'itemDescription', 'required' => false),
            )
		);
		
		/**
		 * List ID for Inventory
		 * @var string
		 */
		protected $listID;
		
		/**
		 * Constructor that also instatiates the $listID property
		 */
		public function __construct() {
			
		}
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
