<?php
  /**
   * Index
   *
   * @package Wojo Framework
   * @author wojoscripts.com
   * @copyright 2014
   * @version $Id: index.php, v1.00 2014-10-05 10:12:05 gewa Exp $
   */
  define("_WOJO", true);
  require_once("init.php");
?>
<?php include (THEME . "/header.tpl.php");?>
<?php include($data->template);?>
<?php include (THEME . "/footer.tpl.php");?>