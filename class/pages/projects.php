<?php
/**
 * A class that contains code to handle any requests for  /projects/
 *
 * @author Your Name <Your@email.org>
 * @copyright year You
 * @package Framework
 * @subpackage UserPages
 */
    namespace Pages;

    use \Support\Context as Context;
/**
 * Support /projects/
 */
    class Projects extends \Framework\Siteaction
    {
/**
 * Handle projects operations
 *
 * @param Context   $context    The context object for the site
 *
 * @return string|array   A template name
 */
        public function handle(Context $context)
        {
            return '@content/projects.twig';
        }
    }
?>