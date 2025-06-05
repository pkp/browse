<?php

/**
 * @file plugins/blocks/browse/BrowseBlockPlugin.php
 *
 * Copyright (c) 2014-2022 Simon Fraser University
 * Copyright (c) 2003-2022 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @class BrowseBlockPlugin
 * @brief Class for browse block plugin
 */

namespace APP\plugins\blocks\browse;

use APP\facades\Repo;
use PKP\context\Context;
use PKP\plugins\BlockPlugin;

class BrowseBlockPlugin extends BlockPlugin
{
    private array $processedCategoryIds = [];
    /**
     * Get the display name of this plugin.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return __('plugins.block.browse.displayName');
    }

    /**
     * Get a description of the plugin.
     */
    public function getDescription()
    {
        return __('plugins.block.browse.description');
    }

    /**
     * Get the HTML contents of the browse block.
     *
     * @param PKPTemplateManager $templateMgr
     * @param null|mixed $request
     *
     * @return string
     */
    public function getContents($templateMgr, $request = null)
    {
        $context = $request->getContext(); /** @var Context $context */
        if (!$context) {
            return '';
        }
        $router = $request->getRouter();

        $requestedCategoryPath = null;
        if ($router->getRequestedPage($request) . '/' . $router->getRequestedOp($request) == 'catalog/category') {
            $args = $router->getRequestedArgs($request);
            $requestedCategoryPath = reset($args);
        }

        // Get parent categories
        $categories = Repo::category()
            ->getCollector()
            ->filterByContextIds([$context->getId()])
            ->filterByParentIds([null])
            ->getMany();

        // Process each root category
        $processedCategories = $categories->map(fn($category) => $this->formatCategoryData($category));
        $templateMgr->assign([
            'browseBlockSelectedCategory' => $requestedCategoryPath,
            'browseCategories' => $processedCategories
        ]);
        return parent::getContents($templateMgr);
    }

    /**
     * Format category data.
     */
    private function formatCategoryData(\Category $category): array
    {
        // Avoid processing the same category multiple times
        if (in_array($category->getId(), $this->processedCategoryIds)) {
            return [];
        }

        $this->processedCategoryIds[] = $category->getId();

        return [
            'id' => $category->getId(),
            'path' => $category->getPath(),
            'parentId' => $category->getParentId(),
            'localizedTitle' => $category->getLocalizedTitle(),
            'subCategories' => $this->getSubCategories($category->getId(), $category->getContextId())
        ];
    }

    /**
     * Get subcategories for a given parent category.
     */
    private function getSubCategories(int $parentId, int $contextId): array
    {
        $children = Repo::category()->getCollector()
            ->filterByContextIds([$contextId])
            ->filterByParentIds([$parentId])
            ->getMany();

        if ($children->isEmpty()) {
            return [];
        }

        return $children->map(fn($category) => $this->formatCategoryData($category))->all();
    }
}

if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\blocks\browse\BrowseBlockPlugin', '\BrowseBlockPlugin');
}
