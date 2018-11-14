<?php
namespace musan\attachments\file;


class RemoteFile extends BaseFile
{
    public $url;

    private $errors = [];

    private $_name;
    private $_extension;
    private $_size;

    public function init()
    {
        parent::init();

        if (!(new \yii\validators\UrlValidator())->validate($this->url, $error))
        {
            $this->errors[] = $error;
        }

        $url_filename = parse_url($this->url, PHP_URL_PATH);

        $this->_name = basename($url_filename);

        $tmp_filename = explode(".", $url_filename);
        $this->_extension = array_pop($tmp_filename);
    }

    public function save($deleteTempFile = false)
    {
        try {
            copy($this->url, $this->getFullSavePath());

            $this->_size = filesize($this->getFullSavePath());
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