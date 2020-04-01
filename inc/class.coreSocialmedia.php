<?php

/*
   ____  __      __    ___  _  _  ___    __   ____     ___  __  __  ___
  (  _ \(  )    /__\  / __)( )/ )/ __)  /__\ (_  _)   / __)(  \/  )/ __)
   ) _ < )(__  /(__)\( (__  )  (( (__  /(__)\  )(    ( (__  )    ( \__ \
  (____/(____)(__)(__)\___)(_)\_)\___)(__)(__)(__)    \___)(_/\/\_)(___/

   @author          Black Cat Development
   @copyright       2018 Black Cat Development
   @link            http://blackcat-cms.org
   @license         http://www.gnu.org/licenses/gpl.html
   @category        CAT_Module
   @package         coreSocialmedia

*/

namespace CAT\Addon;

use \CAT\Base as Base;

if(!class_exists('\CAT\Addon\coreSocialmedia',false))
{
    final class coreSocialmedia extends Tool
    {
        protected static $type        = 'tool';
        protected static $directory   = 'coreSocialmedia';
        protected static $name        = 'Socialmedia Link Manager';
        protected static $version     = '0.2';
        protected static $description = 'Manage your Social Media Services here<br /><small>Icon by <a href="https://pixabay.com/illustrations/social-media-3d-render-bubble-2636256/">Pixabay</a></small>';
        protected static $author      = "Black Cat Development";
        protected static $guid        = "d3079e37-179a-4e79-ae69-3c454adc3594";
        protected static $license     = "GNU General Public License";

        const SOCIALMEDIA_EDIT        = 'socialmedia_site_edit';

        /**
         *
         * @access public
         * @return
         **/
        public static function edit()
        {
            // check permissions
            self::user()->checkPermission(SOCIALMEDIA_EDIT);

            // field name
            $field = \CAT\Helper\Validate::get('name','string');
            // new value
            $value = \CAT\Helper\Validate::get('value','string');
            // id
            $id    = \CAT\Helper\Validate::get('pk','numeric');

            if($field && $value && $id) {
                $table = 'socialmedia';
                if($field=='account') {
                    $table .= '_site';
                    // add item to sites table if it does not exist
                    if(!self::exists($id)) {
                        self::addToSiteTable($id);
                    }
                }
                // check if entry exists
                //if(!self::entryExists())
                self::db()->query(
                    'UPDATE `:prefix:'.$table.'` SET `:field:`=:value WHERE `id`=:id',
                    array('field'=>$field,'value'=>$value,'id'=>intval($id))
                );
            }
        }   // end function edit()
        
        /**
         *
         * @access public
         * @return
         **/
        public static function enable()
        {
            // check permissions
            self::user()->checkPermission(SOCIALMEDIA_EDIT);

            $item    = \CAT\Helper\Validate::get("item","numeric");
            $type    = \CAT\Helper\Validate::get("url","string");
            $enabled = \CAT\Helper\Validate::get("enabled");
            
            if($item && $type) {
                if(!self::exists($item)) {
                    self::addToSiteTable($id);
                }

                self::db()->query(
                      'UPDATE `:prefix:socialmedia_site` SET '
                    . '`:type:_url`=:enabled '
                    . 'WHERE `id`=:id',
                    array(':type:'=>$type,':id'=>$id,':enabled'=>$enabled)
                );
            }
        }

        /**
         *
         * @access public
         * @return
         **/
        public static function tool()
        {
            self::user()->checkPermission(SOCIALMEDIA_EDIT);

            // get available services
            $services = \CAT\Helper\Socialmedia::getServices(CAT_SITE_ID);
            return self::tpl()->get(
                    'tool',
                    array(
                        'services' => $services
                    )
                );
        }   // end function tool()

        /**
         *
         * @access protected
         * @return
         **/
        protected static function addToSiteTable($item)
        {
            $services = \CAT\Helper\Socialmedia::getServices(CAT_SITE_ID);
            $data     = array();
            for($i=0;$i<count($services);$i++) {
                if($services[$i]['id']==$item) {
                    $data = $services[$i];
                    self::db()->query(
                          'INSERT INTO `:prefix:socialmedia_site` VALUES ( '
                        . ":id, :site, '', :follow, :share, 'N', 'N' )",
                        array('id'=>intval($item),':site'=>1,':follow'=>$data['follow_url'],':share'=>$data['share_url'])
                    );
                }
            }
        }   // end function addToSiteTable()
        

        /**
         *
         * @access protected
         * @return
         **/
        protected static function exists($id)
        {
            $sth = self::db()->query(
                "SELECT `id` FROM `:prefix:socialmedia_site` WHERE `id`=:id",
                array('id'=>$id)
            );
            if ($sth->rowCount() > 0) {
                return true;
            }
            return false;
        }   // end function exists()
    }
}