<?php namespace Yuan\Ckeditor;

use Form;
use Html;
use Config;
use URL;

class Ckeditor {

    protected static $import;
    protected static $instance;

    /**
     * Make a ckeditor
     *
     * @param       $name
     * @param null  $value
     * @param array $options
     *
     * @return string
     */
    public function make($name, $options = array(), $value = null)
    {
        $options['class'] = array_key_exists('class', $options) ? $options['class'] . ' ckeditor' : 'ckeditor';
        $html = static::$import ?
            $this->importCssAndJs() . Form::textarea($name, $value, $options)
            : Form::textarea($name, $value, $options);

        if ($this->getConfig('ckfinder'))
        {
            $html = $html .
                $this->ckfinderIntegratedJs($name) .
                $this->ckfinderJs();
        }
        return $html;
    }


    /**
     * get assets base path
     *
     * @return string
     */
    public function getAssetsBasePath()
    {
        return Url::to('/') . '/packages/yuan/ckeditor/';
    }

    /**
     * Js for integrate ckfinder to ckeditor
     *
     * @param $name
     *
     * @return string
     */
    public function ckfinderIntegratedJs($name)
    {

        $path = $this->getAssetsBasePath() . 'ckfinder/';
        return
            '<script type="text/javascript">' .
            'var editor = CKEDITOR.replace( \'' . $name . '\', {' .
            'filebrowserBrowseUrl : \'' . $path . 'ckfinder.html\',' .
            'filebrowserBrowseUrl : \'' . $path . 'ckfinder.html\',' .
            'filebrowserImageBrowseUrl : \'' . $path . 'ckfinder.html?type=Images\',' .
            'filebrowserFlashBrowseUrl : \'' . $path . 'ckfinder.html?type=Flash\',' .
            'filebrowserUploadUrl : \'' . $path . 'core/connector/php/connector.php?command=QuickUpload&type=Files\',' .
            'filebrowserImageUploadUrl : \'' . $path . 'core/connector/php/connector.php?command=QuickUpload&type=Images\',' .
            'filebrowserFlashUploadUrl : \'' . $path . 'core/connector/php/connector.php?command=QuickUpload&type=Flash\'' .
            '});' .
            'CKFinder.setupCKEditor( editor, \'../\' );' .
            '</script>';
    }

    /**
     * js for single finder input
     *
     * @return string
     */
    public function ckfinderJs()
    {
        return
            '<script type="text/javascript">
                   var editedField;
                   function BrowseServer(field)
                   {
                       var finder = new CKFinder();
                       editedField = field ;
                       finder.basePath = \'../\';
                       finder.selectActionFunction = SetFileField;
                       finder.popup( \'../\', null, null, SetFileField ) ;
                   }
                   function SetFileField( fileUrl )
                   {
                       document.getElementById( editedField ).value = fileUrl ;
                   }
               </script>';
    }


    /**
     * get configs
     *
     * @param $config
     *
     * @return mixed
     */
    protected function getConfig($config)
    {
        return Config::get("ckeditor::config.{$config}");
    }

    public function importCssAndJs()
    {
        $html = Html::style($this->getAssetsBasePath() . 'ckeditor/samples.css') .
            Html::script($this->getAssetsBasePath() . 'ckeditor/ckeditor.js');

        if ($this->getConfig('ckfinder'))
        {

            $html = $html . Html::script($this->getAssetsBasePath() . 'ckfinder/ckfinder.js');
        }
        return $html;
    }

    public static function __callStatic($name, $args)
    {

        $instance = static::$instance;

        if (!$instance)
        {
            // import base js and css to first ckeditor
            static::$import = true;
            $instance = static::$instance = new static;
        } else
        {
            // not import more base js and css if there are multiple ckeditor on one page
            static::$import = false;
        }
        switch (count($args))
        {
            case 0:
                return $instance->make($name);
            case 1:
                return $instance->make($name, $args[0]);
            case 2:
                return $instance->make($name, $args[0], $args[1]);
        }
    }
}
