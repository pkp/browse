<?php

/**
 * @file plugins/blocks/browse/BrowseBlockPlugin.php
 *
 * Copyright (c) 2014-2026 Simon Fraser University
 * Copyright (c) 2003-2026 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @class BrowseBlockPlugin
 * @brief Class for browse block plugin
 */

namespace APP\plugins\blocks\browse;

use APP\core\Application;
use APP\facades\Repo;
use PKP\plugins\BlockPlugin;
use PKP\template\PKPTemplateManager;

class BrowseBlockPlugin extends BlockPlugin
{
    /**
     * Get the display name of this plugin.
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return __('plugins.block.browse.displayName');
    }

    /**
     * Get a description of the plugin.
     */
    public function getDescription(): string
    {
        return __('plugins.block.browse.description');
    }

    /**
     * Get the HTML contents of the browse block.
     *
     * @param PKPTemplateManager $templateMgr
     * @param null|mixed $request
     */
    public function getContents($templateMgr, $request = null): string
    {
        $context = $request->getContext();
        if (!$context) {
            return '';
        }
        $router = $request->getRouter();

        $catalogPage = (Application::get()->getName() === 'ops') ? 'preprints' : 'catalog';
        $requestedCategoryPath = null;
        if ($router->getRequestedPage($request) . '/' . $router->getRequestedOp($request) == "$catalogPage/category") {
            $args = $router->getRequestedArgs($request);
            $requestedCategoryPath = reset($args);
        }
        $templateMgr->assign([
            'browseBlockSelectedCategory' => $requestedCategoryPath,
            'browseCategories' => Repo::category()->getCollector()
                ->filterByContextIds([$context->getId()])
                ->getMany(),
            'catalogPage' => $catalogPage
        ]);
        return parent::getContents($templateMgr);
    }
}

if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\blocks\browse\BrowseBlockPlugin', '\BrowseBlockPlugin');
}
