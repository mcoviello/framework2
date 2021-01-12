<?php
/**
 * A class that contains code to handle any requests for  /project/
 *
 * @author Michael Coviello <b8034196@newcastle.ac.uk>
 * @copyright 2021 Michael Coviello
 * @package Framework
 * @subpackage UserPages
 */
    namespace pages;

    use \Support\Context as Context;
/**
 * Support /project/
 */
    class project extends \Framework\Siteaction
    {
/**
 * Handle project operations
 *
 * @param Context   $context    The context object for the site
 *
 * @return string|array   A template name
 */
        public function handle(Context $context)
        {
            $id = $context->rest();
            
            return '@content/project.twig';
        }
    }
?>