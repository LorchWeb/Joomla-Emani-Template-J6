<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$app   = Factory::getApplication();
$input = $app->getInput();
$wa    = $this->getWebAssetManager();

$templatePath = 'templates/' . $this->template . '/css/';

$wa->registerAndUseStyle('j51.template.error', $templatePath . 'error.css')
    ->registerAndUseStyle('j51.fontawesome', $templatePath . 'fontawesome/fontawesome.min.css')
    ->registerAndUseStyle('j51.fontawesome.solid', $templatePath . 'fontawesome/solid.min.css', [], [], ['j51.fontawesome']);

$params   = $app->getTemplate(true)->params;
$option   = $input->getCmd('option', '');
$view     = $input->getCmd('view', '');
$layout   = $input->getCmd('layout', '');
$task     = $input->getCmd('task', '');
$itemid   = $input->getCmd('Itemid', '');
$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');
$renderModules = $app->getIdentity() && $app->getLanguage();

$this->setTitle(($this->title ?: $sitename) . ' - ' . $this->error->getMessage());
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <jdoc:include type="metas" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <jdoc:include type="styles" />
    <jdoc:include type="scripts" />
</head>
<body class="site <?php echo $option
    . ' view-' . $view
    . ($layout ? ' layout-' . $layout  : ' no-layout')
    . ($task   ? ' task-'   . $task    : ' no-task')
    . ($itemid ? ' itemid-' . $itemid  : '')
    . ($params->get('fluidContainer') ? ' fluid' : ''); ?>">

    <div class="error-wrapper">
        <div class="error-code"><?php echo $this->error->getCode(); ?></div>
        <div class="error-message">
            <h2><?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></h2>
            <p><strong><?php echo Text::_('JERROR_LAYOUT_ERROR_HAS_OCCURRED_WHILE_PROCESSING_YOUR_REQUEST'); ?></strong></p>
            <p><?php echo Text::_('JERROR_LAYOUT_NOT_ABLE_TO_VISIT'); ?></p>
            <ul>
                <li><?php echo Text::_('JERROR_LAYOUT_AN_OUT_OF_DATE_BOOKMARK_FAVOURITE'); ?></li>
                <li><?php echo Text::_('JERROR_LAYOUT_MISTYPED_ADDRESS'); ?></li>
                <li><?php echo Text::_('JERROR_LAYOUT_SEARCH_ENGINE_OUT_OF_DATE_LISTING'); ?></li>
                <li><?php echo Text::_('JERROR_LAYOUT_YOU_HAVE_NO_ACCESS_TO_THIS_PAGE'); ?></li>
            </ul>
            <blockquote>
                <span class="label label-inverse"><?php echo $this->error->getCode(); ?></span>
                <?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?>
                <?php if ($this->debug) : ?>
                    <br /><?php echo htmlspecialchars($this->error->getFile(), ENT_QUOTES, 'UTF-8'); ?>
                    :<?php echo $this->error->getLine(); ?>
                <?php endif; ?>
            </blockquote>
        </div>
    </div>

    <div class="page-redirect">
        <?php echo Text::_('JERROR_LAYOUT_GO_TO_THE_HOME_PAGE'); ?>
        <a href="<?php echo Uri::root(); ?>">
            <span class="fa fa-home"></span> <?php echo Text::_('JERROR_LAYOUT_HOME_PAGE'); ?>
        </a>
    </div>

    <?php if ($this->debug) : ?>
        <div class="debug-trace">
            <?php echo $this->renderBacktrace(); ?>
            <?php if ($this->error->getPrevious()) : ?>
                <?php $loop = true; ?>
                <?php $this->setError($this->_error->getPrevious()); ?>
                <?php while ($loop === true) : ?>
                    <p><strong><?php echo Text::_('JERROR_LAYOUT_PREVIOUS_ERROR'); ?></strong></p>
                    <p>
                        <?php echo htmlspecialchars($this->_error->getMessage(), ENT_QUOTES, 'UTF-8'); ?>
                        <br /><?php echo htmlspecialchars($this->_error->getFile(), ENT_QUOTES, 'UTF-8'); ?>
                        :<?php echo $this->_error->getLine(); ?>
                    </p>
                    <?php echo $this->renderBacktrace(); ?>
                    <?php $loop = $this->setError($this->_error->getPrevious()); ?>
                <?php endwhile; ?>
                <?php $this->setError($this->error); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($renderModules) : ?>
        <jdoc:include type="modules" name="debug" style="none" />
    <?php endif; ?>
</body>
</html>
