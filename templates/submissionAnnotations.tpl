{assign var="submission" value=$submissionAnnotations->submission}
{assign var="annotations" value=$submissionAnnotations->annotations}

<div class="submission_annotations">
    <div class="title">
		{assign var=submissionPath value=$submission->getBestId()}
		{if $application == 'ojs2'}
			{capture assign=submissionUrl}{if $context}{url journal=$context->getPath() page="article" op="view" path=$submissionPath}{else}{url page="article" op="view" path=$submissionPath}{/if}{/capture}
		{else}
			{capture assign=submissionUrl}{if $context}{url server=$context->getPath() page="preprint" op="view" path=$submissionPath}{else}{url page="preprint" op="view" path=$submissionPath}{/if}{/capture}
		{/if}
		<a id="submission-{$submission->getId()}" href="{$submissionUrl}">
			{assign var=publication value=$submission->getCurrentPublication()}	
			{$publication->getLocalizedTitle()|strip_unsafe_html}
			{if $publication->getLocalizedSubtitle()}
				<span class="subtitle">
					{$publication->getLocalizedSubtitle()|escape}
				</span>
			{/if}
		</a>
	</div>
	<div class="meta">
		<div class="authors">
			{$submission->getAuthorString()|escape}
		</div>

		{if $application == 'ops'}
			{* DOI *}
			{foreach from=$pubIdPlugins item=pubIdPlugin}
				{if $pubIdPlugin->getPubIdType() != 'doi'}
					{continue}
				{/if}
				{assign var=pubId value=$submission->getCurrentPublication()->getStoredPubId($pubIdPlugin->getPubIdType())}
				{if $pubId}
					{assign var="doiUrl" value=$pubIdPlugin->getResolvingURL($context->getId(), $pubId)|escape}
					<div class="doi">
							{capture assign=translatedDOI}{translate key="plugins.pubIds.doi.readerDisplayName"}{/capture}
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
				{translate key="submission.dates" submitted=$submission->getDateSubmitted()|date_format:$dateFormatShort published=$submission->getDatePublished()|date_format:$dateFormatShort}
			</div>
		{/if}
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