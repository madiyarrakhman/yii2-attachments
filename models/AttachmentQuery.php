<?php
/**
 * Poject: kapital2
 * User: mitrii
 * Date: 5.04.2018
 * Time: 16:31
 * Original File Name: AttachmentQuery.php
 */

namespace musan\attachments\models;


use yii\db\ActiveQuery;

class AttachmentQuery extends ActiveQuery
{
    public function whereUID($uid)
    {
        return $this->andWhere(['uid' => $uid]);
    }
}