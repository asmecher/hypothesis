{assign var="preprint" value=$submissionAnnotations->submission}
{assign var="annotations" value=$submissionAnnotations->annotations}

<div class="submission_annotations">
    <div class="title">
		{assign var=preprintPath value=$preprint->getBestId()}
		<a id="preprint-{$preprint->getId()}" {if $journal}href="{url journal=$journal->getPath() page="preprint" op="view" path=$preprintPath}"{else}href="{url page="preprint" op="view" path=$preprintPath}"{/if}>
			{$preprint->getLocalizedTitle()|strip_unsafe_html}
			{if $preprint->getLocalizedSubtitle()}
				<span class="subtitle">
					{$preprint->getLocalizedSubtitle()|escape}
				</span>
			{/if}
		</a>
	</div>
	{foreach from=$annotations item="annotation"}
		<div class="annotation">
			<div class="annotation_header">
				<strong>{$annotation->user}</strong>
				<span>{$annotation->dateCreated|date_format:$dateFormatLong}</span>
			</div>
			<blockquote class="annotation_target">
				{$annotation->target}
			</blockquote>
			<span class="annotation_content">
				{$annotation->content}
			</span>
		</div>
	{/foreach}
</div>