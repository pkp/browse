{**
 * templates/block.tpl
 *
 * Copyright (c) 2014-2025 Simon Fraser University
 * Copyright (c) 2003-2025 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @brief Common site sidebar menu for browsing the catalog.
 *
 * @uses $browseNewReleases bool Whether or not to show a new releases link
 * @uses $browseCategoryFactory object Category factory providing access to
 *  browseable categories.
 * @uses $browseSeriesFactory object Series factory providing access to
 *  browseable series.
 *}

{function name=displayCategories categories=[] level=0}
	<ul{if $level === 0} class="categories_list" {/if}>
		{foreach from=$categories item=category}
			<li class="category_{$category.id}{if $category.parentId} is_sub{/if}">
				<a href="{url router=PKP\core\PKPApplication::ROUTE_PAGE page="catalog" op="category" path=$category.path|escape}" class="{if $browseBlockSelectedCategory == $category.path} current{/if}">
					{$category.localizedTitle|escape}
				</a>
				{if $category.subCategories}
					{call name=displayCategories categories=$category.subCategories level=$level+1}
				{/if}
			</li>
		{/foreach}
	</ul>
{/function}

<div class="pkp_block block_browse">
	<h2 class="title">
		{translate key="plugins.block.browse"}
	</h2>

	<nav class="content" role="navigation" aria-label="{translate|escape key="plugins.block.browse"}">
		<ul>
			{if $browseCategories}
				<li class="has_submenu">
					<span class="category_header">{translate key="plugins.block.browse.category"}</span>
					{call name=displayCategories categories=$browseCategories}
				</li>
			{/if}
		</ul>
	</nav>
</div><!-- .block_browse -->

<style>
	.current {
		padding-left: 0.5em !important;
		border-left: 4px solid #ddd !important;
		color: rgba(0, 0, 0, 0.54) !important;
		cursor: text !important;
	}
</style>
