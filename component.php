<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

$app = Factory::getApplication();
$wa  = $this->getWebAssetManager();

$this->language  = $this->document->language;
$this->direction = $this->document->direction;

// ✅ $document->addStyleSheet() ersetzt durch $wa->registerAndUseStyle()
$wa->registerAndUseStyle('template.base',      'templates/' . $this->template . '/css/base/template.css');
$wa->registerAndUseStyle('template.nexus',     'templates/' . $this->template . '/css/nexus.min.css');
$wa->registerAndUseStyle('template.responsive','templates/' . $this->template . '/css/responsive.min.css');

// Add JavaScript Frameworks
HTMLHelper::_('behavior.core');
HTMLHelper::_('bootstrap.framework');

$wa->useStyle('fontawesome');
$wa->addInlineStyle(":root {
    --hue: 214;
    --template-bg-light: #f0f4fb;
    --template-text-dark: #495057;
    --template-text-light: #ffffff;
    --template-link-color: #2a69b8;
    --template-special-color: #001B4C;
}
body {
    font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,'Noto Sans',sans-serif;
}
");
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <jdoc:include type="metas" />
    <jdoc:include type="styles" />
    <jdoc:include type="scripts" />
    <!-- ✅ print.css ebenfalls über wa registrieren wäre sauberer, -->
    <!-- hier als direkter Link noch tolerierbar für print-only CSS -->
    <link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/print.css" media="print" />
</head>
<body>
    <jdoc:include type="message" />
    <jdoc:include type="component" />
</body>
</html>
