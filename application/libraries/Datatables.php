<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Ignited Datatables
 *
 * This is a wrapper class/library based on the native Datatables server-side implementation by Allan Jardine
 * found at http://datatables.net/examples/data_sources/server_side.html for CodeIgniter
 * @package    CodeIgniter
 * @subpackage libraries
 * @category   library
 * @version    2.x <modified version>
 * Supports PHP Version 8.4
 */
class Datatables
{
    private $ci;
    private $search_arr = '';
    private $order_by = array();
    private $column_order  = array();
    private $group_by = array();
    private $select = array();
    private $table;
    private $distinct;
    private $joins = array();
    private $columns = array();
    private $where = array();
    private $or_where = array();
    private $where_in = array();
    private $like = array();
    private $or_like = array();
    private $filter = array();
    private $add_columns = array();
    private $search = array();
    private $edit_columns  = array();
    private $unset_columns = array();

    /**
     * Copies an instance of CI
     */
    public function __construct()
    {
        $this->ci = &get_instance();
    }

     /**
     * Sets additional column variables for adding custom columns
     *
     * @param string $column
     * @param string $content
     * @param string $match_replacement
     * @return mixed
     */
    public function add_column($column, $content, $match_replacement = null)
    {
        $this->add_columns[$column] = array('content' => $content, 'replacement' => $this->explode(',', $match_replacement));
        return $this;
    }

    /**
     * Generates the FROM portion of the query
     *
     * @param string $table
     * @return mixed
     */
    public function from($table)
    {
        $this->ci->db->from($table);
        $this->table = $table;
        return $this;
    }

    /**
     * Generates the SELECT portion of the query
     *
     * @param string $columns
     * @param bool $backtick_protect
     * @return mixed
     */
    public function select($columns, $backtick_protect = true)
    {
        foreach ($this->explode(',', $columns) as $val) {
            $column                  = trim(strtolower($val));
            $variable                = strstr($column, ' as ', true) ?: $column;
            $this->columns[]         = $variable;
            $this->select[$variable] = trim($variable);
        }
        $this->ci->db->select($columns, $backtick_protect);
        return $this;
    }

    public function search_value($data)
    {
        $this->search_arr = $data;
        return $this;
    }

    /**
     * Generates the DISTINCT portion of the query
     *
     * @param string $column
     * @return mixed
     */
    public function distinct($column)
    {
        $this->distinct = $column;
        $this->ci->db->distinct($column);
        return $this;
    }

    /**
     * Generates a custom GROUP BY portion of the query
     *
     * @param string $val
     * @return mixed
     */
    public function group_by($val, $sql = false)
    {
        $this->group_by[] = $val;
        if (!$sql) {
            $this->ci->db->group_by($val);
        }
        return $this;
    }

    /**
     * Generates the JOIN portion of the query
     *
     * @param string $table
     * @param string $fk
     * @param string $type
     * @return mixed
     */
    public function join($table, $fk, $type = null)
    {
        $this->joins[] = array($table, $fk, $type);
        $this->ci->db->join($table, $fk, $type);
        return $this;
    }

    public function column_order($columns, $backtick_protect = true)
    {
        foreach ($this->explode(',', $columns) as $val) {
            $column            = trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$2', $val));
            $this->column_order[] = $column;
        }
        return $this;
    }

    public function order_by($column, $order = 'asc')
    {
        $this->order_by[] = array($column, $order);
        return $this;
    }

    /**
     * Generates the WHERE portion of the query
     *
     * @param mixed $key_condition
     * @param string $val
     * @param bool $backtick_protect
     * @return mixed
     */
    public function where($key_condition, $val = null, $sql = false, $backtick_protect = true)
    {
        $this->where[] = array($key_condition, $val, $backtick_protect);
        if (!$sql) {
            $this->ci->db->where($key_condition, $val, $backtick_protect);
        }
        return $this;
    }

    /**
     * Generates the WHERE portion of the query
     *
     * @param mixed $key_condition
     * @param string $val
     * @param bool $backtick_protect
     * @return mixed
     */
    public function or_where($key_condition, $val = null, $sql = false, $backtick_protect = true)
    {
        $this->or_where[] = array($key_condition, $val, $backtick_protect);
        if (!$sql) {
            $this->ci->db->or_where($key_condition, $val, $backtick_protect);
        }
        return $this;
    }

    /**
     * Generates the WHERE IN portion of the query
     *
     * @param mixed $key_condition
     * @param string $val
     * @param bool $backtick_protect
     * @return mixed
     */
    public function where_in($key_condition, $val = null)
    {
        $this->where_in[] = array($key_condition, $val);
        $this->ci->db->where_in($key_condition, $val);
        return $this;
    }

    /**
     * Generates the WHERE portion of the query
     *
     * @param mixed $key_condition
     * @param string $val
     * @param bool $backtick_protect
     * @return mixed
     */
    public function filter($key_condition, $val = null, $backtick_protect = true)
    {
        $this->filter[] = array($key_condition, $val, $backtick_protect);
        return $this;
    }

    /**
     * Generates a %LIKE% portion of the query
     *
     * @param mixed $key_condition
     * @param string $val
     * @param bool $backtick_protect
     * @return mixed
     */
    public function like($key_condition, $val = null, $side = 'both')
    {
        $this->like[] = array($key_condition, $val, $side);
        $this->ci->db->like($key_condition, $val, $side);
        return $this;
    }

    /**
     * Generates the OR %LIKE% portion of the query
     *
     * @param mixed $key_condition
     * @param string $val
     * @param bool $backtick_protect
     * @return mixed
     */
    public function or_like($key_condition, $val = null, $side = 'both')
    {
        $this->or_like[] = array($key_condition, $val, $side);
        $this->ci->db->or_like($key_condition, $val, $side);
        return $this;
    }

    public function or_like_string($array_string, $match)
    {
        $array_like = explode(',', $array_string);
        foreach ($array_like as $key => $string_value) {
            if ($key == 0) {
                $this->ci->db->like($string_value, $match);
            } else {
                $this->ci->db->or_like($string_value, $match);
            }
        }
    }

    public function or_group_start()
    {
        $this->ci->db->or_group_start();
        return $this;
    }

    public function group_start()
    {
        $this->ci->db->group_start();
        return $this;
    }

    public function group_end()
    {
        $this->ci->db->group_end();
        return $this;
    }

    /**
     * Sets additional column variables for editing columns
     *
     * @param string $column
     * @param string $content
     * @param string $match_replacement
     * @return mixed
     */
    public function edit_column($column, $content, $match_replacement)
    {
        $this->edit_columns[$column][] = array('content' => $content, 'replacement' => $this->explode(',', $match_replacement));
        return $this;
    }

    public function set_database($db_name)
    {
        $db_data      = $this->ci->load->database($db_name, true);
        $this->ci->db = $db_data;
    }

    /**
     * Unset column
     *
     * @param string $column
     * @return mixed
     */
    public function unset_column($column)
    {
        $column              = explode(',', $column);
        $this->unset_columns = array_merge($this->unset_columns, $column);
        return $this;
    }

    /**
     * Builds all the necessary query segments and performs the main query based on results set from chained statements
     *
     * @param string $output
     * @param string $charset
     * @return string
     */
    public function generate($output = 'json')
    {
        $this->get_ordering();
        $this->get_filtering();
        $total                  = $this->ci->db->count_all_results('', false);
        $sOutput['query_count'] = $this->ci->db->last_query();
        $iTotal                 = $iFilteredTotal = $total;
        $aaData                 = array();
        $this->get_paging();
        $rResult = $this->ci->db->get();
        $sOutput = array(
            'draw'            => intval($this->ci->input->post('draw')),
            'recordsTotal'    => $iTotal,
            'recordsFiltered' => $iFilteredTotal,
            'data'            => $rResult->result_array(),
        );
        return json_encode($sOutput);
    }

    /**
     * Generates the LIMIT portion of the query
     *
     * @return mixed
     */
    private function get_paging()
    {
        $iStart  = $this->ci->input->post('start');
        $iLength = $this->ci->input->post('length');
        if ($iLength != '' && $iLength != '-1') {
            $this->ci->db->limit($iLength, ($iStart) ? $iStart : 0);
        }
    }

    /**
     * Generates the ORDER BY portion of the query
     *
     * @return mixed
     */
    private function get_ordering()
    {
        $Data = $this->ci->input->post('columns');
        if ($this->ci->input->post('order')) {
            foreach ($this->ci->input->post('order') as $key) {
                $this->ci->db->order_by($this->column_order[$key['column']], $key['dir']);
            }
        }

        foreach ($this->order_by as $val) {
            $this->ci->db->order_by($val[0], $val[1]);
        }
    }

    /**
     * Generates a %LIKE% portion of the query
     *
     * @return mixed
     */
    private function get_filtering()
    {
        $sWhere    = '';
        $searchCol = array();
        $mColArray = $this->ci->input->post('columns');
        $search    = $this->ci->input->post('search');
        $sSearch   = $this->ci->db->escape_like_str(trim($search['value']));
        $columns   = array_values(array_diff($this->columns, $this->unset_columns));

        if ($sSearch != '' && $this->search_arr != '') {
            if ($this->search_arr == '*') {
                $field            = $this->ci->db->list_fields($this->table);
                $this->search_arr = implode(',', $field);
            }

            $column = explode(',', $this->search_arr);
            foreach ($column as $key => $col) {
                $col         = strtolower($col);
                $col         = strstr($col, ' as ', true) ?: $col;
                $searchCol[] = $col;
            }

            if ($sSearch != "") {
                for ($i = 0; $i < count($searchCol); $i++) {
                    if ($i == 0) {
                        $this->ci->db->group_start();
                        $this->ci->db->like($searchCol[$i], $sSearch);
                    } else {
                        $this->ci->db->or_like($searchCol[$i], $sSearch);
                    }

                    if (count($searchCol) - 1 == $i)  // last loop
                    {
                        $this->ci->db->group_end();
                    }
                    // close bracket
                }
            }
        } else {
            if ($sSearch != '') {
                for ($i = 0; $i < count($mColArray); $i++) {
                    if ($mColArray[$i]['searchable'] == 'true' && !array_key_exists($mColArray[$i]['data'], $this->add_columns)) {
                        $sWhere .= $this->select[$this->columns[$i]] . " LIKE '%" . $sSearch . "%' OR ";
                    }
                }
            }

            $sWhere = substr_replace($sWhere, '', -3);
            if ($sWhere != '')
                $this->ci->db->where('(' . $sWhere . ')');
        }
        // TODO : sRangeSeparator
        foreach ($this->filter as $val) {
            $this->ci->db->where($val[0], $val[1], $val[2]);
        }
    }

    /**
     * Compiles the select statement based on the other functions called and runs the query
     *
     * @return mixed
     */
    private function get_display_result()
    {
        return $this->ci->db->get();
    }

    /**
     * Get result count
     *
     * @return integer
     */
    private function get_total_results($filtering = false)
    {
        if ($filtering) {
            $this->get_filtering();
        }

        foreach ($this->joins as $val) {
            $this->ci->db->join($val[0], $val[1], $val[2]);
        }

        foreach ($this->where as $val) {
            $this->ci->db->where($val[0], $val[1], $val[2]);
        }

        foreach ($this->or_where as $val) {
            $this->ci->db->or_where($val[0], $val[1], $val[2]);
        }

        foreach ($this->where_in as $val) {
            $this->ci->db->where_in($val[0], $val[1]);
        }

        foreach ($this->group_by as $val) {
            $this->ci->db->group_by($val);
        }

        foreach ($this->like as $val) {
            $this->ci->db->like($val[0], $val[1], $val[2]);
        }

        foreach ($this->or_like as $val) {
            $this->ci->db->or_like($val[0], $val[1], $val[2]);
        }

        if (strlen($this->distinct) > 0) {
            $this->ci->db->distinct($this->distinct);
            $this->ci->db->select($this->columns);
        }
        $query = $this->ci->db->get($this->table, null, null, false);
        return $query->num_rows();
    }

    /**
     * Runs callback functions and makes replacements
     *
     * @param mixed $custom_val
     * @param mixed $row_data
     * @return string $custom_val['content']
     */
    private function exec_replace($custom_val, $row_data)
    {
        $replace_string = '';

        // Go through our array backwards, else $1 (foo) will replace $11, $12 etc with foo1, foo2 etc
        $custom_val['replacement'] = array_reverse($custom_val['replacement'], true);

        if (isset($custom_val['replacement']) && is_array($custom_val['replacement'])) {
            // Added this line because when the replacement has over 10 elements replaced the variable "$1" first by the "$10"
            $custom_val['replacement'] = array_reverse($custom_val['replacement'], true);
            foreach ($custom_val['replacement'] as $key => $val) {
                $sval = preg_replace("/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim($val));

                if (preg_match('/(\w+::\w+|\w+)\((.*)\)/i', $val, $matches) && is_callable($matches[1])) {
                    $func = $matches[1];
                    $args = preg_split("/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|" . "[\s,]*'([^']+)'[\s,]*|" . "[,]+/", $matches[2], 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

                    foreach ($args as $args_key => $args_val) {
                        $args_val        = preg_replace("/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim($args_val));
                        $args[$args_key] = (in_array($args_val, $this->columns)) ? ($row_data[($this->check_cType()) ? $args_val : array_search($args_val, $this->columns)]) : $args_val;
                    }

                    $replace_string = call_user_func_array($func, $args);
                } elseif (in_array($sval, $this->columns)) {
                    $replace_string = $row_data[($this->check_cType()) ? $sval : array_search($sval, $this->columns)];
                } else {
                    $replace_string = $sval;
                }

                $custom_val['content'] = str_ireplace('$' . ($key + 1), $replace_string, $custom_val['content']);
            }
        }

        return $custom_val['content'];
    }

    /**
     * Check column type -numeric or column name
     *
     * @return bool
     */
    private function check_cType()
    {
        $column = $this->ci->input->post('columns');
        if (is_numeric($column[0]['data'])) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Return the difference of open and close characters
     *
     * @param string $str
     * @param string $open
     * @param string $close
     * @return string $retval
     */
    private function balanceChars($str, $open, $close)
    {
        $openCount  = substr_count($str, $open);
        $closeCount = substr_count($str, $close);
        $retval     = $openCount - $closeCount;
        return $retval;
    }

    /**
     * Explode, but ignore delimiter until closing characters are found
     *
     * @param string $delimiter
     * @param string $str
     * @param string $open
     * @param string $close
     * @return mixed $retval
     */
    private function explode($delimiter, $str, $open = '(', $close = ')')
    {
        $retval  = array();
        $hold    = array();
        $balance = 0;
        $parts   = explode($delimiter, $str);

        foreach ($parts as $part) {
            $hold[]   = $part;
            $balance += $this->balanceChars($part, $open, $close);

            if ($balance < 1) {
                $retval[] = implode($delimiter, $hold);
                $hold     = array();
                $balance  = 0;
            }
        }

        if (count($hold) > 0) {
            $retval[] = implode($delimiter, $hold);
        }

        return $retval;
    }

    /**
     * returns the sql statement of the last query run
     * @return type
     */
    public function last_query()
    {
        return $this->ci->db->last_query();
    }
}