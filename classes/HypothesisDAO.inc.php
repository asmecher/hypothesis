<?php

import('lib.pkp.classes.db.DAO');

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Collection;

class HypothesisDAO extends DAO {
	public function getDatePublished($submissionId): string {
		$result = Capsule::table('submissions')
			->where('submission_id', $submissionId)
			->select('current_publication_id')
			->first();
		$currentPublicationId = get_object_vars($result)['current_publication_id'];

		$result = Capsule::table('publications')
			->where('publication_id', $currentPublicationId)
			->select('date_published')
			->first();

		return get_object_vars($result)['date_published'];
	}
}