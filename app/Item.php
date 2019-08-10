<?php
  
namespace App;
   
use Illuminate\Database\Eloquent\Model;
  
class Item extends Model
{
     public static function getData($name)
     {
        return Item::select("name", "price", "description", "url", "picture")
        ->where("name","=","{$name}")
        ->first();
     }  
}