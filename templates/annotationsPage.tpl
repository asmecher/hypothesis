{capture assign="pageTitle"}
	{if $prevPage}
		{translate key="plugins.generic.hypothesis.annotationsPageNumber" pageNumber=$prevPage+1}
	{else}
		{translate key="plugins.generic.hypothesis.annotationsPage"}
	{/if}
{/capture}
<link rel="stylesheet" type="text/css" href="/plugins/generic/hypothesis/styles/annotationsPage.css">
{include file="frontend/components/header.tpl" pageTitleTranslated=$pageTitle}

<div class="page page_issue_archive">

	<h1>{$pageTitle|escape}</h1>
    
	{if empty($submissionsAnnotations)}
		<p>{translate key="plugins.generic.hypothesis.noSubmissionsWithAnnotations"}</p>
	{else}
		
		<div id="orderSubmissions">
			<label for="selectOrderSubmissions">{translate key="plugins.generic.hypothesis.orderBy"}</label>
			<select id="selectOrderSubmissions">
				<option value="datePublished" {if $orderBy == "datePublished"}selected="selected"{/if}>
					{translate key="plugins.generic.hypothesis.orderBy.datePublished"}
				</option>
				<option value="lastAnnotation" {if $orderBy == "lastAnnotation"}selected="selected"{/if}>
					{translate key="plugins.generic.hypothesis.orderBy.lastAnnotation"}
				</option>
			</select>
		</div>
		
		<ul class="cmp_article_list articles">
			{foreach from=$submissionsAnnotations item="submissionAnnotations"}
				<li>
					{include file="../../../plugins/generic/hypothesis/templates/submissionAnnotations.tpl"}
				</li>
			{/foreach}
		</ul>

		{* Pagination *}
		{if $prevPage > 1}
			{capture assign=prevUrl}{url router=$smarty.const.ROUTE_PAGE page="annotations" path=$prevPage}{/capture}
		{elseif $prevPage === 1}
			{capture assign=prevUrl}{url router=$smarty.const.ROUTE_PAGE page="annotations"}{/capture}
		{/if}
		{if $nextPage}
			{capture assign=nextUrl}{url router=$smarty.const.ROUTE_PAGE page="annotations" path=$nextPage}{/capture}
		{/if}
		{include
			file="frontend/components/pagination.tpl"
			prevUrl=$prevUrl
			nextUrl=$nextUrl
			showingStart=$showingStart
			showingEnd=$showingEnd
			total=$total
		}
	{/if}
</div>

{include file="frontend/components/footer.tpl"}