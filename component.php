<?php
defined('_JEXEC') or die;

$wa = $this->getWebAssetManager();

$this->language  = $this->document->language;
$this->direction = $this->document->direction;

$templatePath = 'templates/' . $this->template . '/css/';

$wa->registerAndUseStyle('j51.template.base', $templatePath . 'base/template.css')
    ->registerAndUseStyle('j51.template.nexus.min', $templatePath . 'nexus.min.css')
    ->registerAndUseStyle('j51.template.responsive.min', $templatePath . 'responsive.min.css')
    ->registerAndUseStyle('j51.template.print', $templatePath . 'print.css', [], ['media' => 'print'])
    ->registerAndUseStyle('j51.fontawesome', $templatePath . 'fontawesome/fontawesome.min.css')
    ->registerAndUseStyle('j51.fontawesome.solid', $templatePath . 'fontawesome/solid.min.css', [], [], ['j51.fontawesome']);

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <jdoc:include type="styles" />
    <jdoc:include type="scripts" />
</head>
<body class="contentpane component<?php echo $this->direction === 'rtl' ? ' rtl' : ''; ?>">
    <jdoc:include type="message" />
    <jdoc:include type="component" />
</body>
</html>
