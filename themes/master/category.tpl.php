<?php
  /**
   * Body Type
   *
   * @package Wojo Framework
   * @author wojoscripts.com
   * @copyright 2015
   * @version $Id: category.tpl.php, v1.00 2015-08-05 10:12:05 gewa Exp $
   */
  if (!defined("_WOJO"))
      die('Direct access to this location is not allowed.');
	  
  $dataset = $items->renderByCategory($data->result->id);
?>
<div class="wojo-grid">
  <div class="wojo primary segment">
    <div class="wojo huge header"> <img src="<?php echo UPLOADURL . 'catico/' . $data->result->slug . '.png';?>" class="wojo normal image">
      <div class="content"> <?php echo $data->result->name?>
        <p class="subheader"><?php echo Lang::$word->HOME_SUB8P;?></p>
      </div>
    </div>
  </div>
  <!--/* Small Search Start */-->
  <?php include("small_search.tpl.php");?>
  <!--/* Small Search End */-->
  <?php if(!$dataset):?>
  <?php echo Message::msgSingleInfo(Lang::$word->NOLISTFOUND);?>
  <?php else:?>
  <div class="columns gutters">
    <?php foreach($dataset as $row):?>
    <div class="screen-33 tablet-50 phone-100">
      <div class="wojo tertiary segment">
        <?php if($row->sold):?>
        <span class="wojo negative right ribbon label"><?php echo strtoupper(Lang::$word->SOLD);?></span>
        <?php endif;?>
        <div class="header"><a href="<?php echo Url::doUrl(URL_ITEM, $row->idx . '/' . $row->slug);?>" class="white"><?php echo $row->year . ' ' . $row->nice_title;?></a>
          <p>
            <?php if($row->price_sale <> 0):?>
            <span class="wojo strike negative label"><?php echo Utility::formatMoney($row->price, true);?></span> <span class="wojo positive label"><?php echo Utility::formatMoney($row->price_sale, true);?></span>
            <?php else:?>
            <span class="wojo positive label"><?php echo Utility::formatMoney($row->price, true);?></span>
            <?php endif;?>
          </p>
        </div>
        <div class="content-center"><a href="<?php echo Url::doUrl(URL_ITEM, $row->idx . '/' . $row->slug);?>" class="wojo block shine"><img src="<?php echo UPLOADURL . 'listings/thumbs/' . $row->thumb;?>" alt=""></a></div>
        <div class="footer">
          <div class="content-center">
            <div class="wojo small divided horizontal list">
              <div class="item"><?php echo $row->condition_name;?></div>
              <div class="item"><?php echo $row->trans_name;?></div>
              <div class="item"><?php echo $row->category_name;?></div>
              <div class="item"><?php echo Utility::doDate("short_date", $row->created);?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach;?>
    <?php unset($row);?>
  </div>
  <?php endif;?>
  <div class="wojo tabular segment">
    <div class="wojo cell"> <?php echo $pager->display_pages();?></div>
    <div class="wojo cell right"> <?php echo Lang::$word->TOTAL.': '.$pager->items_total;?> / <?php echo Lang::$word->CURPAGE.': '.$pager->current_page.' '.Lang::$word->OF.' '.$pager->num_pages;?> </div>
  </div>
  <div class="wojo double space divider"></div>
  <?php $result = $items->getFooterBits();?>
  <?php if($result):?>
  <div class="wojo top bottom attached segment">
    <div class="double-padding">
      <h4><?php echo Lang::$word->HOME_SUB2P;?></h4>
      <div class="wojo double space divider"></div>
      <div class="columns half-gutters">
        <?php $makes = Utility::groupToLoop($result, "make_name");?>
        <?php foreach($makes as $make => $i):?>
        <div class="screen-25 tablet-33 phone-50"><a href="<?php echo Url::doUrl(URL_BRAND, Url::doSeo($make));?>"><img src="<?php echo UPLOADURL . 'brandico/' . str_replace(" ", "-", strtolower($make));?>.png" class="wojo avatar image" alt=""><?php echo $make;?> <span class="wojo bold negative text"><?php echo count($i);?></span></a></div>
        <?php endforeach;?>
      </div>
      <?php unset($i);?>
    </div>
  </div>
  <?php endif;?>
</div>