<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * ShiroiRightKey 设置鼠标右键菜单
 * @package ShiroiRightKey
 * @author shiroi
 * @version 1.0.0
 * @link https://shiroi.top
 */
class ShiroiRightKey_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Typecho_Plugin::factory('admin/menu.php')->navBar = array('ShiroiRightKey_Plugin', 'render');
        Typecho_Plugin::factory('Widget_Archive')->footer = array('ShiroiRightKey_Plugin', 'footer');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate()
    {
    }

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        include_once(__DIR__ . '/template.php');//引入模板
        $hidden = new Typecho_Widget_Helper_Form_Element_Hidden('hidden', NULL, 'hidden', _t('hidden'));
        $form->addInput($hidden);
        $target = new Typecho_Widget_Helper_Form_Element_Text('target', NULL, '_blank', _t('跳转模式'), _t('跳转模式<a style="color: red">支持四种模式: _blank | _self | _parent | _top</a>'));
        $form->addInput($target);
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }

    /**
     * 尾部加载
     */
    public static function footer()
    {
        $style_css = self::getFilePath('style.css');
        $target = self::toValue('target');
        $rightKey = json_decode(file_get_contents(__DIR__ . '/rightKey.json'), true);
        $rightContent = '<ul class="contextmenu" id="contextmenu">';
        foreach ($rightKey as $v) $rightContent .= '<li><a target="'.$target.'" href="' . end($v) . '">' . reset($v) . '</a></li>';
        $rightContent .= '</ul>';
        echo "{$style_css}{$rightContent}";
        echo <<<EOF
<script>
    window.onload = function () {
			//Show contextmenu:
			document.oncontextmenu = function (e) {
				//Get window size:
				// var winWidth = $(document).width();
				var winWidth = document.body.clientWidth;
				// var winHeight = $(document).height();
				var winHeight = document.body.clientHeight;
				//Get pointer position:
				var posX = e.pageX;
				var posY = e.pageY;
				//Get contextmenu size:
				// var menuWidth = $(".contextmenu").width();
				var menuWidth = document.getElementsByClassName('contextmenu').clientWidth;
				// var menuHeight = $(".contextmenu").height();
				var menuHeight = document.getElementsByClassName('contextmenu').clientHeight;
				//Security margin:
				var secMargin = 10;
				//Prevent page overflow:
				if (posX + menuWidth + secMargin >= winWidth &&
					posY + menuHeight + secMargin >= winHeight) {
					//Case 1: right-bottom overflow:
					posLeft = posX - menuWidth - secMargin + "px";
					posTop = posY - menuHeight - secMargin + "px";
				} else if (posX + menuWidth + secMargin >= winWidth) {
					//Case 2: right overflow:
					posLeft = posX - menuWidth - secMargin + "px";
					posTop = posY + secMargin + "px";
				} else if (posY + menuHeight + secMargin >= winHeight) {
					//Case 3: bottom overflow:
					posLeft = posX + secMargin + "px";
					posTop = posY - menuHeight - secMargin + "px";
				} else {
					//Case 4: default values:
					posLeft = posX + secMargin + "px";
					posTop = posY + secMargin + "px";
				};
				//Display contextmenu:
				document.getElementById("contextmenu").style.left = posLeft;
				document.getElementById("contextmenu").style.top = posTop;
				document.getElementById("contextmenu").style.display = 'block';
				//Prevent browser default contextmenu.
				return false;
			};
			//Hide contextmenu:
			// $(document).click(function () {
			// 	document.getElementById("contextmenu").style.display = 'none';
			// });
			document.onclick = function () {
				document.getElementById("contextmenu").style.display = 'none';
			};
		};
</script>
EOF;
    }

    /**
     * 组成js css
     * @param $name
     * @return string
     */
    public static function getFilePath($name)
    {
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $path = Typecho_Common::url("ShiroiRightKey/style/{$name}", Helper::options()->pluginUrl);
        if ($extension == 'css') {
            return '<link rel="stylesheet" href="' . $path . '">';
        } elseif ($extension == 'js') {
            return '<script src="' . $path . '"></script>';
        }
    }

    /**
     * 值返回
     * @param $value
     * @return string
     * @throws Typecho_Exception
     */
    public static function toValue($value)
    {
        return htmlspecialchars(Typecho_Widget::widget('Widget_Options')->plugin('ShiroiRightKey')->$value);
    }
}