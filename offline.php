<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\AuthenticationHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$app          = Factory::getApplication();
$extraButtons = AuthenticationHelper::getLoginButtons('form-login');
$wa           = $this->getWebAssetManager();

$templatePath = 'templates/' . $this->template . '/css/';

$wa->registerAndUseStyle('j51.template.base', $templatePath . 'base/template.css')
    ->registerAndUseStyle('j51.template.offline', $templatePath . 'offline.css')
    ->registerAndUseStyle('j51.fontawesome', $templatePath . 'fontawesome/fontawesome.min.css')
    ->registerAndUseStyle('j51.fontawesome.solid', $templatePath . 'fontawesome/solid.min.css', [], [], ['j51.fontawesome']);
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <jdoc:include type="metas" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <jdoc:include type="styles" />
    <jdoc:include type="scripts" />
</head>
<body>
<jdoc:include type="message" />

<div id="frame" class="offline-wrapper">
    <?php if ($app->get('offline_image')) : ?>
        <div class="offline-image">
            <?php echo HTMLHelper::_('image', $app->get('offline_image'), $app->get('sitename'), [], false, 0); ?>
        </div>
    <?php endif; ?>

    <h1 class="site-title"><?php echo htmlspecialchars($app->get('sitename')); ?></h1>

    <?php if ($app->get('display_offline_message', 1) == 1 && str_replace(' ', '', $app->get('offline_message')) != '') : ?>
        <p class="offline-message"><?php echo $app->get('offline_message'); ?></p>
    <?php elseif ($app->get('display_offline_message', 1) == 2 && str_replace(' ', '', Text::_('JOFFLINE_MESSAGE')) != '') : ?>
        <p class="offline-message"><?php echo Text::_('JOFFLINE_MESSAGE'); ?></p>
    <?php endif; ?>

    <form action="<?php echo Route::_('index.php', true); ?>" method="post" id="form-login">
        <fieldset class="input">
            <p id="form-login-username">
                <label for="username"><?php echo Text::_('JGLOBAL_USERNAME'); ?></label>
                <input name="username" id="username" type="text" class="inputbox" 
                       alt="<?php echo Text::_('JGLOBAL_USERNAME'); ?>" size="18" />
            </p>
            <p id="form-login-password">
                <label for="passwd"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
                <input type="password" name="password" class="inputbox" size="18" 
                       alt="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>" id="passwd" />
            </p>
            <?php foreach ($extraButtons as $button) : ?>
                <?php
                $dataAttributeKeys = array_filter(
                    array_keys($button),
                    static fn ($key) => str_starts_with($key, 'data-')
                );
                ?>
                <p class="form-login-authentication">
                    <button
                        type="button"
                        class="button login <?php echo $button['class'] ?? ''; ?>"
                        <?php foreach ($dataAttributeKeys as $key) : ?>
                            <?php echo $key; ?>="<?php echo $button[$key]; ?>"
                        <?php endforeach; ?>
                        <?php if (!empty($button['onclick'])) : ?>
                            onclick="<?php echo $button['onclick']; ?>"
                        <?php endif; ?>
                        title="<?php echo Text::_($button['label']); ?>"
                        id="<?php echo $button['id']; ?>"
                    >
                        <?php if (!empty($button['icon'])) : ?>
                            <span class="<?php echo $button['icon']; ?>" aria-hidden="true"></span>
                        <?php elseif (!empty($button['image'])) : ?>
                            <?php echo $button['image']; ?>
                        <?php elseif (!empty($button['svg'])) : ?>
                            <?php echo $button['svg']; ?>
                        <?php endif; ?>
                        <?php echo Text::_($button['label']); ?>
                    </button>
                </p>
            <?php endforeach; ?>
            <p id="submit-button">
                <label>&nbsp;</label>
                <input type="submit" name="Submit" class="button login" 
                       value="<?php echo Text::_('JLOGIN'); ?>" />
            </p>
            <input type="hidden" name="option" value="com_users" />
            <input type="hidden" name="task"   value="user.login" />
            <input type="hidden" name="return" value="<?php echo base64_encode(Uri::base()); ?>" />
            <?php echo HTMLHelper::_('form.token'); ?>
        </fieldset>
    </form>
</div>
</body>
</html>
