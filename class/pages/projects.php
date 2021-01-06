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
 * Handle projects operations
 *
 * @param Context   $context    The context object for the site
 *
 * @return string|array   A template name
 */
        public function handle(Context $context)
        {
            $fdt = $context->formdata('post');
            if($fdt->exists('title') && $fdt->exists('desc'))
            {
                $user = $context->user();
                $title = $fdt->mustfetch('title');
                $desc = $fdt->mustfetch('desc');
                $priv = ($fdt->mustfetch('privc'));

                if($title !== '' && $desc !== '')
                {
                    $project = R::dispense('project');
                    $project->title = $title;
                    $project->description = $desc;
                    $project->private = $priv;
                    //$user->noLoad()->ownProject[] = $project;
                    //R::store($user);
                    $context->local()->message(\Framework\Local::MESSAGE, 'Project Successfully Created'); 
                }
            }
            return '@content/projects.twig';
        }
    }
?>