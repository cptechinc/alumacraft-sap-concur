<?php
    namespace Dplus\Base;
    
    class QueryBuilder extends \atk4\dsql\Query {
        /**
         * $sqlkeywords is a list of SQL keywords that will be shown in uppercase when we debug the query
         * @var array
         */
        protected $sqlkeywords = array(
            'select',
            'from',
            'where',
            'update',
            'insert',
            'between',
            'and',
            'order',
            'cast',
            'as',
            'group',
            'by',
            'or',
            'asc',
            'desc',
            'limit',
            'values',
            'into',
            'set',
            'is',
            'in',
            'join',
            'left',
            'inner',
            'outer',
            'right',
            'like', 
            'on'
        );
        
        /**
         * Convert PHP Date Format code to MYSQL format code
         * @param  string $filter      key of filter array
         * @param  array  $filtertypes  has data on filter
         * @return string              MySQL date format code
         * @uses
         */
        public function generate_dateformat($filter, $filtertypes) {
            $find = array('m', 'd', 'Y', 'H', 'i', 's');
            $format = isset($filtertypes[$filter]['date-format']) ? $filtertypes[$filter]['date-format'] : 'm/d/Y';
            $sqlformat = $format;

            foreach ($find as $code) {
                $sqlformat = str_replace($code, "%$code", $sqlformat);
            }
            return $sqlformat;
        }
        
        /**
         * Takes the filters array and adds where statements for each filter based on its type
         * @param  array $filters      Filters Keyed by filter
         * @param  array $filtertypes  Properties for each filter
         * @return void
         */
        public function generate_filters($filters, $filtertypes) {
            foreach ($filters as $filter => $filtervalue) {
                switch ($filtertypes[$filter]['querytype']) {
                    case 'between':
                        $filtervalue = array_unique(array_values(array_filter($filtervalue, 'strlen')));

                        if (sizeof($filtervalue) == 1) {
                            if ($filtertypes[$filter]['datatype'] == 'mysql-date') {
                                $this->where($this->expr("DATE($filter) = STR_TO_DATE([], '%m/%d/%Y')", $filtervalue));
                            } elseif ($filtertypes[$filter]['datatype'] == 'date') {
                                $dateformat = $this->generate_dateformat($filter, $filtertypes);
                                $this->where($this->expr("STR_TO_DATE($filter, '$dateformat') = STR_TO_DATE([], '%m/%d/%Y')", $filtervalue));
                            } else if ($filtertypes[$filter]['datatype'] == 'numeric') {
                                $this->where($this->expr("$filter = CAST([] AS DECIMAL) ", $filtervalue));
                            } else {
                                $this->where($filter, $filtervalue[0]);
                            }
                        } else {
                            if ($filtertypes[$filter]['datatype'] == 'mysql-date') {
                                $dateformat = $this->generate_dateformat($filter, $filtertypes);
                                $this->where($this->expr("DATE($filter) BETWEEN STR_TO_DATE([], '%m/%d/%Y') AND STR_TO_DATE([], '%m/%d/%Y')", $filtervalue));
                            } elseif ($filtertypes[$filter]['datatype'] == 'date') {
                                $dateformat = $this->generate_dateformat($filter, $filtertypes);
                                $this->where($this->expr("STR_TO_DATE($filter, '$dateformat') BETWEEN STR_TO_DATE([], '%m/%d/%Y') AND STR_TO_DATE([], '%m/%d/%Y')", $filtervalue));
                            } else if ($filtertypes[$filter]['datatype'] == 'numeric') {
                                $this->where($this->expr("$filter between CAST([] as DECIMAL) and CAST([] as DECIMAL)", $filtervalue));
                            } else {
                                $this->where($this->expr("$filter between [] and []", $filtervalue));
                            }
                        }
                        break;
                    case 'in':
                        $this->where($filter, $filtervalue);
                        break;
                }
            }
        }

       /**
        * Loops through the array of key values and
        * uses the $this->set('') to set the column to the new value
        * @param  array $querylinks associative array with the new corresponding values
        */
       public function generate_setvaluesquery($querylinks) {
           foreach ($querylinks as $column => $val) {
               if (!empty($val)) {
                   $this->set($column, $val);
               }
           }
       }

       /**
        * Loops through the $new associative array to determine
        * if values are different at each key
        * @param  array  $old original associative array with key -> value
        * @param  array  $new updated associative array with key -> value
        */
       public function generate_setdifferencesquery(array $old, array $new) {
           foreach ($new as $column => $val) {
               if ($val != $old[$column]) {
                   $this->set($column, $val);
               }
           }
       }

        /**
         * Returns the page offset by multiplying $page and $limit subtracted by $limit
         * @param int $page page number
         * @param int $limit number of records per page
         */
        public function generate_offset($page, $limit) {
            return $page > 1 ? ($page * $limit) - $limit : 0;
        }

        /**
         * Returns the Order By string by parsing the string
         * into the format needed : column (ASC|DESC) or blank
         * @param  string $orderby e.g. columnname-ASC
         * @return string          Blank or columnname ASC
         */
        public function generate_orderby($orderby) {
            if (!empty($orderby)) {
                return str_replace('-', ' ', $orderby);
            } else {
                return '';
            }
        }
        
        /**
         * Parses the Paramterized query provided by $this->render()
         * Returns it in a Easy to read format with SQL keywords in CAPS and spaces after commas
         * @return string SQL Query
         */
        public function generate_sqlquery() {
            $sql = $this->getDebugQuery();
            $sql = str_replace(',', ', ', $sql);
            $sql = str_replace('!=', ' != ', $sql);
            $sql = str_replace('`=``', '` = `', $sql);
            
            foreach ($this->params as $param => $value) {
                $sql = str_replace($param, "'".$value."'", $sql);
            }

            foreach ($this->sqlkeywords as $keyword) {
                $sql = preg_replace('/\b'.$keyword.'\b/', strtoupper($keyword), $sql);
            }
            return $sql;
        }

        /**
         * Returns filter description for the filter
         * @param  string $key         Name of filter
         * @param  array $val          Array of Values for that filter
         * @param  array $filtertypes  Array of filters indexed by the names
         * @return string              Description of Filter based on the values, and type of filter
         */
        public static function generate_filterdescription($key, $val, $filtertypes) {
            switch ($filtertypes[$key]['querytype']) {
                case 'between':
                    $val = array_values(array_filter($val, 'strlen'));
                    if (sizeof($val) == 1) {
                        return " ".$filtertypes[$key]['label'] ." = " . $val[0];
                    } else {
                        return " ".$filtertypes[$key]['label'] . " between " . $val[0] . " and " . $val[1];
                    }
                    break;
                case 'in':
                    $values = implode(', ', $val);
                    return " ".$filtertypes[$key]['label'] ." IN ($values)";
                    break;
            }
        }

        /**
         * Returns a keyword so it can be searched
         * @param  string $keyword Keyword
         * @return string          Keyword cleaned up for Database search
         */
        public static function generate_searchkeyword($keyword) {
            $replace = array(' ', '-');
            $replacewith = array('%', '');
            return '%'.str_replace($replace, $replacewith, $keyword).'%';;
        }
    }
