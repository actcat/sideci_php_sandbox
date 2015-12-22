/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

(function ($) {
	$.fn.categorytree = function (settings) {
		var isMethodCall = (typeof settings == 'string'), // is this a method call like $().jstree("open_node")
			returnValue = this;
		// if a method call execute the method on all selected instances
		if(isMethodCall) {
			// Put here code when called like $(item).categorytree('method');
		}
		// initialize tree
		else {
			$("li > ul", this).each(function(i, item) {
				$(item).prev("div").on('click', function(event) {
					var ui = event.target;
					$(ui).next("ul").toggle();
					if ($(ui).next("ul").is(":visible")) {
						$(ui).parent("li").attr("style", "list-style-image:url('"+ baseDir +"web/bundles/framework/images/blue_picto_less.gif')");
					} else {
						$(ui).parent("li").attr("style", "list-style-image:url('"+ baseDir +"web/bundles/framework/images/blue_picto_more.gif')");
					}
				});
				if ($(item).find("input:radio:checked").length == 0) {
					$(item).toggle(); // initial collapse, except if child selected
				}
				if ($(item).is(":visible")) {
					$(item).parent("li").attr("style", "list-style-image:url('"+ baseDir +"web/bundles/framework/images/blue_picto_less.gif')");
				} else {
					$(item).parent("li").attr("style", "list-style-image:url('"+ baseDir +"web/bundles/framework/images/blue_picto_more.gif')");
				}
			});
			$("li:not(:has(ul))", this).each(function(i, item) {
				$(item).attr("style", "list-style-image:none;"); // leafs do not need the folder icon.
			});
		}
		// return the jquery selection (or if it was a method call that returned a value - the returned value)
		return returnValue;
	};
})(jQuery);
