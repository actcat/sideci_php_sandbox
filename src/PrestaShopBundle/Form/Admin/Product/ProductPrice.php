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
use Symfony\Component\Validator\Constraints as Assert;
use PrestaShopBundle\Form\Admin\Product\ProductSpecificPrice;

/**
 * This form class is responsible to generate the product price form
 */
class ProductPrice extends CommonAbstractType
{
    private $translator;
    private $tax_rules;

    /**
     * Constructor
     *
     * @param object $translator
     * @param object $taxDataProvider
     * @param object $router
     * @param object $shopContextAdapter
     * @param object $countryDataprovider
     * @param object $currencyDataprovider
     * @param object $groupDataprovider
     * @param object $legacyContext
     */
    public function __construct($translator, $taxDataProvider, $router, $shopContextAdapter, $countryDataprovider, $currencyDataprovider, $groupDataprovider, $legacyContext)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->shopContextAdapter = $shopContextAdapter;
        $this->countryDataprovider = $countryDataprovider;
        $this->currencyDataprovider = $currencyDataprovider;
        $this->groupDataprovider= $groupDataprovider;
        $this->legacyContext = $legacyContext;
        $this->tax_rules = $this->formatDataChoicesList(
            $taxDataProvider->getTaxRulesGroups(true),
            'id_tax_rules_group'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('price', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Pre-tax retail price', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('id_tax_rules_group', 'choice', array(
            'choices' =>  $this->tax_rules,
            'required' => true,
            'label' => $this->translator->trans('Tax rule:', [], 'AdminProducts'),
        ))
        ->add('price_ttc', 'number', array(
            'required' => false,
            'mapped' => false,
            'label' => $this->translator->trans('Retail price with tax', [], 'AdminProducts'),
        ))
        ->add('on_sale', 'checkbox', array(
            'required' => false,
            'label' => $this->translator->trans('On sale', [], 'AdminProducts'),
        ))
        ->add('wholesale_price', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Pre-tax wholesale price', [], 'AdminProducts')
        ))
        ->add('unit_price', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Unit price (tax excl.)', [], 'AdminProducts')
        ))
        ->add('unity', 'text', array(
            'required' => false,
            'label' => $this->translator->trans('per', [], 'AdminProducts')
        ))
        ->add('specific_price', new ProductSpecificPrice(
            $this->router,
            $this->translator,
            $this->shopContextAdapter,
            $this->countryDataprovider,
            $this->currencyDataprovider,
            $this->groupDataprovider,
            $this->legacyContext
        ))
        ->add('specificPricePriorityToAll', 'checkbox', array(
            'required' => false,
            'label' => $this->translator->trans('Apply to all products', [], 'AdminProducts'),
        ));

        //generates fields for price priority
        $specificPricePriorityChoices = [
            'id_shop' => $this->translator->trans('Shop', [], 'AdminProducts'),
            'id_currency' => $this->translator->trans('Currency', [], 'AdminProducts'),
            'id_country' => $this->translator->trans('Country', [], 'AdminProducts'),
            'id_group' => $this->translator->trans('Group', [], 'AdminProducts'),
        ];

        for ($i=0; $i < count($specificPricePriorityChoices); $i++) {
            $builder->add('specificPricePriority_'.$i, 'choice', array(
                'choices' => $specificPricePriorityChoices,
                'required' => true
            ));
        }
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'product_price';
    }
}
