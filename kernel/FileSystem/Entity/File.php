<?php

namespace Lampion\FileSystem\Entity;

class File {

    /** @var(type="int") */
    public $id;

    /** @var(type="string") */
    public $filename;

    /** @var(type="string") */
    public $fullPath;

    /** @var(type="string") */
    public $relativePath;

    /** @var(type="string") */
    public $extension;

    /** @var(type="string") */
    public $note;

    /** @var(type="json") */
    public $metadata;

    /** @var(type="json") */
    public $tags;

    /** @var(type="entity", entity="Lampion\User\Entity\User", mappedBy="user_id") */
    public $user;

    public function __toString() {
        return $this->filename;
    }
    
}