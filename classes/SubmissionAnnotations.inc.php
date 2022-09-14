<?php

class SubmissionAnnotations {
    private $submissionId;
    private $annotations;

    public function __construct(int $submissionId) {
        $this->submissionId = $submissionId;
        $this->annotations = [];
    }

    public function addAnnotation(string $annotation) {
        $this->annotations[] = $annotation;
    }

    public function getAnnotations(): array {
        return $this->annotations;
    }
}