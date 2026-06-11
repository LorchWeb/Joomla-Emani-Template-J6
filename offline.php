<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$app = Factory::getApplication();
$wa  = $this->getWebAssetManager();

// ✅ HTMLHelper::_('behavior.core') ist in J4/J5 nicht mehr nötig
// ✅ HTMLHelper::_('bootstrap.framework') deprecated - wa verwenden
HTMLHelper::_('bootstrap.framework');

$wa->useStyle('template.nexus.min');
$wa->useStyle('fontawesome');

// ✅ require_once + UsersHelper komplett entfernt - in J4/J5 nicht mehr verfügbar
// Zwei-Faktor wird jetzt über com_users MFA API gehandhabt
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <jdoc:include type="head" />
    <?php if ($this->direction == 'rtl') : ?>
        <link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/offline_rtl.css" />
    <?php endif; ?>
</head>
<body>
<jdoc:include type="message" />

<div id="frame" class="offline-wrapper">
    <?php if ($app->get('offline_image')) : ?>
        <div style="margin-bottom: 1rem;">
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
            <!-- ✅ TwoFactor Block entfernt - wird in J4/J5 anders gehandhabt (MFA) -->
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
