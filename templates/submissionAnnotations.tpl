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
				<blockquote>
					{$annotation->target}
				</blockquote>
				<button onclick="readMore(this)">{translate key="common.more"}</button>
			</div>
			{/if}
			<span class="annotation_content">
				{$annotation->content}
			</span>
		</div>
	{/foreach}
</div>
<script>
	$(function(){ldelim}
		let maxCharNumber = 300;
		let annotationTargets = document.querySelectorAll('.annotation_target > blockquote');

		annotationTargets.forEach(target => {ldelim}
			if(target.textContent.length <= maxCharNumber) {ldelim}
				target.nextElementSibling.style.display = "none";
			{rdelim}
			else {ldelim}
				let trimmedText = target.textContent.trim();
				let textToDisplay = trimmedText.slice(0, maxCharNumber);
				let textMore = trimmedText.slice(maxCharNumber);
				target.innerHTML = `${ldelim}textToDisplay{rdelim}<span class="dots">...</span><span class="more hide">${ldelim}textMore{rdelim}</span>`;
			{rdelim}
		{rdelim});
	{rdelim});
	
	function readMore(btn){ldelim}
		let annotation = btn.parentElement;
		annotation.querySelector('.dots').classList.toggle('hide');
		annotation.querySelector('.more').classList.toggle('hide');
		if(btn.textContent == '{translate key="common.more"}')
			btn.textContent = '{translate key="common.less"}';
		else
			btn.textContent = '{translate key="common.more"}';
	{rdelim}	
</script>