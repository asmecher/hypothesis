<?php

namespace APP\plugins\generic\hypothesis\classes;

use APP\plugins\generic\hypothesis\classes\Annotation;

class SubmissionAnnotations {
    public $submissionId;
    public $submission;
    public $annotations;

    public function __construct(int $submissionId) {
        $this->submissionId = $submissionId;
        $this->annotations = [];
    }

    public static function __set_state($dump) {
        $obj = new SubmissionAnnotations($dump['submissionId']);
        $obj->annotations = $dump['annotations'];
        return $obj;
    }

    public function addAnnotation(Annotation $annotation) {
        $this->annotations[] = $annotation;
    }
}