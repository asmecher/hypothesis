<?php

class Annotation {
    public $user;
    public $dateCreated;
    public $target;
    public $content;

    public function __construct(string $user, string $dateCreated, string $target, string $content) {
        $this->$user = $user;
        $this->$dateCreated = $dateCreated;
        $this->$target = $target;
        $this->$content = $content;
    }

    public static function __set_state($dump) {
        $obj = new Annotation($dump['user'], $dump['dateCreated'], $dump['target'], $dump['content']);
        return $obj;
    }
}