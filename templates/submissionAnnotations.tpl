{assign var="preprint" value=$submissionAnnotations->submission}
{assign var="annotations" value=$submissionAnnotations->annotations}

<div class="submission_annotations">
    <div class="title">
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
		<p>
			{$annotation|escape}
		</p>
	{/foreach}
</div>