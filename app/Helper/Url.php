<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;

/**
 * Url helpers
 *
 * @package helper
 * @author  Frederic Guillot
 */
class Url extends Base
{
    private $base = '';
    private $directory = '';

    /**
     * Helper to generate a link to the documentation
     *
     * @access public
     * @param  string  $label
     * @param  string  $file
     * @return string
     */
    public function doc($label, $file)
    {
        return $this->link($label, 'doc', 'show', array('file' => $file), false, '', '', true);
    }

    /**
     * HTML Link tag
     *
     * @access public
     * @param  string   $label       Link label
     * @param  string   $controller  Controller name
     * @param  string   $action      Action name
     * @param  array    $params      Url parameters
     * @param  boolean  $csrf        Add a CSRF token
     * @param  string   $class       CSS class attribute
     * @param  boolean  $new_tab     Open the link in a new tab
     * @param  string   $anchor      Link Anchor
     * @return string
     */
    public function link($label, $controller, $action, array $params = array(), $csrf = false, $class = '', $title = '', $new_tab = false, $anchor = '')
    {
        return '<a href="'.$this->href($controller, $action, $params, $csrf, $anchor).'" class="'.$class.'" title="'.$title.'" '.($new_tab ? 'target="_blank"' : '').'>'.$label.'</a>';
    }

    /**
     * HTML Link tag specific to cancel an action
     *
     * @access public
     * @param array   $task     Task data
     * @param string  $redirect Type of redirection: 'board' or 'listing' to redirect to the given project view, empty to redirect to the current task, numeric value to redirect to a specific task
     * @param string  $anchor   Optional anchor for default redirection (without the prefix #)
     * @return string
     */
    public function cancel($task, $redirect='', $anchor='')
    {
        $actions = array('board' => 'show', 'listing' => 'show');
        
        if (in_array($redirect, array('board', 'listing'))) {
            return $this->link(t('cancel'), $redirect, $actions[$redirect], array('project_id' => $task['project_id']), false, 'close-popover', t('Cancel'), false, $anchor);
        }
        if (is_numeric($redirect)) {
            return $this->link(t('cancel'), 'task', 'show', array('task_id' => $redirect, 'project_id' => ''), false, 'close-popover', t('Cancel'), false, empty($anchor) ? $anchor : 'links');
        }
        return $this->link(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'close-popover', t('Cancel'), false, $anchor);
    }

    /**
     * HTML Hyperlink
     *
     * @access public
     * @param  string   $controller  Controller name
     * @param  string   $action      Action name
     * @param  array    $params      Url parameters
     * @param  boolean  $csrf        Add a CSRF token
     * @param  string   $anchor      Link Anchor
     * @param  boolean  $absolute    Absolute or relative link
     * @return string
     */
    public function href($controller, $action, array $params = array(), $csrf = false, $anchor = '', $absolute = false)
    {
        return $this->build('&amp;', $controller, $action, $params, $csrf, $anchor, $absolute);
    }

    /**
     * Generate controller/action url
     *
     * @access public
     * @param  string   $controller  Controller name
     * @param  string   $action      Action name
     * @param  array    $params      Url parameters
     * @param  string   $anchor      Link Anchor
     * @param  boolean  $absolute    Absolute or relative link
     * @return string
     */
    public function to($controller, $action, array $params = array(), $anchor = '', $absolute = false)
    {
        return $this->build('&', $controller, $action, $params, false, $anchor, $absolute);
    }

    /**
     * Get application base url
     *
     * @access public
     * @return string
     */
    public function base()
    {
        if (empty($this->base)) {
            $this->base = $this->config->get('application_url') ?: $this->server();
        }

        return $this->base;
    }

    /**
     * Get application base directory
     *
     * @access public
     * @return string
     */
    public function dir()
    {
        if ($this->directory === '' && $this->request->getMethod() !== '') {
            $this->directory = str_replace('\\', '/', dirname($this->request->getServerVariable('PHP_SELF')));
            $this->directory = $this->directory !== '/' ? $this->directory.'/' : '/';
            $this->directory = str_replace('//', '/', $this->directory);
        }

        return $this->directory;
    }

    /**
     * Get current server base url
     *
     * @access public
     * @return string
     */
    public function server()
    {
        if ($this->request->getServerVariable('SERVER_NAME') === '') {
            return 'http://localhost/';
        }

        $url = $this->request->isHTTPS() ? 'https://' : 'http://';
        $url .= $this->request->getServerVariable('SERVER_NAME');
        $url .= $this->request->getServerVariable('SERVER_PORT') == 80 || $this->request->getServerVariable('SERVER_PORT') == 443 ? '' : ':'.$this->request->getServerVariable('SERVER_PORT');
        $url .= $this->dir() ?: '/';

        return $url;
    }

    /**
     * Build relative url
     *
     * @access private
     * @param  string   $separator   Querystring argument separator
     * @param  string   $controller  Controller name
     * @param  string   $action      Action name
     * @param  array    $params      Url parameters
     * @param  boolean  $csrf        Add a CSRF token
     * @param  string   $anchor      Link Anchor
     * @param  boolean  $absolute    Absolute or relative link
     * @return string
     */
    private function build($separator, $controller, $action, array $params = array(), $csrf = false, $anchor = '', $absolute = false)
    {
        $path = $this->route->findUrl($controller, $action, $params);
        $qs = array();

        if (empty($path)) {
            $qs['controller'] = $controller;
            $qs['action'] = $action;
            $qs += $params;
        } else {
            unset($params['plugin']);
        }

        if ($csrf) {
            $qs['csrf_token'] = $this->token->getCSRFToken();
        }

        if (! empty($qs)) {
            $path .= '?'.http_build_query($qs, '', $separator);
        }

        return ($absolute ? $this->base() : $this->dir()).$path.(empty($anchor) ? '' : '#'.$anchor);
    }
}
