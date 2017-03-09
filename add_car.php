<?php

  $a_config['db_host']="localhost";
  $a_config['db_name']="incacar"; 
  $a_config['db_user']="incacar";
  $a_config['db_pass']="T2r9Z6r2#T2r9Z6r2##";
  $a_config['db_charset']="utf8";
  $a_config['db_prefix']="";
  $a_config['secret_key']="FNBvewwrsd3556y*%#sd";
  
  $a_config['maindir']="/var/www/incacar/data/www/incacar.com/incacar";
  
  try
  {$a_config['db'] = new PDO('mysql:dbname='.$a_config['db_name'].';host='.$a_config['db_host'], $a_config['db_user'], $a_config['db_pass']);
  } catch (PDOException $ex) {echo 'Connection failed: ' . $ex->getMessage();}

  $a_config['db']->prepare("SET NAMES ".$a_config['db_charset'])->execute();
    
  $a_data=unserialize($_POST['a_data']);
  if($a_config['secret_key']==$a_data['secret_key'])
  {$a_features="";
   
   for($i=0;$i<count($a_data['features']);$i++)
   {if(trim($a_data['features'][$i])!="Features:")
	{$a_config['sql']="Insert into features set name='".str_replace("'", "\'", $a_data['features'][$i])."'";
	 sql_query($a_config);	
	 
	 $a_config['sql']="Select *From features where name='".str_replace("'", "\'", $a_data['features'][$i])."'";
	 $dfeatures=sql_query($a_config)->fetch(PDO::FETCH_ASSOC);
	 
	 if($dfeatures['id'])
	 {$a_features.=$dfeatures['id'].",";}
	 
    }	
   }
   
   $a_features=rtrim($a_features, ",");
   
   $a_config['sql']="Insert into makes set name='".str_replace("'", "\'", $a_data['make'])."', name_slug='".strtolower(rus_to_lat($a_data['make']))."'";
   sql_query($a_config);	
   
   $a_config['sql']="Select *from makes where name_slug='".strtolower(rus_to_lat($a_data['make']))."'";
   $dmake=sql_query($a_config)->fetch(PDO::FETCH_ASSOC);
   $id_make=$dmake['id'];	
   
   $a_config['sql']="Select *from models where name='".str_replace("'", "\'", $a_data['model'])."' and make_id=".$id_make;
   $dmodel=sql_query($a_config)->fetch(PDO::FETCH_ASSOC);
   
   if(!$dmodel['id'])
   {$a_config['sql']="Insert into models set name='".str_replace("'", "\'", $a_data['model'])."', make_id=".$id_make;
    sql_query($a_config);	
   
    $a_config['sql']="Select *from models where name='".str_replace("'", "\'", $a_data['model'])."' and make_id=".$id_make;
    $dmodel=sql_query($a_config)->fetch(PDO::FETCH_ASSOC);
   } 
   $id_model=$dmodel['id'];	
	 
   $a_config['sql']="Select *from listings where vin='".str_replace("'", "\'", $a_data['vehicle']['VIN'])."'";
   $dlisting=sql_query($a_config)->fetch(PDO::FETCH_ASSOC);
   
   if($dlisting['id'])
   {echo "caraddtosite"; exit;}
   else
   {$id_location=0;
	
	if($a_data['lat'] && $a_data['lng'])
    {$a_config['sql']="Select *from locations where lat='".$a_data['lat']."' and lng='".$a_data['lng']."'";
     $dlocation=sql_query($a_config)->fetch(PDO::FETCH_ASSOC);
     
     if($dlocation['id'])
     {$id_location=$dlocation['id'];}
    }	
    
    if(!$id_location)
    {$a_config['sql']="Select *from locations where zip='".$a_data['zipcode']."' and name='".str_replace("'", "\'", $a_data['location_name'])."'";
     $dlocation=sql_query($a_config)->fetch(PDO::FETCH_ASSOC);
     if($dlocation['id'])
     {$id_location=$dlocation['id'];}
    }	
      
    if(!(int)$id_location)
    {$sql="zip='".$a_data['zipcode']."'|||name='".str_replace("'", "\'", $a_data['location_name'])."'|||name_slug='".strtolower(rus_to_lat(str_replace("'", "\'", $a_data['location_name'])))."'|||
		   user_id=1|||ltype='owner'|||email='me@mail.com'|||address='".str_replace("'", "\'", $a_data['street'])."'|||city='".str_replace("'", "\'", $a_data['locality'])."'|||
		   state='".$a_data['region']."'|||country='".$a_data['country']."'|||phone='".str_replace("'", "\'", $a_data['phone'])."'|||zoom='12'|||
		   lat='".$a_data['lat']."'|||lng='".$a_data['lng']."'";
	 echo $a_config['sql']="Insert into locations set ".str_replace("|||", ", ", $sql);
	 sql_query($a_config);
	 
	 $a_config['sql']="Select *from locations where ".str_replace("|||", " and ", $sql);
	 $dlocation=sql_query($a_config)->fetch(PDO::FETCH_ASSOC);
	 
	 if($dlocation['id'])
     {$id_location=$dlocation['id'];}
    }
    
    $a_config['sql']="Select * from locations where id=".$id_location;
	$dlocation=sql_query($a_config)->fetch(PDO::FETCH_ASSOC);
	$location_slug=$dlocation['name_slug'];
	
	$location_obj = (object)$dlocation;
	  	
    if($id_location)
    {$a_config['sql']="Select *From categories where name='".str_replace("'", "\'", $a_data['body_style'])."'";
	 $dcategory=sql_query($a_config)->fetch(PDO::FETCH_ASSOC);	
	 
	 if(!$dcategory['id'])
	 {$a_config['sql']="insert into categories set name='".str_replace("'", "\'", $a_data['body_style'])."', slug='".strtolower(rus_to_lat($a_data['body_style']))."'";
	  sql_query($a_config);
	  $a_config['sql']="Select *From categories where name='".str_replace("'", "\'", $a_data['body_style'])."'";
	  $dcategory=sql_query($a_config)->fetch(PDO::FETCH_ASSOC);	
	 
	 }	 
	 
	 echo (int)$id_cat=$dcategory['id'];
		
	 while(1)
	 {$idx=mt_rand(1,999999);
	  $a_config['sql']="Select id from listings where idx='".$idx."'";
      $dlisting=sql_query($a_config)->fetch(PDO::FETCH_ASSOC);
     
      if(!$dlisting['id'])
      {break;}
 	 }	
 	 
 	 if($a_data['vehicle']['Fuel'])
 	 {$a_config['sql']="Select *From fuel where name='".str_replace("'", "\'", $a_data['vehicle']['Fuel'])."'";
	  $dfuel=sql_query($a_config)->fetch(PDO::FETCH_ASSOC);	
	  
	  if(!$dfuel['id'])
	  {$a_config['sql']="Insert into fuel set name='".str_replace("'", "\'", $a_data['vehicle']['Fuel'])."'";
	   sql_query($a_config);
	  }  
	 } 
	 
	 if($a_data['vehicle']['Transmission'])
 	 {$a_config['sql']="Select *From transmissions where name='".str_replace("'", "\'", $a_data['vehicle']['Transmission'])."'";
	  $dtransmissions=sql_query($a_config)->fetch(PDO::FETCH_ASSOC);	
	  
	  if(!$dtransmissions['id'])
	  {$a_config['sql']="Insert into transmissions set name='".str_replace("'", "\'", $a_data['vehicle']['Transmission'])."'";
	   sql_query($a_config);
	  }  
	 } 
	 
	 $nice_title=str_replace("'", "\'", $a_data['make']." ".$a_data['model']);  
	 $title=str_replace("'", "\'", $a_data['title']);  
	 $sql="user_id=1|||idx='".$idx."'|||title='".$title."'|||nice_title='".$nice_title."'|||slug='".strtolower(rus_to_lat($title))."'|||location=".$id_location."|||
	       stock_id='RST-P-".$idx."'|||vin='".str_replace("'", "\'", $a_data['vehicle']['VIN'])."'|||make_id=".(int)$id_make."|||model_id=".$id_model."|||
	       year=".$a_data['year']."|||vcondition=2|||category='".$id_cat."'|||mileage='".$a_data['vehicle']['Mileage']."'|||price='".$a_data['price']."'|||
	       color_e='".str_replace("'", "\'", $a_data['vehicle']['Exterior Color'])."'|||color_i='".str_replace("'", "\'", $a_data['vehicle']['Interior Color'])."'|||
	       doors='".(int)$a_data['vehicle']['doors']."'|||fuel='".str_replace("'", "\'", $a_data['vehicle']['Fuel'])."'|||
	       drive_train='".str_replace("'", "\'", $a_data['vehicle']['Drive Type'])."'|||engine='".str_replace("'", "\'", $a_data['vehicle']['Engine'])."'|||
	       transmission='".str_replace("'", "\'", $a_data['vehicle']['Transmission'])."'|||features='".$a_features."'|||
	       body='".str_replace("'", "\'", $a_data['description'])."'|||status=1|||expire='".date("Y-m-d H:i:s")."'|||modified='".date("Y-m-d H:i:s")."'|||
	       created='".date("Y-m-d H:i:s")."'";
     
     $a_config['sql']="Insert into listings set ".str_replace("|||", ", ", $sql);
     sql_query($a_config);
     
     $a_config['sql']="Select id From listings where vin='".str_replace("'", "\'", $a_data['vehicle']['VIN'])."'";
     $dlisting=sql_query($a_config)->fetch(PDO::FETCH_ASSOC);
     
     if($dlisting['id'])
     {echo $a_config['sql']="Insert into listings_info set listing_id='".$dlisting['id']."', make_name='".str_replace("'", "\'",$a_data['make'])."', user_id=1, 
		                make_slug='".strtolower(rus_to_lat($a_data['make']))."', model_name='".str_replace("'", "\'",$a_data['model'])."', 
		                location_slug='".$location_slug."', location_name='".str_replace("'", "\'", serialize($location_obj))."',
		                condition_name='Used', category_name='".str_replace("'", "\'", $a_data['body_style'])."',
		                trans_name='".str_replace("'", "\'", $a_data['vehicle']['Transmission'])."', 
		                category_slug='".strtolower(rus_to_lat($a_data['body_style']))."', fuel_name='".str_replace("'", "\'", $a_data['vehicle']['Fuel'])."',
		                color_name='".str_replace("'", "\'", $a_data['vehicle']['Exterior Color'])."', special=1, lstatus=1";
      echo "<br>";		                
      sql_query($a_config); 	
      
      
      echo $a_config['sql']="Select id from listings_info where listing_id='".$dlisting['id']."'";
      echo "<br>";
      $dlisting_info=sql_query($a_config)->fetch(PDO::FETCH_ASSOC);
      
      echo "ID_listing - ".$dlisting_info['id']."<br>";
      
      if(!$dlisting_info['id'])
      {$a_config['del_listing']=$dlisting['id']; del_listing($a_config);}
      
      if($dlisting_info['id'])
      {mkdir($a_config['maindir']."/uploads/listings/thumbs", 0777);
      
       for($id_img=0;$id_img<count($a_data['images']);$id_img++)
       {$a_data['images'][$id_img]=str_replace("-103.jpg", "-600.jpg", $a_data['images'][$id_img]);
		
		if(!$id_img)
	    {$file=$a_config['maindir']."/uploads/listings/".$dlisting['id'].".jpg";
		 $a_data['images'][$id_img]."<br>";	
	  	 copy($a_data['images'][$id_img], $file);
		 $file_thumb=$a_config['maindir']."/uploads/listings/thumbs/".$dlisting['id'].".jpg";
		 
		 if(file_exists($file))
		 {imageresize($file, $file_thumb);
	      $a_config['sql']="update listings set thumb='".$dlisting['id'].".jpg' where id=".$dlisting['id'];
          sql_query($a_config);
         }
         else
         {$a_config['del_listing']=$dlisting['id']; del_listing($a_config);}  
	    }
	    
	    mkdir($a_config['maindir']."/uploads/listings/pics".$dlisting['id'], 0777);
        mkdir($a_config['maindir']."/uploads/listings/pics".$dlisting['id']."/thumbs", 0777);
       
	    $name_file=md5($id_img.time());
	    $file=$a_config['maindir']."/uploads/listings/pics".$dlisting['id']."/IMG_".$name_file.".jpg";
	    $file_thumb=$a_config['maindir']."/uploads/listings/pics".$dlisting['id']."/thumbs/IMG_".$name_file.".jpg";
	    copy($a_data['images'][$id_img], $file);
	   
 	    if(file_exists($file))
	    {imageresize($file, $file_thumb);
	     $a_config['sql']="Insert into gallery set listing_id='".$dlisting['id']."', user_id=1,
		                   title='".str_replace("'", "\'",$a_data['title'])." Interior', photo='IMG_".$name_file.".jpg', sorting=".($id_img+1);
         sql_query($a_config); 	
        }  
	   }
	  
	   $a_config['sql']="Select * From gallery where listing_id='".$dlisting['id']."' ORDER BY sorting";
       $dgalery=sql_query($a_config);
      
       $a_gallery=array();
      
       while($value_galery=$dgalery->fetch(PDO::FETCH_ASSOC))
       {$gal = new stdClass();
	    foreach($value_galery as $id_val=>$value_val)
	    {$gal->$id_val=$value_val;}	  
	    $a_gallery[]=$gal;
	   }   
         
       $a_config['sql']="update listings set gallery='".str_replace("'", "\'", serialize($a_gallery))."' where id=".$dlisting['id'];
       sql_query($a_config);   
       echo "caraddtosite";
      } 
	 }	 
    }
   } 
  } 
	  
	 
  
//
  function del_listing($a_config)
  {$a_config['sql']="delete from listings where id=".$a_config['del_listing'];
   sql_query($a_config);   
   
   $a_config['sql']="delete from listings_info where listing_id=".$a_config['del_listing'];
   sql_query($a_config);   
   
   $a_config['sql']="delete from gallery where listing_id=".$a_config['del_listing'];
   sql_query($a_config); 
   exit;
  }   
  
  
  function imageresize($infile, $outfile) 
  {$im=imagecreatefromjpeg($infile);
   $k1=400/imagesx($im);
   $k2=300/imagesy($im);
   $k=$k1>$k2?$k2:$k1;

   $w=intval(imagesx($im)*$k);
   $h=intval(imagesy($im)*$k);

   $im1=imagecreatetruecolor(400,300);
   imagecopyresampled($im1,$im,0,0,0,0,400,300,imagesx($im),imagesy($im));

   imagejpeg($im1,$outfile,85);
   imagedestroy($im);
   imagedestroy($im1);
  }
  
  function picture_size($fileold, $filenew, $a_config)
  {clearstatcache();
   $source="";
   $target="";
   
   $a_config['cfg_width_image']=400;
   $a_config['cfg_height_image']=300;
   $a_config['cfg_image_size']=1;

   $size = getimagesize($fileold);

   $mime = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
  
   if($mime=="jpg" || $mime=="jpeg")
   {$source = imagecreatefromjpeg($fileold);}

   if($mime=="gif")
   {$source = imagecreatefromgif($fileold);}

   if($mime=="png")
   {$source = imagecreatefrompng($fileold);}

   $nx=(int)$size[0];
   $ny=(int)$size[1];
   
   $new_width=(int)$size[0];
   $new_height=(int)$size[1];
      
   if($a_config['cfg_image_size'])
   {$koefx=0;
    $koefy=0;

    if($size[0]>$a_config['cfg_width_image'] || $size[1]>$a_config['cfg_height_image'])
    {$koefx=$size[0]/$a_config['cfg_width_image'];
     $koefy=$size[1]/$a_config['cfg_height_image'];
    }

    if($koefx || $koefy)
    {if($koefx>$koefy)
     {$ny=ceil($size[1]/$koefx);
      $nx=ceil($size[0]/$koefx);
     }
     else
     {$ny=ceil($size[1]/$koefy);
      $nx=ceil($size[0]/$koefy);
     }
    }
    
    $new_width=$nx;
    $new_height=$ny;
   }

   $target = imagecreatetruecolor($a_config['cfg_width_image'], $a_config['cfg_height_image']);

   $white=imagecolorallocate($target, 255, 255, 255);
   imagefill($target, 0, 0, $white);
   
   $dist_x=(int)ceil(($new_width-$nx)/2);
   $dist_y=(int)ceil(($new_height-$ny)/2);
   
   imagecopyresampled($target, $source, $dist_x, $dist_y, 0, 0, $nx, $ny, $size[0], $size[1]);
   
   imagejpeg($target, $filenew, 75);
   imagedestroy($target);
   imagedestroy($source);
  }
  
  
  function create_dir($a_config)
  {if(strstr($a_config['dir'], $a_config['maindir']))
   {$a_config['dir']=str_replace($a_config['maindir']."/", "", $a_config['dir']);}

   $a_config['dir']=str_replace("//", "/", $a_config['dir']);
   $a_dir=explode("/", $a_config['dir']);

   $dir=$a_config['maindir'];

   for($i=0;$i<count($a_dir);$i++)
   {if(trim($a_dir[$i]))
    {$dir.="/".$a_dir[$i];
	 
     if(!is_dir($dir))
     {mkdir($dir, 0777);}
    }
   }
  }
  
  
  function sql_query($a_config)
  {$dbdata=$a_config['db']->prepare($a_config['sql']);
   $dbdata->execute();
   return $dbdata;
  }
  
  
  
  function get_curl($a_config)
  {$mh = curl_multi_init();

   for($i=0;$i<sizeof($a_config['urls']);$i++)
   {if($a_config['urls'][$i]['url'])
	{$ch[$i] = curl_init($a_config['urls'][$i]['url']);
     
     curl_setopt($ch[$i], CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)");
     curl_setopt($ch[$i], CURLOPT_HEADER, true);
     curl_setopt($ch[$i], CURLOPT_RETURNTRANSFER, true);
     curl_setopt($ch[$i], CURLOPT_FOLLOWLOCATION, true);
     curl_setopt($ch[$i], CURLOPT_CONNECTTIMEOUT, 300);

     curl_setopt($ch[$i], CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch[$i], CURLOPT_SSL_VERIFYHOST, false);

     if(isset($a_config['urls'][$i]['referer']))
     {curl_setopt($ch[$i], CURLOPT_REFERER, $a_config['urls'][$i]['referer']);}
     curl_setopt($ch[$i], CURLOPT_AUTOREFERER, 1);

     if($a_config['urls'][$i]['proxy'][0])
     {shuffle($a_config['urls'][$i]['proxy']);
      $a_proxy=explode("|||", $a_config['urls'][$i]['proxy'][0]);

      if($a_proxy[0])
      {curl_setopt ($ch[$i], CURLOPT_PROXY, $a_proxy[0]);}

      if(stristr($a_proxy[1], "SOCKS"))
      {curl_setopt ($ch[$i], CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);}

      if(stristr($a_proxy[1], "HTTP"))
      {curl_setopt ($ch[$i], CURLOPT_PROXYTYPE, CURLPROXY_HTTP);}
     }


     if(isset($a_config['urls'][$i]['post']))
     {if(count($a_config['urls'][$i]['post']))
      {curl_setopt($ch[$i], CURLOPT_POST, 1);
 	   curl_setopt($ch[$i], CURLOPT_POSTFIELDS, $a_config['urls'][$i]['post']);
      }
     }

     if(isset($a_config['urls'][$i]['cookie_file']))
     {if(!file_exists($a_config['urls'][$i]['cookie_file']))
	  {$f=fopen($a_config['urls'][$i]['cookie_file'], "w");
	   fclose($f);
	  }
	  curl_setopt($ch[$i], CURLOPT_COOKIEJAR, $a_config['urls'][$i]['cookie_file']);
      curl_setopt($ch[$i], CURLOPT_COOKIEFILE, $a_config['urls'][$i]['cookie_file']);
     }

     curl_multi_add_handle($mh, $ch[$i]);
    }
   }

   do { $n = curl_multi_exec($mh, $active); } while ($active);

   for ($i=0; $i<sizeof($a_config['urls']); $i++)
   {if($a_config['urls'][$i]['url'])
	{$a_config['urls'][$i]['res']= curl_multi_getcontent($ch[$i]);
	 curl_multi_remove_handle($mh, $ch[$i]);
	 curl_close($ch[$i]);
	}
   }

   curl_multi_close($mh);


   return $a_config['urls'];
  }

  
  
  function rus_to_lat($title)
  {$iso = array(
    "Є"=>"YE","І"=>"I","Ѓ"=>"G","і"=>"i","№"=>"#","є"=>"ye","ѓ"=>"g",
    "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D",
    "Е"=>"E","Ё"=>"YO","Ж"=>"ZH",
    "З"=>"Z","И"=>"I","Й"=>"J","К"=>"K","Л"=>"L",
    "М"=>"M","Н"=>"N","О"=>"O","П"=>"P","Р"=>"R",
    "С"=>"S","Т"=>"T","У"=>"U","Ф"=>"F","Х"=>"X",
    "Ц"=>"C","Ч"=>"CH","Ш"=>"SH","Щ"=>"SHH","Ъ"=>"'",
    "Ы"=>"Y","Ь"=>"","Э"=>"E","Ю"=>"YU","Я"=>"YA",
    "а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d",
    "е"=>"e","ё"=>"yo","ж"=>"zh",
    "з"=>"z","и"=>"i","й"=>"j","к"=>"k","л"=>"l",
    "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
    "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"x",
    "ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"shh","ъ"=>"",
    "ы"=>"y","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya","«"=>"","»"=>"","—"=>"-"
   );


   $title=strtr($title, $iso);
   $title=preg_replace('/[^0-9a-zа-яА-ЯЁёA-Z\s-]/usi', "", trim($title));
   $title=str_replace(" ", "-", $title);
   while(strstr($title, "--"))
   {$title=str_replace("--", "-", $title);}
   return $title;
  }
