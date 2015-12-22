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
namespace PrestaShopBundle\Form\Admin\Category;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This form class is responsible to generate the basic category form
 * Name (not translated), and parent category selector
 */
class SimpleCategory extends CommonAbstractType
{
    private $translator;
    private $categories;
    private $ajax;

    /**
     * Constructor
     *
     * @param object $translator
     * @param object $categoryDataProvider
     * @param bool $ajax If the form is called from ajax query
     */
    public function __construct($translator, $categoryDataProvider, $ajax = false)
    {
        $this->translator = $translator;
        $this->ajax = $ajax;
        $this->formatValidList($categoryDataProvider->getNestedCategories());
    }

    /**
     * Create and format a valid array keys categories that can be validate by the choice SF2 cform component
     *
     * @param array $list The nested array categories
     */
    protected function formatValidList($list)
    {
        foreach ($list as $item) {
            $this->categories[$item['id_category']] = $item['name'];

            if (isset($item['children'])) {
                $this->formatValidList($item['children']);
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
            'label' => $this->translator->trans('Name', [], 'AdminCategories'),
            'required' => false,
            'attr' => ['placeholder' => $this->translator->trans('Name', [], 'AdminCategories'), 'class' => 'ajax'],
            'constraints' => $this->ajax ? [] : array(
                new Assert\NotBlank(),
                new Assert\Length(array('min' => 3))
            )
        ))
        ->add('id_parent', 'choice', array(
            'choices' =>  $this->categories,
            'required' =>  true,
            'label' => $this->translator->trans('Parent category', [], 'AdminProducts')
        ))
        ->add('save', 'button', array(
            'label' => $this->translator->trans('Save', [], 'AdminCategories'),
            'attr' => ['class' => 'submit'],
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'new_simple_category';
    }
}
