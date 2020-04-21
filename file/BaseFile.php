<?php
namespace musan\attachments\file;

use yii\base\BaseObject;
use yii\helpers\FileHelper;

abstract class BaseFile extends BaseObject
{
    protected $_uid;

    protected $_save_path;

    public $upload_dir;

    public $path_deep = 5;

    /**
     * @param  bool $deleteTempFile whether to delete the temporary file after saving.
     * @return bool
     */
    abstract function save($deleteTempFile = true);

    /**
     * @return string Path for saving file
     */
    public function getSavePath()
    {
        if ($this->_save_path === null)
        {
            $uid = $this->getUid();
            $path = '';
            for ($i=0; $i<$this->path_deep; $i++) {
                $c = $uid[$i];
                $path .= $c . DIRECTORY_SEPARATOR;
            }

            FileHelper::createDirectory($this->upload_dir . $path, 0777);

            $this->_save_path = $path . substr($uid, $this->path_deep) . '.' . $this->getExtensionName();
        }

        return $this->_save_path;
    }

    public function getFullSavePath()
    {
        return $this->upload_dir . $this->getSavePath();
    }

    /**
     * @return string "Random" generated UID
     */
    public function getUid()
    {
        return empty($this->_uid) ? $this->_uid = md5(uniqid()) : $this->_uid;
    }

    /**
     * @return string the original name of the file
     */
    abstract function getName();

    /**
     * @return string the MIME-type of the file (such as "image/gif").
     * Since this MIME type is not checked on the server side, do not take this value for granted.
     */
    abstract function getType();

    /**
     * @return integer the actual size of the file in bytes
     */
    abstract function getSize();

    /**
     * Returns an error code describing the status of this file.
     */
    abstract function getError();

    /**
     * @return boolean whether there is an error with the uploaded file.
     */
    abstract function getHasError();

    /**
     * @return string the file extension name for {@link name}.
     * The extension name does not include the dot character. An empty string
     * is returned if {@link name} does not have an extension name.
     */
    abstract function getExtensionName();
}