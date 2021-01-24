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
    use \Config\Config;
    use \R;
/**
 * Support /projects/
 */
    class Projects extends \Framework\Siteaction
    {

/**
 * Handle viewing either a project or a note
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
                    if ($proj->id && $context->user()->id == $proj->user_id)
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
                    $note = $context->load($bean, $id);
                    $proj = $context->load('project',$note->project_id);
                    if ($proj->id && $note->id && $context->user()->id == $proj->user_id)
                    {
                        $context->local()->addval('project', $proj);
                        $context->local()->addval($bean, $note);
                    }
                    else
                    {
                        throw new \Framework\Exception\BadValue('You do not have permission to view this note.');
                    }
                    $fdt = $context->formdata('file');
                    if ($fdt->exists('uploads'))
                    {
                        foreach($fdt->fileArray('uploads') as $ix => $fa)
                        { // we only support private or public in this case so there is no flag
                            $upl = \R::dispense('upload');
                            $upl->note_id = $note->id;
                            if (!$upl->savefile($context, $fa, Config::UPUBLIC, $context->user(), $ix))
                            { // something went wrong
                                \Model\Upload::fail($context, $fa);
                            }
                            else
                            {
                                $context->local()->message(\Framework\Local::MESSAGE, $fa['name'].' uploaded');
                            }
                        }
                    }
                    return '@content/note.twig';
                default:
                    throw new \Framework\Exception\BadValue($rest[1].' is not viewable.');
            }
        }

/**
 * Handle viewing either a project or a note
 *
 * @param Context           $context  The Context object
 * @param array<string>     $rest     The rest of the URL
 *
 * @return string
 */
public function edit(Context $context, array $rest) : string
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
            $editbean = $context->load($bean, $id);
            if($editbean->id && $context->user()->id == $editbean->user_id)
            {
                $context->local()->addval($bean, $editbean);
            }
            else
            {
                throw new \Framework\Exception\BadValue('You do not have permission to view this project.');
            }
            break;
        case 'note' :
            $rest = $context->rest();
            $id = $rest[2];
            $editbean = $context->load($bean, $id);
            $proj = $context->load('project',$editbean->project_id);
            if($proj->id && $editbean->id && $context->user()->id == $proj->user_id)
            {
                $context->local()->addval('project', $proj);
                $context->local()->addval($bean, $editbean);
            }
            else
            {
                throw new \Framework\Exception\BadValue('You do not have permission to view this note.');
            }
            break;
        default:
            throw new \Framework\Exception\BadValue($rest[1].' is not viewable.');
    }

    if (is_object($editbean))
    {
        if (($bid = $context->formdata('post')->fetch('bean', '')) !== '')
        { // this is a post
            if (($bid != $editbean->getID()))
            { // something odd...
                throw new \Framework\Exception\BadValue('Invalid Bean Parameter');
            }
            \Framework\Utility\CSRFGuard::getinstance()->check();
            try
            {
                [$error, $emess] = $editbean->edit($context);
            }
            catch (\Exception $e)
            {
                $error = TRUE;
                $emess = $e->getMessage();
            }
            if ($error)
            {
                $context->local()->message(\Framework\Local::ERROR, $emess);
            } 
            else
            {
                $context->local()->message(\Framework\Local::MESSAGE, 'Saved');
            }
        }
    }
    return '@content/edit/'.$bean.'.twig';
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
            switch ($rest[0])
            {
                case 'view':
                    $context->setPages();
                    return $this->view($context, $rest);
                case 'edit':
                    return $this->edit($context,$rest);
                default:
                    $context->setPages();
                    return '@content/projects.twig';
            }
        }
    }
?>