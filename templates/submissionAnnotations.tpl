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
			{if not empty($annotation->target)}
			<div class="annotation_target">
				<blockquote>{$annotation->target}</blockquote>
				<button class="read_more" onclick="toggleReadMore(this)">{translate key="common.more"}</button>
				<button class="read_less hide" onclick="toggleReadMore(this)">{translate key="common.less"}</button>
			</div>
			{/if}
			<span class="annotation_content">
				<blockquote>{$annotation->content}</blockquote>
				<button class="read_more" onclick="toggleReadMore(this)">{translate key="common.more"}</button>
				<button class="read_less hide" onclick="toggleReadMore(this)">{translate key="common.less"}</button>
			</span>
		</div>
	{/foreach}
</div>