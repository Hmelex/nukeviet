<?php

/**
 * NUKEVIET Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

namespace NukeViet\Cache;

use Memcached;

/**
 * Memcacheds
 *
 * @package NukeViet Cache
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2016 VINADES.,JSC. All rights reserved
 * @version 4.0
 * @access public
 */
class Memcacheds
{
    private $_Lang = 'vi';

    private $_Cache_Prefix = '';

    private $_Db;

    private $_Cache;

    /**
     * Memcacheds::__construct()
     *
     * @param mixed $Host
     * @param mixed $Port
     * @param mixed $Lang
     * @param mixed $Cache_Prefix
     */
    public function __construct($Host, $Port, $Lang, $Cache_Prefix)
    {
        $this->_Lang = $Lang;
        $this->_Cache_Prefix = $Cache_Prefix;
        $this->_Cache = new Memcached();
        $this->_Cache->addServer($Host, $Port);
    }

    /**
     * @param mixed $sys
     *
     * @return
     */
    public function delAll($sys = true)
    {
        $this->_Cache->flush();
    }

    /**
     * @param mixed $module_name
     * @param mixed $lang
     */
    public function delMod($module_name, $lang = '')
    {
        $AllKeys = $this->_Cache->getAllKeys();
        foreach ($AllKeys as $_key) {
            if (preg_match('/^' . preg_quote($module_name) . '\_/', $_key)) {
                $this->_Cache->delete($_key);
            }
        }
    }

    /**
     * @param mixed $module_name
     * @param mixed $filename
     * @param int   $ttl
     * @return
     */
    public function getItem($module_name, $filename, $ttl = 0)
    {
        // Note: $ttl not check in Memcached cache
        return $this->_Cache->get($module_name . '_' . md5($filename));
    }

    /**
     * @param mixed $module_name
     * @param mixed $filename
     * @param mixed $content
     * @param int   $ttl
     * @return
     */
    public function setItem($module_name, $filename, $content, $ttl = 0)
    {
        return $this->_Cache->set($module_name . '_' . md5($filename), $content, $ttl);
    }

    /**
     * @param resource $db
     */
    public function setDb($db)
    {
        $this->_Db = $db;
    }

    /**
     * @param mixed $sql
     * @param mixed $key
     * @param mixed $modname
     * @param mixed $lang
     * @param int   $ttl
     * @return
     */
    public function db($sql, $key, $modname, $lang = '', $ttl = 0)
    {
        $_rows = [];

        if (empty($sql)) {
            return $_rows;
        }

        if (empty($lang)) {
            $lang = $this->_Lang;
        }

        $cache_key = $modname . '_' . $lang . '_' . md5($sql . '_' . $this->_Cache_Prefix);

        if (!($_rows = $this->_Cache->get($cache_key))) {
            if (($result = $this->_Db->query($sql)) !== false) {
                $a = 0;
                while ($row = $result->fetch()) {
                    $key2 = (!empty($key) and isset($row[$key])) ? $row[$key] : $a;
                    $_rows[$key2] = $row;
                    ++$a;
                }
                $result->closeCursor();
                $this->_Cache->set($cache_key, $_rows, $ttl);
            }
        }

        return $_rows;
    }
}
