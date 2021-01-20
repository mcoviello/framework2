<?php
/**
 * A class that contains code to handle any requests for  /projects/
 *
 * @author Michael Coviello <b8034196@newcastle.ac.uk>
 * @copyright 2021 Michael Coviello
 * @package Framework
 * @subpackage UserPages
 */
    namespace Pages;

    use \Support\Context as Context;
    use \R;
/**
 * Support /projects/
 */
    class Projects extends \Framework\Siteaction
    {

/**
 * View project
 *
 * @param Context           $context  The Context object
 * @param array<string>     $rest     The rest of the URL
 *
 * @return string
 */
        public function view(Context $context, array $rest) : string
        {
            if (count($rest) < 3)
            {
                throw new \Framework\Exception\ParameterCount('Too few parameters');
            }
            $bean = $rest[1];
            switch($bean)
            {
                case 'project':
                    $rest = $context->rest();
                    $id = $rest[2];
                    $proj = $context->load($bean, $id);
                    if($proj->id && $context->user()->id == $proj->user_id)
                    {
                        $context->local()->addval($bean, $proj);
                    }
                    else
                    {
                        throw new \Framework\Exception\BadValue('You do not have permission to view this project.');
                    }
                    return '@content/project.twig';
                case 'note' :
                    $rest = $context->rest();
                    $id = $rest[2];
                    $proj = $context->load($bean, $id);
                    if($proj->id && $context->user()->id == $proj->user_id)
                    {
                        $context->local()->addval($bean, $proj);
                    }
                    else
                    {
                        throw new \Framework\Exception\BadValue('You do not have permission to view this note.');
                    }
                    return '@content/note.twig';
                default:
                    throw new \Framework\Exception\BadValue($rest[1].' is not viewable.');
            }
        }
/**
 * Handle projects operations
 *
 * @param Context   $context    The context object for the site
 *
 * @return string|array   A template name
 */
        public function handle(Context $context)
        {
            $rest = $context->rest();
            switch($rest[0])
            {
                case 'view':
                    return $this->view($context, $rest);
                default:
                    $context->setPages();
                    return '@content/projects.twig';
            }
        }
    }
?>