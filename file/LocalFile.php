<?php
namespace musan\attachments\file;


use yii\helpers\FileHelper;

class LocalFile extends BaseFile
{
    public $filepath;

    private $errors = [];

    private $_name;
    private $_extension;
    private $_size;

    public function init()
    {
        parent::init();

        $this->filepath = FileHelper::normalizePath($this->filepath);

        $pathinfo = pathinfo($this->filepath);

        if (empty($pathinfo['filename']))
        {
            $this->errors[] = 'File not found';
        }

        $this->_name = $pathinfo['filename'];
        $this->_extension = $pathinfo['extension'];

        $this->_size = filesize($this->filepath);
    }

    public function save($deleteTempFile = true)
    {
        try {
            if ($deleteTempFile)
            {
                rename($this->filepath, $this->getFullSavePath());
            }
            else
            {
                copy($this->filepath, $this->getFullSavePath());
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    public function getHasError()
    {
        return count($this->errors) > 0;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getError()
    {
        return implode(PHP_EOL, $this->errors);
    }

    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return string the MIME-type of the file (such as "image/gif").
     * Since this MIME type is not checked on the server side, do not take this value for granted.
     */
    public function getType()
    {
        return FileHelper::getMimeTypeByExtension($this->getExtensionName());
    }

    /**
     * @return integer the actual size of the file in bytes
     */
    public function getSize()
    {
        return $this->_size;
    }

    public function getExtensionName()
    {
        return $this->_extension;
    }

}