<?php
/**
 * TagSearch type.
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class TagSearchType.
 */
class TagSearchType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder The form builder
     * @param array                                        $options The options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'phrase',
            TextType::class,
            [
                'label' => 'label_name',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new Length(['min' => 3, 'max' => 64]),
                    new NotBlank(),
                ],
            ]
        );
    }
}
