{assign var="preprint" value=$submissionAnnotations->submission}
{assign var="annotations" value=$submissionAnnotations->annotations}

<div class="submission_annotations">
    <div class="title">
		{assign var=preprintPath value=$preprint->getBestId()}
		<a id="preprint-{$preprint->getId()}" {if $journal}href="{url journal=$journal->getPath() page="preprint" op="view" path=$preprintPath}"{else}href="{url page="preprint" op="view" path=$preprintPath}"{/if}>
			{assign var=publication value=$preprint->getCurrentPublication()}
			{$publication->getLocalizedTitle(null, 'html')|strip_unsafe_html}
			{if $publication->getLocalizedSubtitle()}
				<span class="subtitle">
					{$publication->getLocalizedSubtitle(null, 'html')|strip_unsafe_html}
				</span>
			{/if}
		</a>
	</div>
	<div class="meta">
		<div class="authors">
			{$preprint->getCurrentPublication()->getAuthorString($authorUserGroups)|escape}
		</div>

		{* DOI *}
		{foreach from=$pubIdPlugins item=pubIdPlugin}
			{if $pubIdPlugin->getPubIdType() != 'doi'}
				{continue}
			{/if}
			{assign var=pubId value=$preprint->getCurrentPublication()->getStoredPubId($pubIdPlugin->getPubIdType())}
			{if $pubId}
				{assign var="doiUrl" value=$pubIdPlugin->getResolvingURL($currentServer->getId(), $pubId)|escape}
				<div class="doi">
						{capture assign=translatedDOI}{translate key="doi.readerDisplayName"}{/capture}
						{translate key="semicolon" label=$translatedDOI}
					<span class="value">
						<a href="{$doiUrl}">
							{$doiUrl}
						</a>
					</span>
				</div>
			{/if}
		{/foreach}

		<div class="published">
			{translate key="submission.dates" submitted=$preprint->getDateSubmitted()|date_format:$dateFormatShort published=$preprint->getDatePublished()|date_format:$dateFormatShort}
		</div>
	</div>

	{foreach from=$annotations item="annotation"}
		<div class="annotation">
			<div class="annotation_header">
				<strong>{$annotation->user|escape}</strong>
				<span>{$annotation->dateCreated|date_format:$dateFormatLong}</span>
			</div>
			{if not empty($annotation->target)}
			<div class="annotation_target">
				<blockquote>{$annotation->target|escape}</blockquote>
				<button class="read_more" onclick="toggleReadMore(this)">{translate key="common.more"}</button>
				<button class="read_less hide" onclick="toggleReadMore(this)">{translate key="common.less"}</button>
			</div>
			{/if}
			<span class="annotation_content">
				<blockquote>{$annotation->content|escape}</blockquote>
				<button class="read_more" onclick="toggleReadMore(this)">{translate key="common.more"}</button>
				<button class="read_less hide" onclick="toggleReadMore(this)">{translate key="common.less"}</button>
			</span>
		</div>
	{/foreach}
</div>
