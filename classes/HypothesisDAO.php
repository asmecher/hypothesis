<?php

namespace APP\plugins\generic\hypothesis\classes;

use PKP\db\DAO;
use Illuminate\Support\Facades\DB;

class HypothesisDAO extends DAO {
    public function getDatePublished($submissionId): string {
        $result = DB::table('submissions')
            ->where('submission_id', $submissionId)
            ->select('current_publication_id')
            ->first();
        $currentPublicationId = get_object_vars($result)['current_publication_id'];

        $result = DB::table('publications')
            ->where('publication_id', $currentPublicationId)
            ->select('date_published')
            ->first();

        return get_object_vars($result)['date_published'];
    }
}