<?

if (!DEFINED('BASEPATH'))
    exit('No direct script access allowed');

class KI_DB_ACTIVE_RECORD extends KI_DB_DRIVER
{

    public $ar_select = array();
    public $ar_distinct = FALSE;
    public $ar_from = array();
    public $ar_join = array();
    public $ar_where = array();
    public $ar_like = array();
    public $ar_groupby = array();
    public $ar_having = array();
    public $ar_limit = FALSE;
    public $ar_offset = FALSE;
    public $ar_order = FALSE;
    public $ar_orderby = array();
    public $ar_set = array();
    public $ar_wherein = array();
    public $ar_aliased_tables = array();
    public $ar_store_array = array();
    // Active Record Caching publiciables
    public $ar_caching = FALSE;
    public $ar_cache_exists = array();
    public $ar_cache_select = array();
    public $ar_cache_from = array();
    public $ar_cache_join = array();
    public $ar_cache_where = array();
    public $ar_cache_like = array();
    public $ar_cache_groupby = array();
    public $ar_cache_having = array();
    public $ar_cache_orderby = array();
    public $ar_cache_set = array();


    function select($select='*', $escape=NULL)
    {
	if (is_bool($escape))
	{
	    $this->_protect_identifiers = $escape;
	}

	if (is_string($select))
	{
	    $select = explode(',', $select);
	}

	foreach ($select as $val)
	{
	    $val = trim($val);

	    if ($val != '')
	    {
		$this->ar_select[] = $val;

		if ($this->ar_caching === TRUE)
		{
		    $this->ar_cache_select[] = $val;
		    $this->ar_cache_exists[] = 'select';
		}
	    }
	}

	return $this;
    }

    function select_max($select='', $alias='')
    {
	return $this->_max_min_avg_sum($select, $alias, 'MAX');
    }

    function select_min($select='', $alias='')
    {
	return $this->_max_min_avg_sum($select, $alias, 'MIN');
    }

    function select_avg($select='', $alias='')
    {
	return $this->_max_min_avg_sum($select, $alias, 'AVG');
    }

    function select_sum($select='', $alias='')
    {
	return $this->_max_min_avg_sum($select, $alias, 'SUM');
    }

    function _max_min_avg_sum($select='', $alias='', $type='MAX')
    {
	if (!is_string($select) OR $select == '')
	{
	    $this->display_error('db_invalid_query');
	}

	$type = strtoupper($type);

	if (!in_array($type, array('MAX', 'MIN', 'AVG', 'SUM')))
	{
	    show_error('Invalid function type: ' . $type);
	}

	if ($alias == '')
	{
	    $alias = $this->_create_alias_from_table(trim($select));
	}

	$sql = $type . '(' . $this->_protect_identifiers(trim($select)) . ') AS ' . $alias;
	$this->ar_select[] = $sql;

	if ($this->ar_caching === TRUE)
	{
	    $this->ar_cache_select[] = $sql;
	    $this->ar_cache_exists[] = 'select';
	}

	return $this;
    }

    function _create_alias_from_table($item)
    {
	if (strpos($item, '.') !== FALSE)
	{
	    return end(explode('.', $item));
	}

	return $item;
    }

    function distinct($val=TRUE)
    {
	$this->ar_distinct = (is_bool($val)) ? $val : TRUE;
	return $this;
    }

    function from($from)
    {
	foreach ((array) $from as $val)
	{
	    if (strpos($val, ',') !== FALSE)
	    {
		foreach (explode(',', $val) as $v)
		{
		    $v = trim($v);
		    $this->_track_aliases($v);
		    $this->ar_from[] = $this->_protect_identifiers($v, TRUE, NULL, FALSE);

		    if ($this->ar_caching === TRUE)
		    {
			$this->ar_cache_from[] = $this->_protect_identifiers($v, TRUE, NULL, FALSE);
			$this->ar_cache_exists[] = 'from';
		    }
		}
	    }
	    else
	    {
		$val = trim($val);
		$this->_track_aliases($val);
		$this->ar_from[] = $this->_protect_identifiers($val, TRUE, NULL, FALSE);

		if ($this->ar_caching === TRUE)
		{
		    $this->ar_cache_from[] = $this->_protect_identifiers($val, TRUE, NULL, FALSE);
		    $this->ar_cache_exists[] = 'from';
		}
	    }
	}

	return $this;
    }

    function join($table, $cond, $type='')
    {
	if ($type != '')
	{
	    $type = strtoupper(trim($type));

	    if (!in_array($type, array('LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER')))
	    {
		$type = '';
	    }
	    else
	    {
		$type .= ' ';
	    }
	}

	$this->_track_aliases($table);

	if (preg_match('/([\w\.]+)([\W\s]+)(.+)/', $cond, $match))
	{
	    $match[1] = $this->_protect_identifiers($match[1]);
	    $match[3] = $this->_protect_identifiers($match[3]);
	    $cond = $match[1] . $match[2] . $match[3];
	}

	$join = $type . 'JOIN ' . $this->_protect_identifiers($table, TRUE, NULL, FALSE) . ' ON ' . $cond;
	$this->ar_join[] = $join;

	if ($this->ar_caching === TRUE)
	{
	    $this->ar_cache_join[] = $join;
	    $this->ar_cache_exists[] = 'join';
	}

	return $this;
    }

    function where($key, $value=NULL, $escape=TRUE)
    {
	return $this->_where($key, $value, 'AND ', $escape);
    }

    function or_where($key, $value=NULL, $escape=TRUE)
    {
	return $this->_where($key, $value, 'OR ', $escape);
    }

    function orwhere($key, $value=NULL, $escape=TRUE)
    {
	return $this->or_where($key, $value, $escape);
    }

    function _where($key, $value=NULL, $type='AND ', $escape=NULL)
    {
	if (!is_array($key))
	    $key = array($key => $value);
	if (!is_bool($escape))
	    $escape = $this->_protect_identifiers;

	foreach ($key as $k => $v)
	{
	    $prefix = (count($this->ar_where) == 0 AND count($this->ar_cache_where) == 0) ? '' : $type;

	    if (is_null($v) && !$this->_has_operator($k))
	    {
		$k .= ' IS NULL';
	    }

	    if (!is_null($v))
	    {
		if ($escape === TRUE)
		{
		    $k = $this->_protect_identifiers($k, FALSE, $escape);
		    $v = ' ' . $this->escape($v);
		}

		if (!$this->_has_operator($k))
		{
		    $k .= ' =';
		}
	    }
	    else
	    {
		$k = $this->_protect_identifiers($k, FALSE, $escape);
	    }

	    $this->ar_where[] = $prefix . $k . $v;

	    if ($this->ar_caching === TRUE)
	    {
		$this->ar_cache_where[] = $prefix . $k . $v;
		$this->ar_cache_exists[] = 'where';
	    }
	}

	return $this;
    }

    function where_in($key=NULL, $values=NULL)
    {
	return $this->_where_in($key, $values);
    }

    function or_where_in($key=NULL, $values=NULL)
    {
	return $this->_where_in($key, $values, FALSE, 'OR ');
    }

    function where_not_in($key=NULL, $values=NULL)
    {
	return $this->_where_in($key, $values, TRUE);
    }

    function or_where_not_in($key=NULL, $values=NULL)
    {
	return $this->_where_in($key, $values, TRUE, 'OR ');
    }

    function _where_in($key=NULL, $values=NULL, $not=FALSE, $type='AND ')
    {
	if ($key === NULL OR $values === NULL)
	    return "";
	if (!is_array($values))
	    $values = array($values);

	$not = ($not) ? ' NOT' : '';

	foreach ($values as $value)
	{
	    $this->ar_wherein[] = $this->escape($value);
	}

	$prefix = (count($this->ar_where) == 0) ? '' : $type;
	$where_in = $prefix . $this->_protect_identifiers($key) . $not . " IN (" . implode(", ", $this->ar_wherein) . ") ";
	$this->ar_where[] = $where_in;

	if ($this->ar_caching === TRUE)
	{
	    $this->ar_cache_where[] = $where_in;
	    $this->ar_cache_exists[] = 'where';
	}

	$this->ar_wherein = array();
	return $this;
    }

    function like($field, $match='', $side='both')
    {
	return $this->_like($field, $match, 'AND ', $side);
    }

    function not_like($field, $match='', $side='both')
    {
	return $this->_like($field, $match, 'AND ', $side, 'NOT');
    }

    function or_like($field, $match='', $side='both')
    {
	return $this->_like($field, $match, 'OR ', $side);
    }

    function or_not_like($field, $match='', $side='both')
    {
	return $this->_like($field, $match, 'OR ', $side, 'NOT');
    }

    function orlike($field, $match='', $side='both')
    {
	return $this->or_like($field, $match, $side);
    }

    function _like($field, $match='', $type='AND ', $side='both', $not='')
    {
	if (!is_array($field))
	{
	    $field = array($field => $match);
	}

	foreach ($field as $k => $v)
	{
	    $k = $this->_protect_identifiers($k);
	    $prefix = (count($this->ar_like) == 0) ? '' : $type;
	    $v = $this->escape_str($v);

	    if ($side == 'before')
	    {
		$like_statement = $prefix . " $k $not LIKE '%{$v}'";
	    }
	    else if ($side == 'after')
	    {
		$like_statement = $prefix . " $k $not LIKE '{$v}%'";
	    }
	    else
	    {
		$like_statement = $prefix . " $k $not LIKE '%{$v}%'";
	    }

	    $this->ar_like[] = $like_statement;

	    if ($this->ar_caching === TRUE)
	    {
		$this->ar_cache_like[] = $like_statement;
		$this->ar_cache_exists[] = 'like';
	    }
	}

	return $this;
    }

    function group_by($by)
    {
	if (is_string($by))
	{
	    $by = explode(',', $by);
	}

	foreach ($by as $val)
	{
	    $val = trim($val);

	    if ($val != '')
	    {
		$this->ar_groupby[] = $this->_protect_identifiers($val);

		if ($this->ar_caching === TRUE)
		{
		    $this->ar_cache_groupby[] = $this->_protect_identifiers($val);
		    $this->ar_cache_exists[] = 'groupby';
		}
	    }
	}

	return $this;
    }

    function groupby($by)
    {
	return $this->group_by($by);
    }

    function having($key, $value='', $escape=TRUE)
    {
	return $this->_having($key, $value, 'AND ', $escape);
    }

    function orhaving($key, $value='', $escape=TRUE)
    {
	return $this->or_having($key, $value, $escape);
    }

    function or_having($key, $value='', $escape=TRUE)
    {
	return $this->_having($key, $value, 'OR ', $escape);
    }

    function _having($key, $value='', $type='AND ', $escape=TRUE)
    {
	if (!is_array($key))
	    $key = array($key => $value);

	foreach ($key as $k => $v)
	{
	    $prefix = (count($this->ar_having) == 0) ? '' : $type;

	    if ($escape === TRUE)
	    {
		$k = $this->_protect_identifiers($k);
	    }

	    if (!$this->_has_operator($k))
	    {
		$k .= ' = ';
	    }

	    if ($v != '')
	    {
		$v = ' ' . $this->escape_str($v);
	    }

	    $this->ar_having[] = $prefix . $k . $v;

	    if ($this->ar_caching === TRUE)
	    {
		$this->ar_cache_having[] = $prefix . $k . $v;
		$this->ar_cache_exists[] = 'having';
	    }
	}

	return $this;
    }

    function order_by($orderby, $direction='')
    {
	if (strtolower($direction) == 'random')
	{
	    $orderby = '';
	    $direction = $this->_random_keyword;
	}
	else if (trim($direction) != '')
	{
	    $direction = (in_array(strtoupper(trim($direction)), array('ASC', 'DESC'), TRUE)) ? ' ' . $direction : ' ASC';
	}


	if (strpos($orderby, ',') !== FALSE)
	{
	    $temp = array();

	    foreach (explode(',', $orderby) as $part)
	    {
		$part = trim($part);

		if (!in_array($part, $this->ar_aliased_tables))
		{
		    $part = $this->_protect_identifiers(trim($part));
		}

		$temp[] = $part;
	    }

	    $orderby = implode(', ', $temp);
	}
	else if ($direction != $this->_random_keyword)
	{
	    $orderby = $this->_protect_identifiers($orderby);
	}

	$orderby_statement = $orderby . $direction;
	$this->ar_orderby[] = $orderby_statement;

	if ($this->ar_caching === TRUE)
	{
	    $this->ar_cache_orderby[] = $orderby_statement;
	    $this->ar_cache_exists[] = 'orderby';
	}

	return $this;
    }

    function orderby($orderby, $direction='')
    {
	return $this->order_by($orderby, $direction);
    }

    function limit($value, $offset='')
    {
	$this->ar_limit = $value;

	if ($offset != '')
	{
	    $this->ar_offset = $offset;
	}

	return $this;
    }

    function offset($offset)
    {
	$this->ar_offset = $offset;
	return $this;
    }

    function set($key, $value='', $escape=TRUE)
    {
	$key = $this->_object_to_array($key);

	if (!is_array($key))
	{
	    $key = array($key => $value);
	}

	foreach ($key as $k => $v)
	{
	    if ($escape === FALSE)
	    {
		$this->ar_set[$this->_protect_identifiers($k)] = $v;
	    }
	    else
	    {
		$this->ar_set[$this->_protect_identifiers($k)] = $this->escape($v);
	    }
	}

	return $this;
    }

    function get($table='', $limit=null, $offset=null)
    {
	if ($table != '')
	{
	    $this->_track_aliases($table);
	    $this->from($table);
	}

	if (!is_null($limit))
	{
	    $this->limit($limit, $offset);
	}

	$sql = $this->_compile_select();
	$result = $this->query($sql);
	$this->_reset_select();
	return $result;
    }

    function count_all_results($table='')
    {
	if ($table != '')
	{
	    $this->_track_aliases($table);
	    $this->from($table);
	}

	$sql = $this->_compile_select($this->_count_string . $this->_protect_identifiers('numrows'));
	$query = $this->query($sql);
	$this->_reset_select();

	if ($query->num_rows() == 0)
	{
	    return '0';
	}

	$row = $query->row();
	return $row->numrows;
    }

    function get_where($table='', $where=null, $limit=null, $offset=null)
    {
	if ($table != '')
	{
	    $this->from($table);
	}

	if (!is_null($where))
	{
	    $this->where($where);
	}

	if (!is_null($limit))
	{
	    $this->limit($limit, $offset);
	}

	$sql = $this->_compile_select();
	$result = $this->query($sql);
	$this->_reset_select();
	return $result;
    }

    function getwhere($table='', $where=null, $limit=null, $offset=null)
    {
	return $this->get_where($table, $where, $limit, $offset);
    }

    function insert($table='', $set=NULL)
    {
	if (!is_null($set))
	{
	    $this->set($set);
	}

	if (count($this->ar_set) == 0)
	{
	    if ($this->db_debug)
	    {
		return $this->display_error('db_must_use_set');
	    }

	    return FALSE;
	}

	if ($table == '')
	{
	    if (!isset($this->ar_from[0]))
	    {
		if ($this->db_debug)
		{
		    return $this->display_error('db_must_set_table');
		}

		return FALSE;
	    }

	    $table = $this->ar_from[0];
	}

	$sql = $this->_insert($this->_protect_identifiers($table, TRUE, NULL, FALSE), array_keys($this->ar_set), array_values($this->ar_set));
	$this->_reset_write();
	return $this->query($sql);
    }

    function insert_ignore($table='', $set=NULL)
    {
	if (!is_null($set))
	{
	    $this->set($set);
	}

	if (count($this->ar_set) == 0)
	{
	    if ($this->db_debug)
	    {
		return $this->display_error('db_must_use_set');
	    }

	    return FALSE;
	}

	if ($table == '')
	{
	    if (!isset($this->ar_from[0]))
	    {
		if ($this->db_debug)
		{
		    return $this->display_error('db_must_set_table');
		}

		return FALSE;
	    }

	    $table = $this->ar_from[0];
	}

	$sql = $this->_insert_ignore($this->_protect_identifiers($table, TRUE, NULL, FALSE), array_keys($this->ar_set), array_values($this->ar_set));
	$this->_reset_write();
	return $this->query($sql);
    }

    function update($table='', $set=NULL, $where=NULL, $limit=NULL)
    {
	$this->_merge_cache();

	if (!is_null($set))
	{
	    $this->set($set);
	}

	if (count($this->ar_set) == 0)
	{
	    if ($this->db_debug)
	    {
		return $this->display_error('db_must_use_set');
	    }

	    return FALSE;
	}

	if ($table == '')
	{
	    if (!isset($this->ar_from[0]))
	    {
		if ($this->db_debug)
		{
		    return $this->display_error('db_must_set_table');
		}

		return FALSE;
	    }

	    $table = $this->ar_from[0];
	}

	if ($where != NULL)
	{
	    $this->where($where);
	}

	if ($limit != NULL)
	{
	    $this->limit($limit);
	}

	$sql = $this->_update($this->_protect_identifiers($table, TRUE, NULL, FALSE), $this->ar_set, $this->ar_where, $this->ar_orderby, $this->ar_limit);
	$this->_reset_write();
	return $this->query($sql);
    }

    function empty_table($table='')
    {
	if ($table == '')
	{
	    if (!isset($this->ar_from[0]))
	    {
		if ($this->db_debug)
		{
		    return $this->display_error('db_must_set_table');
		}

		return FALSE;
	    }

	    $table = $this->ar_from[0];
	}
	else
	{
	    $table = $this->_protect_identifiers($table, TRUE, NULL, FALSE);
	}

	$sql = $this->_delete($table);
	$this->_reset_write();
	return $this->query($sql);
    }

    function truncate($table='')
    {
	if ($table == '')
	{
	    if (!isset($this->ar_from[0]))
	    {
		if ($this->db_debug)
		{
		    return $this->display_error('db_must_set_table');
		}

		return FALSE;
	    }

	    $table = $this->ar_from[0];
	}
	else
	{
	    $table = $this->_protect_identifiers($table, TRUE, NULL, FALSE);
	}

	$sql = $this->_truncate($table);
	$this->_reset_write();
	return $this->query($sql);
    }

    function delete($table='', $where='', $limit=NULL, $reset_data=TRUE)
    {
	$this->_merge_cache();

	if ($table == '')
	{
	    if (!isset($this->ar_from[0]))
	    {
		if ($this->db_debug)
		{
		    return $this->display_error('db_must_set_table');
		}

		return FALSE;
	    }

	    $table = $this->ar_from[0];
	}
	else if (is_array($table))
	{
	    foreach ($table as $single_table)
	    {
		$this->delete($single_table, $where, $limit, FALSE);
	    }

	    $this->_reset_write();
	    return "";
	}
	else
	{
	    $table = $this->_protect_identifiers($table, TRUE, NULL, FALSE);
	}

	if ($where != '')
	{
	    $this->where($where);
	}

	if ($limit != NULL)
	{
	    $this->limit($limit);
	}

	if (count($this->ar_where) == 0 && count($this->ar_wherein) == 0 && count($this->ar_like))
	{
	    if ($this->db_debug)
	    {
		return $this->display_error('db_del_must_use_where');
	    }

	    return FALSE;
	}

	$sql = $this->_delete($table, $this->ar_where, $this->ar_like, $this->ar_limit);

	if ($reset_data)
	{
	    $this->_reset_write();
	}

	return $this->query($sql);
    }

    function delete_ignore($table='', $where='', $limit=NULL, $reset_data=TRUE)
    {
	$this->_merge_cache();

	if ($table == '')
	{
	    if (!isset($this->ar_from[0]))
	    {
		if ($this->db_debug)
		{
		    return $this->display_error('db_must_set_table');
		}

		return FALSE;
	    }

	    $table = $this->ar_from[0];
	}
	else if (is_array($table))
	{
	    foreach ($table as $single_table)
	    {
		$this->delete_ignore($single_table, $where, $limit, FALSE);
	    }

	    $this->_reset_write();
	    return "";
	}
	else
	{
	    $table = $this->_protect_identifiers($table, TRUE, NULL, FALSE);
	}

	if ($where != '')
	{
	    $this->where($where);
	}

	if ($limit != NULL)
	{
	    $this->limit($limit);
	}

	if (count($this->ar_where) == 0 && count($this->ar_wherein) == 0 && count($this->ar_like))
	{
	    if ($this->db_debug)
	    {
		return $this->display_error('db_del_must_use_where');
	    }

	    return FALSE;
	}

	$sql = $this->_delete_ignore($table, $this->ar_where, $this->ar_like, $this->ar_limit);

	if ($reset_data)
	{
	    $this->_reset_write();
	}

	return $this->query($sql);
    }

    function dbprefix($table='')
    {
	if ($table == '')
	{
	    $this->display_error('db_table_name_required');
	}

	return $this->dbprefix . $table;
    }

    function _track_aliases($table)
    {
	if (is_array($table))
	{
	    foreach ($table as $t)
	    {
		$this->_track_aliases($t);
	    }

	    return "";
	}

	if (strpos($table, ',') !== FALSE)
	{
	    return $this->_track_aliases(explode(',', $table));
	}

	if (strpos($table, " ") !== FALSE)
	{
	    $table = preg_replace('/ AS /i', ' ', $table);
	    $table = trim(strrchr($table, " "));

	    if (!in_array($table, $this->ar_aliased_tables))
	    {
		$this->ar_aliased_tables[] = $table;
	    }
	}

	return "";
    }

    function _compile_select($select_override=FALSE)
    {
	$this->_merge_cache();

	if ($select_override !== FALSE)
	{
	    $sql = $select_override;
	}
	else
	{
	    $sql = (!$this->ar_distinct) ? 'SELECT ' : 'SELECT DISTINCT ';

	    if (count($this->ar_select) == 0)
	    {
		$sql .= '*';
	    }
	    else
	    {
		foreach ($this->ar_select as $key => $val)
		{
		    $this->ar_select[$key] = $this->_protect_identifiers($val);
		}

		$sql .= implode(', ', $this->ar_select);
	    }
	}

	if (count($this->ar_from) > 0)
	{
	    $sql .= "\nFROM ";
	    $sql .= $this->_from_tables($this->ar_from);
	}

	if (count($this->ar_join) > 0)
	{
	    $sql .= "\n";
	    $sql .= implode("\n", $this->ar_join);
	}

	if (count($this->ar_where) > 0 OR count($this->ar_like) > 0)
	{
	    $sql .= "\n";
	    $sql .= "WHERE ";
	}

	$sql .= implode("\n", $this->ar_where);

	if (count($this->ar_like) > 0)
	{
	    if (count($this->ar_where) > 0)
	    {
		$sql .= "\nAND ";
	    }

	    $sql .= implode("\n", $this->ar_like);
	}

	if (count($this->ar_groupby) > 0)
	{
	    $sql .= "\nGROUP BY ";
	    $sql .= implode(', ', $this->ar_groupby);
	}

	if (count($this->ar_having) > 0)
	{
	    $sql .= "\nHAVING ";
	    $sql .= implode("\n", $this->ar_having);
	}

	if (count($this->ar_orderby) > 0)
	{
	    $sql .= "\nORDER BY ";
	    $sql .= implode(', ', $this->ar_orderby);

	    if ($this->ar_order !== FALSE)
	    {
		$sql .= ( $this->ar_order == 'desc') ? ' DESC' : ' ASC';
	    }
	}

	if (is_numeric($this->ar_limit))
	{
	    $sql .= "\n";
	    $sql = $this->_limit($sql, $this->ar_limit, $this->ar_offset);
	}

	return $sql;
    }

    function _object_to_array($object)
    {
	if (!is_object($object))
	    return $object;

	$array = array();

	foreach (get_object_vars($object) as $key => $val)
	{
	    if (!is_object($val) && !is_array($val) && $key != '_parent_name' && $key != '_ki_scaffolding' && $key != '_ki_scaff_table')
	    {
		$array[$key] = $val;
	    }
	}

	return $array;
    }

    function start_cache()
    {
	$this->ar_caching = TRUE;
    }

    function stop_cache()
    {
	$this->ar_caching = FALSE;
    }

    function flush_cache()
    {
	$this->_reset_run(
		array(
		    'ar_cache_select' => array(),
		    'ar_cache_from' => array(),
		    'ar_cache_join' => array(),
		    'ar_cache_where' => array(),
		    'ar_cache_like' => array(),
		    'ar_cache_groupby' => array(),
		    'ar_cache_having' => array(),
		    'ar_cache_orderby' => array(),
		    'ar_cache_set' => array(),
		    'ar_cache_exists' => array()
		)
	);
    }

    function _merge_cache()
    {
	if (count($this->ar_cache_exists) == 0)
	    return;

	foreach ($this->ar_cache_exists as $val)
	{
	    $ar_variable = 'ar_' . $val;
	    $ar_cache_var = 'ar_cache_' . $val;

	    if (count($this->$ar_cache_var) == 0)
		continue;

	    $this->$ar_variable = array_unique(array_merge($this->$ar_cache_var, $this->$ar_variable));
	}

	if ($this->_protect_identifiers === TRUE AND count($this->ar_cache_from) > 0)
	{
	    $this->_track_aliases($this->ar_from);
	}
    }

    function _reset_run($ar_reset_items)
    {
	foreach ($ar_reset_items as $item => $default_value)
	{
	    if (!in_array($item, $this->ar_store_array))
	    {
		$this->$item = $default_value;
	    }
	}
    }

    function _reset_select()
    {
	$ar_reset_items = array(
	    'ar_select' => array(),
	    'ar_from' => array(),
	    'ar_join' => array(),
	    'ar_where' => array(),
	    'ar_like' => array(),
	    'ar_groupby' => array(),
	    'ar_having' => array(),
	    'ar_orderby' => array(),
	    'ar_wherein' => array(),
	    'ar_aliased_tables' => array(),
	    'ar_distinct' => FALSE,
	    'ar_limit' => FALSE,
	    'ar_offset' => FALSE,
	    'ar_order' => FALSE,
	);

	$this->_reset_run($ar_reset_items);
    }

    function _reset_write()
    {
	$ar_reset_items = array(
	    'ar_set' => array(),
	    'ar_from' => array(),
	    'ar_where' => array(),
	    'ar_like' => array(),
	    'ar_orderby' => array(),
	    'ar_limit' => FALSE,
	    'ar_order' => FALSE
	);

	$this->_reset_run($ar_reset_items);
    }

}

?>