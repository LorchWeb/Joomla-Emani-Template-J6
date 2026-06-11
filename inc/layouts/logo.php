<?php 
defined('_JEXEC') or die('Restricted index access');

use Joomla\CMS\Factory;

$app      = Factory::getApplication(); // ✅ $app war nicht definiert
$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');
// ✅ $logoimagefile aus params holen, nicht als undefinierte Variable
$logoimagefile       = $this->params->get('logoimagefile');
$mobilelogoimagefile = $this->params->get('mobilelogoimagefile');
?>
<div id="logo" class="logo">
    <a href="<?php echo $this->baseurl; ?>" title="<?php echo $sitename; ?>">
        <?php if ($this->params->get('logoImage') == '1') : ?>
            <?php if (empty($logoimagefile)) : ?>
                <img class="logo-image primary-logo-image" 
                     src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/images/logo.png" 
                     alt="<?php echo $sitename; ?>" />
            <?php else : ?>
                <img class="logo-image primary-logo-image" 
                     src="<?php echo $this->baseurl; ?>/<?php echo htmlspecialchars($logoimagefile, ENT_QUOTES, 'UTF-8'); ?>" 
                     alt="<?php echo $sitename; ?>" />
            <?php endif; ?>
            <?php if (!empty($mobilelogoimagefile)) : ?>
                <img class="logo-image mobile-logo-image" 
                     src="<?php echo $this->baseurl; ?>/<?php echo htmlspecialchars($mobilelogoimagefile, ENT_QUOTES, 'UTF-8'); ?>" 
                     alt="Mobile Logo" />
            <?php endif; ?>
        <?php else : ?>
            <div class="logo-text"><?php echo $this->params->get('logoText'); ?></div>
            <div class="logo-slogan"><?php echo $this->params->get('sloganText'); ?></div>
        <?php endif; ?>
    </a> 
</div>
