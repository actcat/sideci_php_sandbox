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
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

/**
 * This form class is responsible to generate the basic product information form
 */
class ProductSpecificPrice extends CommonAbstractType
{
    private $translator;
    private $locales;
    private $shops;
    private $countries;
    private $currencies;
    private $groups;

    /**
     * Constructor
     *
     * @param object $router
     * @param object $translator
     * @param object $shopContextAdapter
     * @param object $countryDataprovider
     * @param object $currencyDataprovider
     * @param object $groupDataprovider
     * @param object $legacyContext
     */
    public function __construct($router, $translator, $shopContextAdapter, $countryDataprovider, $currencyDataprovider, $groupDataprovider, $legacyContext)
    {
        $this->router = $router;
        $this->translator = $translator;
        $this->locales = $legacyContext->getLanguages();
        $this->shops = $this->formatDataChoicesList($shopContextAdapter->getShops(), 'id_shop');
        $this->countries = $this->formatDataChoicesList($countryDataprovider->getCountries($this->locales[0]['id_lang']), 'id_country');
        $this->currencies = $this->formatDataChoicesList($currencyDataprovider->getCurrencies(), 'id_currency');
        $this->groups = $this->formatDataChoicesList($groupDataprovider->getGroups($this->locales[0]['id_lang']), 'id_group');
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('sp_id_shop', 'choice', array(
            'choices' =>  $this->shops,
            'required' =>  false,
            'label' =>  false,
            'placeholder' => $this->translator->trans('All shops', [], 'AdminProducts'),
        ))
        ->add('sp_id_currency', 'choice', array(
            'choices' =>  $this->currencies,
            'required' =>  false,
            'label' =>  false,
            'placeholder' =>  $this->translator->trans('All currencies', [], 'AdminProducts'),
        ))
        ->add('sp_id_country', 'choice', array(
            'choices' =>  $this->countries,
            'required' =>  false,
            'label' =>  false,
            'placeholder' => $this->translator->trans('All countries', [], 'AdminProducts'),
        ))
        ->add('sp_id_group', 'choice', array(
            'choices' =>  $this->groups,
            'required' =>  false,
            'label' =>  false,
            'placeholder' => $this->translator->trans('All groups', [], 'AdminProducts'),
        ))
        //TODO : add customer field
        ->add('sp_id_product_attribute', 'choice', array(
            'choices' =>  [],
            'required' =>  false,
            'placeholder' => $this->translator->trans('Apply to all combinations', [], 'AdminProducts'),
            'label' => $this->translator->trans('Combination:s', [], 'AdminProducts'),
            'attr' => ['data-action' =>  $this->router->generate('admin_get_product_combinations')],
        ))
        ->add('sp_from', 'text', array(
            'required' => false,
            'label' => $this->translator->trans('from', [], 'AdminProducts'),
            'attr' => ['class' => 'date', 'placeholder' => 'YYYY-MM-DD HH:II']
        ))
        ->add('sp_to', 'text', array(
            'required' => false,
            'label' => $this->translator->trans('to', [], 'AdminProducts'),
            'attr' => ['class' => 'date', 'placeholder' => 'YYYY-MM-DD HH:II']
        ))
        ->add('sp_from_quantity', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Starting at', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'numeric')),
            )
        ))
        ->add('sp_price', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Product price', [], 'AdminProducts'),
            'attr' => ['class' => 'price']
        ))
        ->add('leave_bprice', 'checkbox', array(
            'label'    => $this->translator->trans('Leave base price:', [], 'AdminProducts'),
            'required' => false,
        ))
        ->add('sp_reduction', 'number', array(
            'required' => false,
        ))
        ->add('sp_reduction_type', 'choice', array(
            'choices'  => array(
                'amount' => '€',
                'percentage' => $this->translator->trans('%', [], 'AdminProducts'),
            ),
            'required' => true,
        ))
        ->add('sp_reduction_tax', 'choice', array(
            'choices'  => array(
                '0' => $this->translator->trans('Tax excluded', [], 'AdminProducts'),
                '1' => $this->translator->trans('Tax included', [], 'AdminProducts'),
            ),
            'required' => true,
        ))
        ->add('save', 'button', array(
            'label' => $this->translator->trans('Save', [], 'AdminProducts'),
            'attr' => array('class' => 'js-save pull-right btn-primary'),
        ));

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if (empty($data['sp_id_product_attribute'])) {
                return;
            }

            //bypass SF validation, define submitted value in choice list
            $form->add('sp_id_product_attribute', 'choice', array(
                'choices' =>  [$data['sp_id_product_attribute'] => ''],
                'required' =>  false,
            ));
        });
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'product_combination';
    }
}
