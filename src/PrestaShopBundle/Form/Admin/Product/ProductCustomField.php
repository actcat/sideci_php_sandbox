<?php
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
namespace PrestaShopBundle\Form\Admin\Product;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This form class is responsible to generate the product custom fields configuration form
 */
class ProductCustomField extends CommonAbstractType
{
    private $translator;
    private $locales;

    /**
     * Constructor
     *
     * @param object $translator
     * @param object $legacyContext
     */
    public function __construct($translator, $legacyContext)
    {
        $this->translator = $translator;
        $this->locales = $legacyContext->getLanguages();
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('label', new TranslateType('text', array(
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Length(array('min' => 2))
            )
        ), $this->locales), array(
            'label' => ''
        ))
        ->add('type', 'choice', array(
            'choices'  => array(
                '1' => $this->translator->trans('Text', [], 'AdminProducts'),
                '0' => $this->translator->trans('File', [], 'AdminProducts'),
            ),
            'required' =>  true
        ))->add('require', 'checkbox', array(
            'label'    => $this->translator->trans('Required', [], 'AdminProducts'),
            'required' => false,
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'product_custom_field';
    }
}
